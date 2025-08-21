<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Product;

use Catalog\Config\File;
use Catalog\Model\CostApi;
use Catalog\Model\CurrencyApi;
use Catalog\Model\Orm\Currency;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\Typecost;
use Catalog\Model\Orm\Xcost;
use Catalog\Model\Orm\Xstock;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use RS\Orm\Request;
use ExternalApi\Model\Exception as ApiException;
use RS\Site\Manager;

/**
 * Изменяет остатки товаров по артикулу
 */
class SetPrice extends AbstractAuthorizedMethod
{
    const RIGHT_SET_PRICE = 1;

    protected $errors;
    protected $updated_product_ids;
    protected $manual_costs = [];
    protected $auto_costs = [];
    protected $currencies = [];
    protected $site_id;
    protected $default_currency;
    protected $default_cost_id;

    function __construct()
    {
        parent::__construct();
        $this->site_id = Manager::getSiteId();
    }

    /**
     * Возвращает комментарии к кодам прав доступа
     *
     * @return [
     *     КОД => КОММЕНТАРИЙ,
     *     КОД => КОММЕНТАРИЙ,
     *     ...
     * ]
     */
    public function getRightTitles()
    {
        return [
            self::RIGHT_SET_PRICE => t('Установка цен')
        ];
    }

    /**
     * Обновляет массово цены у товаров и комплектаций
     *
     * @param string $token Авторизационный токен
     * @param array $data Данные для обновления заказа
     * <b>data[0][sku]</b> - Артикул товара 1
     * <b>data[0][price]</b> - Цена товара 1
     * <b>data[0][cost]</b> - (Опционально) ID цены товара 1. Если не задано, то будет использована цена по-умолчанию
     * <b>data[0][currency]</b> - (Опционально) Международный символьный идентификатор валюты товара 1. Если не задано, то будет использована валюта сайта по-умолчанию
     * ...
     *
     * @example Для использования данного метода API у всех комплектаций товаров и комплектаций должен быть уникальный Артикул.
     *
     * POST /api/methods/product.setPrice?token=b9574d1b793605e3ff86dcc6413efd4f330ee943
     *
     * JSON тело запроса:
     * {"data": [{"sku": 'A182743S', "price": 100.00, "cost": 1, "currency": "RUB"}, {"sku": "A663743S", "price": 200}]}
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true
     *     }
     * }
     * </pre>
     *
     *
     * Ответ, для случая, когда обновлены не все артикулы:
     *
     * <pre>
     * {
     *     "error": {
     *        "code": "wrong_param_value",
     *        "title": "Обновлены цены не всех товаров. Следующие артикулы не найдены: a0786812"
     *     }
     * }
     * </pre>
     *
     * @return array
     */
    function process($token, $data)
    {
        $config = File::config();
        if ($config['inventory_control_enable']) {
            throw new APIException(t('Включен складской учет, прямое изменение остатков невозможно'));
        }

        $this->runUpdatePrice($data);

        return [
            'response' => [
                'success' => true
            ]
        ];
    }

