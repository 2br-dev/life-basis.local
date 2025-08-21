<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;

use RS\RemoteApp\AbstractAppType;
use RS\RemoteApp\Manager;
use ExternalApi\Model\Exception as ApiException;

/**
* Вспомогательные возможности для внешних API общего назначения
*/
class Utils
{
    /**
    * Возвращает значения свойств ORM объекта, которые разрешены для отдачи через API
    * 
    * @param mixed $orm_object - ORM объект
    * @param array $change_link_fields - поля, у которых нужно изменить относительные ссылки на абсолютные
    * @return array
    */
    public static function extractOrm(\RS\Orm\AbstractObject $orm_object, $change_link_fields = ['description', 'html', 'content'])
    {
        $result = [];
        
        foreach($orm_object->getProperties() as $key => $property) {
            if ($property->isVisible('app') 
                && !($property instanceof \RS\Orm\Type\UserTemplate)) 
            {
                if ($property instanceof \RS\Orm\Type\Image && $orm_object[$key]){
                   $result[$key] = \Catalog\Model\ApiUtils::prepareImagesSection($property) ; 
                }else{
                   $result[$key] =
                       in_array($key, $change_link_fields)
                           ? self::prepareLinks($orm_object[$key])
                           : $orm_object[$key];
                }
                
            }
        }
        
        return $result;
    }
    
    /**
    * Возвращает значения свойств ORM объектов в списке, которые разрешены для отдачи через API
    * 
    * @param array $list_of_orm_objects - массив объектов
    * @param string $index_key - указывается если необходим информация по определённому ключу
    * @return array
    */
    public static function extractOrmList($list_of_orm_objects, $index_key = null)
    {
        $result = [];
        foreach($list_of_orm_objects as $orm_object) {
            if ($index_key === null) {
                $result[] = self::extractOrm($orm_object);
            } else {
                $result[$orm_object[$index_key]] = self::extractOrm($orm_object);
            }
        }
        return $result;
    }

    /**
     * Изменяет относительные ссылки на абсолютные
     *
     * @param string $text
     * @return string
     */
    public static function prepareLinks($text)
    {
        if ($text) {
            $callback = function($matches) {
                $src = trim($matches[2],"'\"");
                if (mb_stripos($src, '://') === false) {
                    return $matches[1] . \RS\Site\Manager::getSite()->getAbsoluteUrl($src) . $matches[3];
                }else{
                    return $matches[0];
                }
            };

            $text = preg_replace_callback('/(<img[^>]*src=["\'])(.*?)(["\'][^>]*>)/i', $callback, $text);
            $text = preg_replace_callback('/(<video[^>]*src=["\'])(.*?)(["\'][^>]*>)/i', $callback, $text);
            $text = str_replace('autoplay="autoplay"', '', $text);
        }

        return $text;
    }
    
    
    /**
    * Возвращает значения свойств ORM объектов в списке, которые разрешены для отдачи через API
    * 
    * @param array $list_of_orm_objects - массив объектов
    * @return array
    */
    public static function extractOrmTreeList($list_of_orm_objects)
    {
        $result = [];
        foreach($list_of_orm_objects as $index_key=>$orm_object) {
            $result[$index_key] = self::extractOrm($orm_object->getObject());
            $result[$index_key]['child'] = [];
            if ($orm_object->getChildsCount()){
                $result[$index_key]['child'] = self::extractOrmTreeList($orm_object['child']);
            }
        }
        return $result;
    }
    
    /**
    * Возвращает PHPDoc комментарии к константам, т.к. в Reflection 
    * такого, к сожалению, на сегодняшний день нет
    * 
    * @param \ReflectionClass $reflection
    * @return array
    */
    public static function getConstantComments(\ReflectionClass $reflection)
    {
        $tokens = token_get_all(file_get_contents($reflection->getFileName()));
        
        $doc_comments = [];
        $doc = null;
        $isConst = false;        
        foreach($tokens as $n => $token) {
            if (!is_array($token)) continue;
            list($tokenType, $tokenValue) = $token;

            switch ($tokenType)
            {
                case T_WHITESPACE:
                case T_COMMENT:
                case T_LNUMBER:
                case T_CONSTANT_ENCAPSED_STRING:
                    break;

                case T_DOC_COMMENT:
                    $doc = $tokenValue;
                    break;

                case T_CONST:
                    $isConst = true;
                    break;

                case T_STRING:
                    if ($isConst && $doc) {
                        $doc_comments[$tokenValue] = $doc;
                        $doc = null;
                    }
                    break;
                default:
                    $doc = null;
                    $isConst = false;
                    break;
            } 
        }
        
        return $doc_comments;
    }
    
