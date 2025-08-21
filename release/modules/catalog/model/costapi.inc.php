<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use Catalog\Model\Behavior\UsersUser;
use Catalog\Model\Orm\Currency;
use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\Typecost;
use RS\Application\Auth;
use RS\Config\Loader as ConfigLoader;
use RS\Db\Adapter as DbAdapter;
use RS\Db\Exception as DbException;
use RS\Exception as RSException;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\AbstractObject;
use RS\Orm\Request;
use RS\Site\Manager as SiteManager;
use RS\Orm\Request as OrmRequest;
use Site\Model\Orm\Site;
use Users\Model\Orm\User;

class CostApi extends EntityList
{
    const ROUND = 'round';
    const CEIL = 'ceil';
    const FLOOR = 'floor';

    protected static $session_old_cost_id = [];
    protected static $session_default_cost_id;
    protected static $full_costlist = [];

    function __construct()
    {
        parent::__construct(new Orm\Typecost, [
            'multisite' => true,
            'nameField' => 'title',
            'defaultOrder' => 'id'
        ]);
    }

    /**
     * Аналог getSelectList, только для статичского вызова
     *
     * @param array $first - значения, которые нужно добавить в начало списка
     * @return array
     */
    static function staticSelectList($first = [])
    {
        if ($first === true) { // для совместимости
            $first = [0 => t('Не выбрано')];
        }
        return parent::staticSelectList($first);
    }

    /**
     * Возвращает подготовленный для отображения в таблице массив данных
     *
     * @param int $page - номер страницы
     * @param int $size - кол-во элементов на странице
     * @return Orm\Typecost[]
     */
    function getTableList($page = 0, $size = 0)
    {
        $config = ConfigLoader::byModule($this);

        /** @var Orm\Typecost[] $list */
        $list = $this->getList($page, $size);

        foreach ($list as $item) {
            if ($item['type'] == 'manual') {
                $item['_type_text'] = t('Задается вручную');
            } else {
                $val_type = ($item['val_type'] == 'sum') ? t(' руб') : '%';
                $depend_cost = $item->getDependCost();
                $item['_type_text'] = t("Вычисляется автоматически: <strong>{$item['val_znak']}{$item['val']}{$val_type}</strong> от {$depend_cost['title']}");
            }

            if ($item['id'] == $config['default_cost']) {
                $item['title'] = t("<strong>{$item['title']}</strong> (по умолчанию)");
            }
        }
        return $list;
    }

    /**
     * Возвращает список цен. Из списка исключаются автовычесляемые цены и редактируемая сейчас.
     */
    public static function getManualCostList()
    {
        $obj = new self();
        //Отображаем только не автоматически вычесляемые типы цен
        $obj->setFilter('type', 'manual');

        return $obj->getSelectList();
    }

    /**
     * Возвращает список цен для селектора в меню пользователи
     *
     * @return array
     */
    public static function getUserSelectList()
    {
        $sites = OrmRequest::make()
            ->from(new Site())
            ->orderby('title ASC')
            ->objects();

        foreach ($sites as $k => $site) {
            $prices = OrmRequest::make()
                ->from(new Orm\Typecost())
                ->where([
                    'site_id' => $site['id']
                ])
                ->orderby('title ASC')
                ->objects();

            $prices = [-1 => [
                    'id' => 0,
                    'title' => t('По умолчанию')
                ]] + $prices;
            $sites[$k]['prices'] = $prices;
        }

        return $sites;
    }

    /**
     * Заполняет массив с полями цен и сайтов, значениями цен установленными для данного пользователя
     *
     * @param AbstractObject $user
     * @param array $sites
     * @return array
     */
    public static function fillUsersPriceList(AbstractObject $user, array $sites)
    {
        if (is_numeric($user['cost_id'])) {
            //Для совместимости с предыдущей версией
            $cost_array = [SiteManager::getSiteId() => $user['cost_id']];
        } else {
            $cost_array = @unserialize($user['cost_id']); //Массив текущими ценами пользователя
        }

        if (!empty($sites) && $cost_array) {
            foreach ($sites as $key => $site) { //Походимся по сайтам

                if (!empty($site['prices'])) {
                    foreach ($site['prices'] as $keyp => $price) { //Походимся по ценам
                        if ($cost_array[$site['id']] == $price['id']) {
                            $sites[$key]['prices'][$keyp]['selected'] = true;
                        }
                    }
                }
            }
        }

        return $sites;
    }

