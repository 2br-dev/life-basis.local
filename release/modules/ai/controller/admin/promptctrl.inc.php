<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Controller\Admin;

use Ai\Model\PromptApi;
use Ai\Model\TransformerApi;
use RS\Controller\Admin\Crud;
use RS\Html\Category\Element;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;
use RS\Html\Toolbar;

/**
 * Контроллер управления промптами для полей объектов
 */
class PromptCtrl extends Crud
{
    public $transformer_id;

    public function __construct()
    {
        parent::__construct(new PromptApi());
        $this->transformer_id = $this->url->get('tid', TYPE_STRING, '');
    }

    /**
     * Помощник компановки страницы
     *
     * @return \RS\Controller\Admin\Helper\CrudCollection
     */
    public function helperIndex()
    {
        if ($this->transformer_id) {
            $this->getApi()->setFilter('transformer_id', $this->transformer_id);
        }

        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Шаблоны запросов на генерацию текстов'));
        $helper->setTopHelp(t('В этом разделе вы можете создать/изменить запрос к GPT-сервису для конкретного поля объекта, чтобы затем в 1 клик генерировать текстовое значение для данного поля. <br>Допустимы различные шаблоны на разных мультисайтах. В случае необходимости восстановления стандартных шаблонов, перейдите в настройки модуля и запустите утилиту "Установить демо данные".'));

        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new Toolbar\Button\Add($this->router->getAdminUrl('add', ['tid' => $this->transformer_id]), t('Добавить шаблон')),
            ]
        ]));
        $helper->addCsvButton('ai-prompt');
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'id', 'Sortable' => SORTABLE_ASC, 'CurrentSort' => SORTABLE_ASC, 'ThAttr' => ['width' => '20']]),
                new TableType\Text('transformer_id', t('Объект'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('field', t('Поле'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('note', t('Примечание'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('prompt', t('Запрос')),
                new TableType\Text('service_id', t('GPT-сервис')),
                new TableType\Text('temperature', t('Креативность'), ['Hidden' => true]),
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
                    ]])
                ]
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()]
        ]));

        $helper->setCategory(new Element([
            'sortIdField' => 'id',
            'activeField' => 'id',
            'activeValue' => $this->transformer_id,
            'rootItem' => [
                'id' => '',
                'title' => t('Все'),
                'noOtherColumns' => true,
                'noCheckbox' => true,
                'noDraggable' => true,
                'noRedMarker' => true
            ],
            'noExpandCollapseButton' => true,
            'noCheckbox' => true,
            'sortable' => false,
            'mainColumn' => new TableType\Text('title', t('Название'), ['href' => $this->router->getAdminPattern(false, [':tid' => '@id'])]),
            'headButtons' => [
                [
                    'text' => t('Объекты'),
                    'tag' => 'span',
                    'attr' => [
                        'class' => 'lefttext'
                    ]
                ],
            ],

        ]), new TransformerApi());

        $helper->setCategoryBottomToolbar(new Toolbar\Element());
        $helper->setBottomToolbar($this->buttons(['delete']));
        $helper->viewAsTableCategory();

        return $helper;
    }

    /**
     * Отвечает за создание нового промпта
     *
     * @param $primaryKeyValue
     * @param $returnOnSuccess
     * @param $helper
     * @return bool|\RS\Controller\Result\Standard
     */
    public function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $elem = $this->getApi()->getElement();
        if ($primaryKeyValue === null) { //Только на создание (null), а не на клонирование (0)
            $elem->transformer_id = $this->transformer_id;
        }

        if ($elem->transformer_id) {
            $elem['__field']->setListFromArray(TransformerApi::staticSelectFieldList($elem->transformer_id));
        } else {
            $elem['__field']->setListFromArray([
                '' => t('Не выбрано')
            ]);
        }

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     * Возвращает select со списком полей
     *
     * @return bool|\RS\Controller\Result\Standard
     */
    public function actionChangeTransformer()
    {
        $elem = $this->api->getElement();
        $elem['transformer_id'] = $this->url->get('transformer_id', TYPE_STRING);
        $elem['__field']->setListFromArray(TransformerApi::staticSelectFieldList($elem['transformer_id']));
        $this->view->assign('elem', $elem);
        return $this->result->setTemplate('%ai%/admin/prompt/field_form.tpl');
    }
}