    /**
     * Изменяет ссылки в HTML на абсолютные
     *
     * @param string $body - HTML для редактирования
     * @return string
     */
    public static function prepareHTML($body)
    {
        $replace_function = function($matches) {
            $src = trim($matches[2],"'\"");
            if (mb_stripos($src, '://') === false) {
                if ((mb_stripos($src, 'mailto:') === false) && (mb_stripos($src, 'tel:') === false)) { //Если это ссылка не на E-mail и не на телефон
                    //Если путь относительный, значит фото локальное
                    $return = $matches[1] . \RS\Site\Manager::getSite()->getAbsoluteUrl($src) . $matches[3];
                }else{
                    $return = $matches[1].$src.$matches[3];
                }
            }else{
                $return = $matches[0];
            }

            return $return;
        };

        $body = preg_replace_callback('/(<img[^>]*src=["\'])(.*?)(["\'][^>]*>)/i', $replace_function, $body);
        $body = preg_replace_callback('/(style=["\'][^>]*url\()(.*?)(\))/i', $replace_function, $body);
        $body = preg_replace_callback('/(background=["\'])(.*?)(["\'])/i', $replace_function, $body);
        $body = preg_replace_callback('/(<a[^>]*href=["\'])(.*?)(["\'][^>]*>)/i', $replace_function, $body);

        $body = preg_replace('/(<img[^>]*)(width=["\'].*?["\'])([^>]*>)/i', "$1$3", $body);
        $body = preg_replace('/(<img[^>]*)(height=["\'].*?["\'])([^>]*>)/i', "$1$3", $body);

        return $body;
    }


