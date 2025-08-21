<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use Catalog\Model\CurrencyApi;
use RS\Db\Adapter as DbAdapter;
use RS\Helper\CustomView;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;
use RS\Orm\Request as OrmRequest;
use Shop\Config\File;
use Shop\Model\Marking\MarkingException;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\OrderItemUIT;
use Shop\Model\Orm\Shipment;
use Shop\Model\Orm\ShipmentItem;
use Shop\Model\Orm\Transaction;

/**
 * API функции для работы с отгрузками
 */
class ShipmentApi extends EntityList
{
    const SHIPMENT_STATUS_SHIPPED = 'shipped';
    const SHIPMENT_STATUS_CAN_FULLY_SHIP = 'can_fully_ship';
    const SHIPMENT_STATUS_CAN_PARTIALLY_SHIP = 'can_partially_ship';
    const SHIPMENT_STATUS_CAN_NOT_SHIP = 'can_not_ship';

    function __construct()
    {
        parent::__construct(new Shipment());
    }

    /**
     * Возвращает уже имеющиеся uit'ы в базе
     *
     * @param array $uits_post_data Данные от формы Отгрузки в администратиной панели
     * @return array
     */
    public function getExistsUits($order_id, $uits_post_data)
    {
        $uit_ids = [];
        foreach ($uits_post_data as $item_uit_list) {
            foreach ($item_uit_list as $uit_key => $uit_item) {
                $uit_ids[] = DbAdapter::escape($uit_key);
            }
        }

        $exist_uits = (new OrmRequest())
            ->select('concat(U.gtin, U.serial) uit_id')
            ->from(new OrderItemUIT(), 'U')
            ->where("order_id != '#order_id'", ['order_id' => $order_id])
            ->where('concat(U.gtin, U.serial) in ("#0")', [implode('","', $uit_ids)])
            ->exec()->fetchSelected(null, 'uit_id');

        return $exist_uits;
    }

    /**
     * Обновляет Uits в базе данных
     *
     * @param array $product_items результат $order->getCart()->getProductItems()
     * @param array $uits_post_data Данные от формы Отгрузки в администратиной панели
     * @throws MarkingException
     */
    public function saveUits($product_items, $uits_post_data)
    {
        foreach ($product_items as $uniq => $item) {
            /** @var OrderItem $order_item */
            $order_item = $item[Cart::CART_ITEM_KEY];
            try {
                $order_item->rewriteUITs($uits_post_data[$uniq] ?? []);
            } catch (MarkingException $e) {
                throw $e;
            }
        }
    }

    /**
     * Возвращает информацию о товарных позициях для следующей отгрузки.
     * Эта информация необходима для формирования формы следующей отгрузки.
     *
     * @param Orm\Order $order Заказ
     * @return array
     */
    public function getNextShipmentData(Orm\Order $order)
    {
        $cart = $order->getCart();
        $product_items = $cart->getProductItems();
        $already_shipped_items_amount = $this->getShippedItemsAmountByOrder($order['id']);
        $already_shipped_uits = $this->getShippedItemsUITsByOrder($order['id']);
        $currency_liter = CurrencyApi::getBaseCurrency()->stitle;
        $has_marked_items = false;

        foreach($product_items as $uniq => $item) {
            $product = $item[Cart::TYPE_PRODUCT];
            $order_item = $item[Cart::CART_ITEM_KEY];

            //Добавляем динамические данные
            $uits = $order_item->getUITs();
            foreach($uits as $uit) {
                $uit['is_shipped'] = in_array($uit['gtin'].$uit['serial'], $already_shipped_uits);
            }

            $order_item['already_shipped'] = (float)($already_shipped_items_amount[$uniq] ?? 0);
            $order_item['amount_for_shipment'] = (float)($order_item['amount'] - $order_item['already_shipped']);

            $order_item['cost'] = $order_item['price'] - $order_item['discount'];
            $order_item['cost_formatted'] = CustomView::cost($order_item['cost'])." ".$currency_liter;
            $order_item['unit_title'] = $order_item->getUnit()->stitle;

            $order_item['uits'] = $uits;
            $has_marked_items = $has_marked_items || $product['marked_class'];
        }

        return [
            'items' => $product_items,
            'has_marked_items' => $has_marked_items
        ];
    }

