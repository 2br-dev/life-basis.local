<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Config;

use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Right;
use RS\AccessControl\RightGroup;

/**
 * Класс описывает права доступа к модулю
 */
class ModuleRights extends DefaultModuleRights
{
    const RIGHT_ONECLICK_SHOW_IN_APP = 'oneclick_show_in_app';
    const RIGHT_ONECLICK_CHANGING = 'oneclick_changing';
    const RIGHT_ONECLICK_ACTIONS = 'oneclick_actions';
    const RIGHT_ONECLICK_DELETE = 'oneclick_delete';

    /**
     * Возвращает древовидный список собственных прав модуля
     *
     * @return (Right|RightGroup)[]
     */
    protected function getSelfModuleRights()
    {
        return array_merge(parent::getSelfModuleRights(), [
                new RightGroup('group_oneclick', t('Покупки в 1 клик'), [
                    new Right(self::RIGHT_ONECLICK_SHOW_IN_APP, t('Доступ из приложения')),
                    new Right(self::RIGHT_ONECLICK_CHANGING, t('Изменение покупки в 1 клик')),
                    new Right(self::RIGHT_ONECLICK_ACTIONS, t('Создание заказа из покупки')),
                    new Right(self::RIGHT_ONECLICK_DELETE, t('Удаление покупки в 1 клик')),
                ]),
            ]);
    }
}
