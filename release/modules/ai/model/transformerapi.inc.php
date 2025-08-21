<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model;

use Ai\Model\Transformer\AbstractTransformer;

class TransformerApi
{
    /**
     * Возвращает список трансформеров
     *
     * @return array
     */
    public function getList()
    {
        $result = [];
        foreach(AbstractTransformer::getAllTransformers() as $transformer) {
            $result[] = [
                'id' => $transformer::getId(),
                'title' => $transformer::getTitle(),
            ];
        }
        return $result;
    }

    /**
     * Возвращает список транформеров в виде ассоциативного массива
     *
     * @param array $first
     * @return array
     */
    public static function staticSelectList(array $first = [])
    {
        $result = [];
        foreach(AbstractTransformer::getAllTransformers() as $transformer) {
            $result[$transformer::getId()] = $transformer::getTitle();
        }

        return $first + $result;
    }

    /**
     * Возвращает список полей трансформера
     *
     * @param string $transformer_id Идентификатор трансформера
     * @param array $first первый жлемент массива
     * @return array
     */
    public static function staticSelectFieldList($transformer_id, array $first = [])
    {
        $result = [];
        foreach(AbstractTransformer::getAllTransformers() as $transformer) {
            if ($transformer::getId() == $transformer_id) {
                $transformer_instance = new $transformer();
                foreach ($transformer_instance->getFields() as $field) {
                    $result[$field->getFieldName()] = $field->getTitle() . ' (' . $field->getTypeTitle() . ')';
                }
            }
        }

        return $first + $result;
    }
}