    /**
     * Возвращает статус следующей отгрузки, опираясь на данные, возвращаемые методом getNextShipmentData
     *
     * @param array $items
     * @return string
     */
    public function getNextShipmentStatus(array $next_shipment_data)
    {
        $already_full_shipment = true;
        $can_full_shipment = true;
        $can_part_shipment = false;

        foreach($next_shipment_data['items'] as $item) {
            $product = $item[Cart::TYPE_PRODUCT];
            $order_item = $item[Cart::CART_ITEM_KEY];
            $order_item_uits_count = count($order_item['uits']);
            if ($order_item['amount_for_shipment'] > 0) {
                $already_full_shipment = false;
            }

            if ($product['marked_class']) {
                //Если добавлено маркировок больше, чем раньше было отгружено, значит можно частично отгрузить еще
                if ($order_item_uits_count > $order_item['already_shipped']) {
                    $can_part_shipment = true;
                }

                //Если маркировок добавлено меньше, чем количество товара, то полная отгрузка невозможна (только частичная)
                if ($order_item_uits_count < $order_item['amount']) {
                    $can_full_shipment = false;
                }
            } else {
                if ($order_item['amount_for_shipment'] > 0) {
                    $can_part_shipment = true;
                }
            }
        }

        if ($already_full_shipment) {
            $status = self::SHIPMENT_STATUS_SHIPPED;
        } elseif ($can_full_shipment) {
            $status = self::SHIPMENT_STATUS_CAN_FULLY_SHIP;
        } elseif ($can_part_shipment) {
            $status = self::SHIPMENT_STATUS_CAN_PARTIALLY_SHIP;
        }  else {
            $status = self::SHIPMENT_STATUS_CAN_NOT_SHIP;
        }

        return $status;
    }

    /**
     * Возвращает текстовое представление статуса
     *
     * @param string $status
     * @return string
     */
    public function getShipmentStatusText($status)
    {
        $handbook = [
            self::SHIPMENT_STATUS_CAN_NOT_SHIP => t('Отсканируйте маркировки для отгрузки'),
            self::SHIPMENT_STATUS_CAN_PARTIALLY_SHIP => t('Можно совершить частичную отгрузку'),
            self::SHIPMENT_STATUS_CAN_FULLY_SHIP => t('Можно совершить полную отгрузку'),
            self::SHIPMENT_STATUS_SHIPPED => t('Все товары уже отгружены'),
        ];

        return $handbook[$status] ?? '';
    }


    /**
     * Возвращает цвет фона для статуса отгрузки
     *
     * @param string $status
     * @return string
     */
    public function getShipmentStatusBgColor(string $status)
    {
        $handbook = [
            self::SHIPMENT_STATUS_CAN_NOT_SHIP => '#d9534f',
            self::SHIPMENT_STATUS_CAN_PARTIALLY_SHIP => '#f0ad4e',
            self::SHIPMENT_STATUS_CAN_FULLY_SHIP => '#d3b756',
            self::SHIPMENT_STATUS_SHIPPED => '#5cb85c',
        ];

        return $handbook[$status] ?? '';
    }

    /**
     * Возвращает количество уже отгруженных товаров в заказе
     *
     * @param integer $order_id ID заказа
     * @return array
     */
    public function getShippedItemsAmountByOrder($order_id)
    {
        return (new OrmRequest())
            ->select('order_item_uniq, SUM(amount) amount')
            ->from(new ShipmentItem())
            ->where([
                'order_id' => $order_id,
            ])
            ->groupby('order_item_uniq')
            ->exec()
            ->fetchSelected('order_item_uniq', 'amount');
    }

