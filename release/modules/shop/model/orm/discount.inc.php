<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm;

use Catalog\Model\CurrencyApi;
use Catalog\Model\DirApi;
use Catalog\Model\Orm\Product;
use Catalog\Model\ProductDialog;
use RS\Config\Loader as ConfigLoader;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Exception as RSException;
use RS\Helper\CustomView;
use RS\Helper\Tools as HelperTools;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;

/**
 * Скидочный купон
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property array $products Продукты
 * @property array $exclude_products Продукты, которые нужно исключить
 * @property string $code Код
 * @property string $descr Описание скидки
 * @property integer $active Включен
 * @property string $sproducts Список товаров, на которые распространяется скидка
 * @property string $exproducts Список товаров, на которые не распространяется скидка
 * @property string $period Срок действия
 * @property string $endtime Время окончания действия скидки
 * @property float $min_order_price Минимальная сумма заказа
 * @property array $free_deliveries Сделать следующие доставки бесплатными
 * @property string $_free_deliveries Сделать следующие доставки бесплатными в сериализованном виде
 * @property float $discount Скидка
 * @property string $discount_type Скидка указана в процентах или в базовой валюте?
 * @property integer $uselimit Лимит использования, раз
 * @property integer $oneuserlimit Лимит использования одним пользователем, раз
 * @property integer $only_first_order Только для первого заказа
 * @property integer $wasused Была использована, раз
 * @property integer $makecount Сгенерировать купонов
 * @property array $need_products Обязательные товары, которые должны присутствовать для применения купона
 * @property string $needproducts Обязательные товары, которые должны присутствовать для применения купона
 * @property float $need_products_sum Минимальная сумма обязательных товаров
 * @property array $order_cost_type Тип цен в заказе
 * @property string $_order_cost_type Тип цен в заказе в сериализованном виде
 * @property array $user_groups Группы пользователя
 * @property string $_user_groups Группа пользователя в сериализованном виде
 * --\--
 */
class Discount extends OrmObject
{
    const PERIOD_FOREVER = 'forever';
    const PERIOD_TIMELIMIT = 'timelimit';
    const DISCOUNT_TYPE_PERCENT = '%';
    const DISCOUNT_TYPE_BASE_CURRENCY = 'base';

    protected static $table = 'order_discount';

