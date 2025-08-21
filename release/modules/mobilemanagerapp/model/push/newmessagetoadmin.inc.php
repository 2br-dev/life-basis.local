<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\Push;
use Catalog\Model\CurrencyApi;
use RS\Http\Request;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Transaction;
use Support\Model\Orm\Support;
use Users\Model\Orm\User;

/**
 * Push уведомление администратору о новом сообщении в поддержку
 */
class NewMessageToAdmin extends AbstractPushToAdmin
{
    /**
     * @var Support
     */
    public $message;

    /**
     * Инициализирует данный класс
     *
     * @param Support $message
     */
    public function init(Support $message)
    {
        $this->message = $message;
    }

    /*
    * Возвращает описание уведомления для внутренних нужд системы и
    * отображения в списках админ. панели
    *
    * @return string
    */
    public function getTitle()
    {
        return t('Новое сообщение в поддержку');
    }


    /**
     * Возвращает Заголовок для Push уведомления
     *
     * @return string
     */
    public function getPushTitle()
    {
        $request = Request::commonInstance();
        return t('Сообщение от клиента на сайте %site', [
            'site' => $request->getDomainStr(),
        ]);
    }

    /**
     * Возвращает текст Push уведомления
     *
     * @return string
     */
    public function getPushBody()
    {
        $user = $this->message->getUser();
        if ($user['name'] != '' || $user['surname'] != '') {
            $username = $user['name'].' '.$user['surname'];
        } else {
            $username = t('Клиент');
        }

        return t('%fio: %message', [
            'fio' => $username,
            'message' => mb_substr($this->message->message, 0, 80)
        ]);
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
            'link' => "/tabs/my/support/{$site->getSiteHash()}/topic/{$this->message['id']}",
        ];
    }

}