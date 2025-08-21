<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\ExternalApi\User;
use ExternalApi\Model\AbstractMethods\AbstractMethod;
use RS\Application\Auth;
use RS\Config\Loader;

/**
 * Инициализация пользователя
 */
class RegistrationInit extends AbstractMethod
{
    public $user;

    /**
     *  Инициализирует регистрацию пользователя
     *
     * @return array
     * @throws \RS\Exception
     * @example POST /api/methods/user.registrationInit
     *
     * Ответ:
     * <pre>
     *
     * {
     *      "response": {
     *          "success": true,
     *          "need_captcha": true
     *      }
     * }
     * </pre>
     *
     */
    protected function process()
    {
        $this->user = Auth::getCurrentUser();
        $response['response']['success'] = false;

        if ($this->user) {
            $response['response']['success'] = true;
            $system_config = Loader::getSystemConfig();
            $mobilesiteapp_config = Loader::byModule('mobilesiteapp');

            if (!$this->user['__phone']->isEnabledVerification() && $mobilesiteapp_config['captcha'] != 'none') {
                if (
                    ($mobilesiteapp_config['captcha'] == 'system' && $system_config['captcha_class'] != 'stub')
                    || $mobilesiteapp_config['captcha'] == 'RS-default'
                ) {
                    $response['response']['need_captcha'] = true;
                }
            }
        }
        return $response;
    }
}