    /**
     * Возвращает список идентификаторов уже отгруженных маркировок.
     * Массив состоит из строк GTIN + SERIAL, однозначно идентифицирующих маркировку
     *
     * @param integer $order_id ID заказа
     * @return array
     */
    public function getShippedItemsUITsByOrder($order_id)
    {
        return (new OrmRequest())
            ->select('CONCAT(U.gtin, U.serial) uit_uniq')
            ->from(OrderItemUIT::_getTable(), 'U')
            ->join(ShipmentItem::_getTable(), 'U.id = I.uit_id', 'I')
            ->where([
                'I.order_id' => $order_id,
            ])
            ->exec()
            ->fetchSelected(null, 'uit_uniq');
    }

    /**
     * Возвращает сумму уже отгруженных товаров в заказе
     *
     * @param integer $order_id - ID Заказа
     * @return float[]
     */
    public function getShippedItemsCostByOrder($order_id)
    {
        return (new OrmRequest())
            ->select('order_item_uniq, SUM(cost) as cost')
            ->from(new ShipmentItem())
            ->where([
                'order_id' => $order_id,
            ])
            ->groupby('order_item_uniq')
            ->exec()
            ->fetchSelected('order_item_uniq', 'cost');
    }

    /**
     * Создает отгрузку для указанного заказа
     *
     * @param Order $order
     * @param array $shipment_data Массив с элементами отгрузки. Массив приходит из формы в админ.панели
     * [order_item_uniq => [amount => 1]] для товаров не требующих маркировку
     * [order_item_uniq => [uit => ['GTIN + SERIAL', ...]]] для товаров, требующих маркировку
     *
     * @param bool $add_delivery Если true, то доставка будет добавлена в чек отгрузки
     * @param bool $create_receipt Если true, то чек отгрузки будет создан
     * @return Shipment|false
     */
    public function createShipment(Order $order, $shipment_data, $add_delivery, $create_receipt)
    {
        if (!$order['is_payed']) {
            return $this->addError(t('Отгрузку можно произвести только, если заказ оплачен'));
        }

        $cart = $order->getCart();
        $product_items = $cart->getProductItems();

        $config = File::config();
        if ($config['true_mark_block_shipment']) {
            //Проверяем, что все маркировки прошли проверку в честном знаке
            foreach($product_items as $product_item) {
                /**
                 * @var $order_item OrderItem
                 */
                $order_item = $product_item[Cart::CART_ITEM_KEY];
                foreach($order_item->getUITs() as $uit) {
                    if (!$uit->getCheckResult()->isOk()) {
                        return $this->addError(t('В заказе имеются маркировки, которые не прошли проверку в Честном знаке.'));
                    }
                }
            }
        }

        $ship_uits_id = $this->prepareUitsId($order['id'], $shipment_data); //Список UIT id, которые должны быть отгружены

        $shipped_cost = $this->getShippedItemsCostByOrder($order['id']);
        $shipped_amount = $this->getShippedItemsAmountByOrder($order['id']);

        $shipment = new Shipment();
        $shipment['order_id'] = $order['id'];
        $shipment['info_order_num'] = $order['order_num'];
        $shipment->setTempId();

        $total_shipment_cost = 0;
        $is_shipped = false;

        foreach ($shipment_data as $uniq => $item) {
            $cart_item = $product_items[$uniq][Cart::CART_ITEM_KEY];
            $product = $product_items[$uniq][Cart::TYPE_PRODUCT];

            $shipped_item_cost = $shipped_cost[$uniq] ?? 0;
            $shipped_item_amount = $shipped_amount[$uniq] ?? 0;

            $item_cost_left = $cart_item['price'] - $cart_item['discount'] - $shipped_item_cost;
            $item_amount_left = $cart_item['amount'] - $shipped_item_amount;

            if ($item_amount_left > 0) {
                if (isset($item['uit'])) {
                    foreach ($item['uit'] as $front_uit) {
                        if (isset($ship_uits_id[$front_uit])) {
                            //Продаваемое количество. Если это не штучный товар, то он продается одним кодом
                            $amount = $product->isBulk() ? $cart_item['amount'] : 1;
                            $shipment_item = new ShipmentItem();
                            $shipment_item['order_id'] = $order['id'];
                            $shipment_item['shipment_id'] = $shipment['temp_id'];
                            $shipment_item['order_item_uniq'] = $uniq;
                            $shipment_item['amount'] = $amount;
                            $shipment_item['uit_id'] = $ship_uits_id[$front_uit];

                            if ($shipped_item_amount + $amount == $cart_item['amount']) {
                                $shipment_item['cost'] = $item_cost_left;
                            } else {
                                $shipment_item['cost'] = round($item_cost_left / $item_amount_left * 100) / 100;
                            }

                            $shipment_item->insert();
                            $total_shipment_cost += $shipment_item['cost'];
                            $shipped_item_amount += $shipment_item['amount'];
                            $item_cost_left -= $shipment_item['cost'];
                        }
                    }
                } elseif (!empty($item['amount'])) {
                    $shipment_item = new ShipmentItem();
                    $shipment_item['order_id'] = $order['id'];
                    $shipment_item['shipment_id'] = $shipment['temp_id'];
                    $shipment_item['order_item_uniq'] = $uniq;
                    $shipment_item['amount'] = ($item['amount'] > $item_amount_left) ? $item_amount_left : $item['amount'];

                    if ($shipped_item_amount + $item['amount'] == $cart_item['amount']) {
                        $shipment_item['cost'] = $item_cost_left;
                    } else {
                        $shipment_item['cost'] = round($item_cost_left / $item_amount_left * $item['amount'], 2);
                    }

                    $shipment_item->insert();
                    $total_shipment_cost += $shipment_item['cost'];
                }
                $is_shipped = true;
            }
        }

        if ($is_shipped) {
            if ($add_delivery) {
                foreach ($cart->getCartItemsByType(Cart::TYPE_DELIVERY) as $item) {
                    $shipment_item = new ShipmentItem();
                    $shipment_item['order_id'] = $order['id'];
                    $shipment_item['shipment_id'] = $shipment['temp_id'];
                    $shipment_item['order_item_uniq'] = $item['uniq'];
                    $shipment_item['amount'] = $item['amount'];
                    $shipment_item['cost'] = $item['price'] - $item['discount'];

                    $shipment_item->insert();
                    $total_shipment_cost += $shipment_item['cost'];
                }
            }

            $shipment['info_total_sum'] = $total_shipment_cost;
            if ($shipment->insert()) {
                if ($this->createTransactionByShipment($order, $shipment, $create_receipt)) {
                    return $shipment;
                } else {
                    return false;
                }
            } else {
                return $this->addError(t('Не удалось создать отгрузку: %0', [$shipment->getErrorsStr()]));
            }
        } else {
            return $this->addError(t('Нет товаров для отгрузки'));
        }
    }

