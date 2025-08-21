<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin\Block;

use Main\Config\ModuleRights;
use Main\Model\NoticeSystem\InternalAlerts;

/**
 * Класс отвечает за отображение системных уведомлений в административной панели
 */
class RsAlerts extends \RS\Controller\Admin\Block
{
    protected
        $action_var = 'alerts_do';

    public
        $api;

    function init()
    {
        $this->api = InternalAlerts::getInstance();
    }

    function actionIndex()
    {
        if (!$this->api->getCount()) return;

        $rights = [
            'main_alerts' => $this->user->checkModuleRight('main', ModuleRights::RIGHT_VIEW_ICON_ALERTS)
        ];

        $this->view->assign([
            'counter_status' => $this->api->getStatus(),
            'rights' => $rights
        ]);

        return $this->result->setTemplate('%main%/adminblocks/rsalerts/alert_item.tpl');
    }

    function actionAjaxGetAlerts()
    {
        $this->view->assign([
            'messages' => $this->api->getMessages()
        ]);

        return $this->result
                    ->addSection('title', t('Уведомления'))
                    ->setTemplate('%main%/adminblocks/rsalerts/alert_messages.tpl');
    }
}