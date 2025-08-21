<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model;

use RS\HashStore\Api;
use RS\Module\AbstractModel\EntityList;

/**
 * API для работы со сборщиками почты
 */
class CrawlerProfileApi extends EntityList
{
    const RUN_HASHSTORE_KEY = 'crawler_need_run';

    function __construct()
    {
        parent::__construct(new Orm\CrawlerProfile(), [
            'multisite' => true,
            'nameField' => 'title'
        ]);
    }

    /**
     * Устанавливает отметку о том, что нужно проверить почту
     */
    public function setNeedRunCrawlerMark()
    {
        Api::set(self::RUN_HASHSTORE_KEY.$this->getSiteContext(), true);
    }

    /**
     * Возвращает true, если установлена отметка "нужно проверить почту"
     *
     * @return bool
     */
    public function isNeedRunCrawlerMark()
    {
        return Api::get(self::RUN_HASHSTORE_KEY.$this->getSiteContext(), false);
    }

    /**
     * Удаляет отметку о необходимости проверки почты
     */
    public function removeNeedRunCrawlerMark()
    {
        Api::set(self::RUN_HASHSTORE_KEY.$this->getSiteContext(), null);
    }

    /**
     * Возвращает true, если все необходимые модули установлены в PHP
     *
     * @return array
     */
    public static function getUnexistsModules()
    {
        $result = [];
        if (!function_exists('imap_open')) {
            $result[] = 'imap';
        }
        if (!function_exists('finfo_file')) {
            $result[] = 'fileinfo';
        }

        return $result;
    }
}