<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Microdata;

use Catalog\Model\Orm\Dir;
use RS\Application\Microdata\AbstractMicrodataEntity;
use RS\Application\Microdata\InterfaceMicrodataSchemaOrgJsonLd;

/**
 * Микроразметка категории
 */
class MicrodataCatalog extends AbstractMicrodataEntity implements InterfaceMicrodataSchemaOrgJsonLd
{
    /** @var Dir */
    protected $dir;
    protected $offer_data;

    /**
     * @param Dir $dir
     * @param array $offer_data - массив, содержащий данные о ценах (максимальная и минимальная цены, валюта по умолчанию на сайте, количество товаров в категории)
     */
    public function __construct(Dir $dir, array $offer_data)
    {
        $this->dir = $dir;
        $this->offer_data = $offer_data;
    }

    /**
     * Возвращает данные для микроразметки Schema.org в формате JSON-LD
     *
     * @return array
     */
    function getSchemaOrgJsonLd(): array
    {
        $result = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $this->dir['name'],
            'offers' => [],
        ];

        $offer_data = [
            '@type' => 'AggregateOffer',
            'highPrice' => (int)$this->offer_data['interval_to'] ?? 0,
            'lowPrice' => (int)$this->offer_data['interval_from'] ?? 0,
            'priceCurrency' => $this->offer_data['currency'] ?? '',
            'offerCount' => (int)$this->offer_data['offer_count'] ?? 0,
        ];

        $result['offers'] = $offer_data;

        return $result;
    }
}
