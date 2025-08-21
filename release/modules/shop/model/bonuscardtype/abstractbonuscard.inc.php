<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\BonusCardType;

use RS\Exception as RSException;
use Shop\Model\Orm\BonusCards;

/**
 * Абстрактный класс бонусной карты.
 */
abstract class AbstractBonusCard
{
    protected $additional_fields = [];

    /**
     * Возвращает сокращенное название провайдера (только латинские буквы)
     * @return string
     */
    abstract public function getShortName();

    /**
     * Возвращает отображаемое название провайдера
     * @return string
     */
    abstract public function getTitle();

    /**
     * Добавляет бонусную карту в систему
     *
     * @param $user_id - id пользователя
     * @param $number - номер бонусной карты
     * @param $data - дополнительные данные
     * @return BonusCards
     * @throws RSException
     */
    abstract public function addBonusCard($user_id, $number, $data);

    /**
     * Возвращает дополнительные поля бонусной карты
     *
     * @return mixed
     */
    public function getAdditionalFields()
    {
        $fields = $this->additional_fields;

        foreach ($fields as $key => $field) {
            $fields[$key]['value'] = null;
            $fields[$key]['user_field'] = $field['alias'];
        }
        return $fields;
    }

    /**
     * Устанавливает значение дополнительных полей
     *
     * @param array $field
     */
    public function setAdditionalFields($field = [])
    {
        $this->additional_fields[] = $field;
    }
}
