<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Platform;

use RS\Event\Manager as EventManager;
use RS\Exception;

/**
 * Менеджер загрузки списка платформ поддержки.
 * Пример платформ поддержки - Сайт, telegram, электронная почта,...
 */
class Manager
{
    /**
     * Возвращает список всех имеющихся платформ поддержки,
     * зарегистрированных в системе. Для регистрации в системе используется событие support.getPlatforms
     *
     * @param bool $cache - если true, то будет использовано кэширование
     * @return AbstractPlatform[]
     */
    public static function getPlatforms($cache = true)
    {
        static $result;

        if (!$cache || $result === null) {
            $result = [];
            $event_result = EventManager::fire('support.getPlatforms', []);
            foreach($event_result->getResult() as $item) {
                if ($item instanceof AbstractPlatform) {
                    $result[$item->getId()] = $item;
                } else {
                    throw new Exception(t('Платформа поддержки должна быть наследником класса Support\Model\Platform\AbstractPlatform, а передан %type', [
                        'type' => (string)$item
                    ]));
                }
            }
        }
        return $result;
    }

    /**
     * Возвращает список названий всех имеющихся платформ поддержки,
     * зарегистрированных в системе.
     *
     * @param bool $cache - если true, то будет использовано кэширование
     * @param array $first - первый элемент
     * @return string[]
     * @throws Exception
     */
    public static function getPlatfromTitles($cache = true, array $first = [])
    {
        $platforms = self::getPlatforms($cache);
        $result = [];
        foreach($platforms as $id => $platform) {
            $result[$id] = $platform->getTitle();
        }
        return $first + $result;
    }

    /**
     * Возвращает список названий всех имеющихся платформ поддержки,
     * зарегистрированных в системе, которые возможно выводить в личном кабинете пользователя на сайте
     * Здесь не присутствует в списке платформа site, так как она по умолчанию всегда считается, что присутствует
     *
     * @param bool $cache - если true, то будет использовано кэширование
     * @param array $first - первый элемент
     * @return string[]
     * @throws Exception
     */
    public static function getAllowOnSitePlatfromTitles($cache = true, array $first = [])
    {
        $platforms = self::getPlatforms($cache);
        $result = [];
        foreach($platforms as $id => $platform) {
            if ($platform->canShowOnSite() && $id != PlatformSite::PLATFORM_ID) {
                $result[$id] = $platform->getTitle();
            }
        }
        return $first + $result;
    }

    /**
     * Возвращает экземпляр класса платформы поддержки
     *
     * @param string $id
     * @param bool $cache - если true, то будет использовано кэширование
     * @return AbstractPlatform
     */
    public static function getPlatformById($id, $cache = true)
    {
        $types = self::getPlatforms($cache);
        return isset($types[$id]) ? clone $types[$id] : new PlatformUnknown($id);
    }
}