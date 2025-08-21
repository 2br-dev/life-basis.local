<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Statistic\Model\Orm;
use \RS\Orm\Type;

/**
 * Класс связь с источником от которого пришел пользователь
 * @package Statistic\Model\Orm
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $partner_id Партнёрский сайт
 * @property integer $source_type Идентификатор типа источника на сайте
 * @property string $referer_site Сайт источник из поля реферер
 * @property string $referer_source Полный источник из поля реферер
 * @property string $landing_page Страница первого посещения
 * @property string $utm_source Рекламная система UTM_SOURCE
 * @property string $utm_medium Тип трафика UTM_MEDIUM
 * @property string $utm_campaign Рекламная кампания UTM_COMPAING
 * @property string $utm_term Ключевое слово UTM_TERM
 * @property string $utm_content Различия UTM_CONTENT
 * @property string $dateof Дата события
 * --\--
 */
class Source extends \RS\Orm\OrmObject
{
    protected static
        $table = 'statistic_source';
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'partner_id' => new Type\Integer([
                'description' => t('Партнёрский сайт'),
                'index' => true
            ]),
            'source_type' => new Type\Integer([
                'description' => t('Идентификатор типа источника на сайте'),
            ]),
            'referer_site' => new Type\Varchar([
                'description' => t('Сайт источник из поля реферер'),
            ]),
            'referer_source' => new Type\Text([
                'description' => t('Полный источник из поля реферер'),
            ]),
            'landing_page' => new Type\Varchar([
                'description' => t('Страница первого посещения'),
            ]),
            //Параметры UTM меток
            'utm_source' => new Type\Varchar([
                'description' => t('Рекламная система UTM_SOURCE'),
            ]),
            'utm_medium' => new Type\Varchar([
                'description' => t('Тип трафика UTM_MEDIUM'),
            ]),
            'utm_campaign' => new Type\Varchar([
                'description' => t('Рекламная кампания UTM_COMPAING'),
            ]),
            'utm_term' => new Type\Varchar([
                'description' => t('Ключевое слово UTM_TERM'),
            ]),
            'utm_content' => new Type\Varchar([
                'description' => t('Различия UTM_CONTENT'),
            ]),
            'dateof' => new Type\Datetime([
                'description' => t('Дата события'),
                'index' => true
            ]),
        ]);
    }

    /**
     * Действия перед созданием источника
     *
     * @param string $save_flag - insert или update
     * @return false|null|void
     */
    function beforeWrite($save_flag)
    {
        if ($save_flag == self::INSERT_FLAG){
            $source_type_api = new \Statistic\Model\SourceTypesApi();
            $source_type_api->setSourceTypeToSource($this);
        }
    }

    /**
     * Возвращает объект типа источника
     *
     * @return SourceType
     */
    function getType()
    {
        $source_type = new SourceType($this['source_type']);
        if (!$source_type['id']){ //Если тип не назначен
            $source_type['title'] = t('Не определён');
        }
        return $source_type;
    }


}