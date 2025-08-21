<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\ExternalApi\Push;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use PushSender\Model\PushTokenApi;
use RS\Exception;

class UnregisterToken extends AbstractAuthorizedMethod
{
    const RIGHT_UNREGISTER = 1;

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
            self::RIGHT_UNREGISTER => t('Удаление Push-токена')
        ];
    }

    /**
     * Удаляет Push-токен из базы
     *
     * @param string $push_token Токен, полученный при регистрации устройства в одном из Push сервисов, например Firebase Cloud Messaging
     * @param string $token Авторизационный токен
     *
     * @return array
     * @example GET /api/methods/push.unregisterToken?push_token=fRK5BdgHTPOa3gwBVd6Uc7:AMA91bF-LPy_BBvScapMoI_AqwWR3TLvq.....
     *
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *          "success": true
     *      }
     * }
     * </pre>
     */
    public function process($push_token, $token = null)
    {
        try {
            if (!$push_token) {
                throw new ApiException(t('Push-token не может быть пустым'), ApiException::ERROR_WRONG_PARAM_VALUE);
            }

            PushTokenApi::unregisterUserToken($push_token);

            return [
                'response' => [
                    'success' => true
                ]
            ];

        } catch (Exception $e) {
            throw new ApiException($e->getMessage(), ApiException::ERROR_WRITE_ERROR);
        }
    }
}