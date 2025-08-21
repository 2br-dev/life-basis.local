<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Block;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\Orm\Brand;
use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Product;
use Catalog\Model\SearchLineApi;
use RS\Config\Loader as ConfigLoader;
use RS\Controller\Result\Standard as ResultStandard;
use RS\Controller\StandartBlock;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Orm\AbstractObject;
use RS\Orm\Type;

/**
 * Блок-контроллер Поиск по товарам
 */
class SearchLine extends StandartBlock
{
    const SORT_RELEVANT = 'relevant';

    protected static $controller_title = 'Поиск товаров по названию';
    protected static $controller_description = 'Отображает форму для поиска товаров по ключевым словам';

    protected $action_var = 'sldo';
    protected $default_params = [
        'searchLimit' => 5,
        'searchBrandLimit' => 1,
        'searchCategoryLimit' => 1,
        'hideAutoComplete' => 0,
        'indexTemplate' => 'blocks/searchline/searchform.tpl',
        'imageWidth' => 62,
        'imageHeight' => 62,
        'imageResizeType' => 'xy',
        'order_field' => self::SORT_RELEVANT,
        'order_direction' => 'asc',
        'showUnavailableProducts' => 0
    ];

    /** @var ProductApi $api */
    public $api;
    /** @var SearchLineApi $search_line_api */
    public $search_line_api;

    protected $term;
    protected $term_escaped;
    protected $shop_config;


    /**
     * Инициализация
     */
    public function init()
    {
        $this->api = new ProductApi();
        $this->search_line_api = new SearchLineApi();
        EventManager::fire('init.searchlineapi.' . $this->getUrlName(), $this);
    }

