<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace MobileSiteApp\Model\ExternalApi\MobileSiteApp;

use ExternalApi\Model\Exception as ApiException;
use Mobilesiteapp\Model\OnBoardingApi;
use RS\Config\Loader;
use RS\Router\Manager;
use Shop\Config\File as ShopConfig;
use Shop\Model\Orm\Region;
use Shop\Model\BonusCardsApi;
use Shop\Model\PaymentApi;
use Shop\Model\PaymentType\InterfaceRecurringPayments;
use Support\Model\FilesType\SupportFiles;
use Users\Model\Api as UserApi;

/**
* Возвращает конфиг модуля мобильного приложения
*/
class Config extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;

    protected $token_require = false; //Токен не обязателен

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
            self::RIGHT_LOAD => t('Получение данных приложением')
        ];
    }

    /**
     * Пробует установить адрес по городу, который находится по IP адресу
     *
     * @return array
     * @throws \RS\Orm\Exception
     */
    function getAddressFromGeoCity()
    {
        /** @var ShopConfig $config */
        $config = \RS\Config\Loader::byModule('shop');
        $geo_api   = new \Main\Model\GeoIpApi();
        $city_name = $geo_api->getCityByIp($_SERVER['REMOTE_ADDR']); //Получим имя города по IP
        //Подгрузим из базы магистральный город и поробуем его найти
        $city = false;
        if ($config['require_city']) { //Если требуется указание города
            /** @var Region $city */
            $city = \RS\Orm\Request::make()
                ->from(new Region())
                ->where([
                    'site_id' => \RS\Site\Manager::getSiteId(),
                    'is_city' => 1,
                    'title' => $city_name
                ])->object();
        }

        if (empty($city['id']) && $config->getDefaultCityId()){ //Если по локации не нашли, но есть город по умолчанию
            $city = new Region($config->getDefaultCityId());
        }

        $address = [];
        if ($city){ //Если нашли город, то установим данные
            if (!$config['require_region'] && $config->getDefaultRegionId()) {
                $region = new Region($config->getDefaultRegionId());
            }else{
                $region = $city->getParent();
            }
            if (!$config['require_country'] && $config->getDefaultCountryId()) {
                $country = new Region($config->getDefaultCountryId());
            }else{
                $country = $region->getParent();
            }

            //Индекс города
            $zipcode = $city['zipcode'];
            if (!$config['require_zipcode'] && $config['default_zipcode']){
                $zipcode = $config['default_zipcode'];
            }

            //Добавим в адрес
            $address['zipcode']    = $zipcode;
            $address['country']    = $country['title'];
            $address['region']     = $region['title'];
            $address['city']       = $city['title'];
            $address['city_id']    = $city['id'];
            $address['region_id']  = $region['id'];
            $address['country_id'] = $country['id'];
        }else{
            if (!$config['require_region'] && $config->getDefaultRegionId()) {
               $region = new Region($config->getDefaultRegionId());
               $address['region'] = $region['title'];
               $address['region_id'] = $region['id'];
            }

            if (!$config['require_country'] && $config->getDefaultCountryId()) {
                $country = new Region($config->getDefaultCountryId());
                $address['country'] = $country['title'];
                $address['country_id'] = $country['id'];
            }

            if (!$config['require_zipcode'] && $config['default_zipcode']){
                $address['zipcode'] = $config['default_zipcode'];
            }
        }

        return $address;
    }

    /**
     * Возвращает true, если хоть один из способов оплаты использует рекуррентные платежи
     *
     * @return bool
     */
    function showPaymentCardsMenu(): bool
    {
        $payment_api = new PaymentApi();
        $payment_api->setFilter('public', 1);
        $payments = $payment_api->getList();

        if ($payments) {
            foreach ($payments as $payment) {
                $payment_type = $payment->getTypeObject();
                if ($payment_type instanceof InterfaceRecurringPayments) {
                    $recurring_payments_type = $payment_type->getRecurringPaymentsType();
                    if (
                        $recurring_payments_type == InterfaceRecurringPayments::RECURRING_TYPE_SAVE_METHOD
                        || $recurring_payments_type == InterfaceRecurringPayments::RECURRING_TYPE_ONLY_SAVE_METHOD) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Возвращает фразы, которые используются в приложении
     *
     * @return array
     */
    function getAppPhrases()
    {
        return [
            "address.house" => t('д.'),
            "address.block" => t('корпус'),
            "address.floor" => t('этаж'),
            "address.apartment" => t('кв./офис'),
            "address.entrance" => t('подъезд'),
            "address.entryphone" => t('домофон'),
            "address.subway" => t('метро'),
            "delivery.freeCost" => t('бесплатно'),
            "productitem.configuration" => t('Комплектация'),
            "product.configurations" => t('Комплектации'),
            "alert.clearCart" => t('Вы уверены, что желаете очистить корзину?'),
            "alert.clearFavorite" => t('Вы уверены, что желаете очистить избранное?'),
            "alert.deleteProfileTitle" => t('Вы уверены, что хотите удалить ваш профиль?'),
            "alert.deleteProfileMessage" => t('Без профиля вы не сможете совершать заказы в приложении.'),
            "alert.deleteRequestProfileTitle" => t('Запрос на удаления профиля успешно отправлен'),
            "alert.deleteRequestProfileMessage" => t('Ваш запрос будет обработан в ближайшее время'),
            "alert.deleteTopics" => t('Вы действительно хотите удалить переписку по теме?'),
            "alert.adultConfirmTitle" => t('Вам уже исполнилось 18 лет?'),
            "alert.adultConfirmMessage" => t('Пожалуйста, подтвердите своё совершеннолетие для продолжения просмотра.'),
            "alert.appNewVersion" => t('Доступна новая версия приложения'),
            "alert.requestError" => t('Ошибка запроса'),
            "alert.emptyResponse" => t('пустой ответ.'),
            "alert.favoriteAdd" => t('Товар добавлен в избранное'),
            "alert.favoriteDelete" => t('Товар удален из избранного'),
            "alert.yes" => t('Да'),
            "alert.no" => t('Нет'),
            "alert.success" => t('Успешно сохранено'),
            "alert.cartAdd" => t('Товар добавлен в корзину'),
            "appgallery.permissionGalleryTitle" => t('Нет доступа к галерее'),
            "appgallery.permissionGalleryMessage" => t('Для выбора изображения из галереи необходимо предоставить разрешение в настройках приложения.'),
            "appgallery.permissionCameraTitle" => t('Нет доступа к камере'),
            "appgallery.permissionCameraMessage" => t('Для использования камеры необходимо предоставить разрешение в настройках приложения.'),
            "fileupload.attachDescription" => t('Прикрепить файл'),
            "setamount.availableQuantity" => t('Доступное количество для заказа'),
            "button.open" => t('Развернуть'),
            "button.close" => t('Свернуть'),
            "button.update" => t('Обновить'),
            "button.tryAgain" => t('Попробовать еще'),
            "additionalfields.field" => t('Поле'),
            "additionalfields.required" => t('обязательное для заполнения!'),
            "commentadd.success" => t('Спасибо за Ваш комментарий, он будет опубликован после проверки модератором!'),
            "status.noConnectionTitle" => t('Нет подключения'),
            "status.noConnectionDescription" => t('Проверьте интернет-подключение и попробуйте еще раз'),
            "status.noConnectionMessageFirst" => t('Нет подключения к интернету'),
            "status.noConnectionMessageSecond" => t('Все еще нет подключения к интернету'),
            "status.noConnectionMessageThird" => t('Попробуйте позже, пока интернета нет'),
            "auth.enterCode" => t('Введите код'),
            "auth.somethingWrong" => t('Что-то пошло не так, повторите позднее'),
            "auth.authorizationExpired" => t('Ваша авторизация истекла, пожалуйста авторизуйтесь заново'),
            "file.byte" => t('байт'),
            "file.kilobyte" => t('Кб'),
            "file.megabyte" => t('Мб'),
            "file.gigabyte" => t('Гб'),
            "file.terabyte" => t('Тб'),
            "file.petabyte" => t('Пб'),
            "file.failedUpload" => t('Не удалось загрузить файл'),
            "sort.sortn" => t('По умолчанию'),
            "sort.cost" => t('Увеличение цены'),
            "sort.costDesc" => t('Уменьшение цены'),
            "sort.rating" => t('По рейтингу'),
            "sort.title" => t('По названию'),
            "sort.dateOfDesc" => t('По новизне'),
            "sort.numDesc" => t('По наличию'),
            "request.platformNotFound" => t('Платформа ReadyScript не найдена'),
            "request.errorMessageNotFound" => t('Не удается подключиться к API. Неверный API ключ или отключен модуль "Внешнее API" в интернет-магазине'),
            "request.errorMessageCheckDomainAndProtocol" => t('Не удается соединиться с сайтом. Проверьте корректность доменного имени и протокола'),
            "request.errorMessageEstablishConnection" => t('Невозможно установить соединение с сервером'),
            "request.errorMessageNotPossibleConnectStore" => t('К сожалению, не удалось подключиться к интернет-магазину.'),
            "request.errorMessageCheckUpdate" => t('Проверьте, установлены ли у вас последние  обновления.'),
            "push.toUser" => t('(пользователю)'),
            "app.unActive" => t('Мобильное приложение больше не активно.'),
            "app.needUpdate" => t('Ваша версия приложения больше не поддерживается, установите новую версию приложения.')
        ];
    }

    /**
     * Возвращает конфигурационные сведения для модуля мобильного приложения для сайта
     * ---
     * Секция configs возвращает требуемые конфиги модулей на сайте со значениями
     * Секция additional_fields возвращает массив групп дополнительных полей из разных модулей
     *
     * @param string $token Авторизационный токен
     * @param string $client_id id приложения
     * @param string $client_secret секретный ключ приложения
     * @param string $lat координата широты
     * @param string $lon координата долготы
     *
     * @example GET /api/methods/mobilesiteapp.config?client_id=mobilesiteapp&client_secret=4f3de925df123b6bd0ed5dacc4e8819aa87ae5e6
     *
     * Ответ:
     * <pre>
     * {
     *        "response": {
     *              "site": {
     *                   "id": "1",
     *                   "title": "Сайт test22.local",
     *                   "full_title": "Сайт test22.local",
     *                   "domains": "test22.local",
     *                   "folder": "",
     *                   "language": "ru",
     *                   "default": "0",
     *                   "update_robots_txt": null,
     *                   "redirect_to_main_domain": "0",
     *                   "redirect_to_https": "0",
     *                   "theme": null
     *               },
     *               "configs" : {
     *                "shop" : {
     *                   "name": "Магазин",
     *                   "description": "Модуль предоставляет возможность оформлять и администрировать заказы, управлять корзиной",
     *                   "version": "2.0.0.177",
     *                   "core_version": null,
     *                   "author": "ReadyScript lab.",
     *                   "enabled": 1,
     *                   "basketminlimit": 0,
     *                   "check_quantity": 1,
     *                   "first_order_status": 2,
     *                   "discount_code_len": 10,
     *                   "user_orders_page_size": 10,
     *                   "use_personal_account": 1,
     *                   "reservation": 1,
     *                   "allow_concomitant_count_edit": 0,
     *                   "source_cost": 0,
     *                   "auto_change_status": 0,
     *                   "auto_send_supply_notice": 1,
     *                   "courier_user_group": "0",
     *                   "require_address": 1,
     *                   "require_zipcode": 0,
     *                   "use_geolocation_address": 1,
     *                   "require_email_in_noregister": 1,
     *                   "require_phone_in_noregister": 0,
     *                   "require_license_agree": 0,
     *                   "license_agreement": "",
     *                   "use_generated_order_num": 0,
     *                   "generated_ordernum_mask": "{n}",
     *                   "generated_ordernum_numbers": 6,
     *                   "hide_delivery": 0,
     *                   "hide_payment": 0,
     *                   "manager_group": null,
     *                   "set_random_manager": null
     *                },
     *                ...
     *            },
     *            "geolocation": {
     *                "IP": "214.205.45.111",
     *                "address": {
     *                    "zipcode": "101000",
     *                    "country": "Россия",
     *                    "region": "Московская область",
     *                    "city": "Москва",
     *                    "city_id": "1347",
     *                    "region_id": "1240",
     *                    "country_id": "1208"
     *                 }
     *            },
     *            "additional_fields" : {
     *                 "order": [
     *                  {
     *                      "alias": "pole",
     *                      "maxlength": "",
     *                      "necessary": "1",
     *                      "title": "Поле",
     *                      "type": "string",
     *                      "values": "",
     *                      "val": "",
     *                      "current_val": ""
     *                  },
     *                  {
     *                       "alias": "text",
     *                       "maxlength": "",
     *                       "necessary": "",
     *                       "title": "Текст",
     *                       "type": "text",
     *                       "values": "",
     *                       "val": "",
     *                       "current_val": ""
     *                   },
     *                   {
     *                       "alias": "spisok",
     *                       "maxlength": "",
     *                       "necessary": "",
     *                       "title": "Список",
     *                       "type": "list",
     *                       "values": "10 лет, 20 лет, 30 лет",
     *                       "val": "20 лет",
     *                       "current_val": "20 лет"
     *                   },
     *                   {
     *                       "alias": "yesno",
     *                       "maxlength": "",
     *                       "necessary": "",
     *                       "title": "Да нет",
     *                       "type": "bool",
     *                       "values": "",
     *                       "val": false,
     *                       "current_val": "Нет"
     *                   }
     *                ],
     *                ...
     *            },
     *           "current_affiliate" : {
     *                "id": "1",
     *                "title": "Краснодарский край",
     *                "alias": "krasnodarskiy-kray",
     *                "parent_id": "0",
     *                "clickable": "0",
     *                "cost_id": "0",
     *                "short_contacts": "Этот регион называется Краснодарский край",
     *                "contacts": "<p>В нашем региона ресположено множество филиалов нашей сети</p>",
     *                "_geo": null,
     *                "skip_geolocation": "0",
     *                "is_default": "0",
     *                "is_highlight": "0",
     *                "public": "1",
     *                "meta_title": "",
     *                "meta_keywords": "",
     *                "meta_description": "",
     *           },
     *           "affiliate_list" : [
     *             {
     *                "id": "1",
     *                "title": "Краснодарский край",
     *                "alias": "krasnodarskiy-kray",
     *                "parent_id": "0",
     *                "clickable": "0",
     *                "cost_id": "0",
     *                "short_contacts": "Этот регион называется Краснодарский край",
     *                "contacts": "<p>В нашем региона ресположено множество филиалов нашей сети</p>",
     *                "_geo": null,
     *                "skip_geolocation": "0",
     *                "is_default": "0",
     *                "is_highlight": "0",
     *                "public": "1",
     *                "meta_title": "",
     *                "meta_keywords": "",
     *                "meta_description": "",
     *                "child": [
     *                    {
     *                        "id": "2",
     *                        "title": "Краснодар",
     *                        "alias": "krasnodar",
     *                        "parent_id": "1",
     *                        "clickable": "1",
     *                        "cost_id": "0",
     *                        "short_contacts": "Основной склад",
     *                        "contacts": "<p>В Краснодаре нохотся штаб квартира нашей компании</p>",
     *                        "_geo": null,
     *                        "skip_geolocation": "0",
     *                        "is_default": "1",
     *                        "is_highlight": "1",
     *                        "public": "1",
     *                        "meta_title": "",
     *                        "meta_keywords": "",
     *                        "meta_description": "",
     *                        "child": []
     *                    }
     *                ]
     *             }
     *           ]
     *        }
     *    }
     * </pre>
     * Возращает, пустой массив ошибок, если всё успешно
     * @return array
     * @throws \RS\Exception
     */
    protected function process($client_id, $client_secret, $token = null, $lat = "0", $lon = "0")
    {
        //Проверим предварительно приложение
        $app = \RS\RemoteApp\Manager::getAppByType($client_id);
        $onboarding_api = new OnBoardingApi();
        $user_api = new UserApi();
        $template_manager = new \MobileSiteApp\Model\TemplateManager();
        $mobilesiteapp_config = Loader::byModule($this);
        $bonus_card_api = new BonusCardsApi();
        $router = Manager::obj();

        if (!$app || !($app instanceof \ExternalApi\Model\App\InterfaceHasApi)) {
            throw new ApiException(t('Приложения с таким client_id не существует или оно не поддерживает работу с API'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }
        
        //Производим валидацию client_id и client_secret
        if (!$app || !$app->checkSecret($client_secret)) {
            throw new ApiException(t('Приложения с таким client_id не существует или неверный client_secret'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }

        $is_have_shop_config = (\RS\Module\Manager::staticModuleExists('shop') && \RS\Module\Manager::staticModuleEnabled('shop'));
        
        //Получим нужные конфиги модулей
        $configs                  = [];
        $configs['catalog']       = \ExternalApi\Model\Utils::extractOrm(\RS\Config\Loader::byModule('catalog'));  //Каталог товаров
        $configs['mobilesiteapp'] = \ExternalApi\Model\Utils::extractOrm(\RS\Config\Loader::byModule('mobilesiteapp'));  //Мобильное приложение
        $configs['users'] = \ExternalApi\Model\Utils::extractOrm(\RS\Config\Loader::byModule('users'));  // Пользователи и группы
        $configs['support'] = \ExternalApi\Model\Utils::extractOrm(\RS\Config\Loader::byModule('support'));  // Пользователи и группы
        //Модуль магазин
        if ($is_have_shop_config){
            $configs['shop']= \ExternalApi\Model\Utils::extractOrm(\RS\Config\Loader::byModule('shop'));  //Магазин
        }
        
        $site = \RS\Site\Manager::getSite();        
        $response['response']['site']        = \ExternalApi\Model\Utils::extractOrm($site); 
        $response['response']['site_config'] = \ExternalApi\Model\Utils::extractOrm(\RS\Config\Loader::getSiteConfig()); 
        $response['response']['configs']     = $configs; 
        
        //Геолокация
        $response['response']['geolocation']['IP']      = $_SERVER['REMOTE_ADDR']; 
        $response['response']['geolocation']['address'] = ($is_have_shop_config) ? $this->getAddressFromGeoCity() : [];
        
        //Дополнительные поля
        if ($is_have_shop_config) {
            $response['response']['additional_fields']['order'] = \Shop\Model\ApiUtils::getAdditionalOrderFieldsSection();
        }
        $response['response']['additional_fields']['buyoneclick'] = \Catalog\Model\ApiUtils::getAdditionalBuyOneClickFieldsSection();
        $response['response']['additional_fields']['user']        = \Users\Model\ApiUtils::getAdditionalUserFieldsSection();
        $response['response']['additional_fields']['user_info']   = \Users\Model\ApiUtils::getAdditionalUserInfoFieldsSection();

        //Филиалы в городах
        $current_affiliate = null;
        $affiliate_list    = [];

        if (\RS\Module\Manager::staticModuleExists('affiliate') && \RS\Module\Manager::staticModuleEnabled('affiliate')){
            $current_affiliate = $default_affiliate = \ExternalApi\Model\Utils::extractOrm(\Affiliate\Model\AffiliateApi::getCurrentAffiliate());
            if ($lat && $lon){ //Если указаны координаты, то получим ближайший филиал
                $current_affiliate = \ExternalApi\Model\Utils::extractOrm(new \Affiliate\Model\Orm\Affiliate(\Affiliate\Model\AffiliateApi::getNearAffiliate($lat, $lon)));
            }

            $api = new \Affiliate\Model\AffiliateApi();
            $affiliate_list = \ExternalApi\Model\Utils::extractOrmTreeList($api->setFilter('public', 1)->getTreeList());

            //Если филиал определить не удалось, то подставим ближайший
            if (!empty($affiliate_list) && !$current_affiliate['id']){
                $current_affiliate = $default_affiliate;
            }
        }

        if ($this->params && $this->params['v'] >= '2') {
            //Имя сессии
            $response['response']['session_name'] = \Setup::getSessionName();

            $response['response']['api_urls'] = \ExternalApi\Model\Utils::getApiUrls();

            $response['response']['onboarding'] = \ExternalApi\Model\Utils::extractOrmList($onboarding_api->getList());
            
            $response['response']['clientsiteapp_templates'] = $template_manager->getClientSiteAppTemplates($this->params['v']);

            $response['response']['auth_login_placeholder'] = $user_api->getAuthLoginPlaceholder();

            $response['response']['show_payment_cards_menu'] = $this->showPaymentCardsMenu();

            $response['response']['support_urls'] = [
                'upload' => $router->getUrl('files-front-upload', ['linkType' => SupportFiles::getShortName(), 'Act' => 'upload'], true),
                'remove' => $router->getUrl('files-front-upload', ['linkType' => SupportFiles::getShortName(), 'Act' => 'remove'], true),
            ];

            if ($mobilesiteapp_config['enable_bonus_card']) {
                $response['response']['bonus_card_additional_fields'] = $bonus_card_api->getAdditionalFields();
            }
        }

        $response['response']['current_affiliate'] = $current_affiliate;
        $response['response']['affiliate_list'] = $affiliate_list;
        $response['response']['app_phrases'] = $this->getAppPhrases();

        return $response;
    }
}
