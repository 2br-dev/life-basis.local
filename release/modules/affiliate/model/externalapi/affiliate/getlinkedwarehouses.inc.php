<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Affiliate\Model\ExternalApi\Affiliate;

use Affiliate\Model\Orm\Affiliate;
use Catalog\Model\WareHouseApi;
use ExternalApi\Model\Utils;

/**
 * Возвращает список складов, связанных с филиалом
 */
class GetLinkedWarehouses extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    /**
     * Возвращает список складок, связанных с филиалом
     *
     * @param integer $affiliate_id ID филиала
     * @param null $token Авторизационный токен
     *
     * @example GET /api/methods/affiliate.getLinkedWarehouses?affiliate_id=1
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "lists": {
     *           {
     *              "id": "1",
     *              "title": "Склад",
     *              "work_time": "Пн-Вс с 09:00 до 21:00",
     *              },
     *              ...
     *         }
     *     }
     * }
     * </pre>
     *
     * @return array Возвращает массив складов
     */
    protected function process($affiliate_id, $token = null)
    {
        $response = [];
        if ($affiliate_id) {
            $affiliate = new Affiliate($affiliate_id);
            if ($affiliate->id) {
                $warehouses_list = [];
                $warehouses_ids = $affiliate->getLinkedWarehouses();
                if ($warehouses_ids) {
                    $api = new WareHouseApi();
                    $api->setFilter('public', 1);
                    $api->setFilter('id', $warehouses_ids, 'in');
                    $warehouses_list = Utils::extractOrmList($api->getList());
                }
                $response['list'] = $warehouses_list;
            }
        }
        return $response;
    }
}