    /**
     * Создает транзакцию для отгрузки
     *
     * @param Order $order Объект заказа
     * @param Shipment $shipment Объект отгрузки
     * @param bool $create_receipt Если true, то создает чек
     * @return bool
     */
    protected function createTransactionByShipment(Order $order, Shipment $shipment, $create_receipt)
    {
        $transaction = new Transaction();
        $transaction['dateof'] = date('Y-m-d H:i:s');
        $transaction['order_id'] = $order['id'];
        $transaction['user_id'] = $order['user_id'];
        $transaction['personal_account'] = false;
        $transaction['cost'] = $shipment['info_total_sum'];
        $transaction['reason'] = t('Отгрузка заказа №%0', [$order['order_num']]);
        $transaction['status'] = Transaction::STATUS_SUCCESS;
        $transaction['entity'] = Transaction::ENTITY_SHIPMENT;
        $transaction['entity_id'] = $shipment['id'];

        if ($transaction->insert()) {
            $transaction['sign'] = TransactionApi::getTransactionSign($transaction);
            $transaction->update();

            if ($create_receipt) {
                $transaction_api = new TransactionApi();
                $receipt_result = $transaction_api->createReceipt($transaction);
                if ($receipt_result === true) {
                    return true;
                } else {
                    return $this->addError(t('Отгрузка создана. Ошибка при отправке чека: %0', [$receipt_result]));
                }
            } else {
                return true;
            }
        } else {
            return $this->addError(t('Отгрузка создана. Ошибка при создании транзакции: %0', [$transaction->getErrorsStr()]));
        }
    }

