<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model;
use ExternalApi\Model\ExternalApi\Oauth\Login;
use ExternalApi\Model\TokenApi;
use \RS\Orm\Type,
    \RS\Helper\CustomView;

/**
* Класс содержит API функции дополтельные для работы в системе в рамках задач по модулю пользователя
*/
class ApiUtils
{
    /**
    * Возвращает секцию с дополнительными полями пользователя из конфига для внешнего API
    * 
    */
    public static function getAdditionalUserFieldsSection()
    {
        //Добавим доп поля для пользователя для регистрации
        $reg_fields_manager = \RS\Config\Loader::byModule('users')->getUserFieldsManager();
        $reg_fields_manager->setErrorPrefix('regfield_');
        $reg_fields_manager->setArrayWrapper('regfields');
        
        //Пройдёмся по полям
        $fields = [];
        foreach ($reg_fields_manager->getStructure() as $field){
            if ($field['type'] == 'bool'){  //Если тип галочка
                $field['val'] = $field['val'] ? true : false;    
            }
            $fields[] = $field;
        }
        
        return $fields;
    }

    /**
     * Возвращает дополнительные параметры отображения для пользователя
     * Необходимо возвращать массив
     * [
     *    [
     *      'title' => 'Баланс',
     *      'value' => '320 p.'
     *    ]
     * ]
     *
     */
    public static function getAdditionalUserInfoFieldsSection()
    {
        $user_info = [];

        //Добавим сведения по лицевому счету
        if (\RS\Module\Manager::staticModuleExists('shop') && \RS\Application\Auth::isAuthorize()){
            $config = \RS\Config\Loader::byModule('shop');

            $user = \RS\Application\Auth::getCurrentUser();
            if ($config['use_personal_account']){
                $user_info[] = [
                    'title' => t('Баланс'),
                    'value' => $user->getBalance(true, true)
                ];
            }
        }

        return $user_info;
    }

