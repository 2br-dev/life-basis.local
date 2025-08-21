<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Config;

use RS\Module\Exception as ModuleException;
use RS\Orm\ConfigObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use Users\Model\Verification\Provider\Email;
use Users\Model\Verification\Provider\Sms;

/**
* Конфигурационный файл модуля
*/
class File extends ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'profiles' => (new Type\MixedType())
                ->setVisible(true)
                ->setDescription('Профили для Telegram-ботов')
                ->setTemplate('%telegram%/admin/config/profiles.tpl'),
            'verify_providers' => (new Type\ArrayList())
                ->setDescription(t('Каналы отправки кода верификации авторизации'))
                ->setHint(t('Поля верификации задаются в настройках модуля Пользователи и группы. Отметьте хотя бы один способ получения кода верфикации. Если Email будет отмечен и у пользователя будет существовать Email, то код будет направлен на Email приоритетно. Для отправки SMS, в системе должна быть настроена интеграция с SMS-сервисом. SMS используется только если Email выключен, а также только для пользователей, у которых из идентификационных данных есть только Номер телефона.'))
                ->setRuntime(false)
                ->setListFromArray([
                    Email::getId() => t('Разрешить канал Email'),
                    Sms::getId() => t('Разрешить канал SMS')
                ])
                ->setCheckboxListView(true)
        ]);
    }

    /**
     * Возвращает значения свойств по-умолчанию
     *
     * @return array
     * @throws ModuleException
     */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
                'tools' => [
                    [
                        'url' => RouterManager::obj()->getAdminUrl(false, [], 'telegram-userctrl'),
                        'title' => t('Пользователи Telegram'),
                        'class' => ' ',
                        'description' => t('Раздел позволяет управлять списком пользователей, обращавшихся через Telegram'),
                    ]
                ]
            ];
    }

}