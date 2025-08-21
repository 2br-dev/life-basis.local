<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\DeliveryOrder;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use RS\Exception;
use RS\Http\Request;
use RS\Router\Manager;
use Shop\Model\Orm\DeliveryOrder;
use Shop\Model\Orm\Order;

/**
 * Класс метода API для выполнения действий с заказом на доставку
 */
class DoAction extends AbstractAuthorizedMethod
{
    const RIGHT_RUN_ACTION = 1;

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
            self::RIGHT_RUN_ACTION => t('Выполнение действия с заказом на доставку')
        ];
    }

    /**
     * Выполняет действие с заказом на доставку
     * ---
     * Поддерживается выполнение действий расчетного класса, не требующих ввода дополнительных параметров,
     * т.е. действия с view_type = message или output
     *
     * @param string $token Авторизационный токен
     * @param integer $delivery_order_id ID Заказа на доставку
     * @param string $action Тип действия. Может принимать одно из следующих значений:
     * - refresh - обновить данные о заказе на доставку на сайте без изменений на стороне доставке
     * - change - обновить данные о заказе на доставку на стороне сервиса доставки
     * - delete - удалить заказ на доставку
     * Также параметр может принимать одно из значений, которые определяет расчетный класс доставки.
     * Список таких действий будет возвращет в методе deliveryOrder.get в поле actions.action
     * - ... другие
     *
     * @return array
     */
    public function process($token, $delivery_order_id, $action)
    {
        [$delivery_order, $delivery_type, $order] = Get::getDeliveryTypeByDeliveryOrderId($delivery_order_id);

        $result = [
            'response' => [
                'success' => true
            ]
        ];

        try {
            switch ($action) {
                case 'refresh':
                    if (!$delivery_type->canRefreshDeliveryOrder()) {
                        throw new ApiException(t('Обновление сведений по заказу на сайте не поддерживается'), ApiException::ERROR_WRONG_PARAM_VALUE);
                    }
                    $delivery_type->refreshDeliveryOrder($delivery_order);
                    break;

                case 'change':
                    if (!$delivery_type->canRefreshDeliveryOrder()) {
                        throw new ApiException(t('Обновление заказа на стороне доставке не поддерживается'), ApiException::ERROR_WRONG_PARAM_VALUE);
                    }
                    $delivery_type->changeDeliveryOrder($delivery_order, $order);
                    break;

                case 'delete':
                    if (!$delivery_type->canDeleteDeliveryOrder()) {
                        throw new ApiException(t('Удаление заказа на доставку не поддерживается'), ApiException::ERROR_WRONG_PARAM_VALUE);
                    }
                    $delivery_type->deleteDeliveryOrder($delivery_order);
                    break;

                default:
                    $actions = array_filter($delivery_order->getActions(), function ($item) use ($action) {
                        return (in_array(($item['view_type'] ?? ''), ['message', 'output'])) && $item['action'] == $action;
                    });

                    if (!count($actions)) {
                        throw new ApiException(t('Неизвестное действие с заказом на доставку'), ApiException::ERROR_WRONG_PARAM_VALUE);
                    }
                    $selected_action = reset($actions);

                    if ($selected_action['view_type'] == 'message') {
                        $http = new Request();
                        $http->addFromArray([
                            'delivery_order_id' => $delivery_order['id']
                        ], GET);
                        $action_result = $delivery_type->executeInterfaceDeliveryOrderAction($http, $order, $selected_action['action']);
                        $result['response'] += (array)$action_result;

                    } elseif ($selected_action['view_type'] == 'output') {
                        $result['response'] += [
                            'url' => $this->getPrintDocUrl($order, $delivery_order, $selected_action['action'])
                        ];
                    }

                    break;
            }
        } catch (Exception $e) {
            if ($e instanceof ApiException) {
                throw $e;
            } else {
                throw new ApiException($e->getMessage(), ApiException::ERROR_WRITE_ERROR);
            }
        }

        return $result;
    }

    /**
     * Возвращает сгенерированную ссылку на печать документа от сервиса доставки.
     * Данную ссылку можн ооткрыть без авторизации и просмотреть документ
     *
     * @param mixed $delivery_order
     * @param string $action
     * @return string
     */
    private function getPrintDocUrl(Order $order, DeliveryOrder $delivery_order, $action)
    {
        $params = [
            'order_hash' => $order['hash'],
            'doc_type' => $action,
            'delivery_order_id' => $delivery_order['id'],
        ];

        $params['sign'] = self::signParams($params);
        $params['Act'] = 'deliverydocs';

        return Manager::obj()->getUrl('shop-front-printdocs', $params, true);
    }

    /**
     * Возвращает подпись URL-адреса для выполнения действий с заказом на доставку
     *
     * @param array $params
     * @return string
     */
    public static function signParams($params)
    {
        return hash_hmac('sha1', json_encode(array_values($params)), \Setup::$SECRET_KEY. sha1(\Setup::$SECRET_SALT));
    }
}