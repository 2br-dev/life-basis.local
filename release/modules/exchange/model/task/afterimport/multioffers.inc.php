<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Exchange\Model\Task\AfterImport;

use Catalog\Model\MultiOfferLevelApi;
use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\Property\Item as PropertyItem;
use Catalog\Model\Orm\Property\ItemValue;
use Catalog\Model\Orm\Property\Link as PropertyLink;
use Catalog\Model\Product\ProductOffersList;
use Exchange\Model\Log\LogExchange;
use Exchange\Model\Task\AbstractTask;
use RS\Config\Loader as ConfigLoader;
use RS\Db\Adapter as DbAdapter;
use RS\Event\Manager as EventManager;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

/**
 * Импортирует многомерные комплектации
 * Объект этого класса хранится в сессии, соотвественно все свойства объекта доступны
 * не только до окончания выполнения скрипта, но и в течение всей сессии
 */
class MultiOffers extends AbstractTask
{
    protected $new_props_dir_name = 'Характеристики комплектаций'; // Имя категории характеристик куда будут сохраняться новые характеристики добавленные
    protected $exclude_props_name = 'Выключить многомерные комплектации'; // Имя характеристики выключающей использование многомерных комплектаций у товара, берётся из 1С, если таковое присутсвует
    protected $exclude_property = null;    // Характеристика выключающая использование многомерных комплектаций у товара
    protected $new_props_dir_id = null;    // id категории характеристик хранящих новые характеристики
    protected $prop_values = []; // Массив хэш проверки
    protected $prop_insert = []; // Массив характеристик со значениями для записи в базу
    protected $props = []; // Массив характеристик
    protected $part_products = []; // Массив с id товаров которые импортируются
    protected $offset = 0;       // С какого элемента начинать
    protected $productPartNum = 100;     // Количество товаров для обработки вконце импорта
    protected $site_id;     // id текущего сайта с которым работаем
    protected $filename;

    public function __construct($filename)
    {
        parent::__construct();
        $this->filename = $filename;
    }

    public function exec($max_exec_time = 0)
    {
        //Вызовем хук
        EventManager::fire('exchange.task.afterimport.multioffers', [
            'filename' => $this->filename
        ]);
        if (preg_match("/offers/", $this->filename)) {
            // Получим конфиг
            /**
             * @var \Exchange\Config\File
             */
            $config = ConfigLoader::byModule('exchange');
            $need_multioffers = $config['allow_insert_multioffers'];

            // Получим характеристику выключающую использование многомерных комплектаций
            $this->site_id = SiteManager::getSiteId(); //Текущий id сайта
            $offMultiOffersProperty = $this->getOffMultiOffersProperty();

            // Если стоит галка использовать импорт многомерных комплектаций
            if ($need_multioffers) {
                $goods_ids = $this->getProcessedProductsIds();
                if (!empty($goods_ids)) {
                    $this->log->write(t("Начинаем обработку импорта многомерных комплектаций"), LogExchange::LEVEL_PRODUCT_IMPORT);
                    $product_api = new \Catalog\Model\Api();
                    $part_ids = array_slice($goods_ids, $this->offset, $this->productPartNum);//id изменённых товаров

                    while (!empty($part_ids)) {

                        // Собственно обновим количество товара
                        $q = OrmRequest::make()
                            ->from(new Product(), "P")
                            ->whereIn('id', $part_ids);

                        // Если импортирована характеристика "Выключить многомерные комплектации"
                        if ($offMultiOffersProperty) { //Добавим сведения об этой характеристике
                            $q->leftjoin(new PropertyLink(), 'P.id=L.product_id AND L.prop_id=' . $offMultiOffersProperty['id'], 'L')
                                ->select('P.*,L.val_str as off_multi_offers');
                        }

                        $products = $q->objects('\Catalog\Model\Orm\Product', 'id');
                        $products = $product_api->addProductsOffers($products); // Подгрузим всем товарам комплектации

                        // Перебираем товары
                        foreach ($products as $k => $product) {
                            $this->log->write(t("Обрабатываем многомерные для товара - ") . $product['id'], LogExchange::LEVEL_PRODUCT_IMPORT_DETAIL);
                            $this->processProductOffers($product);  // Начинаем обработку
                        }

                        $this->insertProperties();
                        $this->offset += $this->productPartNum;

                        // Добавим многомерные комплектации к товарам
                        $this->addMultiOffersToProducts($products);

                        // Если превышено время выполнения
                        if ($this->isExceed()) {
                            $this->log->write(t("Превышено время выполнения импорта многомерных комплектаций 1"), LogExchange::LEVEL_PRODUCT_IMPORT);
                            return false;
                        }
                        $this->prop_values = []; //Массив хэш проверки обнулим
                        $this->prop_insert = []; //Массив мульти вставки обнулим
                        $this->part_products = [];
                        $part_ids = array_slice($goods_ids, $this->offset, $this->productPartNum);
                    }

                    if ($this->isExceed()) {
                        $this->log->write(t("Превышено время выполнения импорта многомерных комплектаций 2"), LogExchange::LEVEL_PRODUCT_IMPORT);
                        return false;
                    }

                    // Обозначим доступные значения и добавим если они отсутствуют
                    $this->log->write(t("Импорт многомерных комплектаций успешно завершен"), LogExchange::LEVEL_PRODUCT_IMPORT);
                    return true;
                }
            }

            $this->log->write(t("Импорт многомерных комплектаций пропущен"), LogExchange::LEVEL_PRODUCT_IMPORT);
            return true;
        }

        return true;
    }

