<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Partnership\Controller\Admin;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Table;

class Ctrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        parent::__construct(new \Partnership\Model\Api());
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Партнерские сайты'));
        $helper->setTopHelp($this->view->fetch('admin/top_help.tpl'));
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('Добавить партнерский сайт')]));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Text('title', t('Наименование'), ['href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'linkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('domains', t('Доменные имена')),
                new TableType\Text('theme', t('Тема оформления')),
                new TableType\Text('price_inc_value', t('Увеличение цены, %')),
                new TableType\Text('theme', t('Тема оформления')),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]
                    ]),
                    new TableType\Action\DropDown([
                        [
                            'title' => t('Редактировать дизайн'),
                            'attr' => [
                                '@href' => $this->router->getAdminPattern(false, [':context' => 'partner-{id}'], 'templates-blockctrl')
                            ]
                        ],
                        [
                            'title' => t('Клонировать партнёра'),
                            'attr' => [
                                'class' => 'crud-add',
                                '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                            ]
                        ],
                    ])
                ])
            ]
        ]));
        
        return $helper;
    }
    
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $this->api->getElement()->tpl_module_folders = \RS\Module\Item::getResourceFolders('templates');
        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }
    
    /**
    * Метод для клонирования
    * 
    */ 
    function actionClone()
    {
        $this->setHelper( $this->helperAdd() );
        $id = $this->url->get('id', TYPE_INTEGER);
        
        $elem = $this->api->getElement();
        
        if ($elem->load($id)) {
            $clone_id = null;
            if (!$this->url->isPost()) {
                $clone = $elem->cloneSelf();
                $this->api->setElement($clone);
                $clone_id = $clone['id']; 
            }
            unset($elem['id']);
            return $this->actionAdd($clone_id);
        } else {
            return $this->e404();
        }
    }
}
