<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\Orm;

use ExternalApi\Config\File;
use ExternalApi\Model\ApiRouter;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;

/**
 * ORM-объект, описывающий виртуальное приложение,
 * которое имеет доступ к выбранным методам API
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $title Внутреннее название
 * @property string $client_id Client ID приложения
 * @property string $client_secret Client Secret приложения
 * @property string $client_secret_open Client Secret приложения
 * @property integer $enable Включено
 * @property array $groups Группы пользователей приложения
 * @property string $groups_json Группы пользователей приложения в JSON
 * @property array $rights Права доступа к методам
 * @property string $rights_json Права доступа к методам в JSON
 * @property integer $use_vapp_endpoint Использовать отдельный адрес API
 * @property string $vapp_endpoint_api_key API-ключ виртуального приложения
 * @property integer $vapp_endpoint_enable_api_help Включить справку по внешнему API по ссылке /api-[API ключ]/help
 * --\--
 */
class VirtualApp extends OrmObject
{
    const SALT_PHRASE = '--CLIENT-SECRET-LOCAL-SALT--';
    const CLIENT_ID_PREFIX = 'virtual-';

    protected static $table = 'external_api_app';

    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'title' => (new Type\Varchar())
                    ->setDescription(t('Внутреннее название'))
                    ->setHint(t('Придумайте его, любое название')),
                'client_id' => (new Type\Varchar())
                    ->setChecker('ChkEmpty', t('Client ID не может быть пустым'))
                    ->setDescription(t('Client ID приложения'))
                    ->setAttr([
                        'size' => 57
                    ])
                    ->setTemplate('%externalapi%/form/virtualapp/client_id.tpl')
                    ->setHint(t('Необходимо для доступа к методам, требующим авторизацию.')),
                'client_secret' => (new Type\Varchar())
                    ->setVisible(false)
                    ->setDescription(t('Client Secret приложения'))
                    ->setHint(t('Необходимо для доступа к методам, требующим авторизацию')),
                'client_secret_open' => (new Type\Varchar())
                    ->setRuntime(true)
                    ->setDescription(t('Client Secret приложения'))
                    ->setAttr([
                        'type' => 'password',
                        'placeholder' => t('Введите новый пароль')
                    ])
                    ->setChecker(function($_this, $value) {
                        if (!$_this['client_secret'] && $value == '') {
                            return t('Придумайте Client Secret');
                        }
                        if ($value != '' && mb_strlen($value) < 8) {
                            return t('Client Secret не может быть менее 8 знаков');
                        }
                        return true;
                    })
                    ->setHint(t('Необходимо для доступа к методам, требующим авторизацию. Если оставить пустое поле, то предыдущий пароль меняться не будет. Пароль не сохраняется в открытом виде, поэтому после сохранения его увидеть не будет возможности.')),
                'enable' => (new Type\Integer())
                    ->setDescription(t('Включено'))
                    ->setCheckboxView(1, 0),
                'groups' => (new Type\ArrayList())
                    ->setDescription(t('Группы пользователей приложения'))
                    ->setList(['\Users\Model\GroupApi', 'staticSelectList'])
                    ->setAttr([
                        'multiple' => true,
                        'size' => 10
                    ])
                    ->setHint(t('Удерживая CTRL, можно выбрать несколько групп. Только пользователи этих групп смогут авторизоваться, указав это приложение в методе oauth.*')),
                'groups_json' => (new Type\Text())
                    ->setDescription(t('Группы пользователей приложения в JSON'))
                    ->setVisible(false),
                'rights' => (new Type\ArrayList())
                    ->setDescription(t('Права доступа к методам'))
                    ->setHint(t('Доступ к методам, не требующим авторизацию будет ограничен только в случае использования отдельного адреса API для текущего виртуального приложения.'))
                    ->setList([__CLASS__, 'getAuthorizedApiMethod'])
                    ->setChecker(function($_this, $value) {
                        if (empty($value)) {
                            return t('Предоставьте доступ хотя бы к одному методу');
                        }
                        return true;
                    })
                    ->setTemplate('%externalapi%/form/virtualapp/rights.tpl'),
                'rights_json' => (new Type\Text())
                    ->setDescription(t('Права доступа к методам в JSON'))
                    ->setVisible(false),
            t('Подключение'),
                'use_vapp_endpoint' => (new Type\Integer())
                    ->setDescription(t('Использовать отдельный адрес API'))
                    ->setHint(t('При включении данной опции, вы можете создать независимый endpoint (адрес для подключения к API) исключительно для этого приложения.'))
                    ->setCheckboxView(1, 0),
                'vapp_endpoint_api_key' => (new Type\Varchar())
                    ->setUnique(true)
                    ->setMaxLength(80)
                    ->setChecker([__CLASS__, 'checkApiKey'])
                    ->setDescription(t('API-ключ виртуального приложения'))
                    ->setHint(t('API-ключ у виртуального приложения не может быть пустым.'))
                    ->setTemplate('%externalapi%form/virtualapp/vapp_endpoint_api_key.tpl'),
                'vapp_endpoint_enable_api_help' => (new Type\Integer())
                    ->setDescription(t('Включить справку по внешнему API по ссылке /api-[API ключ]/help'))
                    ->setHint(t('Справка будет содержать информацию только о доступных методах для данного виртуального приложения'))
                    ->setCheckboxView(1, 0)
        ]);
    }

    /**
     * Возвращает true, если такой API-ключ можно использовать для текущего объекта, иначе текст ошибки
     *
     * @param string $value значение API-ключа
     * @return true|string
     */
    public static function checkApiKey($_this, $value)
    {
        if ($_this['use_vapp_endpoint']) {
            if ($value == '') {
                return t('API-ключ не может быть пустым');
            }

            //Проверяем, нет ли у других виртуальных приложений таких же API-ключей
            $query = Request::make()
                ->select('vapp_endpoint_api_key')
                ->from(self::_tableName());

            if ($_this['id'] > 0) {
                $query->where("id != '#id'", ['id' => $_this['id']]);
            }
            $keys = $query->exec()->fetchSelected(null, 'vapp_endpoint_api_key');

            $config = File::config();
            $keys[] = $config['api_key'];

            if (in_array($value, $keys)) {
                return t('Такой API-ключ уже занят, придумайте другой');
            }
        }

        return true;
    }

    /**
     * Вызывается перед сохранением объекта
     *
     * @param $save_flag
     * @return bool
     */
    public function beforeWrite($save_flag)
    {
        if ($this['client_secret_open'] != '') {
            $this['client_secret'] = $this->generateSecretHash($this['client_secret_open']);
        }

        if ($this->isModified('rights')) {
            $this['rights_json'] = json_encode($this['rights'], JSON_UNESCAPED_UNICODE);
        }

        if ($this->isModified('groups')) {
            $this['groups_json'] = json_encode($this['groups'], JSON_UNESCAPED_UNICODE);
        }
    }


    /**
     * Возвращает список объектов API методов
     *
     * @return array
     */
    public static function getAuthorizedApiMethod()
    {
        $result = [];
        foreach(ApiRouter::getInstance()->getApiMethods(true, false) as $method) {
            $method_instance = new $method['class']();
            $info = $method_instance->getInfo();
            $last_version = end($info);

            $result[$method['method']] = [
                'instance' => $method_instance,
                'info_last_version' => $last_version
            ];
        }

        return $result;
    }

    /**
     * Вызывается после загрузки объекта
     */
    function afterObjectLoad()
    {
        $this['rights'] = json_decode((string)$this['rights_json'], true) ?: [];
        $this['groups'] = json_decode((string)$this['groups_json'], true) ?: [];
    }

    /**
     * Возвращает префикс для ClientID виртуальных приложений
     *
     * @return string
     */
    function getClientIdPrefix()
    {
        return self::CLIENT_ID_PREFIX;
    }

    /**
     * Возвращает итоговый ClientID для данного виртуального приложения
     *
     * @return string
     */
    function getClientId()
    {
        return $this->getClientIdPrefix().$this['client_id'];
    }

    /**
     * Возвращает ClientSecret для данного виртуального приложения
     *
     * @return string
     */
    function getClientSecret()
    {
        return $this['client_secret'];
    }

    /**
     * Возвращает хэш пароля приложения
     *
     * @param $client_secret_open
     * @return string
     */
    public function generateSecretHash($client_secret_open)
    {
        return sha1($client_secret_open.sha1(\Setup::$SECRET_SALT.self::SALT_PHRASE));
    }

    /**
     * Возвращает методы API, к которым у данного приложения есть доступ
     *
     * @return array
     */
    public function getAllowableMethods()
    {
        $rights = $this['rights'] ?: [];
        return array_map('strtolower', array_keys($rights));
    }
}