    protected $serialized_products_field = 'sproducts';
    protected $products_field = 'products';

    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'products' => new Type\ArrayList([
                    'description' => t('Продукты'),
                    'template' => '%shop%/form/discount/products.tpl',
                ]),
                'exclude_products' => new Type\ArrayList([
                    'description' => t('Продукты, которые нужно исключить'),
                    'hint' => t('Исключение имеет более высокий приоритет и применяется после отбора товаров. Вы можете, например, исключать товары, которые находятся спецкатегориях.'),
                    'template' => '%shop%/form/discount/exclude_products.tpl',
                ]),
                'code' => new Type\Varchar([
                    'maxLength' => '50',
                    'description' => t('Код'),
                    'hint' => t('Данный код можно будет ввести в корзине и получить заданную скидку. Оставьте поле пустым, чтобы код был сгенерирован автоматически'),
                    'Attr' => [['size' => '30']],
                    'meVisible' => false,
                ]),
                'descr' => new Type\Varchar([
                    'maxLength' => '2000',
                    'description' => t('Описание скидки'),
                ]),
                'active' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Включен'),
                    'CheckboxView' => ['1', '0'],
                ]),
                'sproducts' => new Type\Text([
                    'description' => t('Список товаров, на которые распространяется скидка'),
                    'visible' => false,
                ]),
                'exproducts' => new Type\Text([
                    'description' => t('Список товаров, на которые не распространяется скидка'),
                    'visible' => false,
                ]),
                'period' => new Type\Enum(['timelimit', 'forever'], [
                    'template' => '%shop%/form/discount/period.tpl',
                    'description' => t('Срок действия'),
                    'listFromArray' => [[
                        self::PERIOD_TIMELIMIT => t('Ограничен по времени'),
                        self::PERIOD_FOREVER => t('Вечный')
                    ]]
                ]),
                'endtime' => new Type\Datetime([
                    'description' => t('Время окончания действия скидки'),
                    'visible' => false,
                    'allowempty' => true,
                ]),
                'min_order_price' => new Type\Decimal([
                    'maxLength' => 20,
                    'decimal' => 2,
                    'description' => t('Минимальная сумма заказа')
                ]),
                'free_deliveries' => new Type\ArrayList([
                    'description' => t('Сделать следующие доставки бесплатными'),
                    'hint' => t('Удерживая CTRL, можно выбирать или снимать выбор доставки'),
                    'list' => [['Shop\Model\DeliveryApi', 'staticSelectList']],
                    'attr' => [[
                        'multiple' => true,
                        'size' => 10
                    ]]
                ]),
                '_free_deliveries' => new Type\Text([
                    'description' => t('Сделать следующие доставки бесплатными в сериализованном виде'),
                    'visible' => false
                ]),
                'discount' => new Type\Decimal([
                    'template' => '%shop%/form/discount/discount.tpl',
                    'maxLength' => 20,
                    'decimal' => 2,
                    'description' => t('Скидка'),
                    'Attr' => [['size' => '8']],
                    'checker' => [function($_this, $value) {
                        //Нулевая скидка допустима, если указана бесплатная доставка
                        if (!$_this['free_deliveries'] && $value == 0) {
                            return t('Укажите скидку или выберите доставку, которая будет бесплатная');
                        }
                        return true;
                    }]
                ]),
                'discount_type' => new Type\Enum(['', self::DISCOUNT_TYPE_PERCENT, self::DISCOUNT_TYPE_BASE_CURRENCY], [
                    'description' => t('Скидка указана в процентах или в базовой валюте?'),
                    'listFromArray' => [[
                        self::DISCOUNT_TYPE_PERCENT => '%',
                        self::DISCOUNT_TYPE_BASE_CURRENCY => t('в базовой валюте'),
                    ]],
                    'visible' => false
                ]),
                'uselimit' => new Type\Integer([
                    'maxLength' => '5',
                    'description' => t('Лимит использования, раз'),
                    'hint' => t('Количество раз, которое можно использовать купон, 0 - неограниченно'),
                    'Attr' => [['size' => '5']],
                ]),
                'oneuserlimit' => new Type\Integer([
                    'maxLength' => '5',
                    'description' => t('Лимит использования одним пользователем, раз'),
                    'hint' => t('Количество раз, которое можно использовать купон, 0 - неограниченно<br/>
                               Действует только для зарегистрированых пользователей.<br/>
                               Если пользователь не зарегистрирован, то будет выдано <br/>
                               сообщение о авторизации'),
                    'Attr' => [['size' => '5']],
                ]),
                'only_first_order' => (new Type\Integer())
                    ->setDescription(t('Только для первого заказа'))
                    ->setCheckboxView(1, 0)
                    ->setMaxLength(1)
                    ->setDefault(0),
                'wasused' => new Type\Integer([
                    'maxLength' => '5',
                    'description' => t('Была использована, раз'),
                    'Attr' => [['size' => '5']],
                    'default' => 0,
                    'allowempty' => false
                ]),
                'makecount' => new Type\Integer([
                    'maxLength' => '11',
                    'description' => t('Сгенерировать купонов'),
                    'runtime' => true,
                    'visible' => false,
                    'hint' => t('Сгенерировать указанное число купонов с теми же параметрами, но разными кодами'),
                    'Attr' => [['size' => '4']],
                ]),
            t('Дополнительные условия'),
                'need_products' => new Type\ArrayList([
                    'description' => t('Обязательные товары, которые должны присутствовать для применения купона'),
                    'hint' => t('Условие будет выпонено при наличии любого товара из этого списка. В случае если ни одного товара не будет применить купон будет невозможно.'),
                    'template' => '%shop%/form/discount/need_products.tpl',
                ]),
                'needproducts' => new Type\Text([
                    'description' => t('Обязательные товары, которые должны присутствовать для применения купона'),
                    'visible' => false
                ]),
                'need_products_sum' => new Type\Decimal([
                    'maxLength' => 20,
                    'decimal' => 2,
                    'description' => t('Минимальная сумма обязательных товаров'),
                    'hint' => t('Товары, перечисленные в настройке выше должны быть на сумму, превышающую то, что здесь указано')
                ]),
                'order_cost_type' => new Type\ArrayList([
                    'description' => t('Тип цен в заказе'),
                    'hint' => t('Купон будет действовать только для указанных типов цен. <br>Удерживая CTRL, можно выбирать или снимать выбор доставки.'),
                    'list' => [['Catalog\Model\CostApi', 'staticSelectList'], [0 => t('- все -')]],
                    'default' => 0,
                    'attr' => [[
                        'multiple' => true,
                        'size' => 10
                    ]]
                ]),
                '_order_cost_type' => new Type\Text([
                    'description' => t('Тип цен в заказе в сериализованном виде'),
                    'visible' => false
                ]),
                'user_groups' => new Type\ArrayList([
                    'description' => t('Группы пользователя'),
                    'hint' => t('Купон будет действовать только для указанных групп пользователей. <br>Удерживая CTRL, можно выбирать или снимать выбор доставки.'),
                    'list' => [['Users\Model\GroupApi', 'staticSelectList'], [0 => t('- все -')]],
                    'default' => 0,
                    'attr' => [[
                        'multiple' => true,
                        'size' => 10
                    ]]
                ]),
                '_user_groups' => new Type\Text([
                    'description' => t('Группа пользователя в сериализованном виде'),
                    'visible' => false
                ]),
        ])->addMultieditKey(['products', 'endtime']);

        $this->addIndex(['site_id', 'code'], self::INDEX_UNIQUE);
    }

    /**
     * Действия перед записью объекта
     *
     * @param string $flag - insert или update
     * @return boolean
     * @throws RSException
     */
    function beforeWrite($flag)
    {
        if (empty($this['code'])) {
            $this['code'] = $this->generateCode();
        }
        $this[$this->serialized_products_field] = serialize($this[$this->products_field]);
        $this['exproducts'] = serialize($this['exclude_products']);
        $this['needproducts'] = serialize($this['need_products']);
        $this['_free_deliveries'] = serialize($this['free_deliveries']);
        $this['_order_cost_type'] = serialize($this['order_cost_type']);
        $this['_user_groups'] = serialize($this['user_groups']);
        return true;
    }

    /**
     * Генерирует код купона
     *
     * @return string
     * @throws RSException
     */
    function generateCode()
    {
        $config = ConfigLoader::byModule($this);
        return HelperTools::generatePassword($config['discount_code_len'], 'abcdefghkmnpqrstuvwxyz123456789');
    }

    function afterObjectLoad()
    {
        if (!empty($this[$this->serialized_products_field]) && $unserialize = unserialize((string)$this[$this->serialized_products_field])) {
            $this[$this->products_field] = $unserialize;
        }

        $this['exclude_products'] = @unserialize((string)$this['exproducts']) ?: [];
        $this['need_products'] = @unserialize((string)$this['needproducts']) ?: [];
        $this['free_deliveries'] = @unserialize((string)$this['_free_deliveries']) ?: [];
        $this['order_cost_type'] = @unserialize((string)$this['_order_cost_type']) ?: [];
        $this['user_groups'] = @unserialize((string)$this['_user_groups']) ?: [];
    }

    /**
     * Возвращает объект, с помошью которого можно визуализировать выбор товаров
     *
     * @return ProductDialog
     */
    function getProductDialog()
    {
        return new ProductDialog('products', false, $this['products']);
    }

    /**
     * Возвращает объект, с помошью которого можно визуализировать выбор товаров
     *
     * @return ProductDialog
     */
    function getExcludeProductDialog()
    {
        return new ProductDialog('exclude_products', false, $this['exclude_products']);
    }

    /**
     * Возвращает объект, с помошью которого можно визуализировать выбор товаров
     *
     * @return ProductDialog
     */
    function getNeedProductDialog()
    {
        return new ProductDialog('need_products', false, $this['need_products']);
    }

    /**
     * Возвращает true, если активен, иначе - текст ошибки
     */
    function isActive()
    {
        //Скидка считается активной, если:
        //Она включена, срок действия еще не истек, количество использвания - не истекло.
        if ($this['active'] == 0) return t('Скидка не активна');
        if ($this['period'] == 'timelimit' && $this['endtime'] < date('Y-m-d H:i:s')) return t('Срок действия скидки истек');
        if ($this['uselimit'] && ($this['wasused'] >= $this['uselimit'])) return t('Достигнут лимит использования скидки');
        return true;
    }

    /**
     * Возвращает true, если купон распространяется на все товары
     */
    function isForAll()
    {
        return empty($this['products']) || @in_array('0', (array)$this['products']['group']);
    }

    /**
     * Возвращает сумму минимального заказа, к которому может быть прикрепленн купон
     *
     * @return float
     */
    function getMinOrderPrice()
    {
        return $this['min_order_price'];
    }

    /**
     * Увеличивает в базе счетчик использования на 1
     *
     * @return void
     */
    function incrementUse()
    {
        OrmRequest::make()
            ->update($this)
            ->set('wasused = wasused + 1')
            ->where("id = '#id'", ['id' => $this['id']])
            ->exec();
    }

    /**
     * Возвращает величину скидки, отформатированную для отображения (всегда с учетом текущей валюты)
     *
     * @return string
     */
    function getDiscountTextValue()
    {
        if ($this['discount_type'] == '%') {
            $discount = (float)$this['discount'] . "%";
        } else {
            $discount = CurrencyApi::applyCurrency($this['discount']);
            $discount = CustomView::cost($discount) . ' ' . CurrencyApi::getCurrentCurrency()->stitle;
        }
        return $discount;
    }

    /**
     * Возвращает клонированный объект купона
     *
     * @return Discount
     * @throws EventException
     */
    function cloneSelf()
    {
        /** @var Discount $clone */
        $clone = parent::cloneSelf();
        unset($clone['wasused']);
        return $clone;
    }

    /**
     * Возвращает идентификаторы товаров в корзине, которые подходят для применения данного купона
     *
     * @param array $cart_data_items массив со всеми товарами в корзине (результат Cart::getProductItemsWithConcomitants)
     * @return array
     */
    function getLinkedProductsUniq($cart_data_items)
    {
        $linked_products = $this['products']['product'] ?? [];
        $linked_exclude_products = $this['exclude_products']['product'] ?? [];

        $linked_groups = $this['products']['group'] ?? [];
        $linked_exclude_groups = $this['exclude_products']['group'] ?? [];
        $linked_exclude_groups = DirApi::getChildsId($linked_exclude_groups);

        $linked_all = in_array(0, $linked_groups) || (empty($linked_groups) && empty($linked_products));
        if (!$linked_all) {
            $linked_groups = DirApi::getChildsId($linked_groups);
        }

        $result = [];
        foreach ($cart_data_items as $uniq => $basket_item) {
            /** @var AbstractCartItem $cart_item */
            $cart_item = $basket_item['cartitem'];
            /** @var Product $product */
            $product = $basket_item['product'];

            if (!$cart_item->getForbidDiscounts()
                && ($linked_all || array_intersect($product['xdir'], $linked_groups) || in_array($product['id'], $linked_products))
                && !(array_intersect($product['xdir'] ?? [], $linked_exclude_groups) || in_array($product['id'], $linked_exclude_products))
            ) {
                $result[$uniq] = $uniq;
            }
        }

        return $result;
    }

    /**
     * Возвращает список идентификаторов товаров, которые обязательно должны присутствовать в корзине для применения купона
     *
     * @param array $cart_data_items массив со всеми товарами в корзине (результат Cart::getProductItemsWithConcomitants)
     * @param $sum
     *
     * @return array
     */
    function getNeedProductsUniq($cart_data_items, &$sum)
    {
        $sum = 0;
        $linked_products = $this['need_products']['product'] ?? [];
        $linked_groups = $this['need_products']['group'] ?? [];

        $linked_all = in_array(0, $linked_groups) || (empty($linked_groups) && empty($linked_products));
        if (!$linked_all) {
            $linked_groups = DirApi::getChildsId($linked_groups);
        }

        $result = [];
        foreach ($cart_data_items as $uniq => $basket_item) {
            /** @var AbstractCartItem $cart_item */
            $cart_item = $basket_item['cartitem'];
            /** @var Product $product */
            $product = $basket_item['product'];

            if ($linked_all || array_intersect($product['xdir'], $linked_groups) || in_array($product['id'], $linked_products)) {
                $result[$uniq] = $uniq;
                $sum += $product->getCost(null, $cart_item['offer'], false, true) * $cart_item['amount'];
            }
        }

        return $result;
    }
}
