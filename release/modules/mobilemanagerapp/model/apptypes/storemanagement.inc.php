<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\AppTypes;
use RS\Module\Manager;
use Users\Model\ExternalApi\User;
use Shop\Model\ExternalApi\Order;
use Support\Model\ExternalApi\Support;
use Catalog\Model\ExternalApi\Oneclick;
use Shop\Model\ExternalApi\Reservation;
use Shop\Model\ExternalApi\Transaction;
use Shop\Model\ExternalApi\Receipt;
use Shop\Model\ExternalApi\Payment;
use PushSender\Model\ExternalApi\Push as PushSender;
use ExternalApi\Model\App\AbstractAppType;
use RS\Config\Loader as ConfigLoader;
use PushSender\Model\App\InterfaceHasPush;
use MobileManagerApp\Model\Push;

/**
* Приложение - управление магазином
*/
class StoreManagement extends AbstractAppType implements InterfaceHasPush
{
    const ID = 'store-management';

    /**
    * Возвращает строковый идентификатор приложения
    * 
    * @return string
    */
    public function getId()
    {
        return self::ID;
    }
    
    /**
    * Возвращает SHA1 от секретного ключа client_secret, который должен 
    * передаваться вместе с client_id в момент авторизации
    * 
    * @return string
    */
    public function checkSecret($client_secret)
    {
        return sha1( $client_secret ) == 'fdb6e4f2df8f561c773d363fe40f7bbb052c736a';
    }
    
