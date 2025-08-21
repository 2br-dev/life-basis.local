<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Controller\Admin;

use ExternalApi\Model\VirtualAppApi;
use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Rights;
use RS\Controller\Admin\Crud;
use RS\Html\Table;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;

/**
 * Управление виртуальными приложениями, для доступа к API
 */
class AppCtrl extends Crud
{
    function __construct()
    {
        parent::__construct(new VirtualAppApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Внешние приложения'));
        $helper->setTopHelp(t('Создайте приложение и настройте доступ к необходимым методам API из вашего внешнего приложения. Важно: доступ к методам может ограничиваться также в настройках конкретного пользователя.'));
        $helper->setTopToolbar($this->buttons(['add']));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'LinkAttr' => [
                    'class' => 'crud-edit'
                ],
                    'href' => $this->router->getAdminPattern('edit', [':id' => '@id']),]),
                new TableType\Userfunc('client_id', t('Client ID'), function($value, $cell) {
                    return $cell->getRow()->getClientId();
                }),
                new TableType\Usertpl('use_vapp_endpoint', t('Адрес точки входа'), '%externalapi%/form/virtualapp/column_endpoint.tpl'),
                new TableType\Yesno('enable', t('Включено'), ['Sortable' => SORTABLE_BOTH, 'toggleUrl' => $this->router->getAdminPattern('ajaxTogglePublic', [':id' => '@id'])]),
            ]]));

        $helper->setFilter(new Filter\Control( [
            'container' => new Filter\Container( [
                'lines' =>  [
                    new Filter\Line( ['items' => [
                        new Filter\Type\Text('title', t('Название'), ['searchType' => '%like%']),
                        new Filter\Type\Text('client_id', t('Client ID'), ['searchType' => '%like%']),
                        new Filter\Type\Select('enabled', t('Включено'), [
                            '' => t('Не важно'),
                            '1' => t('Да'),
                            '0' => t('Нет')
                        ]),
                    ]
                    ]),
                ],
            ]),

            'field_prefix' => $this->api->getElementClass()
        ]));

        $helper->setBottomToolbar($this->buttons(['delete']));
        return $helper;
    }

    /**
     * Метод переключения флага публичности
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Exception
     */
    public function actionAjaxTogglePublic()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }
        $id = $this->url->get('id', TYPE_STRING);

        $product = $this->api->getOneItem($id);
        if ($product) {
            $product['enable'] = !$product['enable'];
            $product->update();
        }
        return $this->result->setSuccess(true);
    }

}