    /**
     * Возвращает расчитанные "автоматические" цены на основе "заданных вручную"
     *
     * @param array $cost_values
     * @return array массив цен
     */
    function getCalculatedCostList($cost_values)
    {
        $this->fillCostList();

        //Устанавливаем вычисляемые значения.
        $cost_val = [];
        if (!empty(self::$full_costlist[$this->site_context])) {
            foreach (self::$full_costlist[$this->site_context] as $onecost) {
                if ($onecost['type'] == 'auto') {
                    $source_val = isset($cost_values[$onecost['depend']]) ? $cost_values[$onecost['depend']] : 0;
                } else {
                    $source_val = isset($cost_values[$onecost['id']]) ? $cost_values[$onecost['id']] : 0;
                }

                $cost_val[$onecost['id']] = $this->calculateAutoCost($source_val, $onecost);
            }
        }
        return $cost_val;
    }

    /**
     * Высчитывает автоматическую цену из исходной
     *
     * @param double $source_cost исходная цена
     * @param Typecost $cost_arr - объект типа цены
     * @return double возвращает высчитанную цену
     */
    function calculateAutoCost($source_cost, $cost_arr)
    {
        /** @var $cost_arr Typecost */
        if ($cost_arr['type'] == 'auto') {
            if ($cost_arr['val_type'] == 'sum') {
                $dst_val = (float)($cost_arr['val_znak'].$cost_arr['val']);
            } else {
                $dst_val = $source_cost * ($cost_arr['val_znak'].($cost_arr['val']/100));
            }
            $source_cost = $source_cost + $dst_val;
            
            $source_cost = $cost_arr->getRounded($source_cost); //Округление
            $source_cost = number_format($source_cost, 2, '.', '');
        }
        return $source_cost;
    }

    /**
     * Устанавливает тип цены по умолчанию (если ни одна из групп пользователя не сопоставлена с другой ценой)
     *
     * @param int $cost_id - id типа цены
     */
    function setDefaultCost($cost_id)
    {
        $config = ConfigLoader::byModule($this);
        $config['default_cost'] = $cost_id;
        $config->update();
    }
    
    /**
     * Заполняет кэш существующих типов цен
     *
     * @return void
     */
    function fillCostList()
    {
        if (!isset(self::$full_costlist[$this->site_context])) {
            $this->clearFilter();
            self::$full_costlist[$this->site_context] = $this->getAssocList('id');
        }
    }

    /**
     * Возвращает id типа цен, от которой зависит цена $type_id.
     *
     * @param int $type_id
     * @return integer
     */
    function getManualType($type_id)
    {
        $this->fillCostList();
        return (self::$full_costlist[$this->site_context][$type_id]['type'] == 'manual')
            ? $type_id : self::$full_costlist[$this->site_context][$type_id]['depend'];
    }

    /**
     * Возвращает объект типа цены по ID
     *
     * @param integer $id
     * @return \Catalog\Model\Orm\TypeCost
     */
    function getCostById($id)
    {
        $this->fillCostList();
        return self::$full_costlist[$this->site_context][$id];
    }

    /**
     * Возвращает исходную цену из выщитанной автоматически для текущего Пользователя
     *
     * @param mixed $value - Сумма
     * @return float
     */
    function correctCost($value)
    {
        $value = (float)$value;
        $this->fillCostList();
        $cost_id = self::getUserCost();
        $cost = self::$full_costlist[$this->site_context][$cost_id];
        /** @var Typecost $cost */
        if ($cost['type'] == 'auto') {
            if ($cost['val_type'] == 'sum') {
                //Если у цены установлено прибавить 300, значит здесь мы должы отнять 300
                $dst_val = (float)($cost['val_znak'].$cost['val']);
                $value  = $value - $dst_val;                
            } else {
                //Если у цены установлено прибавить 30%, значит здесь мы должы отнять 30%
                $value  = $value / abs((float)($cost['val_znak'].'1') + (float)($cost['val']/100));
            }
           $value  = number_format( $value, 2, '.', '' );
        }
        return $value;
    }

