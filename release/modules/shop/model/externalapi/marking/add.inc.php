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
use RS\Exception;
use Shop\Model\ApiUtils;
use Shop\Model\Marking\MarkingApi;
use Shop\Model\Orm\OrderItem;

/**
 * Метод, добавляет маркировку к товарам заказа
 */
class Add extends AbstractAuthorizedMethod
{
    const RIGHT_ADD_MARKING = 'add_marking';

    public function getRightTitles()
    {
        return [
            self::RIGHT_ADD_MARKING => t('Добавить маркировку')
        ];
    }

    /**
     * Добавляет отсканированный код маркировки к заказу
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID заказа
     * @param string $datamatrix Полный код маркировки. Только печатные символы, без GS
     * @param string $order_item_uniq Идентификатор позиции в заказе
     *
     * @example POST /api/methods/marking.add?token=311211047ab5474dd67ef88345313a6e479bf616
     * Тело запроса: order_id=1519&order_item_uniq=e96c4ae104&datamatrix=010460780959150821sSBmxTYIFT(eq91FFD092testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true,
     *         "uit": {
     *             "id": "24",
     *             "gtin": "06607809591508",
     *             "serial": "sSBmxTYIFT(eq",
     *             "other": "91FFD092test"
     *         }
     *     }
     * }
     * </pre>
     *
     * @return array Возвращает данные по добавленной маркировке или ошибку
     */
    public function process($token, $order_id, $datamatrix, $order_item_uniq)
    {
        $datamatrix = htmlspecialchars_decode($datamatrix);

        $order_item = OrderItem::loadByWhere([
            'uniq' => $order_item_uniq,
            'order_id' => $order_id
        ]);

        if (!$order_item['uniq']) {
            throw new ApiException(t('Товар в заказе не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        if ($order_item['type'] != OrderItem::TYPE_PRODUCT) {
            throw new ApiException(t('Позиция в заказе имеет не товарный тип'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $product = $order_item->getEntity();
        if (!$product['id']) {
            throw new ApiException(t('Не найден товар'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        if (!$product['marked_class']) {
            throw new ApiException(t('Товар не подлежит маркировке'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $marking_classes = MarkingApi::getMarkedClasses();
        if (!isset($marking_classes[$product['marked_class']])) {
            throw new ApiException(t('Не найден класс маркировки, указанный у товара'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $marking_class = $marking_classes[$product['marked_class']];
        try {
            $uit = $marking_class->getUITFromCode(ApiUtils::prepareMobileDataMatrix($datamatrix));
            $uit['order_id'] = $order_id;
            $uit['order_item_uniq'] = $order_item_uniq;

            $exists_uits = $order_item->getUITs();
            foreach($exists_uits as $exists_uit) {
                if ($exists_uit->asString() == $uit->asString()) {
                    throw new ApiException(t('Код маркировки уже присутствует'), ApiException::ERROR_WRONG_PARAM_VALUE);
                }
            }

            $uit_limit = $product->isBulk() ? 1 : $order_item['amount'];
            if (count($exists_uits) >= $uit_limit) {
                throw new ApiException(t('Нельзя добавить маркировок больше, чем кол-во товара'), ApiException::ERROR_WRONG_PARAM_VALUE);
            }

            $uit->insert();

        } catch(Exception $e) {
            throw new ApiException($e->getMessage(), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $check_result = $uit->getCheckResult();

        $uit_data = Utils::extractOrm($uit);
        $uit_data['check_result'] = [
            'status' => $check_result->getCheckStatus(),
            'text' => $check_result->getCheckText(),
            'color' => $check_result->getCheckStatusColor()
        ];

        return [
            'response' => [
                'success' => true,
                'uit' => $uit_data
            ]
        ];
    }
}