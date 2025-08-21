<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Cart;

use ExternalApi\Model\AbstractMethods\AbstractMethod;
use RS\Exception as RSException;
use RS\Orm\Exception as OrmException;
use Shop\Model\ApiUtils;
use Shop\Model\Cart;

/**
* Изменяет кол-во товара в корзине
*/
class ChangeAmount extends AbstractMethod
{
    /**
     * Изменяет колличество товара в корзине и возвращает корзину пользователя со всеми сведениями
     *
     * @param string $id id
     * @param float $amount количество товара
     * @param null $token авторизационный token
     * @return array Возвращает список со сведения об элементах в корзине
     * @throws RSException
     * @throws \RS\Db\Exception
     * @example GET /api/methods/cart.changeAmount?id=1&amount=5
     * Ответ:
     * <pre>
     * "response": {
     *      "cartdata": {
     *          "total": "500 р",
     *          "total_base": "500 р",
     *          "total_discount": "500 р",
     *          "items": [
     *              {
     *                  "id": "696s8zm404",
     *                  "cost": "500 р",
     *                  "base_cost": "1 000 р",
     *                  "amount": 5,
     *                  "single_cost": "200 р",
     *                  "single_weight": 0.1,
     *                  "discount": "500 р",
     *                  "sub_products": [],
     *                  "discount_unformated": 500,
     *                  "title": "Товар",
     *                  "image": {},
     *                  "entity_id": "1",
     *                  "amount_error": "",
     *                  "amount_step": 1,
     *                  "offer": 14514,
     *                  "model": null,
     *                  "multioffers": null,
     *                  "multioffers_string": "",
     *                  "unit": "шт."
     *              }
     *          ],
     *          "items_count": 1,
     *          "total_weight": 0.5,
     *          "checkcount": 1,
     *          "currency": "р",
     *          "errors": [
     *              "Минимальная стоимость заказа должна составлять 1 000 р"
     *          ],
     *          "has_error": true,
     *          "total_base_without_discount": 1000,
     *          "taxes": [],
     *          "total_without_delivery": "500 р",
     *          "total_without_delivery_unformatted": 500,
     *          "total_without_payment_commission": "500 р",
     *          "total_without_payment_commission_unformatted": 500,
     *          "total_base_unformatted": 500,
     *          "total_unformatted": 500,
     *          "total_discount_unformatted": 500,
     *          "min_order_amount": "1 000 р",
     *          "min_order_amount_unformatted": 1000,
     *          "min_order_amount_left_unformatted": 500,
     *          "min_order_amount_left": "500 р",
     *          "coupons": []
     *          }
     *      }
     * </pre>
     */
    protected function process($id, $amount, $token = null)
    {
        $cart = Cart::currentCart();

        $products = $cart->getProductItems();
        $new_items = [];

        foreach ($products as $uniq => $item) {
            if ($item['cartitem']['entity_id'] == $id) {
                if ($amount == 0) {
                    $cart->removeItem($uniq);
                } else {
                    $products[$uniq]['cartitem']['amount'] = $amount;
                    $new_items[$uniq] = $products[$uniq]['cartitem'];
                }
            }
        }
        if ($amount != 0) {
            $cart->update($new_items);
        }

        $response['response']['cartdata'] = ApiUtils::fillProductItemsData();

        return $response;
    }
}
