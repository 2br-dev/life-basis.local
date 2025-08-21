<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Controller\Admin;

use Ai\Config\ModuleRights;
use Ai\Model\Orm\Statistic;
use Ai\Model\ServiceApi;
use Ai\Model\StatisticApi;
use Ai\Model\TransformerApi;
use RS\AccessControl\Rights;
use RS\Controller\Admin\Crud;
use RS\Html\Toolbar;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;
use RS\Router\Manager;

/**
 * Контроллер для просмотра статичтики использования токенов
 */
class StatisticCtrl extends Crud
{
    function __construct()
    {
        parent::__construct(new StatisticApi());
    }

    /**
     * Помощник компановки страницы
     *
     * @return \RS\Controller\Admin\Helper\CrudCollection
     */
    public function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Статистика запросов к ИИ'));
        $helper->setTopHelp(t('В этом разделе, вы можете анализировать расход токенов в GPT-сервисах, при условии, что сервис позволяет собирать такую статистику. Информация об использованных токенах как правило приходит от GPT-сервисов сразу после генерации.'));

        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new Toolbar\Button\Button(
                    Manager::obj()->getAdminUrl('exportCsv', ['schema' => 'ai-statistic', 'referer' => $this->url->selfUri()], 'main-csv'),
                    t('Экспорт'),
                    ['attr' => ['class' => 'crud-add']])
            ]
        ]));
        $view_url = $this->router->getAdminPattern('view', [':id' => '@id']);
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('id', '№', ['ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Datetime('date_of_create', t('Дата создания'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\User('user_id', t('Пользователь'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('service_id', t('GPT-сервис'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('transformer_id', t('Транформер'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('type', t('Тип генерации'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('field', t('Поле'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('prompt_id', t('ID промпта'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('task_id', t('ID задачи'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                new TableType\Text('task_result_id', t('ID результата задачи'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                new TableType\Text('site_id', t('ID мультисайта'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('input_text_tokens', t('Токенов в запросе'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('completion_tokens', t('Токенов в ответе'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('total_tokens', t('Всего токенов'), ['Sortable' => SORTABLE_BOTH]),

                new TableType\Actions('id', [
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ]
        ]));

        //Параметры фильтра
        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['items' => [
                        new Filter\Type\Text('id', '№', ['Attr' => ['size' => 4]]),
                        new Filter\Type\User('user_id', t('Пользователь')),
                        new Filter\Type\DateRange('date_of_create', 'Дата', ['Attr' => ['size' => 4]]),
                        new Filter\Type\Select('service_id', t('GPT-сервис'), ServiceApi::staticSelectList(['' => t('- Любой -')])),
                        new Filter\Type\Select('transformer_id', t('Трансформер'), TransformerApi::staticSelectList(['' => t('- Любой -')])),
                        new Filter\Type\Select('type', t('Тип генерации'), Statistic::getTypeTitles(['' => t('- Любой -')])),
                        new Filter\Type\Select('site_id', t('Мультисайт'), ['' => t('- Любой -')] + array_map(function($value) {
                            return $value['title'];
                        }, \RS\Site\Manager::getSiteList())),
                        new Filter\Type\Text('prompt_id', t('ID промпта')),
                        new Filter\Type\Text('task_id', t('ID задачи')),
                        new Filter\Type\Text('task_result_id', t('ID результата задачи')),
                        new Filter\Type\Text('input_text_tokens', t('Кол-во токенов запроса'), ['showType' => true]),
                        new Filter\Type\Text('completion_tokens', t('Кол-во токенов ответа'), ['showType' => true]),
                        new Filter\Type\Text('total_tokens', t('Общее кол-во токенов'), ['showType' => true]),
                    ]])
                ]
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()]
        ]));

        $helper->setBottomToolbar($this->buttons(['delete']));
        $helper->viewAsTable();

        return $helper;
    }


    /**
     * Отображение списка
     */
    public function actionIndex()
    {
        $helper = $this->getHelper();
        $this->view->assign('elements', $helper->active());
        $this->url->saveUrl($this->controller_name . 'index');
        $this->api->saveRequest($this->controller_name . '_list');

        if (!Rights::hasRight($this, ModuleRights::RIGHT_STATISTIC_SHOW_ALL)) {
            $this->api->setFilter('user_id', $this->user->id);
        }

        $this->view->assign([
            'statistic_tokens' => $this->api->getStatisticTokens()
        ]);
        $helper->setBeforeTableContent($this->view->fetch('%ai%/admin/statistic/before_table.tpl'));

        return $this->result->setHtml($this->view->fetch($helper['template']))->getOutput();
    }
}