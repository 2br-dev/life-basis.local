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
use Shop\Config\File;
use Shop\Model\Orm\Order;

class Delete extends AbstractAuthorizedMethod
{
    const RIGHT_DELETE = 1;
    private $shop_config;

    function __construct()
    {
        parent::__construct();
        $this->shop_config = File::config();
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
            self::RIGHT_DELETE => t('Удаление заказа')
        ];
    }

    /**
     * Удаляет заказ
     *
     * @param string $token Авторизационный токен
     * @param integer $order_id ID заказа
     * @return array
     * @example GET /api/methods/order.delete?token=311211047ab5474dd67ef88345313a6e479bf616&order_id=139
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true
     *     }
     * }
     * </pre>
     */
    public function process($token, $order_id)
    {
        $order = new Order($order_id);
        if (!$order['id']) {
            throw new ApiException(t('Заказ не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $this->validateMethodRights($order);

        if (!$order->delete()) {
            throw new ApiException(t('Не удалось удалить заказЖ %0', [$order->getErrorsStr()]), ApiException::ERROR_WRITE_ERROR);
        }

        return [
            'response' => [
                'success' => true
            ]
        ];
    }

    /**
     * Проверяет права текущего пользователя на доступ к заказу
     *
     * @param Order $order
     * @return void
     */
    protected function validateMethodRights(Order $order)
    {
        $user = $this->token->getUser();

        if ($this->isTokenUserCourier()) {
            if ($order['courier_id'] != $user['id']) {
                throw new ApiException(t('Курьеры могут работать только с назначенными им заказами'), ApiException::ERROR_METHOD_ACCESS_DENIED);
            }
        } elseif ($this->isTokenUserManager()) {
            if ($order['manager_id'] != $user['user_id']) {
                throw new ApiException(t('Менеджеры могут работать только с назначенными им заказами'), ApiException::ERROR_METHOD_ACCESS_DENIED);
            }
        }
    }

    /**
     * Возвращает true, если текущий пользователь менеджер
     *
     * @return bool
     */
    protected function isTokenUserManager()
    {
        return $this->shop_config['manager_group']
            && $this->token->getUser()->inGroup($this->shop_config['manager_group']);
    }

    /**
     * Возвращает true, если текущий пользователь курьер
     *
     * @return bool
     */
    protected function isTokenUserCourier()
    {
        return $this->shop_config['courier_user_group']
            && $this->token->getUser()->inGroup($this->shop_config['courier_user_group']);
    }
}