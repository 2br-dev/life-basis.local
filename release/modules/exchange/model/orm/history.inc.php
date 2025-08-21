<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Exchange\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;

/**
 * Объект записи в историю запросов при импорте
 * Каждый отдельный get или post запрос создает новую запись в таблице `exchange_history`
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $dateof Дата обмена
 * @property string $method Метод
 * @property string $type Тип
 * @property string $mode Режим
 * @property string $query Запрос
 * @property integer $postsize Размер post
 * @property string $response Ответ сервера
 * @property float $duration Время обработки запроса сек.
 * @property integer $memory_peak Памяти израсходовано
 * @property integer $readed_nodes Позиция, на которой остановился импорт
 * --\--
 */
class History extends OrmObject
{
    protected static $table = 'exchange_history';

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'dateof' => new Type\Datetime([
                'description' => t('Дата обмена'),
                'index' => true
            ]),
            'method' => new Type\Varchar([
                'maxLength' => '10',
                'description' => t('Метод'),
            ]),
            'type' => new Type\Varchar([
                'maxLength' => '20',
                'description' => t('Тип'),
            ]),
            'mode' => new Type\Varchar([
                'maxLength' => '20',
                'description' => t('Режим'),
            ]),
            'query' => new Type\Varchar([
                'maxLength' => '1024',
                'description' => t('Запрос'),
            ]),
            'postsize' => new Type\Integer([
                'maxLength' => '10',
                'description' => t('Размер post'),
            ]),
            'response' => new Type\Text([
                'description' => t('Ответ сервера'),
            ]),
            'duration' => new Type\Decimal([
                'maxLength' => 10,
                'decimal' => 3,
                'description' => t('Время обработки запроса сек.'),
            ]),
            'memory_peak' => new Type\Integer([
                'maxLength' => 10,
                'description' => t('Памяти израсходовано'),
            ]),
            'readed_nodes' => new Type\Integer([
                'maxLength' => 10,
                'description' => t('Позиция, на которой остановился импорт'),
            ]),
        ]);
    }

    static public function removeOldItems()
    {
        $total = OrmRequest::make()
            ->from(new self)
            ->count();

        $limit = 400;
        if ($total <= $limit) return;

        OrmRequest::make()
            ->from(new self)
            ->orderby("id")
            ->limit($total - $limit)
            ->delete()
            ->exec();
    }
}
