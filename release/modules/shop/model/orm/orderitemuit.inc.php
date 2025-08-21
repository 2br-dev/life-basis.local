<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use RS\Orm\AbstractObject;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;
use Shop\Model\Marking\MarkingApi;
use Shop\Model\Marking\MarkingException;
use Shop\Model\Marking\TrueApi\CheckCodes;
use Shop\Model\Marking\TrueApi\UitCheckResult;

/**
 * Позиция в корзине
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $order_id ID заказа
 * @property string $order_item_uniq ID позиции в рамках заказа
 * @property string $gtin Глобальный номер предмета торговли (GTIN)
 * @property string $serial Серийный номер
 * @property string $other Остальные данные datamatrix, после серийного номера
 * @property string $check_result Результат проверки в системе Честный знак
 * --\--
 */
class OrderItemUIT extends OrmObject
{
    protected static $table = 'order_item_uit';

    function _init()
    {
        parent::_init()->append([
            'order_id' => (new Type\Integer())
                ->setDescription(t('ID заказа'))
                ->setVisible(false, 'app'),
            'order_item_uniq' => (new Type\Varchar())
                ->setDescription(t('ID позиции в рамках заказа'))
                ->setVisible(false, 'app'),
            'gtin' => (new Type\Varchar())
                ->setDescription(t('Глобальный номер предмета торговли (GTIN)'))
                ->setMaxLength(14),
            'serial' => (new Type\Varchar())
                ->setDescription(t('Серийный номер'))
                ->setMaxLength(100),
            'other' => (new Type\Varchar())
                ->setDescription(t('Остальные данные datamatrix, после серийного номера')),
            'check_result' => (new Type\Text())
                ->setVisible(false)
                ->setDescription(t('Результат проверки в системе Честный знак'))
        ]);
    }

    /**
     * Возвращает объект УИТ на основе массива данных
     *
     * @param string[] $data - данные УИТ
     * @return self
     * @throws MarkingException
     */
    public static function loadFromData(array $data)
    {
        $object = new self();
        $object['gtin'] = $data[MarkingApi::USE_ID_GTIN];
        $object['serial'] = $data[MarkingApi::USE_ID_SERIAL];
        $object['other'] = $data[MarkingApi::USE_ID_OTHER];
        $object->selfCheck();

        // Проверяем код в системе Честный знак, если включена соответствующая опция
        if (CheckCodes::isCheckCodesEnabled()) {
            $check_codes = new CheckCodes();
            $check_result = $check_codes->checkUit($object);
            $object['check_result'] = $check_result->exportJSON();
        }

        return $object;
    }

    /**
     * Возвращает данные УИТ в виде массива
     *
     * @return string[]
     */
    public function asArray()
    {
        $gtin = str_pad($this['gtin'], 14, '0', STR_PAD_LEFT);
        $serial = htmlspecialchars_decode($this['serial'], 3);
        $other = htmlspecialchars_decode($this['other'], 3);

        return [
            MarkingApi::USE_ID_GTIN => $gtin,
            MarkingApi::USE_ID_SERIAL => $serial,
            MarkingApi::USE_ID_OTHER => $other
        ];
    }

    /**
     * Проверяет корректность данных кода
     *
     * @return void
     * @throws MarkingException
     */
    protected function selfCheck(): void
    {
        if (empty($this['gtin'])) {
            throw new MarkingException(t('Код не содержит "%0"', [MarkingApi::handbookUseIdTitleStr(MarkingApi::USE_ID_GTIN)]), MarkingException::ERROR_SINGLE_CODE_PARSE);
        } elseif (strlen($this['gtin']) < 8 || strlen($this['gtin']) > 14 || !is_numeric($this['gtin'])) {
            throw new MarkingException(t('Код содержит некорректный "%0" (%1)', [MarkingApi::handbookUseIdTitleStr(MarkingApi::USE_ID_GTIN), $this['gtin']]), MarkingException::ERROR_SINGLE_CODE_PARSE);
        }

        if (empty($this['serial'])) {
            throw new MarkingException(t('Код не содержит "%0"', [MarkingApi::handbookUseIdTitleStr(MarkingApi::USE_ID_SERIAL)]), MarkingException::ERROR_SINGLE_CODE_PARSE);
        } elseif (strlen($this['serial']) < 6 || strlen($this['serial']) > 20) {
            throw new MarkingException(t('Код содержит некорректный "%0" (%1)', [MarkingApi::handbookUseIdTitleStr(MarkingApi::USE_ID_SERIAL), $this['serial']]), MarkingException::ERROR_SINGLE_CODE_PARSE);
        }
    }

    /**
     * Возвращает идентификатор маркировки в формате, требуемом для кассовых чеков
     *
     * @return string
     */
    public function asString()
    {
        if (!$this['gtin'] || !$this['serial']) return '';

        $uit_parts = $this->asArray();
        return '01'.$uit_parts[MarkingApi::USE_ID_GTIN].'21'.$uit_parts[MarkingApi::USE_ID_SERIAL].$uit_parts[MarkingApi::USE_ID_OTHER];
    }

    /**
     * Возвращает идентификатор маркировки в исходном формате, вместе со спец. символами <GS>
     *
     * @return string
     */
    public function asStringWithGS()
    {
        if (!$this['gtin'] || !$this['serial']) return '';

        $uit_parts = $this->asArray();
        $code = '01'.$uit_parts[MarkingApi::USE_ID_GTIN].'21'.$uit_parts[MarkingApi::USE_ID_SERIAL];

        //DataMatrix коды сканируются без спец.символов <GS>, поэтому приходится их добавлять
        //Согласно спецификациям Честного знака, <GS> есть сразу после серийного номера и после Кода проверки,
        //который имеет длину 6 символов и идет следом за серийным номером
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
     * Возвращает полный идентификатор маркировки DataMatrix в формате base64
     *
     * @return string
     */
    public function asBase64()
    {
        return base64_encode($this->asString());
    }

    /**
     * Возвращает идентификатор маркировки в Hex
     *
     * @return string
     */
    public function asHex()
    {
        if (!$this['gtin'] || !$this['serial']) return '';

        //Пример логики формирования: https://clck.ru/Z4qaN
        $mark_type = '444d'; //Зарезервировано

        //Переводим в hex
        $gtin_hex = dechex($this['gtin']);
        $serial_hex = bin2hex($this['serial']);

        //Дополняем байты нулями
        if (strlen($gtin_hex) % 2 != 0) {
            $gtin_hex = '0'.$gtin_hex;
        }
        if (strlen($serial_hex) % 2 != 0) {
            $serial_hex = '0'.$serial_hex;
        }

        //Рассчитываем длину
        $bytes = strlen($mark_type.$gtin_hex. $serial_hex)/2;
        $bytes_length = sprintf ('%04s', dechex($bytes));
        $bytes_length_reverse = $bytes_length[2].$bytes_length[3].$bytes_length[0].$bytes_length[1];

        return strtoupper($bytes_length_reverse.$mark_type.$gtin_hex.$serial_hex);
    }

    /**
     * Возвращает true, если маркировка уже присутствует в документе "отгрузка"
     *
     * @return bool
     */
    function isInShipment()
    {
        return Request::make()
            ->from(new ShipmentItem())
            ->where([
                'uit_id' => $this['id']
            ])->count() > 0;
    }

    /**
     * Возвращает объект результата проверки маркировки в честном знаке
     *
     * @return UitCheckResult
     */
    function getCheckResult()
    {
        return UitCheckResult::makeFromJSON($this, (string)$this['check_result']);
    }
}
