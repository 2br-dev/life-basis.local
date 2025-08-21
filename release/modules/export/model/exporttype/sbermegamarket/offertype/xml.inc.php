<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType\Sbermegamarket\OfferType;

use Catalog\Model\Orm\WareHouse;
use Export\Model\ExportType\Yandex\OfferType\Simple;
use Export\Model\Orm\ExportProfile as ExportProfile;
use Catalog\Model\Orm\Product as Product;
use RS\Exception as RSException;

class XML extends Simple
{

    function getTitle()
    {
        return 'XML';
    }

    function getShortName()
    {
        return 'xml';
    }

    /**
     * Запись "Особенных" полей, для данного типа описания
     * Перегружается в потомке. По умолчанию выводит все поля в соответсвии с fieldmap
     *
     * @param ExportProfile $profile
     * @param \XMLWriter $writer
     * @param Product $product
     * @param mixed $offer_index
     * @throws RSException
     */
    public function writeEspecialOfferTags(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_index)
    {
        parent::writeEspecialOfferTags($profile, $writer, $product, $offer_index);

        $writer->startElement('outlets');

        $profile['consider_warehouses'] = $profile['consider_warehouses'] ?? [0];
        foreach($profile['consider_warehouses'] as $warehouse_id) {

            $offers = $product->getOffers();
            $offer = $offers[$offer_index] ?? false;
            if ($offer) {
                $stocks = $offer->getStocks();
            }
            $warehouse = $this->getWarehouse($warehouse_id);

            $writer->startElement('outlet');
            $writer->writeAttribute('id', $warehouse['sber_id']);
            $writer->writeAttribute('instock', $stocks[$warehouse_id]['stock'] ?? 0);
            $writer->endElement();
        }
        $writer->endElement();




    }

    /**
     * Возвращает склад по ID
     *
     * @param $id
     * @return Warehouse
     */
    protected function getWarehouse($id)
    {
        static $warehouses = [];

        if (!isset($warehouses[$id])) {
            $warehouses[$id] = new Warehouse($id);
        }

        return $warehouses[$id];
    }


}