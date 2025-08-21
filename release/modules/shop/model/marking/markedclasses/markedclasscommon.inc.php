<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Marking\MarkedClasses;

use Shop\Model\Marking\MarkingApi;
use Shop\Model\Marking\MarkingException;
use Shop\Model\Orm\OrderItemUIT;

class MarkedClassCommon extends AbstractMarkedClass
{
    protected $name;
    protected $title;
    protected $marking_type_id;

    public function __construct(string $name, string $title, string $marking_type_id = '444d')
    {
        $this->name = $name;
        $this->title = $title;
        $this->marking_type_id = $marking_type_id;
    }

    /**
     * Возвращает код товара в строковом представлении
     *
     * @param OrderItemUIT $uit
     * @return string
     */
    public function getNomenclatureCode(OrderItemUIT $uit): string
    {
        $uit_parts = $uit->asArray();
        $code = '01'.$uit_parts[MarkingApi::USE_ID_GTIN].'21'.$uit_parts[MarkingApi::USE_ID_SERIAL];

        //DataMatrix коды сканируются без спец.символов <GS>, поэтому приходится их добавлять
        //Согласно спецификациям Честного знака, <GS> есть сразу после серийного номера и после Кода проверки,
        // который имеет длину 6 символов и идет следом за серийным номером
        $other = $uit_parts[MarkingApi::USE_ID_OTHER];
        if ($other !== '') {
            if (mb_strlen($other) > 6) {
                $other = mb_substr($other, 0, 6).chr(29).mb_substr($other, 6);
            }
            $code .= chr(29).$other;
        }

        return $code;
    }

    /**
     * Разбивает УИТ на составные части
     *
     * @param string $code - УИТ в текстовом виде
     * @return string[]
     * @throws MarkingException
     */
    protected function parseCode(string $code): array
    {
        if (!preg_match('/[а-яёА-ЯЁ]/u', $code)) {
            preg_match('/01(\d{14})21(.{13})(.*)$/u', $code, $matches);
        }

        if (empty($matches)) {
            throw new MarkingException(t('Некорректный код'), MarkingException::ERROR_SINGLE_CODE_PARSE);
        }
        
        $result = [
            MarkingApi::USE_ID_GTIN => $matches[1] ? ltrim($matches[1], '0') : null,
            MarkingApi::USE_ID_SERIAL => $matches[2] ?? null,
            MarkingApi::USE_ID_OTHER => $matches[3] ?? null,
            'n_code' => $matches[0],
        ];

        return $result;
    }

    /**
     * Возвращает имя класса маркированых товаров
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Возвращает публичное имя класса маркированых товаров
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Возвращает код типа маркировки
     *
     * @return string
     */
    public function getMarkingTypeId(): string
    {
        return $this->marking_type_id;
    }
}
