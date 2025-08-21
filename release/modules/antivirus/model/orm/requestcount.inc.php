<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Antivirus\Model\Orm;
use \RS\Orm\Type;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $ip IP адрес
 * @property integer $last_time Дата последнего запроса в милисекундах
 * @property integer $count Количество запросов
 * @property integer $malicious_count Количество вредоносных запросов
 * --\--
 */
class RequestCount extends \RS\Orm\OrmObject
{

    protected static
        $table = 'antivirus_request_count';
        
    function _init()
    {
        parent::_init()->append([
            'ip' => new Type\Varchar([
                'maxLength' => 100,
                'description' => t('IP адрес'),
            ]),
            'last_time' => new Type\Bigint([
                'description' => t('Дата последнего запроса в милисекундах'),
            ]),
            'count' => new Type\Integer([
                'description' => t('Количество запросов'),
                'allowEmpty' => false,
            ]),
            'malicious_count' => new Type\Integer([
                'description' => t('Количество вредоносных запросов'),
                'allowEmpty' => false,
            ]),
        ]);
        
        $this->addIndex(['ip'], self::INDEX_UNIQUE);
    }


}