    /**
     * Проверяет зарегистрировано ли в системе приложение по его секретному ключу и идентификатору, если нет то кидает исключение
     *
     * @param string $client_id - id клиентского приложения
     * @param string $client_secret - секретный ключ приложения
     *
     * @return AbstractAppType
     */
    public static function checkAppIsRegistered($client_id, $client_secret)
    {
        $app = Manager::getAppByType($client_id);

        if (!$app || !($app instanceof \ExternalApi\Model\App\InterfaceHasApi)) {
            throw new ApiException(t('Приложения с таким client_id не существует или оно не поддерживает работу с API'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }

        //Производим валидацию client_id и client_secret
        if (!$app || !$app->checkSecret($client_secret)) {
            throw new ApiException(t('Приложения с таким client_id не существует или неверный client_secret'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }

        return $app;
    }

    /**
     * Возвращает список адресов для API
     *
     * @return array
     */
    public static function getApiUrls()
    {
        $router = \RS\Router\Manager::obj();
        $urls = [];

        $urls['oAuthLoginUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'oauth.login']);

        $urls['verificationCheckCodeUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'verification.checkCode']);
        $urls['verificationSendCodeUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'verification.sendCode']);
        $urls['verificationSessionStartUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'verification.sessionStart']);
        $urls['verificationResetSessionUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'verification.resetSession']);

        $urls['userEmailRecoveryUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'user.emailRecovery']);
        $urls['userRegistrationInitUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'user.registrationInit']);
        $urls['userRegistrationUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'user.registration']);
        $urls['userGetAddressesUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'user.getAddresses']);
        $urls['userUpdateUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'user.update']);
        $urls['userAutocompleteAddressUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'user.autocompleteAddress']);
        $urls['userDeleteUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'user.delete']);
        $urls['userRecoveryUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'user.recovery']);
        $urls['userChangePasswordUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'user.changePassword']);
        $urls['userLogoutUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'user.Logout']);

        $urls['bannerGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'banner.getList']);

        $urls['brandGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'brand.getList']);
        $urls['brandGetUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'brand.get']);

        $urls['cartAddUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.add']);
        $urls['cartClearUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.clear']);
        $urls['cartRemoveUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.remove']);
        $urls['cartUpdateUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.update']);
        $urls['cartGetCartDataUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.getCartData']);
        $urls['cartChangeAmountUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.changeAmount']);
        $urls['cartRepeatOrderUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'cart.repeatOrder']);

        $urls['categoryGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'category.getList']);

        $urls['productGetUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'product.get']);
        $urls['productGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'product.getList']);
        $urls['productReserveUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'product.reserve']);
        $urls['productGetOffersListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'product.getOffersList']);
        $urls['productGetRecommendedListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'product.getRecommendedList']);
        $urls['productSearchUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'product.search']);

        $urls['checkoutInitUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.init']);
        $urls['checkoutGetAddressListsInfoUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.getAddressListsInfo']);
        $urls['checkoutGetCitiesListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.getCitiesList']);
        $urls['checkoutSetAddressUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.setAddress']);
        $urls['checkoutSetDeliveryUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.setDelivery']);
        $urls['checkoutSetPaymentUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.setPayment']);
        $urls['checkoutSetAdditionalFieldsUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.setAdditionalFields']);
        $urls['checkoutConfirmUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.confirm']);
        $urls['checkoutOnlinePayUrl'] = $router->getUrl('shop-front-onlinepay', ['Act'=>'pay']);
        $urls['checkoutCheckTransactionStatusUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'checkout.checkTransactionStatus']);

        $urls['deliveryGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'delivery.getList']);

        $urls['paymentGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'payment.getList']);
        $urls['paymentGetPaymentMethodsListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'payment.getPaymentMethodsList']);
        $urls['paymentSetDefaultPaymentMethodUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'payment.setDefaultPaymentMethod']);
        $urls['paymentDeletePaymentMethodUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'payment.deletePaymentMethod']);

        $urls['commentGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'comment.getList']);
        $urls['commentAddUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'comment.add']);
        $urls['commentCheckUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'comment.check']);

        $urls['favoriteAddUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'favorite.add']);
        $urls['favoriteRemoveUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'favorite.remove']);
        $urls['favoriteClearUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'favorite.clear']);
        $urls['favoriteGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'favorite.getList']);

        $urls['orderGetUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'order.get']);
        $urls['orderGetStatusUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'order.getStatus']);
        $urls['orderUpdateUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'order.update']);
        $urls['orderGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'order.getList']);

        $urls['affiliateGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'affiliate.getList']);
        $urls['affiliateGetLinkedWarehousesUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'affiliate.getLinkedWarehouses']);
        $urls['affiliateSetUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'affiliate.set']);

        $urls['multirequestRunUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'multirequest.run']);
        $urls['mobilesiteappGetSpecialBlockUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'mobilesiteapp.getSpecialBlock']);

        $urls['pushChangeUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'push.change']);
        $urls['pushGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'push.getlist']);
        $urls['pushRegisterTokenUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'push.registerToken']);

        $urls['menuGetUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'menu.get']);
        $urls['menuGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'menu.getList']);

        $urls['articleGetUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'article.get']);
        $urls['articleGetListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'article.getList']);
        $urls['articleGetCategoryListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'article.getCategoryList']);

        $urls['bonusCardAddUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'bonusCard.add']);
        $urls['bonusCardGetUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'bonusCard.get']);
        $urls['bonusCardGetFieldsUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'bonusCard.getFields']);

        $urls['supportGetTopicListUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'support.getTopicList']);
        $urls['supportCreateTopicUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'support.createTopic']);
        $urls['supportDeleteTopicUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'support.deleteTopic']);
        $urls['supportGetNewMessageCountUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'support.getNewMessageCount']);
        $urls['supportGetTopicMessagesUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'support.getTopicMessages']);
        $urls['supportSendMessageUrl'] = $router->getUrl('externalapi-front-apigate', ['method'=>'support.sendMessage']);

        return $urls;
    }
}
