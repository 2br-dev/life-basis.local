<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Marking;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Utils;
use Shop\Model\Cart;
use Shop\Model\Orm\Order;

/**
 * Возвращает все имеющиеся маркировки для товаров заказа.
 */
class GetList extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;

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
            self::RIGHT_LOAD => t('Загрузка маркировок товара')
        ];
    }

    /**
     * Возвращает коды маркировок, привязанные к заказу
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID заказа
     *
     * @example GET /api/methods/marking.getList?token=311211047ab5474dd67ef88345313a6e479bf616&order_id=1519
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "uits": {
     *             "e96c4ae104": [
     *                 "e96c4ae104": [
     *                  {
     *                      "id": "24",
     *                      "gtin": "06607809591508",
     *                      "serial": "sSBmxTYIFT(eq",
     *                      "other": "91FFD092test"
     *                  },
     *                 {
     *                      "id": "25",
     *                      "gtin": "09907809591509",
     *                      "serial": "YIFTAABsSBmxT",
     *                      "other": "01FFD092test"
     *                 }
     *             ]
     *         }
     *     }
     * }
     * </pre>
     *
     * @return array Возвращает список маркировок в разрезе товаров
     */
    public function process($token, $order_id)
    {
        $order = new Order($order_id);

        if (!$order['id']) {
            throw new ApiException(t('Заказ не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $result = [];
        $cart = $order->getCart();
        if ($cart) {
            $order_items = $cart->getCartItemsByType(Cart::TYPE_PRODUCT);
            foreach($order_items as $order_item) {
                foreach($order_item->getUITs() as $uit) {
                    $result[$order_item['uniq']][] = Utils::extractOrm($uit);
                }
            }
        }

        return [
            'response' => [
                'uits' => $result
            ]
        ];
    }
}