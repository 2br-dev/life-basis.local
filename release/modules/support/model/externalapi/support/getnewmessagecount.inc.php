<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\ExternalApi\Support;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use Support\Model\Api;

/**
 * Возвращает суммарное количество новых сообщений по всем темам пользователя
 */
class GetNewMessageCount extends AbstractAuthorizedMethod
{
    protected $token_require = false;
    const RIGHT_LOAD = 1;

    /**
     * Возвращает комментарии к кодам прав доступа
     *
     * @return string[] - [
     *     КОД => КОММЕНТАРИЙ,
     *     КОД => КОММЕНТАРИЙ,
     *     ...
     * ]
     */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Загрузка суммарного кол-ва новых сообщений')
        ];
    }


    /**
     * Возвращает суммарное количество новых сообщений по всем темам пользователя
     *
     * @param string $token - Авторизационный токен
     * @example GET /api/methods/support.getNewMessageCount?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86
     *
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *              "new_message_count": 2
     *          }
     *      }
     * }
     * </pre>
     *
     * @return array
     */
    protected function process($token = null)
    {
        $result['response']['new_message_count'] = 0;

        if ($this->token) {
            $api = new Api();
            $result['response']['new_message_count'] = $api->getNewMessageCount($this->token['user_id']);
        }

        return $result;
    }
}
