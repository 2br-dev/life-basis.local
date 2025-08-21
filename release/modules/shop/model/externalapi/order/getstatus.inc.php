<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Order;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use Shop\Model\Orm\Order;

/**
* Возвращает статус последней транзакции у заказа
*/
class GetStatus extends AbstractAuthorizedMethod
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
            self::RIGHT_LOAD => t('Загрузка статуса транзакции заказа')
        ];
    }

    /**
    * Возвращает ORM объект, который следует загружать
    */
    public function getOrmObject()
    {
        return new Order();
    }

    /**
     * Возвращает статус последней транзакции у заказа
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID заказа
     *
     * @return array
     * @throws ApiException
     * @example GET /api/methods/order.getStatus?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86&order_id=1
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "status": "new"
     *     }
     * }
     * </pre>
     *
     */
    protected function process($token, $order_id)
    {
        /**
        * @var \Shop\Model\Orm\Order $object
        */
        $object = $this->getOrmObject();
        $response['response'] = [];

        if ($object->load($order_id)) {
            if (isset($object->id)) {
                if ($transaction = $object->getOrderTransaction()) {
                    $response['response']['status'] = $transaction->status;
                }
            }
            return $response;
        }

        throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
    }
}
