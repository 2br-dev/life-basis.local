<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Marketplace\Config;

use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Right;

class ModuleRights extends DefaultModuleRights
{
    const RIGHT_VIEW_MENU_MARKETPLACE = 'view_menu_marketplace';


    protected function getSelfModuleRights()
    {
        return [
            new Right(self::RIGHT_READ, t('Чтение')),
            new Right(self::RIGHT_CREATE, t('Создание')),
            new Right(self::RIGHT_UPDATE, t('Изменение')),
            new Right(self::RIGHT_DELETE, t('Удаление')),
            new Right(self::RIGHT_VIEW_MENU_MARKETPLACE, t('Показ кнопки меню Маркетплейс')),
        ];
    }
}
