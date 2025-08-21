<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\AppCall;

use MobileManagerApp\Model\Orm\ScanRequest;
use MobileManagerApp\Model\Push\ScanCode;
use RS\Exception;
use RS\Orm\Request;

/**
 * Класс обеспечивает возможность использовать камеру телефона
 * с приложением ReadyScript в качестве 2D сканера QR-кодов и штрихкодов EAN-13
 */
class CameraScanner
{
    /**
     * Отправляет запрос в приложение на сканирование QR-кода
     *
     * @param string|array $formats Форматы сканирования данных
     * @param string|null $filter Тип обработки результата сканирования, может быть
     * ScanRequest::FILTER_MARKING - Вырежет из маркировки не печатные символы, как это обычно делает стандартный сканер
     * @param int|null $user_id ID пользователя, которому в приложение ReadyScript будет отправлено уведомление
     *
     * @return ScanRequest
     * @throws Exception Бросает исключения в случае ошибок
     */
    public function requestScan($formats, $filter = null, $user_id = null)
    {
        $scan_request = ScanRequest::create($formats, $filter, $user_id);
        $scan_request->sendPush();

        if ($scan_request->insert()) {
            return $scan_request;
        } else {
            throw new Exception(t('Не удалось создать запрос на сканирование. %0', [$scan_request->getErrorsStr()]));
        }
    }

    /**
     * Возвращает объект запроса на сканирование
     *
     * @param string $id ID запроса на сканирование
     * @return ScanRequest
     * @throws Exception
     */
    public function getScanRequest($id)
    {
        $scan_request = new ScanRequest($id);

        if (!$scan_request['id']) {
            throw new Exception(t('Запрос на сканирование не найден'));
        }

        return $scan_request;
    }


    /**
     * Повторяет отправку Push-уведомления для сканирования кода
     *
     * @param string $id ID запроса на сканирование
     * @return true
     */
    public function reSendNotice($id)
    {
        return $this->getScanRequest($id)->sendPush();
    }
}