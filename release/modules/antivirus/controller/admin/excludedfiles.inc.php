<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Antivirus\Controller\Admin;

use Antivirus\Model\Libs\Diff;
use RS\Controller\Admin\Crud;
use RS\Html\Filter;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar;
use RS\Html\Toolbar\Button as ToolbarButton;

class ExcludedFiles extends Crud
{
    
    function __construct()
    {
        $api = new \Antivirus\Model\ExcludedFileApi;
        parent::__construct($api);
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Исключенные файлы'));
        $helper->setTopHelp(t('В этом разделе можно указать файлы, которые не следует проверять антивирусом или компонентом контроля целостности.'));
        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                $this->buttons('add', ['add' => t('Добавить файл')]),
                new ToolbarButton\Button($this->router->getAdminUrl(null, null, 'antivirus-events'), t('Список угроз'))
            ]
        ]));

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('file', t('Файл'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'linkAttr' => [
                    'class' => 'crud-edit'
                ]]),
                new TableType\Text('component', t('Компонент'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Datetime('dateof', t('Дата'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move')
            ]]));
            

        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                'Lines' =>  [
                    new Filter\Line( ['items' => [
                        new Filter\Type\Text('file', t('Файл'), ['searchType' => '%like%']),
                        new Filter\Type\Select('component', t('Компонент'), [''=>t('Любой')] + \Antivirus\Model\Orm\ExcludedFile::getComponentList()),
                        new Filter\Type\DateRange('dateof', t('Дата внесения')),
                    ]
                    ])
                ],
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()]
        ]));

        return $helper;
    }
    
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {        
        if ($primaryKeyValue === null) {
            $this->getHelper()->setTopTitle(t('Добавить файл в исключения'));
        } else {
            $this->getHelper()->setTopTitle(t('Редактировать файл'));
        }

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }    


}
