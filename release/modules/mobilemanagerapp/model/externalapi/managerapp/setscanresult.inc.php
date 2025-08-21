<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\ExternalApi\ManagerApp;

use ExternalApi\Model\AbstractMethods\AbstractMethod;
use MobileManagerApp\Model\Orm\ScanRequest;
use ExternalApi\Model\Exception as ApiException;

/**
 * Метод API устанавливает результат сканирования QR-кода или штрихкода в приложении
 */
class SetScanResult extends AbstractMethod
{
    /**
     * Устанавливает результат сканирования QR-кода или штрихкода
     * ---
     * Данный метод вызывает мобильное приложение ReadyScript после обработки специального Push-уведомления на сканирование QR-кода или штрихкода
     *
     * @param string $request_id ID запроса на сканирование
     * @param integer $is_success Признак успешности сканирования. 1 - успешно, 0 - неудача
     * @param string $result Результат сканирования или причина ошибки
     *
     * @example GET /api/methods/managerApp.setScanResult?request_id=58bc71e5bca43339149f188920c9d26fcdc65f64&is_success=1&result=здесь-код-маркировки
     *
     * или GET /api/methods/managerApp.setScanResult?request_id=58bc71e5bca43339149f188920c9d26fcdc65f64&is_success=0&result=здесь-текст-ошибки
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true
     *     }
     * }
     * </pre>
     *
     * @return array
     */
    public function process($request_id, $is_success, $result)
    {
        $scan_request = new ScanRequest($request_id);
        if (!$scan_request['id'] || $scan_request['status'] != ScanRequest::STATUS_WAITING) {
            throw new ApiException(t('Запрос на сканирование уже не актуален.'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $scan_request['date_of_update'] = date('Y-m-d H:i:s');
        if ($is_success) {
            $scan_request['status'] = ScanRequest::STATUS_SUCCESS;
            $scan_request['raw_result'] = $result;
        } else {
            $scan_request['status'] = ScanRequest::STATUS_FAIL;
            $scan_request['fail_reason'] = $result;
        }

        if ($scan_request->update()) {
            return [
                'response' => [
                    'success' => true
                ]
            ];
        }

        throw new ApiException(t('Не удалось сохранить результат сканирования. %0', [$scan_request->getErrorsStr()]), ApiException::ERROR_WRITE_ERROR);
    }
}