    /**
     * Добавляет к товарам многомерные комплектации
     *
     * @param array $part_ids - массив товаров для которых будут добавляться многомерные комплектации
     */
    private function addMultiOffersToProducts($products)
    {
        $moffer_api = new MultiOfferLevelApi();
        $levels = null; // Уровни многомерных комплектаций

        if (!empty($this->props)) {
            foreach ($this->props as $prop_name => $property) { // Добавим известные нам уровни
                $levels[] = [
                    'title' => $property['title'],
                    'prop' => $property['id']
                ];
            }

            if (!empty($products)) {
                foreach ($products as $product) {
                    if (!$product['off_multi_offers']) {
                        //Подготовим уровни многомерных комплектаций для товара
                        $levels_by_product = $moffer_api->prepareRightMOLevelsToProduct($product['id'], $levels, true);

                        //Сохранение уровней мн. комплектаций
                        $this->log->write(t("Добавляем многомерные комплектации к id товара ") . $product['id'], LogExchange::LEVEL_PRODUCT_IMPORT_DETAIL);
                        $moffer_api->saveMultiOfferLevels($product['id'], $levels_by_product);
                    }
                }
            }
        }
    }

    /**
     * Получает характеристику "Выключить многомерные комплектации"
     *
     */
    private function getOffMultiOffersProperty()
    {
        if ($this->exclude_property === null) { // Если ещё не запрашивалось
            $this->exclude_property = OrmRequest::make()
                ->from(new PropertyItem())
                ->where([
                    'site_id' => $this->site_id,
                    'title' => $this->exclude_props_name,
                ])
                ->object();
        }

        return $this->exclude_property;
    }


    /**
     * Создаёт категорию для новых характеристик
     *
     */
    private function createNewPropertyDir()
    {
        $dir = new \Catalog\Model\Orm\Property\Dir();
        $dir['title'] = $this->new_props_dir_name;
        $dir->insert();
        return $dir;
    }

    /**
     * Получает характеристику по имени, если таковой нет, то создаёт её
     *
     * @param string $prop_name - имя характеристики, которую ищем
     */
    private function getPropertyByNameOrCreate($prop_name)
    {

        if (!isset($this->props[$prop_name])) {
            // Проверим такую в БД
            $property = OrmRequest::make()
                ->from(new PropertyItem())
                ->where([
                    'site_id' => $this->site_id,
                    'title' => $prop_name,
                ])->object();


            if (!$property) { // Если характеристики нет, то создадим её.
                // Проверим есть ли директрия
                if (!$this->new_props_dir_id) {
                    $prop_dir = OrmRequest::make()
                        ->from(new \Catalog\Model\Orm\Property\Dir())
                        ->where([
                            'site_id' => $this->site_id,
                            'title' => $this->new_props_dir_name,
                        ])
                        ->object();
                    if (!$prop_dir) {
                        $prop_dir = $this->createNewPropertyDir();
                    }
                    $this->new_props_dir_id = $prop_dir['id'];
                }
                $property = new PropertyItem();

                $property['title'] = $prop_name;
                $property['type'] = 'list';
                $property['parent_id'] = $this->new_props_dir_id;
                $property->insert();
            } elseif (!$property->isListType()) {  //Если характеристика не списковая оказалась переводим в списковую
                $property['type'] = 'list';
                $property->update();
            }
            $this->props[$prop_name] = $property;
        }

        return $this->props[$prop_name];
    }

