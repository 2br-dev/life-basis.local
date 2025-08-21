<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Statistic\Controller\Admin;

use RS\Controller\Admin\Crud;
use RS\Html\Category;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Filter;
use RS\Html\Toolbar;
use RS\Html\Table;
use Statistic\Model\SourceTypeDirsApi;
use Statistic\Model\SourceTypesApi;

/**
 * Класс контроллера типов источников
 * @package Statistic\Controller\Admin
 */
class SourceTypesCtrl extends Crud
{

    /**
     * @var \Statistic\Model\SourceTypeDirsApi $dir_api
     */
    protected $dir_api;
    protected $dir; //Выбранная категория
    /**
     * @var \Statistic\Model\SourceTypesApi $api
     */
    protected $api;

    function __construct()
    {
        parent::__construct(new SourceTypesApi());
        $this->setCategoryApi(new SourceTypeDirsApi(), t('тип источников'));
    }

    function actionIndex()
    {
        if (!$this->getCategoryApi()->getOneItem($this->dir)) {
            $this->dir = 0; //Если категории не существует, то выбираем пункт "Все"
        }
        $this->api->setFilter('parent_id', $this->dir);

        return parent::actionIndex();
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Типы источников пользователей'));
        $this->dir = $this->url->request('dir', TYPE_INTEGER);
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить тип')]));

        $helper['topToolbar']->addItem(new ToolbarButton\Button(
            $this->router->getAdminUrl('ajaxUpdateSourceTypes', null, 'statistic-tools'),
            t('Обновить источники'),
            [
                'attr' => [
                    'class' => 'crud-get'
                ]
            ]
        ));

        $helper->setTopHelp(t('Здесь указываются правила, по которым опознаётся источник приходящего пользователя. Когда пользователь впервые попадает на сайт, 
        к нему привязывается информация о том, откуда он перешел и дополнительные пареметры UTM меток, если они есть. Далее, когда происходят события оформления заказа или 
        покупки в одик клик или же отработала форма обратной связи, то сохраненная ранее информация об источнике приписывается к соответствующему объекту. В данном разделе вы можете управлять справочником источников. Для описания источника ReadyScript может анализировать заголовок REFERER, а также UTM метки. 
        Информация о том, какие источники привели к целевому действию можно просмотреть в отчетах статистики.'));
        $helper->addCsvButton('statistic-sourcetypes');
        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));
        $helper->viewAsTableCategory();

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('sortn', t('Вес'), ['sortField' => 'id', 'Sortable' => SORTABLE_ASC, 'CurrentSort' => SORTABLE_ASC, 'ThAttr' => ['width' => '20']]),
                new TableType\Text('title', t('Название'), ['LinkAttr' => ['class' => 'crud-edit'], 'Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id', 'dir' => $this->dir])]),
                new TableType\Text('referer_site', t('Домен'), ['LinkAttr' => ['class' => 'crud-edit'], 'Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id', 'dir' => $this->dir])]),
                new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray']]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~', 'dir' => $this->dir]), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]
                    ]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('Клонировать'),
                            'attr' => [
                                'class' => 'crud-add',
                                '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                            ]
                        ],
                        [
                            'title' => t('удалить'),
                            'class' => 'crud-get',
                            'attr' => [
                                '@href' => $this->router->getAdminPattern('del', [':chk[]' => '@id']),
                            ]
                        ],
                    ])
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ]
        ]));

        $helper->setCategory(new Category\Element([
            'activeField' => 'id',
            'disabledField' => 'hidden',
            'disabledValue' => '1',
            'activeValue' => $this->dir,
            'rootItem' => [
                'id' => 0,
                'title' => t('Без группы'),
                'noOtherColumns' => true,
                'noCheckbox' => true,
                'noDraggable' => true,
                'noRedMarker' => true
            ],
            'sortUrl' => $this->router->getAdminUrl('categoryMove'),
            'mainColumn' => new TableType\Text('title', t('Название'), ['href' => $this->router->getAdminPattern(false, [':dir' => '@id', 'c' => $this->url->get('c', TYPE_ARRAY)])]),
            'tools' => new TableType\Actions('id', [
                new TableType\Action\Edit($this->router->getAdminPattern('categoryEdit', [':id' => '~field~']), null, [
                    'attr' => [
                        '@data-id' => '@id'
                    ]]),
                new TableType\Action\DropDown([
                    [
                        'title' => t('Клонировать категорию'),
                        'attr' => [
                            'class' => 'crud-add',
                            '@href' => $this->router->getAdminPattern('categoryClone', [':id' => '~field~']),
                        ]
                    ],
                ]),
            ]),
            'headButtons' => [
                [
                    'text' => t('Название группы'),
                    'tag' => 'span',
                    'attr' => [
                        'class' => 'lefttext'
                    ]
                ],
                [
                    'attr' => [
                        'title' => t('Создать категорию'),
                        'href' => $this->router->getAdminUrl('categoryAdd'),
                        'class' => 'add crud-add'
                    ]
                ],
            ],
        ]), $this->getCategoryApi());

        $helper->setCategoryBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Delete(null, null, ['attr' =>
                    ['data-url' => $this->router->getAdminUrl('categoryDel')]
                ]),
            ]]));

        $helper->setCategoryFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                    ]
                    ])
                ],
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->getCategoryApi()->defAlias()],
            'filterVar' => 'c',
            'Caption' => t('Поиск по группам')
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                    ]
                    ])
                ]]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()],
            'AddParam' => ['hiddenfields' => ['dir' => $this->dir]],
            'Caption' => t('Поиск по типам')
        ]));


        return $helper;
    }

    /**
     * Добавление типа источника
     *
     * @param null $primaryKey
     * @param bool $returnOnSuccess
     * @param null $helper
     * @return bool|\RS\Controller\Result\Standard
     */
    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        $dir = $this->url->request('dir', TYPE_INTEGER);
        if ($primaryKey === null) {
            $elem = $this->api->getElement();
            $elem['parent_id'] = $dir;
        }

        $this->getHelper()->setTopTitle($primaryKey ? t('Редактировать тип {title}') : t('Добавить тип'));

        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }
}
