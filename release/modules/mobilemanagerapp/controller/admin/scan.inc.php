<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Controller\Admin;

use MobileManagerApp\Model\AppCall\CameraScanner;
use MobileManagerApp\Model\Orm\ScanRequest;
use RS\Controller\Admin\Front;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Exception;

class Scan extends Front
{
    protected CameraScanner $camera_scanner;

    function init()
    {
        $this->camera_scanner = new CameraScanner();
    }

    /**
     * Отправляет Push на сканирование штрихкода/QR-кода в приложении, отображает диалог
     */
    function actionIndex()
    {
        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Сканировать код'));
        $helper->viewAsAny();

        $formats = $this->url->get('formats', TYPE_STRING);
        $filter = $this->url->get('filter', TYPE_STRING);

        try {
            $scan_request = $this->camera_scanner->requestScan($formats, $filter);
        } catch (Exception $e) {
            $scan_request = new ScanRequest();
            $scan_request['status'] = ScanRequest::STATUS_FAIL;
            $scan_request['fail_reason'] = $e->getMessage();
        }

        $this->view->assign([
            'scan_request' => $scan_request
        ]);

        $helper->setForm($this->view->fetch('admin/scan/scan_dialog.tpl'));

        return $this->result->setTemplate( $helper->getTemplate() );
    }

    /**
     * Обновляет сведения по текущему сканированию
     */
    function actionRefresh()
    {
        $id = $this->url->get('id', TYPE_STRING);
        $before_status = $this->url->get('status', TYPE_STRING);
        $resend = $this->url->get('resend', TYPE_INTEGER);

        try {
            $scan_request = $this->camera_scanner->getScanRequest($id);

            if ($resend) {
                $scan_request->sendPush();
                $scan_request['status'] = ScanRequest::STATUS_WAITING;
                $scan_request['fail_reason'] = null;
                $scan_request['raw_result'] = null;
                $scan_request->update();
            }

        } catch (Exception $e) {
            $scan_request = new ScanRequest();
            $scan_request['status'] = ScanRequest::STATUS_FAIL;
            $scan_request['fail_reason'] = $e->getMessage();
        }

        if ($before_status != $scan_request['status'] || $resend) {
            $this->view->assign([
                'scan_request' => $scan_request
            ]);

            $this->result->addSection([
                'changed' => true,
                'status' => $scan_request['status']
            ]);

            if ($scan_request['status'] == ScanRequest::STATUS_SUCCESS) {
                $this->result->addSection('result', $scan_request->getResult());
            } else {
                $this->result->setTemplate('admin/scan/scan_dialog.tpl');
            }
        } else {
            $this->result->addSection('changed', false);
        }

        return $this->result;
    }
}