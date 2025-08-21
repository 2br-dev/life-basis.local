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

/**
 * Класс отвечает за маркировку продукции Табак
 */
class MarkedClassTobacco extends MarkedClassCommon
{
    /**
     * Возвращает код товара в строковом представлении
     *
     * @param OrderItemUIT $uit
     * @return string
     */
    public function getNomenclatureCode(OrderItemUIT $uit): string
    {
        if (!$uit['gtin'] || !$uit['serial']) return '';

        $uit_parts = $uit->asArray();
        $serial = $uit_parts[MarkingApi::USE_ID_SERIAL];
        $other = $uit_parts[MarkingApi::USE_ID_OTHER];

        if (mb_strlen($other) == 8) {
            //Пачка
            return '01'.$uit_parts[MarkingApi::USE_ID_GTIN].
                   '21'.$uit_parts[MarkingApi::USE_ID_SERIAL].
                   $other;
        } else {
            //Блок
            return '01'.$uit_parts[MarkingApi::USE_ID_GTIN].
                    '21'.$uit_parts[MarkingApi::USE_ID_SERIAL]
                    .chr(29).mb_substr($other,0, 4) //AI 8005
                    .mb_substr($other,4, 6) //Сумма МРЦ
                    .chr(29).mb_substr($other,10, 6); //AI 93 + Код проверки
        }
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
            //Пачка
            if (preg_match('/^(\d{14})(.{7})(.{8})$/u', $code, $matches)) {
                return [
                    MarkingApi::USE_ID_GTIN => ltrim($matches[1], '0'),
                    MarkingApi::USE_ID_SERIAL => $matches[2],
                    MarkingApi::USE_ID_OTHER => $matches[3],
                    'n_code' => $matches[0],
                ];
            }

            //Блок
            if (preg_match('/^01(\d{14})21(.{7})(.{16})$/u', $code, $matches)) {
                return [
                    MarkingApi::USE_ID_GTIN => ltrim($matches[1], '0'),
                    MarkingApi::USE_ID_SERIAL => $matches[2],
                    MarkingApi::USE_ID_OTHER => $matches[3],
                    'n_code' => $matches[0],
                ];
            }
        }

        if (empty($matches)) {
            throw new MarkingException(t('Некорректный код'), MarkingException::ERROR_SINGLE_CODE_PARSE);
        }
    }
}