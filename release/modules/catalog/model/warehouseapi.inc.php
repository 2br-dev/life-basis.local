<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use Catalog\Model\Orm\WareHouse;
use RS\Config\Loader;
use RS\Config\Loader as ConfigLoader;
use RS\Event\Manager as EventManager;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;
use RS\Site\Manager as SiteManager;

class WareHouseApi extends EntityList
{
    protected static $cache_warehouse_stick = [];

    function __construct()
    {
        parent::__construct(new WareHouse(), [
            'multisite' => true,
            'nameField' => 'title',
            'aliasField' => 'alias',
            'defaultOrder' => 'sortn',
            'sortField' => 'sortn'
        ]);
    }

    /**
     * Возвращает необходимую информацию для отображения остатков по складам на сайте
     * - список складов
     * - количество диапазонов остатков
     *
     * @param bool $cache - использовать кэш
     * @return array
     */
    public static function getWarehouseStickInfo(bool $cache = true)
    {
        $site_id = SiteManager::getSiteId();

        if (!$cache || !isset(self::$cache_warehouse_stick[$site_id])) {
            $config = ConfigLoader::byModule('catalog');

            $result = [];

            $result['warehouses'] =  self::getAvailableWarehouses(true, false, true);
            $result['stick_ranges'] = range(1, count(explode(",", $config['warehouse_sticks'])));

            self::$cache_warehouse_stick[$site_id] = $result;
        }
        return self::$cache_warehouse_stick[$site_id];
    }

    /**
     * Получает склад используемый по умолчанию.
     * Это либо склад с отметкой "по умолчанию", либо первый склад в списке
     *
     * @return WareHouse
     */
    public static function getDefaultWareHouse()
    {
        static $default_warehouse;

        if ($default_warehouse === null) {
            $default_warehouse = Request::make()
                ->from(new WareHouse())
                ->where([
                    'site_id' => SiteManager::getSiteId(),
                ])
                ->orderby('default_house DESC, sortn')
                ->limit(1)
                ->object() ?: new WareHouse();
        }
        return $default_warehouse;
    }

    /**
     * Устанавливает по id склад по умолчанию
     *
     * @param integer $id - id склада
     * @return void
     */
    function setDefaultWareHouse($id)
    {
        $elem = $this->getById($id);
        $elem['default_house'] = 1;
        $elem->update();
    }

    /**
     * Возвращает полный список складов, не смотря на флаг публичности
     *
     * @return WareHouse[]
     */
    static function getWarehousesList()
    {
        $_this = new self();
        /** @var WareHouse[] $list */
        $list = $_this->getList();
        return $list;
    }

    /**
     * Возвращает список пунктов самовывоза
     *
     * @param array $add_first - добавляется в начало списка
     * @return string[]
     */
    static function staticPickupPointsSelectList(array $add_first = [])
    {
        $warehouse_api = new self();
        $warehouse_api->setFilter([
            'public' => 1,
            'checkout_public' => 1
        ]);

        $warehouses = $warehouse_api->getAssocList('id', 'title');

        return (!empty($add_first)) ? $add_first + $warehouses : $warehouses;
    }

    /**
     * Возвращает только пункты самовывоза
     *
     * @return WareHouse[]
     */
    static function getPickupWarehousesPoints()
    {
        return self::getAvailableWarehouses(false, true, true);
    }

    /**
     * Возвращает список доступных складов, с учётом дополнительных фильтров применённых через событие
     *
     * @param bool $only_public - только публичные склады
     * @param bool $only_checkout_public - только пункты самовывоза
     * @param bool $return_objects - если true вернёт объекты складов, иначе id складов
     * @return WareHouse[]|int[]
     */
    public static function getAvailableWarehouses($only_public = false, $only_checkout_public = false, $return_objects = false)
    {
        $warehouse_api = new self();
        $warehouse_api->setFilter('site_id', SiteManager::getSiteId());

        if ($only_public) {
            $warehouse_api->setFilter('public', 1);
        }
        if ($only_checkout_public) {
            $warehouse_api->setFilter('checkout_public', 1);
        }

        EventManager::fire('getwarehouses', [
            'warehouse_api' => $warehouse_api,
            'only_public' => $only_public,
            'only_checkout_public' => $only_checkout_public,
        ]);

        if ($return_objects) {
            /** @var WareHouse[] $warehouses */
            $warehouses = $warehouse_api->getList();
        } else {
            $warehouses = $warehouse_api->queryObj()->exec()->fetchSelected(null, 'id');
        }

        return $warehouses;
    }

    /**
     * Возвращает список складов, по которым можно отфильтровать
     * товары с учетом всех текущих параметров (Выбранный филиал, настройки)
     */
    public static function getFilterWarehouses($return_objects = false)
    {
        $warehouse_api = new self();
        $warehouse_api->setFilter('site_id', SiteManager::getSiteId());
        $warehouse_api->setFilter('public', 1);

        $catalog_config = Loader::byModule(__CLASS__);
        if ($catalog_config['affiliate_stock_restriction']) {
            EventManager::fire('getwarehouses', [
                'warehouse_api' => $warehouse_api,
                'only_public' => true,
                'only_checkout_public' => false,
            ]);
        }

        if ($return_objects) {
            /** @var WareHouse[] $warehouses */
            $warehouses = $warehouse_api->getList();
        } else {
            $warehouses = $warehouse_api->queryObj()->exec()->fetchSelected(null, 'id');
        }

        return $warehouses;
    }
}
