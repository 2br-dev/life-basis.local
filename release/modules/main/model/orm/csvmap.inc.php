<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\Orm;
use RS\Csv\AbstractSchema;
use \RS\Orm\Type;

/**
 * Предустановка для экспорта CSV
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $schema Схема импорта-экспорта
 * @property string $type Тип операции
 * @property string $title Название предустановки
 * @property array $columns 
 * @property string $_columns Информация о колонках
 * @property string $format Формат данных
 * --\--
 */
class CsvMap extends \RS\Orm\OrmObject
{
    const
        TYPE_EXPORT = 'export',
        TYPE_IMPORT = 'import';
    
    protected static
        $table = 'csv_map';

    function _init()
    {
        parent::_init()->append([
            'schema' => new Type\Varchar([
                'description' => t('Схема импорта-экспорта')
            ]),
            'type' => new Type\Enum(['export', 'import'], [
                'description' => t('Тип операции')
            ]),
            'title' => new Type\Varchar([
                'description' => t('Название предустановки')
            ]),
            'columns' => new Type\ArrayList(),
            '_columns' => new Type\Text([
                'description' => t('Информация о колонках')
            ]),
            'format' => new Type\Varchar([
                'description' => t('Формат данных'),
                'default' => 'csv'
            ])
        ]);
    }
    
    function beforeWrite($save_flag)
    {
        $this['_columns'] = serialize($this['columns']);
    }
    
    function afterObjectLoad()
    {
        $columns = @unserialize((string)$this['_columns']) ?: [];
        $this['columns'] = array_map(function($value)  {
                return str_replace('&quot;', '&apos;', $value);
            }, $columns);
    }
    
    /**
    * Возвращает список имеющихся предустанвок для заданной $schema и $type
    * 
    * @param string $schema имя схемы
    * @param string $type импорт или экспорт
    * @return array
    */
    static function loadList($schema, $type)
    {
        return \RS\Orm\Request::make()
            ->from(new self())
            ->where([
                'schema' => $schema,
                'type' => $type
            ])->objects();
    }
    
    /**
    * Возвращает JSON для применения предустановки в JavaScript
     *
    * @return string
    */
    function getJson()
    {
        //оставляем только идентификаторы колонок
        $columns_data = $this['columns'] ?: [];
        if ($this->type == $this::TYPE_IMPORT) {
            $columns = array_combine(array_keys($columns_data), array_values($columns_data));
        } elseif ($this->type == $this::TYPE_EXPORT) {
            $columns = array_combine(array_keys($columns_data), array_keys($columns_data));
        }

        return json_encode([
            'format' => $this['format'],
            'columns' => $columns
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}