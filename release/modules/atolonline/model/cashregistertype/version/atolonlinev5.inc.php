<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace AtolOnline\Model\CashRegisterType\Version;

use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\Unit;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use Shop\Model\CashRegisterApi;
use Shop\Model\Marking\MarkingApi;
use Shop\Model\Marking\TrueApi\UitCheckResult;
use Shop\Model\Orm\OrderItem;
use RS\Config\Loader as ConfigLoader;
use Shop\Model\Orm\ProductsReturnOrderItem;
use Shop\Model\Orm\Receipt;
use Shop\Model\Orm\ShipmentItem;
use Shop\Model\Orm\Transaction;
use Users\Model\Orm\User;

/**
 * Класс интерграции с АТОЛ Онлайн по протоколу версии 5
 */
class AtolOnlineV5 extends AtolOnlineV4
{
    const
        API_URL = "https://online.atol.ru/possystem/v5/";


    /**
     * Возвращает объект формы чека коррекции
     *
     * @return \RS\Orm\FormObject | false Если false, то это означает, что кассовый модуль не поддерживает чеки коррекции
     */
    public function getCorrectionReceiptFormObject()
    {
        return false;
    }

    /**
     * Возвращает признак предмета расчёта для версии АТОЛ 1.2
     *
     * @param string $payment_object Признак предмета расчета
     * @return int
     */
    private function getReceiptPaymentSubject($payment_object)
    {
        return match ($payment_object) {
            CashRegisterApi::PAYMENT_SUBJECT_COMMODITY => 1,
            CashRegisterApi::PAYMENT_SUBJECT_EXCISE => 2,
            CashRegisterApi::PAYMENT_SUBJECT_WORK => 3,
            CashRegisterApi::PAYMENT_SUBJECT_SERVICE => 4,
            CashRegisterApi::PAYMENT_SUBJECT_PAYMENT => 10,
            default => 13,
        };
    }

    /**
     * Возвращает данные для одной позиции в чеке на основе позиции отгрузки
     *
     * @param ShipmentItem $shipment_item Объект отгрузки
     * @return array
     */
    protected function getItemDataFromShipmentItem(ShipmentItem $shipment_item)
    {
        $order_item = $shipment_item->getOrderItem();
        $item_entity = $order_item->getEntity();
        $marked_classes = MarkingApi::getMarkedClasses();

        $result = $this->getItemDataFromOrderItem($order_item, Transaction::ENTITY_SHIPMENT);
        $result['price'] = round($shipment_item['cost'] / $shipment_item['amount'], 2);
        $result['quantity'] = (float)$shipment_item['amount'];
        $result['sum'] = (float)$shipment_item['cost'];
        $result['payment_method'] = CashRegisterApi::PAYMENT_METHOD_FULL_PAYMENT;
        if ($item_entity instanceof Product) {
            $result['declaration_number'] = (string)$item_entity['gtd'];
        }
        if ($item_entity instanceof Product && $item_entity['marked_class'] && isset($marked_classes[$item_entity['marked_class']])) {
            $uit = $shipment_item->getUit();
            if ($uit) {
                $check_result = $uit->getCheckResult();
                if ($check_result->getCheckStatus() == UitCheckResult::STATUS_OK) {
                    $check_data = $check_result->getCheckData();
                    if (isset($check_data['reqId']) && isset($check_data['reqTimestamp'])) {
                        //Добавляем сведения о проверке кода маркировки в ЧестномЗнаке
                        $result['sectoral_item_props'][] = [
                            'federal_id' => '030',
                            'date' => '21.11.2023',
                            'number' => '1944',
                            'value' => implode('&', [
                                'UUID=' . $check_data['reqId'],
                                'Time=' . $check_data['reqTimestamp'],
                            ])
                        ];
                    }
                }
            }
            $result['mark_processing_mode'] = '0';
            $result['mark_code']['gs1m'] = base64_encode($marked_classes[$item_entity['marked_class']]->getNomenclatureCode($uit));
        }

        return $result;
    }

