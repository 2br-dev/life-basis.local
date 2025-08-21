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
 * Класс отвечает за маркировку продукции Молоко
 */
class MarkedClassMilk extends MarkedClassCommon
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
        return '01'.$uit_parts[MarkingApi::USE_ID_GTIN].
               '21'.$uit_parts[MarkingApi::USE_ID_SERIAL].
               chr(29).mb_substr($uit_parts[MarkingApi::USE_ID_OTHER], 0, 6); //AI 93, Код проверки

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
            if (preg_match('/01(\d{14})21(.{6})(.{6,16})$/u', $code, $matches)) {
                return [
                    MarkingApi::USE_ID_GTIN => $matches[1] ? ltrim($matches[1], '0') : null,
                    MarkingApi::USE_ID_SERIAL => $matches[2] ?? null,
                    MarkingApi::USE_ID_OTHER => $matches[3] ?? null,
                    'n_code' => $matches[0],
                ];
            }
        }

        throw new MarkingException(t('Некорректный код'), MarkingException::ERROR_SINGLE_CODE_PARSE);
    }
}