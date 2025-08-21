<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Front;

use RS\Controller\Front;
use RS\Img\Core as ImgCore;
use Shop\Model\DeliveryType\InterfaceDeliveryOrder;
use Shop\Model\ExternalApi\DeliveryOrder\DoAction;
use Shop\Model\Orm\Order;
use Shop\Model\PrintForm\AbstractPrintForm;

/**
 * Контроллер печати документов для заказа из приложения
 */
class PrintDocs extends Front
{
    /**
     * Печатает документы для заказа
     *
     * @return string
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    function actionIndex()
    {
        $this->wrapOutput(false);

        $sign = $this->url->get('sign', TYPE_STRING);
        $order_hash = $this->url->get('order_hash', TYPE_STRING);
        $doc_type = $this->url->get('doc_type', TYPE_STRING);

        if (AbstractPrintForm::signParams([$order_hash, $doc_type]) !== $sign) {
            $this->e404(t('Неверная подпись запроса'));
        }

        $order = Order::loadByWhere([
            'hash' => $order_hash
        ]);

        if (!$order->id) {
            $this->e404(t('Заказ не найден'));
        }

        //Отключаем WEBP, так как PDF не поддерживает этот формат
        ImgCore::switchFormat(ImgCore::FORMAT_WEBP, false);

        $print_form = AbstractPrintForm::getById($doc_type, $order);
        if (!$print_form) {
            $this->e404(t('Печатная форма не найдена'));
        }

        $this->app->headers->addHeader('X-Robots-Tag', 'noindex');
        return $print_form->getHtml();
    }

    /**
     * Печатает документы для доставки
     *
     * @return string
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    function actionDeliveryDocs()
    {
        $this->wrapOutput(false);

        $sign = $this->url->get('sign', TYPE_STRING);
        $order_hash = $this->url->get('order_hash', TYPE_STRING);
        $doc_type = $this->url->get('doc_type', TYPE_STRING);
        $delivery_order_id = $this->url->get('delivery_order_id', TYPE_STRING);

        if (DoAction::signParams([$order_hash, $doc_type, $delivery_order_id]) !== $sign) {
            $this->e404(t('Неверная подпись запроса'));
        }

        $order = Order::loadByWhere([
            'hash' => $order_hash
        ]);

        if (!$order->id) {
            $this->e404(t('Заказ не найден'));
        }

        //Отключаем WEBP, так как PDF не поддерживает этот формат
        ImgCore::switchFormat(ImgCore::FORMAT_WEBP, false);

        $delivery = $order->getDelivery();
        $type = $delivery->getTypeObject();

        if (!$type instanceof InterfaceDeliveryOrder) {
            $this->e404(t('Доставка не поддерживает печать документов'));
        }

        $delivery_orders = $type->getDeliveryOrderList($order);
        foreach($delivery_orders as $delivery_order) {
            if ($delivery_order['id'] == $delivery_order_id) {
                foreach($delivery_order->getActions() as $action) {
                    if ($action['action'] == $doc_type && ($action['view_type'] ?? '') == 'output') {
                        $result = $type->executeInterfaceDeliveryOrderAction($this->url, $order, $action['action']);

                        $this->app->headers
                            ->addHeader('Content-Type', $result['content_type'])->sendHeaders();

                        return $result['content'];
                    }
                }

                $this->e404(t('Действие не найдено'));
            }
        }

        $this->e404(t('Заказ на доставку не найден'));
    }
}