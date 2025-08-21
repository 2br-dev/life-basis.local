<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model;

use RS\Module\AbstractModel\EntityList;

/**
 * API для работы со справочником моделей GPT-сервисов
 */
class ServiceModelApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\ServiceModel(), [
            'nameField' => 'title'
        ]);
    }

    /**
     * Возвращает список моделей по типу сервиса
     *
     * @param string $type Тип сервиса
     * @param array $first
     * @return array
     */
    public static function staticSelectListByType($type, array $first = [])
    {
        $self = new static();
        $self->setFilter('service_type', $type);
        return $first + $self->getAssocList('model_key', $self->getNameField());
    }
}