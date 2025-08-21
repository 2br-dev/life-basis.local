<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\Push;

use MobileManagerApp\Model\AppTypes\StoreManagement;
use MobileManagerApp\Model\Orm\ScanRequest;
use PushSender\Model\Firebase\Push\RsPushNotice;
use RS\Site\Manager as SiteManager;

/**
 * Класс Push-уведомления, которое будет активировать сканирование
 * штрихкодов в приложении ReadyScript
 */
class ScanCode extends RsPushNotice
{
    /**
     * @var ScanRequest
     */
    public $scan_request;

    /**
     * Инициализирует уведомление
     *
     * @param ScanRequest $scan_request
     * @return void
     */
    public function init(ScanRequest $scan_request)
    {
        $this->scan_request = $scan_request;
    }

    /**
     * Возвращает описание уведомления для внутренних нужд системы и
     * отображения в списках админ. панели
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Запрос на сканирование штрихкода');
    }

    /**
     * Возвращает для какого приложения (идентификатора приложения в ReadyScript) предназначается push
     *
     * @return string
     */
    public function getAppId()
    {
        return StoreManagement::ID;
    }

    /**
     * Возвращает одного или нескольких получателей
     *
     * @return array
     */
    public function getRecipientUserIds()
    {
        return [$this->scan_request['user_id']];
    }

    /**
     * Возвращает Заголовок для Push уведомления
     *
     * @return string
     */
    public function getPushTitle()
    {
        return t('Запрос на сканирование');
    }

    /**
     * Возвращает текст Push уведомления
     *
     * @return string
     */
    public function getPushBody()
    {
        return t('Нажмите, чтобы активировать сканер');
    }

    /**
     * Возвращает произвольные данные ключ => значение, которые должны быть переданы с уведомлением
     *
     * @return array
     */
    public function getPushData()
    {
        $site = SiteManager::getSite();
        return [
            'site_uid' => $site->getSiteHash(),
            'action' => 'scanCode',
            'formats' => $this->scan_request['formats'], //Форматы через запятую
            'callback_url' => $this->scan_request->getCallbackUrl(),
        ];
    }
}