    /**
     * Возвращает валидатор для добавления и обновления пользователя
     *
     * @return \ExternalApi\Model\Validator\ValidateArray
     */
    public static function getUserAddAndUpdateValidator()
    {
        return new \ExternalApi\Model\Validator\ValidateArray([
            'fio' => [
                '@title' => t('Фамилия Имя Отчество'),
                '@type' => 'string',
                '@require' => true,
            ],
            'surname' => [
                '@title' => t('Фамилия. Не используется если указан [fio]'),
                '@type' => 'string',
                '@require' => true,
            ],
            'name' => [
                '@title' => t('Имя. Не используется если указан [fio]'),
                '@type' => 'string',
                '@require' => true,
            ],
            'midname' => [
                '@title' => t('Отчество. Не используется если указан [fio]'),
                '@type' => 'string',
            ],
            'phone' => [
                '@title' => t('Телефон.'),
                '@type' => 'string',
                '@require' => true,
            ],
            'e_mail' => [
                '@title' => t('E-mail.'),
                '@type' => 'string',
                '@require' => true,
            ],
            'is_company' => [
                '@title' => t('Признак компании. 1 - компания, 0 - физ.лицо'),
                '@require' => true,
                '@type' => 'integer'
            ],
            'company' => [
                '@title' => t('Название компании. Только если, стоит ключ is_company.'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['is_company']) && $full_data['is_company']){
                        return "Название компании обязательное поле.";
                    }
                    return true;
                }
            ],
            'company_inn' => [
                '@title' => t('ИНН организации. Только если, ключ is_company.'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['is_company']) && $full_data['is_company']){
                        return "ИНН компании обязательное поле.";
                    }
                    return true;
                }
            ],
            'changepass' => [
                '@title' => t('Нужно ли сменить пароль? 0 или 1.'),
                '@type' => 'integer'
            ],
            'pass' => [
                '@title' => t('Текущий пароль. Только если, changepass=1, указан параметр user_id и у приложения есть право на загрузку пользователя по идентификатора'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['changepass']) && $full_data['changepass']){
                        return "Текущий пароль обязательное поле.";
                    }
                    return true;
                }
            ],
            'openpass' => [
                '@title' => t('Открытый пароль. Только если, changepass=1'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['changepass']) && $full_data['changepass']){
                        return "Повтор открытого пароля обязательное поле.";
                    }
                    return true;
                }
            ],
            'openpass_confirm' => [
                '@title' => t('Повтор открытого пароля. Только если, changepass=1, указан параметр user_id и у приложения есть право на загрузку пользователя по идентификатора'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['changepass']) && $full_data['changepass']){
                        return "Повтор открытого пароля обязательное поле.";
                    }
                    return true;
                }
            ],
            'data' => [
                '@title' => t('Данные произвольных полей, созданных в настройках модуля Пользователи и группы. Должны быть предствлены в формате [Имя поля => Значение, Имя поля2 => Значение2]'),
                '@type' => 'array'
            ],
        ]);
    }

    /**
     * Возвращает валидатор для регистрации пользователя
     *
     * @return \ExternalApi\Model\Validator\ValidateArray
     */
    public static function getUserRegistrationValidator()
    {
        return new \ExternalApi\Model\Validator\ValidateArray([
            'fio' => [
                '@title' => t('Фамилия Имя Отчество.'),
                '@type' => 'string',
                '@require' => true,
            ],
            'phone' => [
                '@title' => t('Телефон.'),
                '@type' => 'string',
                '@require' => true,
            ],
            'e_mail' => [
                '@title' => t('E-mail.'),
                '@type' => 'string',
                '@require' => true,
            ],
            'openpass' => [
                '@title' => t('Открытый пароль.'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['changepass']) && $full_data['changepass']){
                        return "Повтор открытого пароля обязательное поле.";
                    }
                    return true;
                }
            ],
            'openpass_confirm' => [
                '@title' => t('Повтор открытого пароля.'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['changepass']) && $full_data['changepass']){
                        return "Повтор открытого пароля обязательное поле.";
                    }
                    return true;
                }
            ],
            'is_company' => [
                '@title' => t('Является ли клиент компанией?'),
                '@type' => 'integer'
            ],
            'company' => [
                '@title' => t('Название компании. Только если, стоит ключ is_company.'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['is_company']) && $full_data['is_company']){
                        return "Название компании обязательное поле.";
                    }
                    return true;
                }
            ],
            'company_inn' => [
                '@title' => t('ИНН компании. Только если, ключ is_company.'),
                '@type' => 'string',
                '@validate_callback' => function($is_company, $full_data) {
                    if (isset($full_data['is_company']) && $full_data['is_company']){
                        return "ИНН компании обязательное поле.";
                    }
                    return true;
                }
            ],
            'data' => [
                '@title' => t('Дополнительные сведения'),
                '@type' => 'array'
            ],
        ]);
    }

    /**
     * Возвращает массив данных ответа после проверки данных пользователя для создания и обновления пользователя
     *
     * @param array $data - массив данных пользователя
     * @param \Users\Model\Orm\User $current_user - текущий пользователь
     * @param string $client_id - идентификатор клиентского приложения
     * @param array $use_post_keys - массив полей POST для проверки
     * @param array $response - массив ответа
     * @param bool $edit_by_admin - пользователь редактируется администратором
     *
     * @return array
     * @throws \RS\Exception
     */
    public static function getUserDataPostAddUpdateCheck($data, \Users\Model\Orm\User $current_user, $client_id, $use_post_keys, $response, $edit_by_admin = false)
    {
        $errors = [];
        $pass = $current_user['pass'];
        $current_user->getFromArray($data);
        $current_user['pass'] = $pass;
        $current_user->validate();

        if (isset($data['changepass']) && $data['changepass'] && !$edit_by_admin) {
            $current_pass = $data['pass'];
            $crypt_current_pass = $current_user->cryptPass($current_pass, $current_user);
            if ($crypt_current_pass === $current_user['pass']) {
                $current_user['pass'] = $crypt_current_pass;
            } else {
                $current_user->addError(t('Неверно указан текущий пароль'), 'pass');
            }

            $password = $data['openpass'];
            $password_confirm = $data['openpass_confirm'];

            if (strcmp($password, $password_confirm) != 0) {
                $current_user->addError(t('Пароли не совпадают'), 'openpass');
            }
        }

        if (!$current_user->hasError()) {
            if ($current_user->update()) {
                $_SESSION['user_profile_result'] = t('Изменения сохранены');
                $response['response']['success'] = true;

                if (!$edit_by_admin) {
                    $token = TokenApi::createToken($current_user['id'], $client_id);
                    $token_data = Login::makeResponseAuthTokenData($token);

                    $response['response']['auth'] = $token_data;
                }

                $auth_user = Login::makeResponseUserData($current_user);

                //Не передаем пароль в ответе
                unset($auth_user['openpass'], $auth_user['openpass_confirm']);

                $response['response']['user'] = $auth_user;
            }else {
                $errors = [t('Произошла ошибка. ').$current_user->getErrorsStr()];
            }
        }else{
            $errors = $current_user->getErrors();
        }
        $response['response']['errors'] = $errors;

        return $response;
    }
}