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
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $dateof Дата события
 * @property string $type Тип события
 * @property string $details Детали события
 * @property integer $count Количество событий за данный день
 * --\--
 */
class StatEvent extends \RS\Orm\OrmObject
{
    protected static
        $table = 'statistic_events';
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'dateof' => new Type\Date([
                'description' => t('Дата события'),
            ]),
            'type' => new Type\Varchar([
                'maxLength' => 100,
                'description' => t('Тип события'),
                'attr' => [['size' => 1]],
                'list' => [['\Main\Model\StatisticEvent', 'getTypeList']],
            ]),
            'details' => new Type\Text([
                'description' => t('Детали события'),
            ]),
            'count' => new Type\Integer([
                'description' => t('Количество событий за данный день'),
                'default' => 1,
                'allowEmpty' => false,
            ]),

        ]);
        
        $this->addIndex(['dateof'], self::INDEX_KEY);
        $this->addIndex(['site_id', 'dateof', 'type'], self::INDEX_UNIQUE);
    }

    public function getDetails()
    {
        return @unserialize((string)$this['details']);
    }


    public function setDetails($object)
    {
        $this['details'] = serialize($object);
    }


}