    /**
     * Формирует массив shipment_data для всех товаров, положенных для отгрузки.
     * Этот массив нужен для метода createShipment, в случае когда нужно создать отгрузку для всех позиций.
     * Через API можно создать отгрузку только для всех позиций
     *
     * @param Orm\Order $order
     * @return array
     */
    public function buildShipmentItemsArray(Orm\Order $order)
    {
        $result = [];
        $data = $this->getNextShipmentData($order);
        foreach($data['items'] as $uniq => $item) {
            $product = $item[Cart::TYPE_PRODUCT];
            $order_item = $item[Cart::CART_ITEM_KEY];

            if ($product->marked_class) {
                foreach($order_item['uits'] as $uit) {
                    if (!$uit['is_shipped']) {
                        $result[$uniq]['uit'][] = $uit['gtin'].$uit['serial'];
                    }
                }
            } elseif ($order_item['amount_for_shipment'] > 0) {
                $result[$uniq] = [
                    'amount' => $order_item['amount_for_shipment']
                ];
            }
        }

        return $result;
    }

    /**
     * Возвращает массив, где в ключе публичный ID маркировки (GTIN + SERIAL), а в значении ID uit
     *
     * @param integer $order_id
     * @param array $shipment_items
     * @return array
     */
    protected function prepareUitsId($order_id, array $shipment_items)
    {
        $order_uits = (new OrmRequest())
            ->from(new OrderItemUIT())
            ->where([
                'order_id' => $order_id,
            ])
            ->objects();

        $order_uits_by_front_id = [];
        foreach ($order_uits as $order_uit) {
            $front_id = $order_uit['gtin'] . $order_uit['serial'];
            $order_uits_by_front_id[$front_id] = $order_uit['id'];
        }

        return $order_uits_by_front_id;
    }

    /**
     * Возвращает GTIN (штрихкод) по коду маркировки
     *
     * @return string
     */
    public function getGtinByDatamatrix($datamatrix)
    {
        if (!preg_match('/[а-яёА-ЯЁ]/u', $datamatrix)) {
            if (preg_match('/^01(\d{14})21/u', $datamatrix, $matches)) {
                return ltrim($matches[1], '0');
            }
        }

        throw new MarkingException(t('Некорректный код'), MarkingException::ERROR_SINGLE_CODE_PARSE);
    }

    /**
     * Возвращает список товаров заказа, которые подходят для маркировки
     *
     * @param integer $order_id ID заказа
     * @param string $gtin GTIN-штрихкод из кода маркировки
     * @return array
     */
    public function getOrderItemsByDataMatrix($order_id, $gtin)
    {
        /**
         * @var OrderItem[] $order_items
         */
        $order_items = Request::make()
            ->from(new OrderItem())
            ->where([
                'order_id' => $order_id,
                'sku' => $gtin,
                'type' => OrderItem::TYPE_PRODUCT
            ])->objects();

        foreach($order_items as $key => $order_item) {
            $product = $order_item->getEntity();
            if (!$product['id'] || !$product['marked_class']) {
                unset($order_items[$key]);
            }
        }

        return $order_items;
    }
}
