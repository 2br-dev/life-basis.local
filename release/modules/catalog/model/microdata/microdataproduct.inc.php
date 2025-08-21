<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Microdata;

use Catalog\Model\Orm\Product;
use Comments\Model\Api as CommentApi;
use RS\Application\Microdata\AbstractMicrodataEntity;
use RS\Application\Microdata\InterfaceMicrodataSchemaOrgJsonLd;

/**
 * Микроразметка товара
 */
class MicrodataProduct extends AbstractMicrodataEntity implements InterfaceMicrodataSchemaOrgJsonLd
{
    /** @var Product */
    protected $product;
    /** @var CommentApi */
    protected $comment_api;
    protected $add_review_section = true;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Включает/выключает добавление секции отзывов
     *
     * @param $bool
     * @return void
     */
    public function reviewSectionEnable($bool)
    {
        $this->add_review_section = $bool;
    }

    /**
     * Возвращает данные для микроразметки Schema.org в формате JSON-LD
     *
     * @return array
     * @throws \Exception
     */
    function getSchemaOrgJsonLd(): array
    {
        $result = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $this->product['title'],
            'description' => $this->product['short_description'],
            'offers' => [],
        ];

        $brand = $this->product->getBrand();
        if ($brand['id']) {
            $result['brand'] = [
                '@type' => 'Brand',
                'name' => $brand['title'],
            ];
        }

        foreach ($this->product->getImages() as $image) {
            $result['image'][] = $image->getUrl(1500, 1500, 'xy', true);
        }

        if ($gtin = $this->product->getSKU()) {
            $result['gtin'] = $gtin;
        }

        $offer_data = [
            '@type' => 'Offer',
            'price' => $this->product->getCost(null, null, false),
            'priceCurrency' => $this->product->getCurrencyCode(),
            'itemCondition' => 'https://schema.org/NewCondition',
        ];

        if ($this->product->shouldReserve()) {
            $offer_data['availability'] = 'https://schema.org/PreOrder';
        } elseif ($this->product->getNum() > 0) {
            $offer_data['availability'] = 'https://schema.org/InStock';
        } else {
            $offer_data['availability'] = 'https://schema.org/OutOfStock';
        }

        $result['offers'] = $offer_data;

        if ($this->add_review_section && $this->product['comments'] > 0) {
            $comment_api = new CommentApi();
            $comment_api->setCommentTypeFilter(new \Catalog\Model\CommentType\Product())->setFilter('aid', $this->product->id);

            $best_rating = $this->product->getMaxBall();
            $aggregate_rating_data = [
                '@type' => 'AggregateRating',
                'ratingValue' => (int)$this->product->getRatingBall(),
                'reviewCount' => $this->product['comments'],
                'bestRating' => $best_rating
            ];

            $result['aggregateRating'] = $aggregate_rating_data;

            $review_data = [];
            $review_list = $comment_api->getList(1, 3);

            foreach ($review_list as $review) {
                $date = new \DateTime($review['dateof']);
                $formatted_date = $date->format('Y-m-d\TH:i:s');
                $review_data[] = [
                    '@type' => 'Review',
                    'author' => [
                        '@type' => 'Person',
                        'name' => $review['user_name']
                    ],
                    'datePublished' => $formatted_date,
                    'reviewBody' => $review['message'],
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => (int)$review['rate'],
                        'bestRating' => $best_rating
                    ],
                ];
            }

            if (!empty($review_data)) {
                $result['review'] = $review_data;
            }
        }

        return $result;
    }
}
