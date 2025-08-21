<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;

use Catalog\Model\OneClickApi;
use Catalog\Model\Orm\OneClickItem;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;

/**
 * Метод API, создает заказ из покупки в 1 клик
 */
class CreateByOneClick extends AbstractAuthorizedMethod
{
    const RIGHT_CREATE = 1;
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
            self::RIGHT_CREATE => t('Создание заказа из покупки в 1 клик'),
        ];
    }

    /**
     * Создает заказ из покупки в 1 клик
     *
     * @param string $token Авторизационный токен
     * @param integer $one_click_id ID покупки в 1 клик
     *
     * @example POST /api/methods/order.createbyoneclick?token=311211047ab5474dd67ef88345313a6e479bf616&one_click_id=205
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true,
     *         "order_id": 1527
     *     }
     *  }
     * </pre>
     *
     * @return array Возвращает ID созданного заказа
     */
    public function process($token, $one_click_id)
    {
        $one_click = OneClickItem::loadByWhere([
            'id' => $one_click_id
        ]);

        if (!$one_click['id']) {
            throw new ApiException(t('Покупка в 1 клик не найдена'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $one_click_api = new OneClickApi();
        $order = $one_click_api->createOrderFromOneClick($one_click);

        return [
            'response' => [
                'success' => true,
                'order_id' => $order['id']
            ]
        ];
    }
}