    /**
     * Возвращает id цены пользователя, если таковая установлена, иначе id цены по-умолчанию (из настроект модуля Каталог)
     *
     * @param User $user - пользователь
     * @return int
     */
    public static function getUserCost(User $user = null)
    {
        if ($user === null) {
            $user = Auth::getCurrentUser();
        }
        $site_id = SiteManager::getSiteId();
        /** @var UsersUser $user */
        $cost_id = $user->getUserTypeCostId($site_id); // метод добавлен через behavior
        if (!$cost_id) {
            $cost_id = self::getDefaultCostId();
        }

        return $cost_id;
    }

    /**
     * Устанавливает тип цен по-умолчанию для одной сессии выполнения скрипта
     *
     * @param int $cost_id
     * @return void
     */
    public static function setSessionDefaultCost($cost_id)
    {
        self::$session_default_cost_id = $cost_id;
    }

    /**
     * Возвращает ID типа цен
     *
     * @return integer
     */
    public static function getDefaultCostId()
    {
        if (!self::$session_default_cost_id) {
            $config = ConfigLoader::byModule(__CLASS__);
            self::$session_default_cost_id = $config['default_cost'];
        }

        return self::$session_default_cost_id;
    }

    /**
     * Возвращает id Старой цены или false, в случае, если такой цены - нет
     *
     * @param integer $cost_id Основной тип цены
     * @return integer | bool(false)
     */
    public static function getOldCostId($cost_id = 0)
    {
        if (!isset(self::$session_old_cost_id[$cost_id])) {
            $cost_id = $cost_id ?: self::getUserCost();
            $user_cost = new Typecost($cost_id);
            if (!empty($user_cost['old_cost'])) {
                $old_cost_id = $user_cost['old_cost'];
            } else {
                $config = ConfigLoader::byModule(__CLASS__);
                $old_cost_id = $config['old_cost'];
            }

            self::$session_old_cost_id[$cost_id] = $old_cost_id;
        }
        return self::$session_old_cost_id[$cost_id];
    }

    /**
     * Устанавливает тип цен по-умолчанию для одной сессии выполнения скрипта
     *
     * @param integer $old_cost_id Идентийикатор старой цены
     * @param integer $cost_id Идентификатор для какой цены устанавливается старая цена
     */
    public static function setSessionOldCost($old_cost_id, $cost_id = 0)
    {
        self::$session_old_cost_id[$cost_id] = $old_cost_id;
    }