    /**
    * Метод возвращает название приложения
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('Управление магазином(администратору/курьеру)');
    }
    
    /**
    * Метод возвращает массив, содержащий требуемые права доступа к json api для приложения
    * 
    * @return [
    *   [
    *       'method' => 'oauth/authorize',
    *       'right_codes' => [код действия, код действия, ...]
    *   ],
    *   ...
    * ]
    */
    public function getAppRights()
    {
        $courier_rights = [];
        if ($this->getToken()) {
            $shop_config = ConfigLoader::byModule('shop');
            if ($shop_config && in_array($shop_config->courier_user_group, $this->getToken()->getUser()->getUserGroups())) {
                $courier_rights[] = Order\Get::RIGHT_COURIER;
            }
        }
        
        $rights = [
            'managerApp.getAppSettings' => self::FULL_RIGHTS,

            'user.get' => [User\Get::RIGHT_LOAD_SELF],
            'user.getCourierList' => self::FULL_RIGHTS,
            
            'product.get' => self::FULL_RIGHTS,
            'product.getList' => self::FULL_RIGHTS,
            'product.getOfferList' => self::FULL_RIGHTS,
            
            'push.getList' => self::FULL_RIGHTS,
            'push.registerToken' => self::FULL_RIGHTS,
            'push.unregisterToken' => [PushSender\UnregisterToken::RIGHT_UNREGISTER],
            'push.change' => self::FULL_RIGHTS,

            'actionTemplate.getList' => self::FULL_RIGHTS,
            'actionTemplate.runAction' => self::FULL_RIGHTS,

            'oneClick.get' => [Oneclick\Get::RIGHT_LOAD],
            'oneClick.getList' => [Oneclick\GetList::RIGHT_LOAD],
            'oneClick.delete' => [Oneclick\Delete::RIGHT_DELETE],
            'oneClick.setStatus' => [Oneclick\SetStatus::RIGHT_UPDATE],
        ];

        if (Manager::staticModuleExists('support')) {
            $rights += [
                'support.getAdminTopicList'   => self::FULL_RIGHTS,
                'support.getAdminTopicMessages'   => [Support\GetTopicMessages::RIGHT_LOAD],
                'support.createAdminTopic'   => [Support\CreateAdminTopic::RIGHT_ADD],
                'support.sendMessage'   => [Support\SendMessage::RIGHT_ADD],
                'support.closeTopic'   => [Support\CloseTopic::RIGHT_CLOSE],
                'support.deleteTopic'   => [Support\DeleteTopic::RIGHT_LOAD],
            ];
        }

        if (Manager::staticModuleExists('shop')) {
            $rights += [
                'order.get' => array_merge([Order\Get::RIGHT_LOAD, Order\Get::RIGHT_FULL_ACCESS], $courier_rights),
                'order.getList' => array_merge([Order\GetList::RIGHT_LOAD], $courier_rights),
                'order.sellStatistic' => [Order\SellStatistic::RIGHT_LOAD],
                'order.sellStatisticMonth' => [Order\SellStatistic::RIGHT_LOAD],
                'order.sellOrderStatus' => [Order\SellOrderStatus::RIGHT_LOAD],
                'order.statisticAvgOrderSum' => self::FULL_RIGHTS,
                'order.sellStatisticYears' => self::FULL_RIGHTS,
                'order.getReceiptList' => array_merge([Order\GetReceiptList::RIGHT_LOAD], $courier_rights),
                'order.update' => array_merge([Order\Update::RIGHT_UPDATE], $courier_rights),
                'order.createByOneClick' => self::FULL_RIGHTS,
                'order.createByReservation' => self::FULL_RIGHTS,
                'order.createByOrder' => self::FULL_RIGHTS,
                'order.save' => self::FULL_RIGHTS,
                'order.addOrderItem' => self::FULL_RIGHTS,
                'order.doPaymentAction' => self::FULL_RIGHTS,
                'order.delete' => self::FULL_RIGHTS,
                'order.payWithPersonalAccount' => [Order\PayWithPersonalAccount::RIGHT_PAY],

                'marking.findItem' => self::FULL_RIGHTS,
                'marking.add' => self::FULL_RIGHTS,
                'marking.delete' => self::FULL_RIGHTS,
                'marking.getList' => self::FULL_RIGHTS,

                'shipment.getList' => self::FULL_RIGHTS,
                'shipment.get' => self::FULL_RIGHTS,
                'shipment.beforeAdd' => self::FULL_RIGHTS,
                'shipment.add' => self::FULL_RIGHTS,
                'shipment.delete' => self::FULL_RIGHTS,

                'address.save' => self::FULL_RIGHTS,
                'address.delete' => self::FULL_RIGHTS,
                'address.getList' => self::FULL_RIGHTS,

                'warehouse.getList' => self::FULL_RIGHTS,
                'status.getList' => self::FULL_RIGHTS,
                'payment.getList' => self::FULL_RIGHTS,
                'payment.getPaymentMethodsList' => [Payment\GetPaymentMethodsList::RIGHT_LOAD],

                'delivery.getList' => self::FULL_RIGHTS,
                'delivery.getPickupPointsList' => self::FULL_RIGHTS,

                'reservation.get' => [Reservation\Get::RIGHT_LOAD],
                'reservation.getList' => [Reservation\GetList::RIGHT_LOAD],
                'reservation.delete' => [Reservation\Delete::RIGHT_DELETE],
                'reservation.setStatus' => [Reservation\SetStatus::RIGHT_UPDATE],

                'transaction.get' => [Transaction\Get::RIGHT_LOAD],
                'transaction.getList' => [Transaction\GetList::RIGHT_LOAD],
                'transaction.action' => [Transaction\Action::RIGHT_RUN],
                'transaction.changeBalance' => [Transaction\ChangeBalance::RIGHT_ADD_FUNDS],

                'receipt.getSuccessInfo' => [Receipt\GetSuccessInfo::RIGHT_LOAD],
                'receipt.getReport' => [Receipt\GetReport::RIGHT_RUN],

                'region.getCountryList' => self::FULL_RIGHTS,
                'region.getRegionList' => self::FULL_RIGHTS,
                'region.getCityList' => self::FULL_RIGHTS,

                'cargo.getPresets' => self::FULL_RIGHTS,
                'cargo.getList' => self::FULL_RIGHTS,
                'cargo.save' => self::FULL_RIGHTS,

                'deliveryOrder.create' => self::FULL_RIGHTS,
                'deliveryOrder.get' => self::FULL_RIGHTS,
                'deliveryOrder.getList' => self::FULL_RIGHTS,
                'deliveryOrder.doAction' => self::FULL_RIGHTS,

                'task.getFilterList' => self::FULL_RIGHTS,
                'task.saveFilter' => self::FULL_RIGHTS,
                'task.deleteFilter' => self::FULL_RIGHTS,
            ];

            if (Manager::staticModuleExists('files')) {
                $rights += [
                    'file.update' => self::FULL_RIGHTS,
                    'file.delete' => self::FULL_RIGHTS,
                    'file.getAccessTypes' => self::FULL_RIGHTS
                ];
            }

            $rights['user.getList'] = [User\GetList::RIGHT_LOAD];
            $rights['user.get'] = [User\Get::RIGHT_LOAD];

            if (!$courier_rights) {
                $rights['user.create'] = [User\Create::RIGHT_CREATE];
                $rights['user.update'] = [User\Update::RIGHT_LOAD];
            }
        }

        return $rights;
    }

    /**
    * Возвращает группы пользователей, которым доступно данное приложение.
    * Сведения загружаются из настроек текущего модуля
    * 
    * @return ["group_id_1", "group_id_2", ...]
    */    
    public function getAllowUserGroup()
    {
        return ConfigLoader::byModule($this)->allow_user_groups;
    }
    
    /**
    * Возвращает массив объектов Push уведомлений
    * 
    * @return \PushSender\Model\AbstractPushNotice[]
    */
    public function getPushNotices()
    {
        return [
            new Push\NewOrderToCourier(),
            new Push\NewOrderToManager(),
            new Push\NewOrderToAdmin(),
            new Push\NewOneClickToAdmin(),
            new Push\NewReservationToAdmin(),
            new Push\AddBalanceToAdmin(),
            new Push\OrderPayedToAdmin(),
            new Push\NewMessageToAdmin(),
            new Push\TextMessage(),
            new Push\ScanCode(),
        ];
    }    
}
