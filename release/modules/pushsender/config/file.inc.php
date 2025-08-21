<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Config;
use \RS\Orm\Type;

class File extends \RS\Orm\ConfigObject
{
    CONST FIREBASE_AUTH_SERVER_KEY = 'googlefcm_server_key';
    CONST FIREBASE_AUTH_API = 'cloud_messaging_api';

    function _init()
    {
        parent::_init()->append([
            'firebase_auth_type' => new Type\Varchar([
                'description' => t('Тип авторизации в Firebase'),
                'listFromArray' => [[
                    self::FIREBASE_AUTH_SERVER_KEY => t('Cloud Messaging API (Legacy)'),
                    self::FIREBASE_AUTH_API => t('Firebase Cloud Messaging API (V1)')
                ]],
                'default' => 'cloud_messaging_api',
                'template' => '%pushsender%/form/auth_type/firebase_auth_type.tpl',
            ]),
            'googlefcm_server_key' => new Type\Varchar([
                'description' => t('Ключ сервера Google FireBase Cloud Messaging'),
                'hint' => t('Ключ из настроек в Google FireBase Cloud Messaging'),
            ]),
            'project_id' => new Type\Varchar([
                'description' => t('Project ID'),
                'hint' => t('Project ID из настроек в Google FireBase'),
            ]),
            'cloud_messaging_api' => new Type\File([
                'description' => t('Файл с настройками Service Account'),
                'hint' => t('Файл Service Account из настроек в Google FireBase'),
                'storage' => [\Setup::$ROOT, \Setup::$STORAGE_DIR.'/firebase/']
            ])
        ]);
    }
    
    /**
    * Возвращает массив кнопок для панели справа
    * 
    * @return array
    */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl(false, [], 'pushsender-pushtokenctrl'),
                    'title' => t('Просмотреть push-токены'),
                    'description' => t('Отображает зарегистрированные токены клиентских устройств'),
                    'class' => ' '
                ]
            ]
            ];
    }

    /**
     * Возвращает список пунктов меню
     * @return array
     */
    function getMenusList()
    {
        return \Menu\Model\Api::staticSelectList();
    }
    
}