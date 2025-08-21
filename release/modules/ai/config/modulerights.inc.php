<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Config;

use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Right;
use RS\AccessControl\RightGroup;

/**
 * Класс описывает права доступа к модулю
 */
class ModuleRights extends DefaultModuleRights
{
    const RIGHT_CHAT = 'chat';
    const RIGHT_FIELD_COMPLETION = 'field_completion';
    const RIGHT_STATISTIC_SHOW_ALL = 'statistic_show_all';

    /**
     * Возвращает древовидный список собственных прав модуля
     *
     * @return (Right|RightGroup)[]
     */
    protected function getSelfModuleRights()
    {
        return array_merge(parent::getSelfModuleRights(), [
                new Right(self::RIGHT_CHAT, t('Доступ к чату')),
                new Right(self::RIGHT_FIELD_COMPLETION, t('Доступ к автозаполнению полей')),
                new Right(self::RIGHT_STATISTIC_SHOW_ALL, t('Доступ к статистке всех пользователей')),
            ]);
    }
}
