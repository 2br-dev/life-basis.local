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
 * Класс категория типа источника от которого пришел пользователь
 * @package Statistic\Model\Orm
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название источника
 * --\--
 */
class SourceTypeDir extends \RS\Orm\OrmObject
{
    protected static
        $table = 'statistic_user_source_type_dir';
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'title' => new Type\Varchar([
                'description' => t('Название источника'),
            ]),
        ]);
    }
}