    /**
     * Вставляет характеристики товара в БД взятые из комплектаций
     *
     */
    private function insertProperties()
    {
        if (!empty($this->prop_insert)) {
            $property_obj = new PropertyLink();
            foreach ($this->prop_insert as $prop_id => $product_values) {
                //Предварительно удалим характеристики
                OrmRequest::make()
                    ->from(new PropertyLink())
                    ->where([
                        'site_id' => $this->site_id,
                        'prop_id' => $prop_id,
                    ])
                    ->whereIn('product_id', $this->part_products)
                    ->delete()
                    ->exec();


                //Вставляем мутивставкой характеристики
                $this->log->write(t('Мульти вставка характеристики, id = ') . $prop_id, LogExchange::LEVEL_PRODUCT_IMPORT_DETAIL);

                DbAdapter::sqlExec('INSERT INTO #table (`product_id`,`prop_id`,`val_str`,`val_list_id`, `available`, `site_id`, `xml_id`) VALUES ' . implode(",", $product_values) . ' ', [
                    'table' => $property_obj->_getTable(),
                ]);
            }
        }
    }

    /**
     * Сортирует комплектации товара ставя в начало те комплектации, что есть в наличии
     *
     * @param mixed $a
     * @param mixed $b
     */
    public function sortOffersByNumCallback($a, $b)
    {
        if ($a['num'] == $b['num']) {
            return 0;
        }
        return ($a['num'] > $b['num']) ? -1 : +1;
    }

    /**
     * Обрабатывает комплектацию товара вытаскивая значения для характеристик
     *
     * @param Product $product
     */
    private function processProductOffers($product)
    {
        $site_id = SiteManager::getSiteId();
        if ($product->isOffersUse()) {
            $this->part_products[] = $product['id'];
            //Поставим вначало только те характеристики, что есть в наличии
            $offers = $product['offers'];

            if ($offers['items'] instanceof ProductOffersList) {
                $offers['items']->usort([$this, 'sortOffersByNumCallback']);
            }

            $product['offers'] = $offers;

            // Перебираем уже добавленные комплектации
            $already_available = [];
            $prop_value_data = [];
            foreach ($product['offers']['items'] as $offer) {
                //Получиим данные характеристик
                if (!empty($offer['propsdata_arr'])) {
                    foreach ($offer['propsdata_arr'] as $prop_name => $prop_value) {
                        // Определим по имени что за характеристика
                        $property = $this->getPropertyByNameOrCreate($prop_name);

                        if (!empty($prop_value)) {
                            $prop_key = $prop_name . ':' . $prop_value;
                            if ($offer['num'] > 0) {
                                //Если значение есть хотя бы в одной комплектации, то ставим отметку, что оно есть, available = 1
                                $already_available[$prop_key] = 1;
                            }

                            // Готовим для мульти вставки 
                            $prop_value_data[$prop_key] = [
                                $product['id'],
                                $property['id'],
                                $prop_value,
                                ItemValue::getIdByValue($property['id'], $prop_value),
                                $already_available[$prop_key] ?? 0, //available
                                $site_id,
                                $property['xml_id'],
                            ];

                        }
                    }
                }
            }

            foreach ($prop_value_data as $key => $data) {
                $ready_data_insert = "(" . implode(',', \RS\Helper\Tools::arrayQuote($data)) . ")"; // Данные для множественной вставки
                $product_id = $data[0];
                $property_id = $data[1];
                $prop_value = $data[2];

                if (!isset($this->prop_values[$property_id][$product_id][$prop_value])) {
                    $this->prop_values[$property_id][$product_id][$prop_value] = 1;  // Установим хэш проверки
                    $this->prop_insert[$property_id][] = $ready_data_insert;
                }
            }
        }
    }
}
