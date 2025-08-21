<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\ExternalApi\User;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\AbstractMethods\AbstractMethod;
use ExternalApi\Model\Orm\AuthorizationToken;
use Users\Model\Orm\DeleteProfileRequests;

/**
* Добавляет запрос на удаление профиля пользователя
*/
class Delete extends AbstractAuthorizedMethod
{
    const RIGHT_DELETE = 1;

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
            self::RIGHT_DELETE => t('Добавление запроса на удаление профиля пользователя.')
        ];
    }

    /**
     * Добавляет запрос на удаление профиля пользователя
     *
     * @param string $token Авторизационный токен
     *
     * @example GET /api/methods/user.delete?token=c4b7a1036d7dbcbf979a40058088297486058519
     * Ответ:
     * <pre>
     *
     * {
     *      "success": true
     * }
     * </pre>
     *
     * Возвращает объект.
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($token)
    {
        $result = [
            'success' => false
        ];

        if ($user = $this->token->getUser()) {
            $delete_request = new DeleteProfileRequests();
            $delete_request->user_id = $user->id;

            $delete_request->insert();
            $result['success'] = true;
        }

        return $result;
    }
}
