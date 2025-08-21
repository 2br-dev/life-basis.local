<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Model\AutoTask\AbstractIfRule;
use Crm\Model\Autotask\AbstractThenRule;
use Crm\Model\AutoTaskApi;
use Crm\Model\AutotaskCategoryApi;
use RS\Controller\Admin\Crud;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;
use RS\Html\Category;
use RS\Html\Toolbar;
use RS\Html\Toolbar\Button as ToolbarButton;

class AutoTaskCtrl extends Crud
{
    public $category;

    function __construct()
    {
        //Устанавливаем, с каким API будет работать CRUD контроллер
        parent::__construct(new AutoTaskApi());
        $this->setCategoryApi(new AutotaskCategoryApi(), t('категорию'));
    }

    function actionIndex()
    {
        //Если категории не существует, то выбираем пункт "Все"
        if ($this->category > 0 && !$this->getCategoryApi()->getById($this->category)) $this->category = 0;
        if ($this->category > 0) $this->api->setFilter('category_id', $this->category);

        return parent::actionIndex();
    }

    function helperIndex()
    {
        $helper = parent::helperIndex(); //Получим helper по-умолчанию
        $helper->setTopTitle('Автозадачи'); //Установим заголовок раздела
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить')]));
        $helper->setTopHelp(t('Здесь описываются правила, согласно которым система будет автоматически выполнять действия при наступлении выбранных событий'));

        //Отобразим таблицу со списком объектов
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray'], 'ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH]),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Yesno('enable', t('Включен'), ['toggleUrl' => $this->router->getAdminPattern('ajaxTogglePublic', [':id' => '@id'])]),

                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('клонировать автозадачу'),
                            'attr' => [
                                'class' => 'crud-add',
                                '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                            ]
                        ],
                    ])
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                )
            ]]));

        //Добавим фильтр значений в таблице по названию
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                'Lines' =>  [
                    new Filter\Line( ['items' => [
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                    ]
                    ])
                ],
            ])
        ]));

        $this->category = $this->url->request('category', TYPE_STRING);
        $helper->setCategory(new Category\Element([
            'sortIdField' => 'id',
            'activeField' => 'id',
            'activeValue' => $this->category,
            'rootItem' => [
                'id' => 0,
                'title' => t('Все'),
                'noOtherColumns' => true,
                'noCheckbox' => true,
                'noDraggable' => true,
                'noRedMarker' => true
            ],
            'noExpandCollapseButton' => true,
            'sortable' => false,
            'mainColumn' => new TableType\Text('title', t('Название'), ['href' => $this->router->getAdminPattern(false, [':category' => '@id'])]),
            'tools' => new TableType\Actions('id', [
                new TableType\Action\Edit($this->router->getAdminPattern('categoryEdit', [':id' => '~field~']), null, [
                    'attr' => [
                        '@data-id' => '@id'
                    ]
                ]),
                new TableType\Action\DropDown([
                    [
                        'title' => t('клонировать категорию'),
                        'attr' => [
                            'class' => 'crud-add',
                            '@href' => $this->router->getAdminPattern('categoryClone', [':id' => '~field~']),
                        ]
                    ],
                ])
            ]),
            'headButtons' => [
                [
                    'attr' => [
                        'title' => t('Создать категорию'),
                        'href' => $this->router->getAdminUrl('categoryAdd'),
                        'class' => 'add crud-add'
                    ]
                ]
            ],
        ]), $this->getCategoryApi());

        $helper->setCategoryFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                    ]])
                ],
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->getCategoryApi()->defAlias()],
            'filterVar' => 'c',
            'Caption' => t('Поиск по категориям')
        ]));

        $helper->setCategoryBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Multiedit($this->router->getAdminUrl('categoryMultiEdit')),
                new ToolbarButton\Delete(null, null, [
                    'attr' => ['data-url' => $this->router->getAdminUrl('categoryDel')]
                ]),
            ],
        ]));

        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));
        $helper->viewAsTableCategory();

        return $helper;
    }

    /**
     * Переключение флага публичности
     *
     */
    function actionAjaxTogglePublic()
    {
        $id = $this->url->get('id', TYPE_STRING);

        $autotask = $this->api->getOneItem($id);
        if ($autotask) {
            $autotask['enable'] = !$autotask['enable'];
            $autotask->update();
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Загружает установленные параметры и добавляет необходимые переменные для шаблона
     *
     * @return void
     * @throws \RS\Exception
     */
    public function setInitParams()
    {
        if ($object = $this->api->getElement()) {
            if (isset($object['if_type'])) {
                $if_type_class = AbstractIfRule::getClassById($object['if_type']);

                $this->view->assign('if_type', $object['if_type']);
                $this->view->assign('if_type_class', $if_type_class);

                if (isset($object['if_action'])) {
                    $this->view->assign('if_action', $object['if_action']);
                }
                if (isset($object['if_params_arr'])) {
                    $if_params = [];
                    if ($params = $if_type_class->getParams($object['if_action'])) {
                        foreach ($object['if_params_arr'] as $key => $value) {
                            if (isset($params[$key])) {
                                $if_params[$key] = [
                                    'label' => $params[$key],
                                    'selected' => $value['value'],
                                    'type' => $if_type_class->getNodeType($key),
                                    'values' => $if_type_class->getParamValues($key),
                                    'multiple' => $if_type_class->isMultiple($key),
                                ];
                            }
                        }
                    }
                    $this->view->assign('main_if_params', $params);
                    $this->view->assign('if_params', $if_params);
                }
            }
            if (isset($object['then_type'])) {
                $then_type_class = AbstractThenRule::getClassById($object['then_type']);

                $this->view->assign('then_type', $object['then_type']);
                $this->view->assign('then_type_class', $then_type_class);

                if (isset($object['then_action'])) {
                    $this->view->assign('then_action', $object['then_action']);
                }
                if (isset($object['then_params_arr']['params'])) {
                    $then_params = [];
                    if ($params = $then_type_class->getParams()) {
                        foreach ($object['then_params_arr']['params'] as $key => $value) {
                            if (isset($value['key']) && isset($value['value'])) {
                                if (isset($params[$key]) && isset($value['value'])) {
                                    $iterator = $then_type_class->getPropertyIteratorField($key, $object['then_action'], 'params', $value['value']);
                                    $then_params[$key] = [
                                        'label' => $params[$key],
                                        'value' => $value['value'],
                                        'name' => str_replace(['[value][]', '[value]'], '[key]', $iterator->getFormName()),
                                        'field' => $iterator,
                                    ];
                                }
                            }else {
                                foreach ($value as $item) {
                                    if (isset($params[$item['key']])) {
                                        $iterator = $then_type_class->getPropertyIteratorField($item['key'], $object['then_action'], 'params', $item['value']);
                                        $then_params[$item['key']] = [
                                            'label' => $params[$item['key']],
                                            'value' => $item['value'],
                                            'name' => str_replace(['[value][]', '[value]'], '[key]', $iterator->getFormName()),
                                            'field' => $iterator,
                                        ];
                                    }
                                }
                            }
                        }
                    }
                    $this->view->assign('then_params', $then_params);
                    $this->view->assign('main_then_params', $params);
                }
                if (isset($object['then_params_arr']['conditions'])) {
                    $then_params = [];
                    if ($params = $then_type_class->getConditionParams()) {
                        foreach ($object['then_params_arr']['conditions'] as $key => $value) {
                            if (isset($params[$key])) {
                                $then_params[$key] = [
                                    'label' => $params[$key],
                                    'value' => $value['value'],
                                    'field' => $then_type_class->getPropertyIteratorField($key, $object['then_action'], 'conditions', $value['value']),
                                ];
                            }
                        }
                    }
                    $this->view->assign('then_conditions', $then_params);
                    $this->view->assign('main_then_conditions', $params);
                }
            }
        }
    }

    /**
     * Отображает список задач
     *
     * @param null $primaryKeyValue
     * @param bool $returnOnSuccess
     * @param null $helper
     * @return bool|\RS\Controller\Result\Standard
     */
    public function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        if ($this->url->isPost()){
            $if_params_arr = $this->url->request('if_params_arr', TYPE_ARRAY);
            $then_params_arr = $this->url->request('then_params_arr', TYPE_ARRAY);
            $type = $this->url->request('if_type', TYPE_STRING);
            if ($type && $type_class = AbstractIfRule::getClassById($type)) {
                if (!empty($type_class->getParams())) {
                    // Проверяем условия и действия, с целью не допустить цикличность создания задач
                    $matches = count($if_params_arr) > 0; //Сразу ставим false, если нет условий
                    foreach ($if_params_arr as $key => $value) {
                        if (isset($then_params_arr[$key])) {
                            if ($then_params_arr[$key]['value'] !== $value['value']) {
                                $matches = false;
                                break;
                            }
                        } else {
                            $matches = false;
                            break;
                        }
                    }

                    if ($matches) {
                        $this->result->addEMessage(t('Подобное сочетание условия и действия недопустимо. 
                                                     Это может привести к ошибкам при создании автозадач.'));
                        return $this->result->setSuccess(false);
                    }
                }
            }
        }

        $this->setInitParams();

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     * Возвращает HTML-код доступных действий для условия
     */
    function actionAjaxGetIfActionsFromType()
    {
        $type = $this->url->get('type', TYPE_STRING);
        $type_class = AbstractIfRule::getClassById($type);

        $this->result->addSection('params', $type_class->getParams());
        $this->result->addSection('vars', $type_class->getReplaceVarTitles());
        $this->view->assign('type_class', $type_class);
        return $this->result->setTemplate( $type_class->getTplFolder() . '/form/autotask/autotask_if_action.tpl' );
    }

    /**
     * Возвращает список доступных дополнительных параметров для условия
     */
    function actionAjaxGetIfParamsFromType()
    {
        $type = $this->url->get('type', TYPE_STRING);
        $action = $this->url->get('action', TYPE_STRING);
        $existing_params = $this->url->get('existingParams', TYPE_ARRAY);
        $type_class = AbstractIfRule::getClassById($type);
        $params = $type_class->getParams($action);
        $expandedParams = [];

        // Исключаем уже существующие параметры
        $filteredParams = array_filter($params, function ($key) use ($existing_params) {
            return !in_array($key, $existing_params);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($filteredParams as $key => $label) {
            $expandedParams[$key] = [
                'label' => $label,
                'type' => $type_class->getNodeType($key),
                'values' => $type_class->getParamValues($key),
                'multiple' => $type_class->isMultiple($key),
            ];
        }

        $this->result->addSection('params', $expandedParams);
        $this->result->addSection('params_count', count($params));
        return $this->result->setSuccess(true);
    }

    /**
     * Возвращает HTML-код доступных действий для действия
     */
    function actionAjaxGetThenActionsFromType()
    {
        $if_type = $this->url->get('if_type', TYPE_STRING);
        $then_type = $this->url->get('then_type', TYPE_STRING);
        if ($if_type) {
            $if_type_class = AbstractIfRule::getClassById($if_type);
            $this->view->assign('if_type_class', $if_type_class);
        }
        $then_type_class = AbstractThenRule::getClassById($then_type);

        $this->view->assign('then_type_class', $then_type_class);
        return $this->result->setTemplate( $then_type_class->getTplFolder() . '/form/autotask/autotask_then_action.tpl' );
    }

    /**
     * Возвращает список доступных дополнительных параметров для действия
     */
    function actionAjaxGetThenParamsFromType()
    {
        $type = $this->url->get('type', TYPE_STRING);
        $existing_params = $this->url->get('existingParams', TYPE_ARRAY);
        $type_class = AbstractThenRule::getClassById($type);
        $params = $type_class->getParams();

        // Исключаем уже существующие параметры
        $filteredParams = array_filter($params, function ($key) use ($existing_params) {
            return !in_array($key, $existing_params);
        }, ARRAY_FILTER_USE_KEY);

        $this->result->addSection('params', $filteredParams);
        $this->result->addSection('params_count', count($params));
        return $this->result->setSuccess(true);
    }

    /**
     * Возвращает список доступных дополнительных параметров выборки для действия
     */
    function actionAjaxGetThenConditionParamsFromType()
    {
        $type = $this->url->get('type', TYPE_STRING);
        $existing_params = $this->url->get('existingParams', TYPE_ARRAY);
        $type_class = AbstractThenRule::getClassById($type);
        $params = $type_class->getConditionParams();

        // Исключаем уже существующие параметры
        $filteredParams = array_filter($params, function ($key) use ($existing_params) {
            return !in_array($key, $existing_params);
        }, ARRAY_FILTER_USE_KEY);

        $this->result->addSection('params', $filteredParams);
        $this->result->addSection('params_count', count($params));
        return $this->result->setSuccess(true);
    }

    /**
     * Возвращает HTML-код доступных значений для дополнительных параметров для действия
     */
    function actionAjaxGetThenParamDataFromType()
    {
        $type = $this->url->get('type', TYPE_STRING);
        $field = $this->url->get('field', TYPE_STRING);
        $action = $this->url->get('action', TYPE_STRING);
        $params_type = $this->url->get('params_type', TYPE_STRING);
        $type_class = AbstractThenRule::getClassById($type);

        $this->view->assign('type_class', $type_class);
        $this->view->assign('field', $type_class->getPropertyIteratorField($field, $action, $params_type));
        return $this->result->setTemplate( $type_class->getTplFolder() . '/form/autotask/autotask_then_params.tpl' );
    }
}