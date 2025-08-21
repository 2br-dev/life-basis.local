<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType\AliExpress\OfferType;

use Catalog\Model\CostApi;
use Catalog\Model\Orm\Product;
use Export\Model\ExportType\Yandex\OfferType\Simple;
use Export\Model\Orm\ExportProfile;
use RS\Exception as RSException;

class Yml extends Simple
{
    /**
     * Возвращает название типа описания
     *
     * @return string
     */
    function getTitle()
    {
        return 'YML';
    }

    /**
     * Возвращает идентификатор данного типа описания. (только англ. буквы)
     *
     * @return string
     */
    public function getShortName()
    {
        return 'yml';
    }

    /**
     * Запись "Особенных" полей, для данного типа описания
     * По умолчанию выводит все поля в соответствии с fieldmap
     *
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @param Product $product
     * @param mixed $offer_index
     * @throws RSException
     */
    function writeEspecialOfferTags(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        parent::writeEspecialOfferTags($profile, $writer, $product, $offer_index);

        //AliExpress принимает только целочисленный остаток
        $writer->writeElement('quantity', (int)$product->getNum($offer_index));

        $writer->writeElement('length', $product->getDimensionsObject()->getLength());
        $writer->writeElement('width', $product->getDimensionsObject()->getWidth());
        $writer->writeElement('height', $product->getDimensionsObject()->getHeight());

        $writer->writeElement('barcode', $product->getSKU($this->getOfferId($product, $offer_index))); // Штрих-код

        $prices = $product->getOfferCost($offer_index, $product['xcost']);
        $cost_id = (!empty($profile['export_cost_id'])) ? $profile['export_cost_id'] : Costapi::getDefaultCostId();
        if ($old_cost_id = CostApi::getOldCostId($cost_id)) {
            $old_price = $prices[$old_cost_id];
            $writer->writeElement('old_price', $old_price);
        }

        $writer->writeElement('sku_code', $product->getBarCode($this->getOfferId($product, $offer_index)));
    }

    /**
     * Возвращает ID для тега offer
     *
     * @param Product $product
     * @param Integer $offer_id
     * @return string
     */
    protected function getOfferId($product, $offer_id)
    {
        if (!$offer_id) {
            $id = $product->getMainOffer()->id;
        } else{
            $id = $offer_id;
        }
        return $id;
    }
}
