<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;

use Catalog\Model\Orm\Product;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Validator\ValidateArray;
use ExternalApi\Model\Exception as ApiException;
use Shop\Model\Cart;
use Shop\Model\Orm\AbstractCartItem;
use Shop\Model\Orm\Discount;
use Shop\Model\Orm\OrderItem;

/**
 * Метод API, возвращает данные OrderItem для нового элемента в составе заказа
 */
class AddOrderItem extends AbstractAuthorizedMethod
{
    const RIGHT_ADD = 1;

    private $validator_product;
    private $validator_coupon;
    private $validator_order_discount;

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
            self::RIGHT_ADD => t('Добавление элемента в состав заказа'),
        ];
    }

    /**
     * Возвращает допустимую структуру значений в переменной data, в которой будут содержаться сведения для обновления
     *
     * @return ValidateArray
     */
    public function getCouponDataValidator()
    {
        if ($this->validator_coupon === null) {
            $this->validator_coupon = new ValidateArray([
                'code' => [
                    '@title' => t('Код купона'),
                    '@type' => 'string',
                    '@require' => true,
                ],
            ]);
        }

        return $this->validator_coupon;
    }

    /**
     * Возвращает допустимую структуру значений в переменной data, в которой будут содержаться сведения для обновления
     *
     * @return ValidateArray
     */
    public function getOrderDiscountDataValidator()
    {
        if ($this->validator_order_discount === null) {
            $this->validator_order_discount = new ValidateArray([
                'discount' => [
                    '@title' => t('Скидка в базовой валюте'),
                    '@type' => 'float',
                    '@require' => true,
                ],
            ]);
        }

        return $this->validator_order_discount;
    }

    /**
     * Возвращает допустимую структуру значений в переменной data, в которой будут содержаться сведения для обновления
     *
     * @return ValidateArray
     */
    public function getProductDataValidator()
    {
        if ($this->validator_product === null) {
            $this->validator_product = new ValidateArray([
                'entity_id' => [
                    '@title' => t('ID товара'),
                    '@type' => 'integer',
                    '@require' => true,
                ],
                'offer' => [
                    '@title' => t('ID комплектации'),
                    '@type' => 'integer',
                ],
                'multioffers_values' => [
                    '@title' => t('Многомерные комплектации. В ключе - ID хар-ки, в значении - текстовое значение'),
                    '@type' => 'array',
                ],
                'amount' => [
                    '@title' => t('Количество'),
                    '@type' => 'float',
                    '@require' => true,
                ],
                'cost_id' => [
                    '@title' => t('ID типа цены (необязательно, если передан single_cost)'),
                    '@type' => 'integer',
                ],
                'single_cost' => [
                    '@title' => t('Цена'),
                    '@type' => 'float',
                ],
                'uniq' => [
                    '@title' => t('Уникальный идентификатор'),
                    '@type' => 'string',
                ],
            ]);
        }
        return $this->validator_product;
    }

    /**
     * Форматирует комментарий, полученный из PHPDoc
     *
     * @param string $text - комментарий
     * @return string
     */
    protected function prepareDocComment($text, $lang)
    {
        $text = parent::prepareDocComment($text, $lang);

        $validator_product = $this->getProductDataValidator();
        $validator_coupon = $this->getCouponDataValidator();
        $validator_order_discount = $this->getOrderDiscountDataValidator();

        $text = preg_replace_callback('/\#data-product-info/', function() use($validator_product) {
            return $validator_product->getParamInfoHtml();
        }, $text);

        $text = preg_replace_callback('/\#data-coupon-info/', function() use($validator_coupon) {
            return $validator_coupon->getParamInfoHtml();
        }, $text);

        $text = preg_replace_callback('/\#data-order-discount-info/', function() use($validator_order_discount) {
            return $validator_order_discount->getParamInfoHtml();
        }, $text);

        return $text;
    }

    /**
     * Возвращает подготовленные данные для добавления нового элемента в состав заказа
     * ---
     * Новым элементом может быть товар, купон на скидку, скидка на заказ. Полученные с помощью этого метода данные необходимо
     * добавить в массив order.items и вызвать order.save, чтобы получить сведения
     *
     * @param string $token Авторизационный токен
     * @param string $type Тип добавляемого элемента. Может иметь значения: product, coupon, order_discount
     * @param array $data Данные добавляемого элемента
     * Для товара:
     * #data-product-info
     * Для купона на скидку:
     * #data-coupon-info
     * Для скидки на заказ:
     * #data-order-discount-info
     *
     * @return array
     *
     * @example api/methods/order.addOrderItem?token=311211047ab5474dd67ef88345313a6e479bf616
     *
     * Тело запроса в формате JSON
     * <pre>
     * {
          "type": "product",
          "data":{
            "entity_id":76506,
            "offer": 12405,
            "multioffers": [
              {"506":"Белый"},
              {"705": "XL"}
            ],
            "amount": 1,
            "cost_id": 2
          }
        }
     * </pre>
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "order_item": {
                    "uniq": "gow5figtkh",
                    "type": "product",
                    "entity_id": 76506,
                    "offer": 12405,
                    "multioffers_values": [
                        {
                            "506": "Белый"
                        },
                        {
                            "705": "XL"
                        }
                    ],
                    "amount": 1,
                    "cost_id": 2
                }
            }
        }
     * </pre>
     */
    function process($token, $type, $data)
    {
        $types = [AbstractCartItem::TYPE_PRODUCT,
            AbstractCartItem::TYPE_COUPON,
            OrderItem::TYPE_ORDER_DISCOUNT];

        if (!in_array($type, $types)) {
            throw new ApiException(t('Параметр type должен иметь значения product, coupon или order_discount'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $order_item = [];
        $order_item['uniq'] = $data['uniq'] ?? Cart::generateId();
        $order_item['type'] = $type;

        switch($type) {
            case AbstractCartItem::TYPE_PRODUCT:
                $this->getProductDataValidator()->validate('data', $data, $this->method_params);
                if (empty($data['cost_id']) && !isset($data['single_cost'])) {
                    throw new ApiException(t('Укажите тип цены или задайте цену вручную'), ApiException::ERROR_WRONG_PARAM_VALUE);
                }

                $product = new Product($data['entity_id']);
                if (!$product['id']) {
                    throw new ApiException(t('Товар с ID %0 не найден', [$data['entity_id']]), ApiException::ERROR_WRONG_PARAM_VALUE);
                }

                $order_item += $data;
                $order_item['title'] = $product['title'];
                break;

            case AbstractCartItem::TYPE_COUPON:
                $this->getCouponDataValidator()->validate('data', $data, $this->method_params);

                $coupon = Discount::loadByWhere([
                    'code' => $data['code']
                ]);

                if (!$coupon['id']) {
                    throw new ApiException(t('Купон с таким кодом не найден'), ApiException::ERROR_WRONG_PARAM_VALUE);
                }

                $check_result = $coupon->isActive();
                if ($check_result !== true) {
                    throw new ApiException($check_result, ApiException::ERROR_WRONG_PARAM_VALUE);
                }

                $order_item['code'] = $data['code'];
                $order_item['amount'] = 1;
                $order_item['title'] = t('Купон на скидку %0', [$data['code']]);
                break;

            case OrderItem::TYPE_ORDER_DISCOUNT:
                $this->getOrderDiscountDataValidator()->validate('data', $data, $this->method_params);
                $order_item['discount'] = (float)$data['discount'];
                $order_item['price'] = $order_item['discount'];
                $order_item['amount'] = 1;
                $order_item['title'] = t('Скидка на заказ %0', [$order_item['discount']]);
                break;
        }

        return [
            'response' => [
                'order_item' => $order_item
            ]
        ];
    }
}