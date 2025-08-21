<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\ExternalApi\Verification;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\ExternalApi\Oauth\Login;
use \ExternalApi\Model\Exception as ApiException;
use Users\Model\Verification\Action\AbstractVerifyAction;
use Users\Model\Verification\VerificationEngine;

/**
 * Запускает верификационную сессию
 */
class SessionStart extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;
    protected $token_require = false;

    private $action_ids = [
        'users-twostepauthorize',
        'users-twostepregisterbyphone',
        'users-twostepprofile',
        'users-twostepregister'
    ];

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
            self::RIGHT_LOAD => t('Запуск верификационной сессии.')
        ];
    }

    /**
     * Запускает верификационную сессию
     *
     * @param string $action_id id
     * Возможные значения:
     * <b>users-twostepauthorize</b>
     * <b>users-twostepregisterbyphone</b>
     *
     * @param string $token Авторизационный токен
     * @param string $phone Телефон
     *
     * @example GET /api/methods/verification.sessionStart?action_id=1
     *
     * Ответ:
     * <pre>
     * {
     *      "response": {
     *          "verification": {
     *              "session": {
     *                  "token": "747e7534ef3cdfd911d379fbe0ca7d8bb657613b",
     *                  "error": "",
     *                  "code_send_flag": false,
     *                  "code_refresh_delay": 0,
     *                  "is_resolved": false
     *              }
     *          }
     *      }
     * }
     * </pre>
     *
     * @return array Возвращает массив сведений о верификационной сессии
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     * @throws \Users\Model\Verification\VerificationException
     */
    function process($action_id, $token = null, $phone = null)
    {
        if (!in_array($action_id, $this->action_ids)) {
            throw new ApiException(t('Неверное значение параметра action_id'), ApiException::ERROR_WRONG_PARAMS);
        }

        $verification_action = AbstractVerifyAction::makeById($action_id);
        if ($verification_action) {
            $verification_engine = new VerificationEngine();
            $verification_engine->setAction($verification_action);
            $verification_engine->initializeSession();

            if ($phone && $user = $this->token->getUser()) {
                $verification_engine->setPhone($phone);
                $user['__phone']->setVerifiedPhone($user['phone']);

                $verified_phone = $user['__phone']->getVerifiedPhone();
                if ($verified_phone !== null && $phone === $verified_phone) {
                    $session = $verification_engine->getSession();
                    $session['is_resolved'] = 1;
                    $session['resolved_time'] = time();
                    $session->update();
                }
            }

            return [
                'response' => [
                    'verification' => [
                        'session' => Login::makeResponseVerificationSessionData($verification_engine->getSession())
                    ]
                ]
            ];
        }
        return ['response' => ['success' => false]
        ];
    }
}