<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\ExternalApi\User;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Utils;
use RS\Orm\Type;

/**
* Загружает объект пользователя
*/
class Get extends AbstractAuthorizedMethod
{
    const
        RIGHT_LOAD_SELF = 1,
        RIGHT_LOAD = 2;
    
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
            self::RIGHT_LOAD_SELF => t('Загрузка авторизованного пользователя'),
            self::RIGHT_LOAD => t('Загрузка пользователя')
        ];
    }
    
    /**
    * Возвращает ORM объект, который следует загружать
    */
    public function getOrmObject()
    {
        return new \Users\Model\Orm\User();
    }

    /**
     * Возвращает объект пользователя
     *
     * @param string $token Авторизационный токен
     * @param integer $user_id ID пользователя. Если не передан, то возвращаются сведения по владельцу токена
     *
     * @example GET /api/methods/user.get?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&user_id=1
     *
     * <pre>
     *  {
     *      "respone": {
     *          "user": {
     *              "name": "Артем",
     *              "surname": "Иванов",
     *              "midname": "Петрович",
     *              "e_mail": "mail@readyscript.ru",
     *              "login": "demo@example.com",
     *              "phone": "+700000000000",
     *              "sex": "",
     *              "subscribe_on": "0",
     *              "dateofreg": "0000-00-00 00:00:00",
     *              "ban_expire": null,
     *              "last_visit": "2016-09-07 18:51:20",
     *              "is_company": 1,
     *              "company": "ООО Ромашка",
     *              "company_inn": "1234567890",
     *              "data": {},
     *              "balance_formatted": "150 ₽",
     *              "balance": "150.00",
     *          }
     *      }
     *   }</pre>
     *
     * @return array Возвращает объект пользователя
     * @throws ApiException
     */
    protected function process($token, $user_id = null)
    {
        if (!$user_id) {
            $user_id = $this->token['user_id'];
        }

        //Проверяем права на доступ к загрузке своего объекта        
        if ($error = $this->checkAccessError($user_id == $this->token['user_id'] ? self::RIGHT_LOAD_SELF : self::RIGHT_LOAD)) {
            throw new ApiException(t('Недостаточно прав для доступа к данному пользователю'), ApiException::ERROR_METHOD_ACCESS_DENIED);
        }
        
        $user = $this->getOrmObject();
        if ($user->load($user_id)) {
            $user->getPropertyIterator()->append([
                'balance' => (new Type\Real())
                    ->setVisible(true, 'app'),
                'balance_formatted' => (new Type\Real())
                    ->setVisible(true, 'app'),
                'fio' => (new Type\Varchar())
                    ->setVisible(true, 'app')
            ]);

            $user['fio'] = $user->getFio();
            $user['balance'] = $user->getBalance(true, false);
            $user['balance_formatted'] = $user->getBalance(true, true);

            return [
                'response' => Utils::extractOrm($user)
            ];
        }
        
        throw new ApiException(t('Пользователь с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
    }
}