    /**
     * Возвращает данные для одной позиции в чеке на основе товарной позиции
     *
     * @param OrderItem $order_item - товарная позиция
     * @param string $transaction_type - тип транзакции ('пусто' - продажа, Transaction::ENTITY_SHIPMENT - отгрузка, Transaction::ENTITY_RETURN - возврат)
     * @return array
     */
    protected function getItemDataFromOrderItem(OrderItem $order_item, $transaction_type = '')
    {
        $result = [];
        $payment = $this->transaction->getOrder()->getPayment();
        $shop_config = ConfigLoader::byModule('shop');

        $result['name'] = $order_item['title'];
        if (!empty($order_item['model'])) {
            $result['name'] .= " ({$order_item['model']})";
        }
        $result['price'] = round(($order_item['price'] - $order_item['discount']) / $order_item['amount'], 2);
        $result['quantity'] = (float)$order_item['amount'];
        $result['sum'] = (float)($order_item['price'] - $order_item['discount']);
        $result['payment_method'] = $payment['payment_method'] ?: $shop_config['payment_method'];

        switch ($order_item['type']) {
            case OrderItem::TYPE_PRODUCT:
                /** @var \Catalog\Model\Orm\Product $product */
                $product = $order_item->getEntity();

                $result['payment_object'] = $this->getReceiptPaymentSubject($product['payment_subject']);

                if($product['gtd'])
                    $result['declaration_number'] = (string)$product['gtd'];

                if ($product->getUnit()['stitle']) {
                    $result['measure'] = (int)$product->getUnit()['measure_value'];
                } else $result['measure'] = 0;

                $result += $this->getItemTaxData($this->getRightTaxForProduct($this->transaction->getOrder(), $product, $transaction_type));

                break;
            case OrderItem::TYPE_DELIVERY:
                $delivery = $order_item->getEntity();
                $result['payment_method'] = $delivery['payment_method'] ? $delivery['payment_method'] : $this->transaction->getOrder()->getDefaultPaymentMethod();
                $result['payment_object'] = $this->getReceiptPaymentSubject('service');
                $result += $this->getItemTaxData($this->getRightTaxForDelivery($this->transaction->getOrder(), $this->transaction->getOrder()->getDelivery(), $transaction_type));
                $result['measure'] = 0;
                break;

        }
        return $result;
    }

    /**
     * Возвращает чек для пополнения/списания средств с лицевого счета
     *
     * @param string $operation_type - тип чека, приход или возврат
     * @return array
     */
    protected function getReceiptsForPersonalAccount($operation_type)
    {
        $transaction = $this->transaction;
        $sum = abs($transaction['cost']);

        if ($sum < 0) {
            $payment_method = CashRegisterApi::PAYMENT_METHOD_FULL_PAYMENT;
            $payment_object = $transaction['receipt_payment_subject'] ?? 'service';
        } else {
            $payment_method = $this->config['personal_account_payment_method'];
            $payment_object = $this->config['personal_account_payment_subject'];
        }

        $item_data = [
            'name' => $transaction['reason'],
            'price' => (float)abs($sum),
            'quantity' => 1,
            'sum' => (float)abs($sum),
            'payment_method' => $payment_method,
            'payment_object' => $this->getReceiptPaymentSubject($payment_object),
            'measure' => 0,
        ];
        $item_data += $this->getItemTaxData(static::TAX_NONE);

        $this->modifyReceiptItemData($item_data);

        $receipt['items'][] = $item_data;
        $receipt['total'] = $sum;

        $receipt = $this->addReceiptOtherData($receipt, $operation_type, 0);

        $receipts = [$receipt];
        return $receipts;
    }

