<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Controller\Admin;

use Ai\Model\Orm\Service;
use Ai\Model\Orm\ServiceModel;
use Ai\Model\ServiceApi;
use Ai\Model\ServiceModelApi;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;
use RS\Html\Toolbar;

/**
 * Контроллер управления промптами для полей объектов
 */
class ServiceCtrl extends Crud
{
    public function __construct()
    {
        parent::__construct(new ServiceApi());
    }

    /**
     * Помощник компановки страницы
     *
     * @return \RS\Controller\Admin\Helper\CrudCollection
     */
    public function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Профили подключения к GPT-сервисам'));
        $helper->setTopHelp(t('В этом разделе вы можете создать/изменить учетную запись GPT-Сервиса, через который может происходить генерация текста. <br>Чтобы воспользоваться данным разделом, предварительно установите модуль, добавляющий прямые интеграции с GPT-сервисами в <a href="%marketplace">нашем маркетплейсе</a>. Профили едины для всех мультисайтов.', [
            'marketplace' => \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$MARKETPLACE_DOMAIN.'/addons/aiconnectors/'
        ]));

        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new Toolbar\Button\Add($this->router->getAdminUrl('add'), t('Добавить сервис'))
            ]
        ]));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'id', 'Sortable' => SORTABLE_ASC, 'CurrentSort' => SORTABLE_ASC, 'ThAttr' => ['width' => '20']]),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('type', t('Тип API'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('api_organization', t('Организация'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                new TableType\Text('api_project', t('Проект'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                new TableType\StrYesno('is_default', t('По умолчанию'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\StrYesno('is_chat_default', t('Для чата'), ['Sortable' => SORTABLE_BOTH]),

                new TableType\Text('id', '№', ['ThAttr' => ['width' => '50'], 'TdAttr' => ['class' => 'cell-sgray'], 'Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_DESC]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id',
                        ]
                    ]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('клонировать'),
                            'attr' => [
                                'class' => 'crud-add',
                                '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                            ]
                        ]
                    ])
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move')
            ]
        ]));

        //Параметры фильтра
        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['items' => [
                        new Filter\Type\Text('id', '№', ['Attr' => ['size' => 4]]),
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                        new Filter\Type\Select('type', t('Тип API'), \Ai\Model\ServiceApi::getServiceTypeTitles([
                            '' => t('- Не выбрано -')
                        ], true)),
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
     * Форма добавления элемента
     *
     * @param mixed $primaryKeyValue - id редактируемой записи
     * @param boolean $returnOnSuccess - Если true, то будет возвращать === true при успешном сохранении, иначе будет вызов стандартного _successSave метода
     * @param CrudCollection $helper - текуй хелпер
     * @return \RS\Controller\Result\Standard|bool
     */
    public function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $elem = $this->getApi()->getElement();
        $elem['__model']->setList(['\Ai\Model\ServiceModelApi', 'staticSelectListByType'], $elem['type'], ['' => t('- Не выбрано -')]);

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     * Выполняет action(действие) текущего контроллера, возвращает результат действия
     *
     * @param boolean $returnAsIs - возвращать как есть. Если true, то метод будет возвращать точно то,
     * что вернет действие, иначе результат будет обработан методом processResult
     *
     * @return mixed
     * @throws \RS\Controller\Exception
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    function exec($returnAsIs = false)
    {
        try {
            return parent::exec($returnAsIs);
        } catch (\Ai\Model\Exception $e) {
            return $this->result
                ->setTemplate(null)
                ->addSection([
                    'close_dialog' => true
                ])
                ->addEMessage(t('Модуль GPT-сервиса не найден. Невозможно открыть для редактирования профиль. Вы можете только удалить данный профиль.'));
        }
    }

    /**
     * Возвращает форму с настройками конкретного типа GPT-сервиса
     *
     * @return Standard
     */
    function actionGetTypeForm()
    {
        $type = $this->url->request('type', TYPE_STRING);
        $service = new Service();
        $service['type'] = $type;
        $service_type = $this->api->getServiceTypeById($type, $service);
        $this->view->assign('service_type', $service_type);
        $this->result->setTemplate( '%ai%/admin/service/type_form.tpl' );

        return $this->result;
    }

    /**
     * Загружает список моделей для GPT-сервиса
     *
     * @return Standard
     */
    public function actionLoadModels()
    {
        //Загружать справочник только из локальной базы
        $local = $this->url->get('local', TYPE_INTEGER);
        $service = new Service();
        $service->fillFromPost();
        $service_type = $service->getServiceTypeObject();

        try {
            if (!$local) {
                $models = $service_type->getModelList();
                foreach ($models as $id => $title) {
                    $service_model = new ServiceModel();
                    $service_model['service_type'] = $service['type'];
                    $service_model['title'] = $title;
                    $service_model['model_key'] = $id;
                    $service_model->insert();
                }
            }
            $service_type_form = $service_type->getSettingsFormObject();
            $service_type_form['__model']->setListFromArray(ServiceModelApi::staticSelectListByType($service['type'], ['' => t('- Не выбрано -')]));

            $this->view->assign([
                'elem' => $service_type_form,
                'field' => $service_type_form['__model']
            ]);

            return $this->result
                ->setSuccess(true)
                ->setTemplate('%ai%/admin/service/model_form.tpl');

        } catch (\Throwable $e) {
            if ($e instanceof \Error) {
                $message = t('Сервис, по указанному адресу возвращает данные, отличающиеся от ожидаемого формата. Проверьте корректность URL API или выберите другой тип API.');
            } else {
                $message = $e->getMessage();
            }

            return $this->result
                ->setSuccess(false)
                ->addEMessage($message);
        }
    }
}