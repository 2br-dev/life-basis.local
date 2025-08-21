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
use Users\Model\Orm\User;

/**
* Обновляет сведения о пользователе, перезаписывает значения полей
*/
class Update extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD_SELF = 1;
    const RIGHT_LOAD = 2;

    protected $user_validator;

    /** Поля, которые следует проверять из POST */
    public $use_post_keys = ['is_company', 'company', 'company_inn', 'name', 'surname', 'midname', 'sex', 'passport', 'phone', 'e_mail', 'openpass', 'captcha', 'data', 'changepass'];

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
            self::RIGHT_LOAD => t('Загрузка пользователя по идентификатору'),
        ];
    }

    public function getAcceptRequestMethod()
    {
        return [POST, JSON];
    }

    /**
    * Возвращает ORM объект, который следует загружать
    */
    public function getOrmObject()
    {
        return new \Users\Model\Orm\User();
    }  
    
    /**
    * Форматирует комментарий, полученный из PHPDoc
    * 
    * @param string $text - комментарий
    * @return string
    */
    protected function prepareDocComment($text, $lang)
    {
        $text = parent::prepareDocComment($text, $lang);
        
        //Валидатор для пользователя
        $validator = $this->getUserValidator();
        $text = preg_replace_callback('/\#data-user/', function() use($validator) {
            return $validator->getParamInfoHtml();
        }, $text);
        
        
        return $text;
    }
    
    /**
    * Возвращает валидатор для пользователя который отправляет поля для сохранения
    * 
    */
    private function getUserValidator()
    {
        if ($this->user_validator === null){
            $this->user_validator = \Users\Model\ApiUtils::getUserAddAndUpdateValidator();
        }
        return $this->user_validator;
    }

    /**
     * Обновляет сведения о пользователе
     * ---
     * Если у приложения есть право на загрузку пользователя по идентификатору и указан параметр user_id,
     * то обновляется пользователь по указанному идентификатору.
     *
     * Иначе можно обновить данные только у авторизованного пользователя, который получается из токена.
     *
     * @param string $token Авторизационный токен
     * @param string $client_id ID Клиентского приложения
     * @param string $client_secret Пароль клиентского приложения
     * @param array $user Поля пользователя для сохранения #data-user
     * @param integer $user_id ID пользователя для обновления
     *
     * @example POST /api/methods/user.update?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&user[name]=Супервизор%20тест%20тест&user[surname]=%20Моя%20фамилия&user[e_mail]=admin%40admin.ru&user[phone]=8(000)800-80-30&user[changepass]=0&user[is_company]=0
     *
     * <pre>
     *  {
     *      "response": {
     *            "success" : false,
     *            "errors" : ['Ошибка']
     *      }
     *   }</pre>
     * @return array Возращает, пустой массив ошибок, если успешно
     * @throws ApiException
     * @throws \RS\Exception
     */
    protected function process($token, $user, $client_id = null, $client_secret = null, $user_id = null)
    {
        //Проверим предварительно приложение
        //\ExternalApi\Model\Utils::checkAppIsRegistered($client_id, $client_secret);

        $response['response']['success'] = false;

        if ($this->hasRights(self::RIGHT_LOAD) && $user_id) {
            $current_user = new User($user_id);
            $current_user['__phone']->setEnableVerification(false);
        }else {
            $current_user = $this->token->getUser();
        }

        if ($current_user['id']) {
            if (isset($user['fio'])) {
                $current_user['__fio']->setChecker([User::class, 'checkFioField']);
                $current_user['__name']->removeAllCheckers();
                $current_user['__surname']->removeAllCheckers();
                $current_user['__midname']->removeAllCheckers();
            }

            return \Users\Model\ApiUtils::getUserDataPostAddUpdateCheck(
                $user,
                $current_user,
                $client_id,
                $this->use_post_keys ,
                $response,
                $this->hasRights(self::RIGHT_LOAD) && $user_id
            );
        }

        throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
    }


}