    /**
     * Добавляет в чек информацию о пользователе
     *
     * @param array $receipt
     * @param User $user
     * @return array
     */
    protected function getClientInfo($receipt, $user)
    {
        if ($user['e_mail'] != ""){
            $receipt['receipt']['client']['email'] = $user['e_mail'];
        }
        if ($user['phone']) {
            $receipt['receipt']['client']['phone'] = $this->preparePhone($user['phone']);
        }
        return $receipt;
    }

    /**
     * Возвращает данные для одной позиции в чеке на основе позиции возврата
     *
     * @param ProductsReturnOrderItem $product_return_item
     * @return array
     */
    protected function getItemDataFromProductReturnItem(ProductsReturnOrderItem $product_return_item)
    {
        $product = new Product($product_return_item['entity_id']);

        $result['name'] = $product_return_item['title'];
        if (!empty($product_return_item['model'])) {
            $result['name'] .= " ({$product_return_item['model']})";
        }
        $result['price'] = (float)$product_return_item['cost'];
        $result['quantity'] = (float)$product_return_item['amount'];
        $result['sum'] = (float)($product_return_item['cost'] * $product_return_item['amount']);
        $result['payment_method'] = CashRegisterApi::PAYMENT_METHOD_FULL_PAYMENT;
        $result['payment_object'] = $this -> getReceiptPaymentSubject($product['payment_subject']);

        $result['measure'] = 0;


        $result += $this->getItemTaxData($this->getRightTaxForProduct($this->transaction->getOrder(), $product));

        return $result;
    }

    /**
     * Возвращает единицу измерения товара
     * Если не находит ко в системе ОКЕИ, то ищет по короткому названию
     *
     * @param $product
     * @return int
     */
    protected function getMeasure($product)
    {
            $unit = $product->getUnit();

            $atol_unit = $this->findUnitByOkei($unit);
            if ($atol_unit === false) {
                $atol_unit = $this->findUnitBySTitle($unit);
            }
            return (int)$atol_unit;
        }

    /**
     * Возвращает единицу измерения в системе ОКЕИ
     *
     *
     * @param integer $unit Код ОКЕИ единицы измерения
     * @return int
     */
    protected function findUnitByOkei($unit)
    {
        $map = [
            796 => 0,
            163 => 10,
            166 => 11,
            168 => 12,
            004 => 20,
            005 => 21,
            006 => 22,
            051 => 30,
            053 => 31,
            055 => 32,
            111 => 40,
            112 => 41,
            113 => 42,
            245 => 50,
            359 => 70,
            356 => 71,
            355 => 72,
            354 => 73,
        ];
        return $map[$unit['code']] ?? false;
    }

    /**
     * Возвращает единицу измерения по краткому названию
     *
     *
     * @param string $unit единица измерения, короткое название
     * @return int
     */
    protected function findUnitByStitle($unit)
    {
        $measure = 0;
        switch ($unit) {
            case 'шт' : $measure = 0; break;
            case "гр" : $measure = 10; break;
            case"кг" : $measure = 11; break;
            case"т"   : $measure = 12; break;
            case"см" : $measure = 20; break;
            case"дм" : $measure = 21; break;
            case"м" : $measure = 22; break;
            case"см2" : $measure = 30; break;
            case"дм2" : $measure = 31; break;
            case"м2" : $measure = 32; break;
            case"мл" : $measure = 40; break;
            case"л" : $measure = 41; break;
            case"м3" : $measure = 42; break;
            case"кВт.ч" : $measure = 50; break;
            case"Гкал" : $measure = 51; break;
            case"сут" : $measure = 70; break;
            case"ч" : $measure = 71; break;
            case"мин" : $measure = 72; break;
            case"сек" : $measure = 73; break;
            case"Кбайт" : $measure = 80; break;
            case"Мбайт" : $measure = 81; break;
            case"Гбайт" : $measure = 82; break;
            case"Тбайт" : $measure = 83; break;
        };
        return $measure;
    }
}
