<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model;

use RS\Module\AbstractModel\EntityList;
use RS\Router\Manager as RouterManager;
use Shop\Model\Orm\Payment;
use Shop\Model\PaymentType\InterfaceRecurringPayments;

class SavedPaymentMethodApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\SavedPaymentMethod, [
            'defaultOrder' => 'save_date ASC',
        ]);
    }

    /**
     * Возвращает массив сведений по привязанным платежным методам к пользователю
     *
     * @param $user - объект пользователя
     * @return array
     */
    static function getSavedPaymentMethods($user)
    {
        $pament_api = new PaymentApi();
        $pament_api->setFilter(['public' => 1]);
        $response = [];
        $recurring_payments = [];
        $add_method_urls = [];
        foreach ($pament_api->getList() as $payment) {
            /** @var Payment $payment */
            $payment->getPropertyIterator()->append([
                'saved_method_list' => new \RS\Orm\Type\ArrayList([
                    'description' => t('Список сохраненных способов оплат для данного метода'),
                    'appVisible' => true
                ])
            ]);


            $payment_type = $payment->getTypeObject();
            if ($payment_type instanceof InterfaceRecurringPayments && $payment_type->isRecurringPaymentsActive()) {
                $payment['saved_method_list'] = \ExternalApi\Model\Utils::extractOrmList($payment_type->getSavedPaymentMethods($user));

                $recurring_payments[$payment->id] = $payment;
                $url_params = [
                    'type' => 'save_payment_method',
                    'payment' => $payment['id'],
                ];
                $add_method_urls[$payment['id']] = RouterManager::obj()->getUrl('shop-front-onlinepay', [
                    'Act' => 'pay',
                    'params' => $url_params,
                    'sign' => OnlinePayApi::getPayParamsSign($url_params),
                ], true);
            }
        }

        $response['response']['payment_list'] = \ExternalApi\Model\Utils::extractOrmList($recurring_payments);
        $response['response']['add_method_urls'] = $add_method_urls;
        $response['response']['user'] = \ExternalApi\Model\Utils::extractOrm($user);

        return $response;
    }
}
