<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Microdata;

use Exception;
use RS\Application\Microdata\AbstractMicrodataEntity;
use RS\Application\Microdata\InterfaceMicrodataSchemaOrgJsonLd;

/**
 * Микроразметка списка товаров на странице каталог товаров
 */
class MicrodataProductList extends AbstractMicrodataEntity implements InterfaceMicrodataSchemaOrgJsonLd
{
    protected array $product_list;

    public function __construct(array $product_list)
    {
        $this->product_list = $product_list;
    }

    /**
     * Возвращает данные для микроразметки Schema.org в формате JSON-LD
     *
     * @return array
     * @throws Exception
     */
    function getSchemaOrgJsonLd(): array
    {
        $result = [
            '@context' => 'https://schema.org/',
            '@type' => 'ItemList',
        ];

        $product_list_data = [];

        foreach ($this->product_list as $product) {
            $microdata_product = new MicrodataProduct($product);
            $microdata_product->reviewSectionEnable(false);
            $product_list_data[] = $microdata_product->getSchemaOrgJsonLd();
        }

        $result['itemListElement'] = $product_list_data;

        return $result;
    }
}