    /**
     * Обновляет остатки у товаров
     *
     * @return void
     */
    protected function runUpdatePrice($data)
    {
        $founded_products = $this->findOffersBySku($data);

        //Загружаем справочник цен
        $this->loadCostTypes();
        $this->loadCurrencies();

        //Обновляем остатки у комплектаций
        $this->updatePrice($data, $founded_products);

        //Очищаем кэш товаров
        $this->cleanProductsCache();

        if ($this->errors) {
            throw new APIException(implode(', ', $this->errors), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
    }

    /**
     * Очищает кэш товаров
     *
     * @return void
     */
    protected function cleanProductsCache()
    {
        if ($this->updated_product_ids) {
            Request::make()
                ->update(new Product)
                ->set([
                    'offers_json' => '',
                    'import_hash' => ''
                ])
                ->whereIn('id', $this->updated_product_ids)
                ->exec();
        }
    }

    /**
     * Загружает валюты, имеющиеся в системе
     *
     * @return void
     */
    protected function loadCurrencies()
    {
        $this->currencies = Request::make()
            ->from(new Currency())
            ->where([
                'site_id' => $this->site_id
            ])->objects(null, 'title');

        foreach($this->currencies as $currency) {
            if ($currency['default']) {
                $this->default_currency = $currency['title'];
            }
        }

        if ($this->default_currency === null) {
            throw new ApiException(t('Не найдена валюта по умолчанию в системе'), ApiException::ERROR_INSIDE);
        }
    }

    /**
     * Загружает тип цен, который может устанавливаться пользователем
     *
     * @return void
     */
    protected function loadCostTypes()
    {
        $costs = Request::make()
            ->from(new Typecost())
            ->where([
                'site_id' => $this->site_id,
            ])->objects();

        foreach($costs as $cost) {
            if ($cost['type'] == Typecost::TYPE_MANUAL) {
                $this->manual_costs[$cost['id']] = $cost;
            } else {
                $this->auto_costs[$cost['id']] = $cost;
            }
        }

        $this->default_cost_id = CostApi::getDefaultCostId();
        if (!$this->default_cost_id) {
            throw new ApiException(t('Не найден тип цен по умолчанию в системе'), ApiException::ERROR_INSIDE);
        }
    }

    /**
     * Пересчитывает суммарные остатки товаров
     *
     * @return void
     */
    protected function recalculateSummaryStock()
    {
        if ($this->updated_product_ids) {
            Request::make()
                ->update()
                ->from(new Offer(), 'O')
                ->set('O.num = (SELECT SUM(stock) FROM '.Xstock::_getTable().' S WHERE S.offer_id = O.id)')
                ->whereIn('O.product_id', $this->updated_product_ids)
                ->exec();

            //Пересчитываем общий остаток товара
            $sub_query = Request::make()
                ->select('SUM(num)')
                ->from(new Offer(), 'O')
                ->where('O.product_id = P.id');

            Request::make()
                ->update()
                ->from(new Product, 'P')
                ->set('P.num = ('.$sub_query.')')
                ->set([
                    'offers_json' => ''
                ])
                ->whereIn('P.id', $this->updated_product_ids)
                ->exec();
        }
    }

    /**
     * Обновляет остатки у комплектаций
     *
     * @param array $data
     * @param array $founded_products
     * @return void
     */
    protected function updatePrice($data, $founded_products)
    {
        $not_found_sku = [];
        $this->updated_product_ids = [];
        $n = 0;
        foreach($data as $item) {
            if (isset($item['sku'])) {
                if (isset($founded_products[(string)$item['sku']])) {
                    $founded_product = $founded_products[(string)$item['sku']];
                    if (isset($item['price'])) {

                        $cost_id = isset($item['cost']) ? (int)$item['cost'] : $this->default_cost_id;
                        if (!isset($this->manual_costs[$cost_id])) {
                            if (isset($this->auto_costs[$cost_id])) {
                                throw new ApiException(t('Тип цен ID:%0 для SKU:%1 является автоматическим, поэтому он не может быть перезаписан. Укажите другой тип цен в параметре cost.', [$cost_id, $item['sku']]), ApiException::ERROR_WRONG_PARAM_VALUE);
                            } else {
                                throw new ApiException(t('Не найден тип цен ID:%0 для SKU:%1.', [$cost_id, $item['sku']]), ApiException::ERROR_WRONG_PARAM_VALUE);
                            }
                        }

                        if (isset($founded_product['product_id'])) {
                            $this->updateProductPrice($founded_product, $item, $cost_id);
                        } else {
                            $this->updateOfferPrice($founded_product, $item, $cost_id);
                        }
                    } else {
                        $this->errors[] = t('У элемента SKU:%0 не найден ключ stock', [$item['sku']]);
                    }
                } else {
                    $not_found_sku[] = (string)$item['sku'];
                }
            } else {
                $this->errors[] = t('У элемента %0 не найден ключ sku', [$n]);
            }
            $n++;
        }

        if ($not_found_sku) {
            $this->errors[] = t('Обновлены цены не всех товаров. Следующие артикулы не найдены: %0', [implode(', ', $not_found_sku)]);
        }
    }

    /**
     * @return void
     */
    protected function updateProductPrice($founded_product, $item, $cost_id)
    {
        //Обновляем цену товару
        $xcost = new Xcost();
        $xcost['product_id'] = $founded_product['product_id'];
        $xcost['cost_id'] = $cost_id;
        $currency = (isset($item['currency']) && isset($this->currencies[$item['currency']])) ? $this->currencies[$item['currency']] : 0;

        if ($currency) {
            $xcost['cost_original_val'] = (float)$item['price'];;
            $xcost['cost_original_currency'] = $currency['id'];
            $xcost['cost_val'] = CurrencyApi::convertToBase((float)$item['price'], $currency);
        } else {
            $xcost['cost_original_val'] = (float)$item['price'];
            $xcost['cost_original_currency'] = 0;
            $xcost['cost_val'] = (float)$item['price'];
        }
        $xcost->replace();

        $this->updated_product_ids[] = $founded_product['product_id'];
    }

    /**
     * Обновляет цены
     *
     * @return void
     */
    protected function updateOfferPrice($founded_product, $item, $cost_id)
    {
        $offer_array = Request::make()
            ->from(Offer::_getTable())
            ->where([
                'id' => $founded_product['offer_id'],
            ])->exec()->fetchRow();

        if ($offer_array) {
            if (isset($item['currency']) && !isset($this->currencies[$item['currency']])) {
                throw new ApiException(t('Валюта с символьным идентификатором %0 не найдена', [$item['currency']]), ApiException::ERROR_WRONG_PARAM_VALUE);
            }

            $currency = $item['currency'] ?? $this->default_currency;

            $price_data_array = @unserialize($offer_array['pricedata']) ?: [];
            if (isset($item['cost'])) {
                //Устанавливаем разные конкретный тип цен
                unset($price_data_array['oneprice']);
                $price_data_array['price'][$cost_id] = [
                    'znak' => '=',
                    'original_value' => (float)$item['price'],
                    'unit' => $this->currencies[$currency]['id'],
                    'value' => CurrencyApi::convertToBase($item['price'], $this->currencies[$currency])
                ];
            } else {
                //Устанавливаем единый тип цен
                $price_data_array = [
                    'oneprice' => [
                        'use' => 1,
                        'znak' => '=',
                        'original_value' => (float)$item['price'],
                        'unit' => $this->currencies[$currency]['id'],
                        'value' => CurrencyApi::convertToBase($item['price'], $this->currencies[$currency])
                    ]
                ];
            }

            Request::make()
                ->update(Offer::_getTable())
                ->set(['pricedata' => serialize($price_data_array)])
                ->where(['id' => $founded_product['offer_id']])
                ->exec();

            $this->updated_product_ids[] = $offer_array['product_id'];
        }
    }

    /**
     * Находит комплектации и товары по артикулу
     *
     * @return array
     */
    protected function findOffersBySku($data)
    {
        $skus = [];
        foreach($data as $item) {
            if (isset($item['sku'])) {
                $skus[$item['sku']] = $item['sku'];
            }
        }
        if ($skus) {
            $offers_barcode = Request::make()
                ->select('barcode, id as offer_id')
                ->from(new Offer())
                ->where('sortn != 0')
                ->whereIn('barcode', $skus)
                ->exec()
                ->fetchSelected('barcode', null);

            $product_barcode = Request::make()
                ->select('P.barcode, P.id as product_id')
                ->from(new Product(), 'P')
                ->whereIn('P.barcode', $skus)
                ->exec()
                ->fetchSelected('barcode', null);

            return $offers_barcode + $product_barcode;
        }

        return [];
    }
}