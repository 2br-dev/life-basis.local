<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;

/**
* Статистический отчет по статусам заказов
*/
class SellOrderStatus extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;

    function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Загрузка статистики')
        ];
    }
    
    /**
    * Возвращает статистический отчет по статусам заказов
    * 
    * @param string $token Авторизационный токен
    * 
    * @return array Возвращает данные для построения графика
    *@example GET /api/methods/order.sellOrderStatus?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486
    * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "statistic": [
     *              {
     *                  "label": "Выбран метод оплаты",
     *                  "data": 1,
     *                  "color": "#4d76ad"
     *              },
     *              {
     *                  "label": "Ожидание чека",
     *                  "data": 1,
     *                  "color": "#808000"
     *              },
     *              {
     *                  "label": "В обработке",
     *                  "data": 1,
     *                  "color": "#f2aa17"
     *              },
     *              {
     *                  "label": "Отменен",
     *                  "data": 1,
     *                  "color": "#ef533a"
     *              },
     *              {
     *                  "label": "Выполнен и закрыт",
     *                  "data": 28,
     *                  "color": "#5f8456"
     *              },
     *              {
     *                  "label": "Ожидает оплату",
     *                  "data": 23,
     *                  "color": "#00f541"
     *              }
     *          ]
    *     }
    * }
    * </pre>
    *
    */
    function process($token)
    {
        $order_api = new \Shop\Model\OrderApi();
        $data = array_values($order_api->getSellStatuses());
        
        return [
            'response' => [
                'statistic' => $data
            ]
        ];
    }
}
