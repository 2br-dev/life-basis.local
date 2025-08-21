<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Model\AppTypes;

use ExternalApi\Model\AbstractMethods\AbstractMethod;
use ExternalApi\Model\App\AbstractAppType;
use MobileSiteApp\Model\Push\MessageToUsers;
use MobileSiteApp\Model\Push\OrderChangeToUser;
use OrderReview\Model\Push\OrderReviewPush;
use RS\Config\Loader;
use Stories\Model\Push\NewStoryPush;
use Support\Model\Push\NewMessage;
use PushSender\Model\App\InterfaceHasPush;
use RS\Module\Manager as ModuleManager;
use Shop\Model\App\InterfaceOrderCreation;
use Shop\Model\Orm\Order as OrmOrder;

use Shop\Model\ExternalApi\Delivery;
use Shop\Model\ExternalApi\Payment;
use Shop\Model\ExternalApi\Order;
use Shop\Model\ExternalApi\Cart;
use Support\Model\ExternalApi\Support;
use Users\Model\ExternalApi\User;
use OrderReview\Model\ExternalApi\Order as OrderReviewOrder;
use Stories\Model\ExternalApi\Stories;
use TelegramShop\Model\ExternalApi\Telegram;
use TelegramShop\Model\ExternalApi\User as TelegramUser;