    /**
     * Возвращает параметры блока
     *
     * @return AbstractObject
     */
    public function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'imageWidth' => new Type\Integer([
                'description' => t('Ширина изображения в подсказках'),
                'maxLength' => 6
            ]),
            'imageHeight' => new Type\Integer([
                'description' => t('Высота изображения в подсказках'),
                'maxLength' => 6
            ]),
            'imageResizeType' => new Type\Varchar([
                'description' => t('Тип масштабирования изображения в подсказках'),
                'maxLength' => 4,
                'listFromArray' => [[
                    'xy' => 'xy',
                    'axy' => 'axy',
                    'cxy' => 'cxy',
                    'ctxy' => 'ctxy',
                ]]
            ]),
            'hideAutoComplete' => new Type\Integer([
                'description' => t('Отключить подсказку результатов поиска в выпадающем списке'),
                'checkboxView' => [1, 0]
            ]),
            'searchLimit' => new Type\Integer([
                'description' => t('Количество товаров в выпадающем списке')
            ]),
            'searchBrandLimit' => new Type\Integer([
                'description' => t('Количество брендов в выпадающем списке')
            ]),
            'searchCategoryLimit' => new Type\Integer([
                'description' => t('Количество категорий в выпадающем списке')
            ]),
            'showUnavailableProducts' => new Type\Integer([
                'description' => t('Показывать товары, даже если их нет в наличии'),
                'checkboxView' => [1, 0]
            ]),
            'order_field' => new Type\Varchar([
                'description' => t('Сортировка результатов среди товаров'),
                'listFromArray' => [[
                    self::SORT_RELEVANT => t('Не выбрано'),
                    'sortn' => t('Вес'),
                    'dateof' => t('Дата'),
                    'rating' => t('Рейтинг'),
                    'cost' => t('Цена'),
                    'title' => t('Название'),
                    'num' => t('По наличию'),
                ]]
            ]),
            'order_direction' => new Type\Varchar([
                'description' => t('Направление сортировки среди товаров'),
                'listFromArray' => [[
                    'asc' => t('по возрастанию'),
                    'desc' => t('по убыванию')
                ]]
            ])
        ]);
    }

    /**
     * Метод обработки отображения поисковой строки
     *
     * @return ResultStandard
     */
    public function actionIndex()
    {
        $query = trim($this->url->get('query', TYPE_STRING));
        if ($this->router->getCurrentRoute() && $this->router->getCurrentRoute()->getId() == 'catalog-front-listproducts' && !empty($query)) {
            $this->view->assign('query', $query);
        }
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }

    /**
     * Метод отработки запроса на поиск. Возвращает JSON ответ
     *
     * @return string
     * @throws RSException
     */
    public function actionAjaxSearchItems()
    {
        session_write_close();
        $this->term = trim($this->url->request('term', TYPE_STRING));
        $result_json = [];

        if (!empty($this->term)) {
            $this->term_escaped = preg_quote($this->term, '#');

            //Найдем подходящие товарам
            /** @var Product[] $list */
            $this->search_line_api->prepareSearchQueryProduct($this->term, $this, $this->getParam('order_field'), $this->getParam('order_direction'));
            $list = $this->search_line_api->getSearchQueryProductResults($this->getParam('searchLimit'));

            $this->shop_config = ConfigLoader::byModule('shop');
            if (!empty($list)) {
                foreach ($list as $product) {
                    if ($data = $this->getProductSection($product)) {
                        $result_json[] = $data;
                    }
                }

                //Секция все результаты товаров
                if ($this->search_line_api->getSearchQueryProductCount() > $this->getParam('searchLimit')) {
                    $result_json[] = [
                        'value' => "",
                        'label' => t("Показать все товары"),
                        'type' => 'search',
                        'url' => $this->router->getUrl('catalog-front-listproducts', ['query' => $this->term])
                    ];
                }
            }

            //Найдем бренды подходящие под запрос
            /** @var Brand[] $list */
            if ($this->getParam('searchBrandLimit')) {
                $list = $this->search_line_api->getSearchQueryBrandsResults($this->term, $this->getParam('searchBrandLimit'));
                if (!empty($list)) {
                    foreach ($list as $brand) {
                        if ($data = $this->getBrandSection($brand)) {
                            $result_json[] = $data;
                        }
                    }
                }
            }

            //Найдем категории подходящие под запрос
            /** @var Dir[] $list */
            if ($this->getParam('searchCategoryLimit')) {
                $list = $this->search_line_api->getSearchQueryCategoryResults($this->term, $this->getParam('searchCategoryLimit'));
                if (!empty($list)) {
                    foreach ($list as $dir) {
                        if ($data = $this->getCategorySection($dir)) {
                            $result_json[] = $data;
                        }
                    }
                }
            }
        }

        $this->app->headers->addHeader('content-type', 'application/json');
        return json_encode($result_json, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Возвращает JSON-сведения об одном товаре
     *
     * @param Product $product
     * @return array
     */
    protected function getProductSection(Product $product)
    {
        $price = ($this->shop_config && $this->shop_config->check_quantity && $product->getNum() <= 0) ? t('Нет в наличии') : $product->getCost() . ' ' . $product->getCurrency();
        if ($substitute_price_text = $product->getSubstitutePriceText()) {
            $price = $substitute_price_text;
        }

        $brand = $product->getBrand();
        $available = ($this->shop_config && $this->shop_config->check_quantity && $product->getNum() <= 0) ? 0 : 1;

        return [
            'value' => $product['title'],
            'label' => preg_replace("#($this->term_escaped)#iu", '<b>$1</b>', $product['title']),
            'barcode' => preg_replace("#($this->term_escaped)#iu", '<b>$1</b>', $product['barcode']),
            'brand' => $brand['title'],
            'image' => $product->getMainImage()->getUrl($this->getParam('imageWidth'), $this->getParam('imageHeight'), $this->getParam('imageResizeType')),
            'available' => $this->getParam('showUnavailableProducts') ? 1 : $available,
            'price' => $price,
            'type' => 'product',
            'url' => $product->getUrl()
        ];
    }

    /**
     * Возвращает JSON-сведения об одном бренде
     *
     * @param Brand $brand
     * @return array
     */
    protected function getBrandSection(Brand $brand)
    {
        return [
            'value' => $brand['title'],
            'label' => preg_replace("#($this->term_escaped)#iu", '<b>$1</b>', $brand['title']),
            'image' => $brand->getMainImage()->getUrl($this->getParam('imageWidth'), $this->getParam('imageHeight'), $this->getParam('imageResizeType')),
            'type' => 'brand',
            'url' => $brand->getUrl()
        ];
    }

    /**
     * Возвращает JSON-сведения об одной категории
     *
     * @param Dir $dir
     * @return array
     */
    protected function getCategorySection(Dir $dir)
    {
        return [
            'value' => $dir['name'],
            'label' => preg_replace("#($this->term_escaped)#iu", '<b>$1</b>', $dir['name']),
            'image' => $dir->getMainImage()->getUrl($this->getParam('imageWidth'), $this->getParam('imageHeight'), $this->getParam('imageResizeType')),
            'type' => 'category',
            'url' => $dir->getUrl()
        ];
    }
}
