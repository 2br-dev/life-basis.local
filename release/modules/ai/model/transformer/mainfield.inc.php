<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Transformer;

use RS\Exception;
use RS\Orm\AbstractObject;

/**
 * Класс, позволяет описать "главное поле" объекта, рядом с которым будет появляться
 * кнопка "сгенерировать все остальные поля". В настоящее время главное поле может быть только одно
 * (Обычно это название товара, заголовок новости, и т.д.)
 */
class MainField
{
    private $generate_fields_order;

    /**
     * Конструктор объекта
     *
     * @param AbstractTransformer $transformer Трансформер
     * @param string $main_field Идентификатор главного поля у исхожного объекта
     * @param string $title Текстовое название поля
     */
    function __construct(
        protected AbstractTransformer $transformer,
        private string $main_field,
        private string $main_field_title)
    {
    }

    /**
     * Возвращает идентификатор "главного" поля
     *
     * @return string
     */
    function getMainFieldName()
    {
        return $this->main_field;
    }

    /**
     * Возвращает название главного поля
     *
     * @return string
     */
    function getMainFieldTitle()
    {
        return $this->main_field_title;
    }

    /**
     * Устанавливает порядок генерации полей. Так как некоторые поля могут зависеть от других,
     * здесь можно указать какие поля в какой последовательности должны генерироваться, а также какие из них могут генерироваться параллельно.
     * Пример, если передать в $fields = [
     *   ['short_description'],
     *   ['description'],
     *   ['meta_title', 'meta_keywords', 'meta_description']
     * ]
     * то это будет означать, что сперва будет сгенерировано поле short_description, за ним description,
     * за ним одновременно 'meta_title', 'meta_keywords', 'meta_description' будут сгенерированы.
     *
     * @return $this
     */
    function setGenerateFieldsOrder(array $generate_fields_order)
    {
        $this->generate_fields_order = $generate_fields_order;
        return $this;
    }

    /**
     * Возвращает поля в том порядке, в котором они должны быть сгенерированы
     *
     * @return array
     */
    function getGenerateFieldsOrder()
    {
        if ($this->generate_fields_order) {
            return $this->generate_fields_order;
        }

        //По умолчанию все поля генерируются последовательно
        $result = [];
        foreach(array_keys($this->transformer->getFields()) as $field_name) {
            $result[] = [$field_name];
        }
        return $result;
    }

    /**
     * Добавляет атрибуты к полю ORM-объекта,
     * необходимые для активации возможностей в административной панели
     *
     * @param AbstractObject $orm_object
     * @return void
     */
    public function addFieldAttributes(AbstractObject $orm_object)
    {
        $orm_object->getProp($this->getMainFieldName())->setAttr([
            'data-ai-main-button' => htmlspecialchars(json_encode([
                'transformer' => $this->transformer->getId(),
                'generate_fields' => $this->getGenerateFieldsOrder(),
                'main_field_title' => $this->getMainFieldTitle()
            ], JSON_UNESCAPED_UNICODE))
        ]);
    }
}