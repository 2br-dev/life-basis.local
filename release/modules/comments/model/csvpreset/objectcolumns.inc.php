<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Comments\Model\CsvPreset;

use RS\Csv\Preset\AbstractPreset;

/**
 * Добавляет колонки в экспорт комментариев
 */
class ObjectColumns extends AbstractPreset
{
    /**
     * Возвращает данные для вывода в CSV
     *
     * @param int $n
     * @return array
     */
    public function getColumnsData($n): array
    {
        $comment = $this->schema->rows[$n];
        $comment_type = $comment->getTypeObject();

        return [
            $this->id.'_object_type' => $comment_type->getTitle(),
            $this->id.'_object_title' => $comment_type->getLinkedObjectTitle(),
        ];
    }

    /**
     * Возвращает колонки, которые добавляются текущим набором
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            $this->id.'_object_type' => [
                'key' => 'object_type',
                'title' => t('Тип'),
            ],
            $this->id.'_object_title' => [
                'key' => 'object_title',
                'title' => t('Название'),
            ],
        ];
    }

    public function importColumnsData() {}
}