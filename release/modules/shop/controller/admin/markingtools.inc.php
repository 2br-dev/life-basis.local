<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Admin;

use Catalog\Model\Orm\Product;
use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\Helper\Tools as HelperTools;
use RS\Html\Toolbar\Button as ToolbarButton;
use Shop\Config\ModuleRights;
use Shop\Model\Exception as ShopException;
use Shop\Model\Marking\MarkingApi;
use Shop\Model\Marking\MarkingException;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Order;
use Shop\Model\ShipmentApi;

/**
 * Контроллер Инструменты маркировок
 */
class MarkingTools extends Crud
{
    /** @var OrderApi */
    protected $api;
    protected $shipment_api;

    function __construct()
    {
        parent::__construct(new OrderApi());
        $this->shipment_api = new ShipmentApi();
        $order_id = $this->url->get('order_id', TYPE_INTEGER);

        if ($order_id) {
            // Установим необходимый текущий сайт, если редактирование заказа
            // происходит из другого мультисайта.
            $order_site_id = $this->api->getSiteIdByOrderId($order_id);
            $this->api->setSiteContext($order_site_id);
            $this->changeSiteIdIfNeed($order_site_id);
        }
    }

    /**
     * Возвращает помощника формирования визуального интерфейса
     *
     * @return CrudCollection
     */
    public function helperShipment()
    {
        $bottomToolbar = $this->buttons(['save', 'cancel']);
        if ($this->user->checkModuleRight('shop', ModuleRights::RIGHT_MAKE_SHIPMENT)) {
            $bottomToolbar->addItem(new ToolbarButton\Button('', t('отгрузить'), [
                'attr' => [
                    'class' => 'execute-shipment btn-warning'
                ]
            ]));
        }
        $helper = new CrudCollection($this, $this->api, $this->url, [
            'bottomToolbar' => $bottomToolbar,
            'viewAs' => 'form',
        ]);
        return $helper;
    }

    /**
     * Отображает форму создания отгрузки
     *
     * @return Standard
     */
    public function actionShipment()
    {
        $helper = $this->getHelper();
        $order_id = $this->url->request('order_id', TYPE_INTEGER);

        $order = new Order($order_id);
        if (!$order['is_payed']) {
            return $this->result->setSuccess(false)->addEMessage(t('Для отгрузки заказ должен быть полностью оплачен'))->addSection('close_dialog', 1);
        }

        $cart = $order->getCart();
        $product_items = $cart->getProductItems();
        if ($this->url->isPost()) {
            $uit = $this->url->request('uit', TYPE_ARRAY);
            $uit = HelperTools::unescapeArrayRecursive($uit);

            $exist_uits = $this->shipment_api->getExistsUits($order['id'], $uit);

            if ($exist_uits) {
                return $this->result->setSuccess(false)
                    ->addSection('error_type', 'uit_highlight')
                    ->addSection('error', t('В отгрузке присутствуют коды, отгруженные в других заказах.'))
                    ->addSection('uit_list', $exist_uits);
            }

            $this->shipment_api->saveUits($product_items, $uit);
            return $this->result->setSuccess(true);
        }

        $this->view->assign([
            'order' => $order,
            'cart' => $cart,
            'product_items' => $product_items,
            'shipped_amount' => $this->shipment_api->getShippedItemsAmountByOrder($order['id']),
            'shipped_uits' => $this->shipment_api->getShippedItemsUITsByOrder($order['id']),
        ]);

        $helper->setTopTitle(t('Отгрузка заказа №{order_num}'), $order);
        $helper['form'] = $this->view->fetch('%shop%/form/order/order_shipment.tpl');
        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Разбирает содержимое штрихкода
     *
     * @return string
     */
    public function actionParseCode()
    {
        $result = [
            'success' => false,
            'error_type' => 'float_head',
        ];

        $this->wrapOutput(false);
        $product_id = $this->url->request('product_id', TYPE_INTEGER);
        $code = htmlspecialchars_decode($this->url->request('code', TYPE_STRING, null, false), 3);

        $product = new Product($product_id);
        if (empty($product['id'])) {
            $result['error'] = t('Указанный товар не найден');
        } elseif (empty($product['marked_class'])) {
            $result['error'] = t('Указанный товар не подлежит маркировке');
        } else {
            $marking_class = MarkingApi::getMarkedClasses()[$product['marked_class']];
            try {
                $uit = $marking_class->getUITFromCode($code);
                $uit_check_result = $uit->getCheckResult();
                $result = [
                    'success' => true,
                    'result' => $uit->asArray() + [
                        'checkResult' => [
                            'status' => $uit_check_result->getCheckStatus(),
                            'text' => $uit_check_result->getCheckText(),
                            'color' => $uit_check_result->getCheckStatusColor(),
                            'icon' => $uit_check_result->getCheckStatusRsIcon()
                        ]
                    ],
                ];
            } catch (MarkingException $e) {
                $result['error'] = $e->getMessage();
            }
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Создаёт "отгрузку"
     *
     * @return Standard
     * @throws ShopException
     */
    public function actionMakeShipment()
    {
        $order_id = $this->url->get('order_id', TYPE_STRING);
        $add_delivery_to_shipment = $this->url->post('add_delivery', TYPE_ARRAY);
        $shipment_data = HelperTools::unescapeArrayRecursive($this->url->post('shipment', TYPE_ARRAY));
        $create_receipt = $this->getModuleConfig()['create_receipt_upon_shipment'] ?: $this->url->post('create_receipt', TYPE_ARRAY);

        $order = new Order($order_id);
        if (!$order['id']) {
            $this->result->setSuccess(false)
                ->addEMessage(t('Заказ не найден'));
        }

        if (empty($shipment_data)) {
            return $this->result->setSuccess(false)
                ->addEMessage(t('Список товарных позиций для отгрузки пуст.'));
        }

        if ($this->shipment_api->createShipment($order, $shipment_data, $add_delivery_to_shipment, $create_receipt)) {
            return $this->result->setSuccess(true)
                ->addMessage(t('Отгрузка успешно создана'));
        } else {
            return $this->result->setSuccess(false)
                ->addEMessage($this->shipment_api->getErrorsStr());
        }
    }
}
