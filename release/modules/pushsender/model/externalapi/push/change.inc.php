<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\ExternalApi\Push;
use \ExternalApi\Model\Exception as ApiException;
use PushSender\Model\Orm\PushLock;

/**
* Включает/выключает право получать определенные уведомления пользователю
*/
class Change extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const RIGHT_CHANGE = 1;
    const ERROR_NO_UUID = 3;

    protected $token_require = false;

    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    public function getRightTitles()
    {
        return [
            self::RIGHT_CHANGE => t('Включение/выключение push уведомлений')
        ];
    }            
    
    protected function validate($push_ids, $pushes)
    {
        foreach($push_ids as $id => $value) {
            if (!isset($pushes[$id]) && $id != \PushSender\Model\Orm\PushLock::PUSH_CLASS_ALL) {
                throw new ApiException(t("Push уведомления с ID '%0' не существует", [$id]), ApiException::ERROR_WRONG_PARAM_VALUE);
            }
        }
    }
    
    /**
    * Включает/выключает право получать определенные уведомления пользователю
    * 
    * @param string $token Авторизационный токен
    * @param string $uuid id устройства ожидающего PUSH уведомления. Обязателен, только тогда когда не передан авторизационный токен
    * @param array $push_ids Массив, может содержать в ключе ID push уведомления, в значении 0 или 1.
    * Если в значении указан 0, то это будет означать, что необходимо отключить получение уведомлений
    * 1 - наоборот будет означать, что нужно включить уведомление
    * 
    * @example /api/methods/push.change?token=c61a91260ba4f9a8aca4648d2580733bc57c33e3&uuid=dv76dub7347f....dfgjbkibj&push_ids[all]=0
    * Ответ: (уведомление с ID ALL будет выключено)
    * <pre>
    * {
    *     "response": {
    *         "success": true
    *     }
    * }
    * </pre>
    * 
    * /api/methods/push.change?token=c61a91260ba4f9a8aca4648d2580733bc57c33e3&uuid=dv76dub7347f....dfgjbkibj&push_ids[all]=1
    * Ответ: (уведомление с ID ALL будет включено)
    * <pre>
    * {
    *     "response": {
    *         "success": true
    *     }
    * }
    * </pre>
    * 
    */
    protected function process($push_ids, $token = null)
    {
        $app = \RS\RemoteApp\Manager::getAppByType($this->token->app_type);
        
        if (!$app) {
            throw new ApiException(t('Приложение с таким ID не найдено'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
        
        if (!($app instanceof \PushSender\Model\App\InterfaceHasPush)) {
            throw new ApiException(t('Приложение не поддерживает push уведомления'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
        
        
        $pushes = [];
        foreach($app->getPushNotices() as $push) {
            $pushes[$push->getId()] = $push->getId();
        }
        
        $this->validate($push_ids, $pushes);

        //Никогда не устанавливаем запрет на все Push-уведомления через Api.
        $push_ids[PushLock::PUSH_CLASS_ALL] = 1;
        
        //Изменяем данны в БД
        foreach($push_ids as $id => $value) {
            $lock = new \PushSender\Model\Orm\PushLock();
            $lock['site_id'] = \RS\Site\Manager::getSiteId();
            $lock['user_id'] = $this->token->user_id;
            $lock['app'] = $this->token->app_type;
            $lock['push_class'] = $id;
            
            if ($value) {
                $lock->delete();
            } else {
                $lock->replace();
            }
        }
        
        return [
            'response' => [
                'success' => true
            ]
        ];
    }
}
