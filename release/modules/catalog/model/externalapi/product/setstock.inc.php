<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\Product;

use Catalog\Config\File;
use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\WareHouse;
use Catalog\Model\Orm\Xstock;
use Catalog\Model\WareHouseApi;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use RS\Orm\Request;
use ExternalApi\Model\Exception as ApiException;
use RS\Site\Manager;

/**
 * Изменяет остатки товаров по артикулу
 */
class SetStock extends AbstractAuthorizedMethod
{
    const RIGHT_SET_STOCK = 1;

    protected $errors;
    protected $updated_product_ids;
    protected $site_id;
    protected $warehouses_id;

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
            self::RIGHT_SET_STOCK => t('Установка остатков')
        ];
    }

    /**
     * Обновляет массово остатки у товаров
     *
     * @param string $token Авторизационный токен
     * @param array $data Данные для обновления заказа. Можно передавать в JSON теле POST запроса.
     *
     * <b>data[0][sku]</b> - Артикул товара 1
     * <b>data[0][stock]</b> - Остаток товара 1
     * <b>data[0][warehouse]</b> - (Опционально) ID склада товара 1. Если не задано, то будет использован склад по умолчанию.
     * <b>data[1][sku]</b> - Артикул товара 2
     * <b>data[1][stock]</b> - Остаток товара 2
     * <b>data[1][warehouse]</b> - (Опционально) ID склада товара 2. Если не задано, то будет использован склад по умолчанию.
     * ...
     *
     * @example Для использования данного метода API у всех комплектаций товаров должен быть уникальный Артикул.
     *
     * POST /api/methods/product.setStock?token=b9574d1b793605e3ff86dcc6413efd4f330ee943
     *
     * JSON тело запроса:
     * {"data": [{"sku": 'A182743S', "stock": 10, "warehouse": 1}, {"sku": "A663743S", "stock": 15, "warehouse": 2}]}
     *
     * Успешный ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true
     *     }
     * }
     * </pre>
     *
     * Ответ, для случая, когда обновлены не все артикулы:
     *
     * <pre>
     * {
     *     "error": {
     *        "code": "wrong_param_value",
     *        "title": "Обновлены не все товары. Следующие артикулы не найдены: a0786812"
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

        $this->runUpdateStock($data);

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
    protected function runUpdateStock($data)
    {
        $founded_products = $this->findOffersBySku($data);

        $this->loadWarehouses();

        //Обновляем остатки у комплектаций
        $this->updateOfferStock($data, $founded_products);

        //Пересчитываем суммарные остатки обновленных товаров и очищаем кэш комплектаций
        $this->recalculateSummaryStock();

        if ($this->errors) {
            throw new APIException(implode(', ', $this->errors), ApiException::ERROR_WRONG_PARAM_VALUE);
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
                    'offers_json' => '',
                    'import_hash' => '',
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
    protected function updateOfferStock($data, $founded_products)
    {
        $default_warehouse_id = WareHouseApi::getDefaultWareHouse()->id;

        if (!$default_warehouse_id) {
            throw new ApiException(t('Хотя бы один склад в системе должен иметь флаг "по умолчанию"'), ApiException::ERROR_INSIDE);
        }

        $not_found_sku = [];
        $this->updated_product_ids = [];
        $n = 0;
        foreach($data as $item) {
            if (isset($item['sku'])) {
                if (isset($founded_products[(string)$item['sku']])) {
                    $founded_product = $founded_products[(string)$item['sku']];
                    if (isset($item['stock'])) {
                        $warehouse_id = isset($item['warehouse']) ? (int)$item['warehouse'] : $default_warehouse_id;
                        if (!isset($this->warehouses_id[$warehouse_id])) {
                            throw new ApiException(t('Склад ID:%0 не найден у артикула SKU:%1', [$warehouse_id, $item['sku']]), ApiException::ERROR_INSIDE);
                        }

                        $xstock = new Xstock();
                        $xstock['product_id'] = $founded_product['product_id'];
                        $xstock['offer_id'] = $founded_product['offer_id'];
                        $xstock['warehouse_id'] = $warehouse_id;
                        $xstock['stock'] = $item['stock'];
                        $xstock->replace();

                        $this->updated_product_ids[] = $founded_product['product_id'];
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
            $this->errors[] = t('Обновлены не все товары. Следующие артикулы не найдены: %0', [implode(', ', $not_found_sku)]);
        }
    }

    /**
     * Находит комплектации по артикулу
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
                ->select('barcode, id as offer_id, product_id')
                ->from(new Offer())
                ->where([
                    'site_id' => $this->site_id
                ])
                ->where('sortn != 0')
                ->whereIn('barcode', $skus)
                ->exec()
                ->fetchSelected('barcode', null);

            $product_barcode = Request::make()
                ->select('P.barcode, O.id as offer_id, P.id as product_id')
                ->from(new Product(), 'P')
                ->join(new Offer(), 'O.product_id = P.id AND O.sortn=0', 'O')
                ->where([
                    'P.site_id' => $this->site_id
                ])
                ->whereIn('P.barcode', $skus)
                ->exec()
                ->fetchSelected('barcode', null);

            return $offers_barcode + $product_barcode;
        }

        return [];
    }

    /**
     * Загружает список имеющихся ID складов
     *
     * @return void
     */
    protected function loadWarehouses()
    {
        $this->warehouses_id = Request::make()
            ->select('id')
            ->from(new WareHouse())
            ->where([
                'site_id' => $this->site_id
            ])->exec()->fetchSelected('id', 'id');
    }
}