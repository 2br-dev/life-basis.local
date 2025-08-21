<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Controller\Utils;

use RS\Cache\Manager as CacheManager;

/**
 * Менеджер кэша для хранения настроек отрендеренных блоков.
 * Вынесен в отдельный класс, чтобы сохранять кэш в отдельной папке.
 * Такой кэш не должен очищаться при стандартном сбросе кэша из шапки административной панели.
 */
class BlockCacheManager extends CacheManager
{
    protected
        $cache_folder = CACHE_BLOCKS_FOLDER; //Определено в \Setup;
}