    /**
     * Пересчитывает цены всех товаров сайта с учетом курсов валют
     *
     * @param integer | null $site_id - id сайта, на котором необходимо пересчитать цены. Если Null, то на текущем
     * @param Currency $currency - объект валюты для которой ведётся пересчёт
     * @return void
     * @throws DbException
     * @throws RSException
     */
    public static function recalculateCosts($site_id = null, $currency = null)
    {
        if (!$site_id) {
            $site_id = SiteManager::getSiteId();
        }
        $xcost = new Orm\Xcost();
        $q = OrmRequest::make()
            ->select('X.*')
            ->from(new Orm\Product(), 'P')
            ->join($xcost, 'P.id = X.product_id', 'X')
            ->where([
                'P.site_id' => $site_id,
            ]);

        if ($currency){//Если валюта задана
            $q->where('X.cost_original_currency='.$currency['id']);
        }else{
            $q->where('X.cost_original_currency>0');
        }

        $offset = 0;
        $pagesize = 1000;
        $res = $q->limit($offset, $pagesize)->exec();
        while( $res->rowCount() ) {
            $values = [];
            $product_ids = [];
            while($row = $res->fetchRow()) {
                if (!isset($currency_cache[$site_id][$row['cost_original_currency']])) {
                    $currency_cache[$site_id][$row['cost_original_currency']] =
                        new Currency($row['cost_original_currency'], false);
                }
                $curr = $currency_cache[$site_id][$row['cost_original_currency']];
                $cost_val = $curr['id'] ? CurrencyApi::convertToBase($row['cost_original_val'], $curr) : $row['cost_original_val'];

                $values[] = "({$row['product_id']},{$row['cost_id']},'{$cost_val}')";
                $product_ids[] = $row['product_id'];
            }
            if ($values) {
                $sql = "INSERT INTO " . $xcost->_getTable() . "(product_id, cost_id, cost_val) VALUES" . implode(',', $values) .
                    " ON DUPLICATE KEY UPDATE cost_val = VALUES(cost_val)";

                DbAdapter::sqlExec($sql);
            }

            if ($product_ids) { //Очищаем кэш комплектаций
                Request::make()
                    ->update(Product::_getTable())
                    ->set(['offers_json' => ''])
                    ->whereIn('id', $product_ids)
                    ->exec();
            }

            $offset += $pagesize;
            $res = $q->limit($offset, $pagesize)->exec();
        }

        //Обновляем цены в комплектациях.
        $offset = 0;
        $pageSize = 400;
        $q = OrmRequest::make()
            ->select('DISTINCT O.*')
            ->from(new Orm\Offer(), 'O')
            ->where(['O.site_id' => $site_id])
            ->limit($offset, $pageSize);

        if ($currency){//Если валюта задана
            $q->join(new Orm\Product(), 'P.id = O.product_id', 'P')
                ->join($xcost, 'P.id = X.product_id', 'X')
                ->where('X.cost_original_currency='.$currency['id']);
        }

        while($list = $q->objects()) {
            foreach($list as $offer) {
                $offer->update();
            }
            $offset += $pageSize;
            $q->offset($offset);
        }
    }

    /**
     * Возвращает округлённую цену
     *
     * @param float $cost - цена
     * @param string $round_type - тип округления (ceil|floor|round)
     * @param integer|null $site_id - ID сайта
     * @return float
     */
    static function roundCost($cost, $round_type = self::CEIL, $site_id = null)
    {
        $cost = (float)$cost;
        $config = \RS\Config\Loader::byModule('catalog', $site_id);
        if ($config['price_round']) {
            switch ($round_type) {
                case self::CEIL:
                    return ceil($cost / $config['price_round']) * $config['price_round'];
                case self::ROUND:
                    return round($cost / $config['price_round'], 0) * $config['price_round'];
                case self::FLOOR:
                    return floor($cost / $config['price_round']) * $config['price_round'];
            }
        }
        return $cost;
    }

    /**
     * Расчитывает цену комплектации, согласно данным в offer[pricedata]
     *
     * @param $pricedata - массив с данными о цене комплектации
     * @param $cost_id - id цены
     * @param $base - значение базовой цены товара
     * @return float|int|void
     */
    public static function calculateCostFromPriceData($pricedata, $cost_id, $base)
    {
        if (isset($pricedata['price'][$cost_id]) || !empty($pricedata['oneprice']['use'])) {
            if (!empty($pricedata['oneprice']['use'])) {
                $price = $pricedata['oneprice'];
            } else {
                $price = $pricedata['price'][$cost_id];
            }
            if (!isset($price['value'])) {
                $price['value'] = 0;
            }

            if ($price['znak'] == '=') {
                $base = $price['value'];
            } else {
                if ($price['unit'] == '%') {
                    $delta = $base * ($price['value'] / 100);
                } else {
                    $delta = $price['value'];
                }
                $base += (float)$delta;
            }

            return self::roundCost($base);
        }
    }

    /**
     * Возвращает объекты всех типов цен для текущего сайта
     *
     * @param $cache - использовать кэш
     * @return array
     *
     */
    static function getFullCostList($cache = true)
    {
        $cost_api = new self();

        if (!$cache || !isset(self::$full_costlist[$cost_api->site_context])) {
            self::$full_costlist[$cost_api->site_context] = $cost_api->getAssocList('id');
        }
        return self::$full_costlist[$cost_api->site_context];
    }

}