<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Config;

use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Right;
use RS\AccessControl\RightGroup;

/**
 * Класс описывает права доступа к модулю
 */
class ModuleRights extends DefaultModuleRights
{
    const RIGHT_SUPPORT_SHOW_IN_APP = 'support_show_in_app';

    /**
     * Возвращает древовидный список собственных прав модуля
     *
     * @return (Right|RightGroup)[]
     */
    protected function getSelfModuleRights()
    {
        return array_merge(parent::getSelfModuleRights(), [
            new Right(self::RIGHT_SUPPORT_SHOW_IN_APP, t('Доступ из приложения')),
        ]);
    }
}
