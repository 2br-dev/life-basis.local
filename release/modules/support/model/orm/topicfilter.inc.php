<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;

/**
 * ORM объект - один сохраненный пресет с фильтрацией
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $user_id Пользователь, для которого настраивается фильтр
 * @property string $title Название выборки
 * @property string $filters Значения фильтров
 * @property array $filters_arr Значения фильтров - массив
 * @property integer $sortn Порядок
 * --\--
 */
class TopicFilter extends OrmObject
{
    protected static
        $table = 'support_topic_filter';

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'user_id' => new Type\User([
                'description' => t('Пользователь, для которого настраивается фильтр'),
                'visible' => false
            ]),
            'title' => new Type\Varchar([
                'description' => t('Название выборки'),
                'hint' => t('Придумайте название выборки. С помощью неё вы сможете быстро отбирать нужные тикеты.'),
                'checker' => ['chkEmpty', t('Укажите название выборки')]
            ]),
            'filters' => new Type\Text([
                'description' => t('Значения фильтров'),
                'visible' => false,
                'listenPost' => false
            ]),
            'filters_arr' => new Type\ArrayList([
                'description' => t('Значения фильтров - массив'),
                'visible' => false,
                'listenPost' => false
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Порядок'),
                'visible' => false
            ])
        ]);
    }

    /**
     * Обработчик перед сохранением объекта
     *
     * @param string $flag
     * @return false|void
     */
    function beforeWrite($flag)
    {
        if ($this->isModified('filters_arr')) {
            $this['filters'] = serialize($this['filters_arr']);
        }

        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = Request::make()
                    ->select('MAX(sortn) as max')
                    ->from($this)
                    ->where([
                        'user_id' => $this['user_id']
                    ])
                    ->exec()->getOneField('max', 0) + 1;
        }
    }

    /**
     * Обработчик загрузки объекта
     */
    function afterObjectLoad()
    {
        $this['filters_arr'] = unserialize((string)$this['filters']);
    }
}