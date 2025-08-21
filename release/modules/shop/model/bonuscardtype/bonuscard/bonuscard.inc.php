<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\BonusCardType\BonusCard;

use RS\Config\Loader;
use RS\Exception as RSException;
use Shop\Model\BonusCardsApi;
use Shop\Model\BonusCardType\AbstractBonusCard;
use Shop\Model\Orm\BonusCards;

/**
 * Бонусная карта
 */
class BonusCard extends AbstractBonusCard
{
    const START_NUMBER = 1111100000000;
    protected $api,
        $config,
        $additional_fields = [];

    public function __construct()
    {
        $this->api = new BonusCardsApi();
        $this->config = Loader::byModule($this);

        $this->setAdditionalFields([
            'alias' => 'sex',
            'title' => 'Пол',
            'type' => 'select',
            'options' => [['key' => 'M', 'value' => 'Мужской'], ['key' => 'W', 'value' => 'Женский']]
        ]);
        $this->setAdditionalFields([
            'alias' => 'phone',
            'title' => 'Телефон',
            'type' => 'text',
            'options' => null
        ]);
        $this->setAdditionalFields([
            'alias' => 'birthday',
            'title' => 'Дата рождения',
            'type' => 'date',
            'options' => null
        ]);
    }

    /**
     * Возвращает сокращенное название провайдера (только латинские буквы)
     * @return string
     */
    public function getShortName()
    {
        return 'rs_bonus_card';
    }

    /**
     * Возвращает отображаемое название провайдера
     * @return string
     */
    public function getTitle()
    {
        return t('Бонусные карты RS');
    }

    /**
     * Добавляет бонусную карту в систему
     *
     * @param $user_id - id пользователя
     * @param $number - номер бонусной карты
     * @param $data - дополнительные данные
     * @return BonusCards
     * @throws RSException
     */
    public function addBonusCard($user_id, $number = null, $data  = [])
    {
        if (!$number) {
            $number = $this->getNextCardNumber();
        }else {
            if (strlen($number) != 13) {
                throw new RSException(t('Номер должен состоять из 13 цифр'));
            }
        }

        if ($this->additional_fields) {
            $err_fields = [];
            foreach ($this->additional_fields as $key => $field) {
                if (!array_key_exists($field['alias'], $data)) {
                    $err_fields[] = $field['title'];
                }
            }
            if (!empty($err_fields)) {
                throw new RSException(t('Отсутствуют обязательные поля: ' . implode(', ', $err_fields)));
            }
        }

        $card = new BonusCards();
        $card->user_id = $user_id;
        $card->number = $number;
        $card->data = $data;
        $card->insert();
        return $card;
    }

    /**
     * Возвращает последний номер карты
     *
     * @return int - номер карты
     */
    protected function getLastCardNumber()
    {
        return (int)$this->config['last_bonus_card_number'] ?: self::START_NUMBER;
    }

    /**
     * Вычисляет и возвращает следующий номер карты
     *
     * @return string - номер карты
     * @throws RSException
     */
    protected function getNextCardNumber()
    {
        $last_card_number = $this->getLastCardNumber();

        if ($last_card_number >= $this->config['bonus_card_limit']) {
            throw new RSException(t('Что-то пошло не так. Позвоните нам в рабочее время и мы предоставим вам скидку.'));
        }
        $last_card_number = substr($last_card_number, 0, -1);
        $next_card_number = ++$last_card_number;
        $next_card_number = (string)$next_card_number . $this->checkNumber($next_card_number);

        $this->config['last_bonus_card_number'] = $next_card_number;
        $this->config->update();

        return $next_card_number;
    }

    /**
     * Вычисляет последнюю цифру номера карты
     *
     * @param $digits - номер карты
     * @return string
     */
    protected function checkNumber($digits)
    {
        $explode = array_reverse(preg_split("//", (string)$digits, -1, PREG_SPLIT_NO_EMPTY));

        $odd = 0;
        $even = 0;
        foreach ($explode as $key => $digit) {
            if ($key % 2 == 0) {
                $odd += $digit;
            } else {
                $even += $digit;
            }
        }

        $total = $even + $odd * 3;
        $result = (10 - ($total % 10)) % 10;
        return (string)$result;
    }
}
