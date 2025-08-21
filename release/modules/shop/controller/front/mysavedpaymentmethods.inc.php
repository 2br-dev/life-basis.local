<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Front;

use RS\Controller\AuthorizedFront;
use RS\Router\Manager as RouterManager;
use Shop\Model\Exception as ShopException;
use Shop\Model\OnlinePayApi;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\SavedPaymentMethod;
use Shop\Model\PaymentApi;
use Shop\Model\PaymentType\InterfaceRecurringPayments;
use RS\Event\Manager as EventManager;

class MySavedPaymentMethods extends AuthorizedFront
{
    public function actionIndex()
    {
        $this->app->breadcrumbs->addBreadCrumb(t('Мои привязанные карты'));

        $payment_api = new PaymentApi();
        $payment_api->setFilter(['public' => 1]);
        $recurring_payments = [];
        $add_method_urls = [];
        foreach ($payment_api->getList() as $payment) {
            /** @var Payment $payment */
            $payment_type = $payment->getTypeObject();
            if ($payment_type instanceof InterfaceRecurringPayments && $payment_type->isRecurringPaymentsActive()) {
                $recurring_payments[] = $payment;
            }
        }

        // Событие для модификации списка оплат
        $result = EventManager::fire('savemethod.payment.list', [
            'list' => $recurring_payments,
            'user' => $this->user
        ]);

        $payment_list = $result->getResult()['list'];
        foreach($payment_list as $payment) {
            $url_params = [
                'type' => 'save_payment_method',
                'payment' => $payment['id'],
            ];
            $add_method_urls[$payment['id']] = RouterManager::obj()->getUrl('shop-front-onlinepay', [
                'Act' => 'pay',
                'params' => $url_params,
                'sign' => OnlinePayApi::getPayParamsSign($url_params),
            ]);
        }

        $this->view->assign([
            'payment_list' => $payment_list,
            'add_method_urls' => $add_method_urls,
            'user' => $this->user,
        ]);

        return $this->result->setTemplate('saved_payment_methods.tpl');
    }

    public function actionSavedMethodDelete()
    {
        $this->wrapOutput(false);

        try {
            $saved_method_id = $this->url->request('saved_method', TYPE_INTEGER);
            $saved_method = new SavedPaymentMethod($saved_method_id);
            $payment_id = $this->url->request('payment', TYPE_INTEGER);
            $payment = new Payment($payment_id);
            $payment_type = $payment->getTypeObject();

            if (empty($saved_method['id']) || $saved_method['user_id'] != $this->user['id']) {
                throw new ShopException(t('Указанный сохранённый способ платежа не существует'));
            }
            if (empty($payment['id'])) {
                throw new ShopException(t('Указанный способ оплаты не существует'));
            }
            if (!($payment_type instanceof InterfaceRecurringPayments) || $payment_type->getRecurringPaymentsType() == InterfaceRecurringPayments::RECURRING_TYPE_NONE) {
                throw new ShopException(t('Указанный способ оплаты не поддерживает рекуррентные платежи'));
            }

            $payment_type->deleteSavedPaymentMethod($saved_method);
        } catch (ShopException $e) {
            return $this->result->setSuccess(false)->addSection('error', $e->getMessage());
        }

        return $this->result->setSuccess(true);
    }

    public function actionSavedMethodMakeDefault()
    {
        $this->wrapOutput(false);

        try {
            $saved_method_id = $this->url->request('saved_method', TYPE_INTEGER);
            $saved_method = new SavedPaymentMethod($saved_method_id);

            if (empty($saved_method['id']) || $saved_method['user_id'] != $this->user['id']) {
                throw new ShopException(t('Указаный сохранённый способ платежа не существует'));
            }

            $saved_method['is_default'] = 1;
            $saved_method->update();
        } catch (ShopException $e) {
            return $this->result->setSuccess(false)->addSection('error', $e->getMessage());
        }

        return $this->result->setSuccess(true);
    }
}
