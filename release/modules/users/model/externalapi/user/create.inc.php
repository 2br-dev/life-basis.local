<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\ExternalApi\User;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\ExternalApi\Oauth\Login;
use ExternalApi\Model\Validator\ValidateArray;
use RS\Helper\Tools;
use Users\Config\File;
use Users\Model\Orm\User;
use ExternalApi\Model\Exception as ApiException;

/**
 * Класс создает пользователя в системе (администраторам)
 */
class Create extends AbstractAuthorizedMethod
{
    const RIGHT_CREATE = 'create';

    private $validator;

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
            self::RIGHT_CREATE => t('Создание пользователя'),
        ];
    }

    /**
     * Возвращает допустимую структуру значений в переменной data, в которой будут содержаться сведения для обновления
     *
     * @return ValidateArray
     */
    public function getUserDataValidator()
    {
        if ($this->validator === null) {
            $this->validator = new ValidateArray([
                'fio' => [
                    '@title' => t('Фамилия Имя Отчество'),
                    '@type' => 'string',
                    '@require' => true,
                ],
                'phone' => [
                    '@title' => t('Телефон'),
                    '@type' => 'string',
                ],
                'e_mail' => [
                    '@title' => t('E-mail'),
                    '@type' => 'string',
                ],
                'is_company' => [
                    '@title' => t('Признак компании. 1 - компания, 0 - физ.лицо'),
                    '@type' => 'integer',
                ],
                'company' => [
                    '@title' => t('Наименование компании'),
                    '@type' => 'string',
                ],
                'company_inn' => [
                    '@title' => t('ИНН организации'),
                    '@type' => 'string',
                ],
                'data' => [
                    '@title' => t('Данные произвольных полей, созданных в настройках модуля Пользователи и группы. Должны быть предствлены в формате [Имя поля => Значение, Имя поля2 => Значение2]'),
                    '@type' => 'array',
                ],
                'generate_password' => [
                    '@title' => t('Признак необходимости сгенерировать пароль автоматически. 1 - Да, 0 - Нет.'),
                    '@type' => 'integer',
                ],
                'openpass' => [
                    '@title' => t('Пароль в открытом виде. Актуально, если generate_password = 0'),
                    '@type' => 'string',
                ],
                'no_send_notice' => [
                    '@title' => t('Не уведомлять пользователя на Email или Телефон о регистрации. 1 - Не уведомлять, 0 - Уведомлять.'),
                    '@type' => 'integer',
                ]
            ]);
        }

        return $this->validator;
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
        $validator_order = $this->getUserDataValidator();

        $text = preg_replace_callback('/\#data-user-info/', function() use($validator_order) {
            return $validator_order->getParamInfoHtml();
        }, $text);

        return $text;
    }


    /**
     * Включает возможность установки ФИО через одно поле
     *
     * @param User $user
     * @return User
     */
    protected function enableFioChecker($user)
    {
        $user['__fio']->setChecker([$user, 'checkFioField']);
        $user['__name']->removeAllCheckers();
        $user['__surname']->removeAllCheckers();
        $user['__midname']->removeAllCheckers();
        return $user;
    }

    /**
     * Заполняет объект пользователя данными
     *
     * @param array $data
     * @param User $user
     * @return void
     */
    protected function fillData(array $data, User $user)
    {
        //Оставляем для заказа только ключи, присутствующие в схеме валидации
        $config = File::config();
        $allow_keys = array_keys($this->getUserDataValidator()->getSchema());
        $data = array_intersect_key($data, array_flip($allow_keys));
        $data['changepass'] = 1;
        $data['creator_app_id'] = $this->token->getApp()->getId();

        if (!empty($data['generate_password'])) {
            $data['openpass'] = Tools::generatePassword($config['generate_password_length'], $config['generate_password_symbols']);
        }

        $user->getFromArray($data);
        $user['__phone']->setEnableVerification(false);

        //Отключаем все остальные Checkers
        $other_keys = array_diff($user->getPropertyIterator()->getKeys(), $allow_keys);
        foreach($other_keys as $key) {
            $user['__'.$key]->removeAllCheckers();
        }
    }

    /**
     * Создает пользователя (администратором)
     * ---
     * Позволяет упрощенно создавать пользователя, не требуется вводить капчу или подтверждать номер телефона.
     * Метод поддерживает прием параметров только методом POST.
     *
     * @param string $token Авторизационный токен
     * @param array $data Данные о пользователе
     * #data-user-info
     *
     * @example POST /api/methods/user.create?token=311211047ab5474dd67ef88345313a6e479bf616
     *
     * <pre>Тело POST запроса (Content-type:application/json)
     * {
     *     "fio":"Иванов Иван Иванович",
     *     "e_mail":"test@example.com",
     *     "phone":"+70000000000",
     *     "is_company":1,
     *     "company":"ООО Ромашка",
     *     "company_inn": "1234567890"
     *     "data": [
     *          "string_field":"Значение доп поля"
     *          "checkbox_field":1
     *     ],
     *     "generate_password":1,
     *     "no_send_notice":0,
     * }</pre>
     *
     * Ответ:
     *
     * <pre>{
     *   "response": {
     *       "success": true
     *   }
     *}</pre>
     *
     * @return array
     */
    public function process($token, $data)
    {
        $this->getUserDataValidator()->validate('data', $data, $this->method_params);
        $user = $this->enableFioChecker(new User());
        $this->fillData($data, $user);

        if ($user->validate() && $user->insert()) {
            return [
                'response' => [
                    'success' => true,
                    'user' => Login::makeResponseUserData($user)
                ]
            ];
        }

        throw new ApiException(implode(', ', $user->getErrors()), ApiException::ERROR_WRITE_ERROR);
    }
}