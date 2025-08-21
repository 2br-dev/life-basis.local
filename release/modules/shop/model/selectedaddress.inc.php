<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model;

use Affiliate\Model\AffiliateApi;
use Main\Model\GeoIpApi;
use Partnership\Model\Api as PartnershipApi;
use RS\Config\Loader as ConfigLoader;
use RS\Event\Manager as EventManager;
use RS\Helper\Tools;
use RS\Http\Request as HttpRequest;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Region;

/**
 * Выбранный адрес, работает в рамках всего магазина
 */
class SelectedAddress
{
    const COOKIE_KEY = 'selected_address';

    /** @var Address */
    protected $address;

    protected function __construct()
    {
        $address = $this->loadAddressFromCookie();
        if (!$address) {
            $address = $this->loadAddressFromGeoIp();
        }
        if (!$address){
            $address = $this->loadAddressFromPartnerSite();
        }
        if (!$address){
            $address = $this->loadAddressFromAffiliate();
        }
        if (!$address) {
            $address = $this->loadAddressFromDefaultRegion();
        }
        if (!$address) {
            $address = new Address();
        }
        $this->setAddress($address);
    }

    /**
     * Возвращает имя ключа, в котором хранится выбранный адрес
     *
     * @return string
     */
    public function getCookieKey()
    {
        return defined('CUSTOM_SELECTED_ADDRESS_COOKIE_KEY')
            ? CUSTOM_SELECTED_ADDRESS_COOKIE_KEY : self::COOKIE_KEY;
    }

    /**
     * Статический вызов объекта
     *
     * @return static
     */
    public static function getInstance(): self
    {
        static $instance;
        if ($instance === null) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * Возвращает выбранный адрес
     *
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * Устанавливает выбранный адрес на основе региона
     *
     * @param Region $region - исходный регион
     * @return void
     */
    public function setAddressFromRegion(Region $region): void
    {
        $this->setAddress(Address::createFromRegion($region));
    }

    /**
     * Устанавливает выбранный адрес
     *
     * @param Address $address - устанавливаемый адрес
     * @return void
     */
    public function setAddress(Address $address): void
    {
        if ($this->address !== null) {
            // todo описать событие в документации
            $event_result = EventManager::fire('selectedaddress.change', [
                'old_address' => $this->address,
                'new_address' => $address,
            ])->getResult();

            $address = $event_result['new_address'];
        }

        $this->address = $address;
        $this->saveAddressInCookie();
    }

    /**
     * Загружает адрес из региона по умолчанию
     *
     * @return Address|null
     */
    protected function loadAddressFromDefaultRegion(): ?Address
    {
        $shop_config = ConfigLoader::byModule('shop');
        if ($shop_config['default_region_id']) {
            $city = null;
            $region = null;
            $country = null;

            /** @var Region $default */
            $default = (new OrmRequest())
                ->from(new Region())
                ->where([
                    'site_id' => SiteManager::getSiteId(),
                    'id' => $shop_config['default_region_id'],
                ])
            ->object();

            if (!empty($default['id'])) {
                return Address::createFromRegion($default);
            }
        }

        return null;
    }

    /**
     * Загружает адрес по геолокации
     *
     * @return Address|null
     */
    protected function loadAddressFromGeoIp(): ?Address
    {
        $shop_config = ConfigLoader::byModule('shop');
        if ($shop_config['use_geolocation_address']) {
            $geo_api = new GeoIpApi();
            $ip = HttpRequest::commonInstance()->server('REMOTE_ADDR');
            $city_name = $geo_api->getCityByIp($ip);
            if ($city_name) {
                /** @var Region $city */
                $city = OrmRequest::make()
                    ->from(new Region())
                    ->where([
                        'site_id' => SiteManager::getSiteId(),
                        'is_city' => 1,
                        'title' => $city_name,
                    ])
                    ->object();

                if (!empty($city['id'])) {
                    $region = $city->getParent();
                    $country = $region->getParent();

                    $address = new Address();
                    $address['country_id'] = $country['id'];
                    $address['region_id'] = $region['id'];
                    $address['city_id'] = $city['id'];
                    return $address;
                }
            }
        }

        return null;
    }

    /**
     * Загружает адрес по текущему партнёрскому сайту
     *
     * @return Address|null
     */
    protected function loadAddressFromPartnerSite(): ?Address
    {
        if (\RS\Module\Manager::staticModuleEnabled('partnership')) {
            $partner = PartnershipApi::getCurrentPartner();
            if (!empty($partner['city_id'])) {
                $city = new Region($partner['city_id']);
                if (!empty($city['id'])) {
                    $region = $city->getParent();
                    $country = $region->getParent();

                    $address = new Address();
                    $address['country_id'] = $country['id'];
                    $address['region_id'] = $region['id'];
                    $address['city_id'] = $city['id'];
                    return $address;
                }
            }
        }

        return null;
    }
    /**
     * Загружает адрес по филиалу
     *
     * @return Address|null
     */
    protected function loadAddressFromAffiliate(): ?Address
    {
        if (\RS\Module\Manager::staticModuleEnabled('affiliate')) {
            $affiliate = AffiliateApi::getCurrentAffiliate();
            if (!empty($affiliate['linked_region_id'])) {
                $city = new Region($affiliate['linked_region_id']);
                if (!empty($city['id'])) {
                    $region = $city->getParent();
                    $country = $region->getParent();

                    $address = new Address();
                    $address['country_id'] = $country['id'];
                    $address['region_id'] = $region['id'];
                    $address['city_id'] = $city['id'];
                    return $address;
                }
            }
        }

        return null;
    }

    /**
     * Загружает сохранённый адрес из cookie, в случае успеха возвращает true
     *
     * @return Address|null
     */
    protected function loadAddressFromCookie(): ?Address
    {
        if ($cookie = HttpRequest::commonInstance()->cookie($this->getCookieKey(), TYPE_STRING)) {
            if ($data = json_decode(htmlspecialchars_decode($cookie), true)) {
                if ($data['country_id'] || $data['region_id'] || $data['city_id']) {
                    $address = new Address();
                    $address['country_id'] = $data['country_id'] ?? null;
                    $address['region_id'] = $data['region_id'] ?? null;
                    $address['city_id'] = $data['city_id'] ?? null;

                    $address['country'] = $data['country'] ?? null;
                    $address['region'] = $data['region'] ?? null;
                    $address['city'] = $data['city'] ?? null;

                    return $address;
                }
            }
        }

        return null;
    }

    /**
     * Сохраняет выбраный адрес в cookie
     *
     * @return void
     */
    protected function saveAddressInCookie(): void
    {
        $address = $this->getAddress();
        $country = $address->getCountry();
        $region = $address->getRegion();
        $city = $address->getCity();

        $data = [
            'country_id' => $country['id'],
            'region_id' => $region['id'],
            'city_id' => $city['id'],
            'city' => $city['title'],
            'region' => $region['title'],
            'country' => $country['title']
        ];
        setcookie($this->getCookieKey(), json_encode($data, JSON_UNESCAPED_UNICODE), time() + 60 * 60 * 24 * 30, '/');
    }
}