/**
* Приложение мобильный сайт
*/
class MobileSiteApp extends AbstractAppType
    implements InterfaceHasPush, InterfaceOrderCreation
{
    const CREATOR_PLATFORM_ID = 'mobile-client-app';

    /**
    * Возвращает строковый идентификатор приложения
    * 
    * @return string
    */
    public function getId()
    {
        return 'mobilesiteapp';
    }
    
    /**
    * Возвращает SHA1 от секретного ключа client_secret, который должен 
    * передаваться вместе с client_id в момент авторизации
    * 32eb1c5e9eadfd48d24967c0cbf80d3f69583786
    *
    * @param string $client_secret - секретное слово на клиентской стороне
    * @return string
    */
    public function checkSecret($client_secret)
    {
        return sha1( $client_secret ) == '32eb1c5e9eadfd48d24967c0cbf80d3f69583786';
    }
    
    /**
    * Метод возвращает название приложения
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('Мобильное приложение для сайта');
    }
    
    /**
    * Метод возвращает массив, содержащий требуемые права доступа к json api для приложения
    * 
    * @return [
    *   [
    *       'method' => 'метод',
    *       'right_codes' => [код действия, код действия, ...]
    *   ],
    *   ...
    * ]
    */
    public function getAppRights()
    {
        $rights = [
            'oauth.token'       => self::FULL_RIGHTS,
            'oauth.login'       => self::FULL_RIGHTS,
            'verification.checkCode'        => self::FULL_RIGHTS,
            'verification.sendCode'         => self::FULL_RIGHTS,
            'verification.resetSession'     => self::FULL_RIGHTS,
            'verification.sessionStart'     => self::FULL_RIGHTS,

            'banner.get'         => self::FULL_RIGHTS,
            'banner.getList'     => self::FULL_RIGHTS,
            
            'brand.get'         => self::FULL_RIGHTS,
            'brand.getList'     => self::FULL_RIGHTS,
            
            'category.get'      => self::FULL_RIGHTS,
            'category.getList'  => self::FULL_RIGHTS,
            
            'favorite.add'      => self::FULL_RIGHTS,
            'favorite.remove'   => self::FULL_RIGHTS,
            'favorite.clear'   => self::FULL_RIGHTS,
            'favorite.getList'  => self::FULL_RIGHTS,
            
            'product.get'           => self::FULL_RIGHTS,
            'product.getList'       => self::FULL_RIGHTS,
            'product.getOffersList' => self::FULL_RIGHTS,
            'product.getRecommendedList' => self::FULL_RIGHTS,
            'product.reserve'       => self::FULL_RIGHTS,
            
            'mobileSiteApp.config'  => self::FULL_RIGHTS,
            'mobileSiteApp.getExtendsJSON' => self::FULL_RIGHTS,
            
            'menu.getList'       => self::FULL_RIGHTS,
            'menu.get'           => self::FULL_RIGHTS,

            'article.get'           => self::FULL_RIGHTS,
            'article.getCategoryList'   => self::FULL_RIGHTS,
            'article.getList'           => self::FULL_RIGHTS,

            'payment.getList'    => [Payment\GetList::RIGHT_LOAD],
            'payment.deletePaymentMethod' => self::FULL_RIGHTS,
            'payment.getPaymentMethodsList' => [Payment\GetPaymentMethodsList::RIGHT_LOAD_SELF],
            'payment.setDefaultPaymentMethod' => self::FULL_RIGHTS,

            'delivery.getList'   => [Delivery\GetList::RIGHT_LOAD],
            
            'status.getList'   => self::FULL_RIGHTS,        
            
            'affiliate.getList'   => self::FULL_RIGHTS,
            'affiliate.set'   => self::FULL_RIGHTS,
            
            'push.registerToken' => self::FULL_RIGHTS,
            'push.getList'       => self::FULL_RIGHTS,
            'push.change'        => self::FULL_RIGHTS,
            
            'order.getList' => [Order\GetList::RIGHT_LOAD],
            'order.get'     => [Order\Get::RIGHT_LOAD],
            'order.update'     => [Order\Update::RIGHT_UPDATE],
            'order.getStatus'     => [Order\GetStatus::RIGHT_LOAD],

            'user.get'          => [User\Get::RIGHT_LOAD_SELF],
            'user.getAddresses' => self::FULL_RIGHTS,
            'user.getCourierList' => self::FULL_RIGHTS,
            'user.update' => [User\Update::RIGHT_LOAD_SELF],
            'user.delete' => self::FULL_RIGHTS,

            'cart.add'  => self::FULL_RIGHTS,
            'cart.clear'  => self::FULL_RIGHTS,
            'cart.getCartData'  => self::FULL_RIGHTS,
            'cart.oneClickCartFields'  => self::FULL_RIGHTS,
            'cart.oneClickCartSend'  => self::FULL_RIGHTS,
            'cart.repeatOrder'  => [Cart\RepeatOrder::RIGHT_LOAD],
            'cart.remove'  => self::FULL_RIGHTS,
            'cart.update'  => self::FULL_RIGHTS,
            
            'checkout.address'  => self::FULL_RIGHTS,
            'checkout.setAddress'  => self::FULL_RIGHTS,
            'checkout.setDelivery'  => self::FULL_RIGHTS,
            'checkout.setPayment'  => self::FULL_RIGHTS,
            'checkout.setAdditionalFields'  => self::FULL_RIGHTS,
            'checkout.deliveryPayment'  => self::FULL_RIGHTS,
            'checkout.getAddressListsInfo'  => self::FULL_RIGHTS,
            'checkout.getCartData'  => self::FULL_RIGHTS,
            'checkout.getOrderPickupPoints'  => self::FULL_RIGHTS,
            'checkout.init'  => self::FULL_RIGHTS,
            'checkout.confirm'  => self::FULL_RIGHTS,

            'comment.add'  => self::FULL_RIGHTS,
            'comment.getList'  => self::FULL_RIGHTS,

            'bonusCard.add' => self::FULL_RIGHTS,
            'bonusCard.get' => self::FULL_RIGHTS,
            'bonusCard.getFields' => self::FULL_RIGHTS,

            'support.getTopicList'   => self::FULL_RIGHTS,
            'support.getNewMessageCount' => [Support\GetNewMessageCount::RIGHT_LOAD],
            'support.deleteTopic' => [Support\DeleteTopic::RIGHT_LOAD],
            'support.getTopicMessages' => [Support\GetTopicMessages::RIGHT_LOAD],
            'support.sendMessage' => [Support\SendMessage::RIGHT_ADD],
            'support.createTopic' => [Support\CreateTopic::RIGHT_ADD],
        ];

        if (ModuleManager::staticModuleExists('OrderReview')) {
            $rights += [
                'order.getReview' => [OrderReviewOrder\GetReview::RIGHT_LOAD],
                'order.addReview' => [OrderReviewOrder\AddReview::RIGHT_ADD],
            ];
        }

        if (ModuleManager::staticModuleExists('Bonuses')) {
            $rights += ['user.getBonuses' => self::FULL_RIGHTS];
        }

        if (ModuleManager::staticModuleExists('TelegramShop')) {
            $rights += [
                'user.logout' => [TelegramUser\Logout::RIGHT_LOGOUT],
                'telegram.createInvoiceLink' => [Telegram\CreateInvoiceLink::RIGHT_LOAD],
            ];
        }

        if (ModuleManager::staticModuleExists('Stories')) {
            $rights += [
                'stories.getGroups' => [Stories\GetGroups::RIGHT_LOAD],
                'stories.getGroup' => [Stories\GetGroup::RIGHT_LOAD],
            ];
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
        return Loader::byModule($this)->allow_user_groups;
    }
    
    /**
    * Возвращает массив объектов Push уведомлений
    * 
    * @return \PushSender\Model\AbstractPushNotice[]
    */
    public function getPushNotices()
    {
        $push_notices = [
            new OrderChangeToUser,
            new MessageToUsers,
            new NewMessage
        ];

        if (ModuleManager::staticModuleExists('orderreview') && ModuleManager::staticModuleEnabled('orderreview')) {
            $push_notices[] = new OrderReviewPush();
        }

        if (ModuleManager::staticModuleExists('stories') && ModuleManager::staticModuleEnabled('stories')) {
            $push_notices[] = new NewStoryPush();
        }

        return $push_notices;
    }

    /**
     * Возвращает строковый идентификатор создателя заказа, при создании заказа через API
     *
     * @return string
     */
    public function getCreatorPlatformId()
    {
        return static::CREATOR_PLATFORM_ID;
    }

    /**
     * Ничего не добавляет в заказ,
     * так как данный тип приложения не предусматривает никаких доп.данных
     *
     * @param AbstractMethod $method
     * @param OrmOrder $order
     */
    public function addOrderExtraData(AbstractMethod $method, OrmOrder $order)
    {}
}