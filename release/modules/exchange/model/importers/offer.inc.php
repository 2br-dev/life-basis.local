<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Exchange\Model\Importers;

use Catalog\Model\Orm\Currency;
use Catalog\Model\Orm\Offer as ProductOffer;
use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\Typecost;
use Catalog\Model\Orm\Unit;
use Catalog\Model\Orm\WareHouse as OrmWareHouse;
use Catalog\Model\WareHouseApi;
use Exchange\Model\Api as ExchangeApi;
use Exchange\Model\Importers\CatalogProduct as ImporterCatalogProduct;
use Exchange\Model\Importers\Warehouse as ImporterWarehouse;
use Exchange\Model\Log\LogExchange;
use Photo\Model\Orm\Image;
use Photo\Model\PhotoApi;
use RS\Event\Manager as EventManager;
use RS\Helper\Tools as Tools;
use RS\Img\Exception as ImgException;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

/**
 * Импорт предложения из пакета предложений
 */
class Offer extends AbstractImporter
{
    static public $pattern = '/Предложения\/Предложение$/i';
    static public $title = 'Импорт товарных предложений';
    static public $product_barcode_by_xml_id = [];

    static private $_cache_import_hash = null; //Кэш по хэшам импорта

    public function init()
    {
        if (self::$_cache_import_hash == null) {
            self::$_cache_import_hash = OrmRequest::make()
                ->select('xml_id, import_hash')
                ->from(new ProductOffer())
                ->where('xml_id > "" and import_hash > ""')
                ->where(['site_id' => SiteManager::getSiteId()])
                ->exec()->fetchSelected('xml_id', 'import_hash');
        }
    }

    public function import(\XMLReader $reader)
    {
        $import_hash = md5($this->getSimpleXML()->asXML());
        $xml_id = (string)$this->getSimpleXML()->Ид;

        if (isset(self::$_cache_import_hash[$xml_id]) && self::$_cache_import_hash[$xml_id] == $import_hash) {
            $this->log->write(t("Нет изменений в предложении: ") . (string)$this->getSimpleXML()->Наименование, LogExchange::LEVEL_OFFER_IMPORT);
            OrmRequest::make()
                ->update(new ProductOffer())
                ->set(['processed' => 1])
                ->where([
                    'xml_id' => $xml_id,
                    'site_id' => SiteManager::getSiteId(),
                ])
                ->exec();
        } else {
            $config = $this->getConfig();
            $catalog_config = $this->getCatalogConfig();
            $this->log->write(t("Импорт предложения: ") . (string)$this->getSimpleXML()->Наименование, LogExchange::LEVEL_OFFER_IMPORT);

            $default_warehouse = WareHouseApi::getDefaultWareHouse();

            // Обрезанный xml_id (то, что до символа #)
            $product_xml_id = $this->getProductXMLId();
            $product = Product::loadByWhere(
                [
                    'xml_id' => $product_xml_id,
                    'site_id' => SiteManager::getSiteId(),
                ]
            );

            // Возможно это старая версия и XML_ID предложения совпадает с XML_ID товара
            if (!$product['id']) {
                $product = Product::loadByWhere(
                    [
                        'xml_id' => (string)$this->getSimpleXML()->Ид,
                        'site_id' => SiteManager::getSiteId(),
                    ]
                );
            }

            if (!$product['id']) {
                $this->log->write(t("Не удалось загрузить товар '") . $product_xml_id, LogExchange::LEVEL_OFFER_IMPORT);
                return;
            }

            $barcode = Tools::toEntityString($this->getSimpleXML()->Артикул);
            $title = Tools::toEntityString($this->getSimpleXML()->Наименование);
            // Если включена настройка "Идентифицировать товары по артикулу" - обновим xml_id комплектации
            if ($config['product_uniq_field'] != 'xml_id') {
                $q = OrmRequest::make()
                    ->update(new ProductOffer())
                    ->set(['xml_id' => $xml_id])
                    ->limit(1);

                switch ($config['product_uniq_field']) {
                    case 'barcode':
                        if ($barcode) {
                            if ($product['barcode'] == $barcode) {
                                $q->where([
                                    'product_id' => $product['id'],
                                    'sortn' => 0
                                ]);
                            } else {
                                $q->where(['barcode' => $barcode]);
                            }
                            $q->exec();
                        }
                        break;
                    case 'title':
                        if ($title) {
                            if ($product['title'] == $title) {
                                $q->where([
                                    'product_id' => $product['id'],
                                    'sortn' => 0
                                ]);
                            } else {
                                $q->where(['title' => $title]);
                            }
                            $q->exec();
                        }
                        break;
                }
            }

            // Добавляем запись в таблицу product_offer (Ценовое предложение)
            $product_offer = new ProductOffer();

            $product_offer['site_id'] = SiteManager::getSiteId();
            $product_offer['product_id'] = $product['id'];
            $product_offer['title'] = $title;
            $product_offer['barcode'] = $barcode;
            $product_offer['num'] = (string)$this->getSimpleXML()->Количество;
            $product_offer['xml_id'] = (string)$this->getSimpleXML()->Ид; //Уникальный идентификатор в 1С
            $product_offer['sku'] = (string)$this->getSimpleXML()->Штрихкод;
            $product_offer['processed'] = 1; //Флаг обработанной комплектации
            $product_offer['import_hash'] = $import_hash;
            $product_offer['use_exchange_xml_id_logic'] = true;

            //Добавим базовую единицу если включена опция использовать единицы измерения комплектаций
            if ($catalog_config['use_offer_unit']) {
                $this->getBaseUnit($product_offer);
            }

            //Если установлен флаг уникализировать артикулы у комплектаций. Сложим артикул товара и уникальный "хвост"
            if ($config['unique_offer_barcode']) {
                $uniq_tail = strtoupper(mb_substr(md5($product_offer['xml_id']), 0, 6));
                if (!strripos($this->getProductBarcodeByXMLId(), $uniq_tail)) {
                    $product_offer['barcode'] = $this->getProductBarcodeByXMLId() . "-" . $uniq_tail;
                }
            }

            // Записываем сериализованные Цены в pricedata
            $pricedata = [];
            if (isset($this->getSimpleXML()->Цены->Цена)) {
                if ($config['dont_delete_costs']) {
                    $old_pricedata = OrmRequest::make()
                        ->select('pricedata')
                        ->from(new ProductOffer())
                        ->where(['xml_id' => $product_offer['xml_id']])
                        ->exec()->getOneField('pricedata');

                    if ($old_pricedata) {
                        $unserialized = @unserialize((string)$old_pricedata);
                        if (isset($unserialized['price'])) {
                            $pricedata = $unserialized['price'];
                        }
                    }
                }

                foreach ($this->getSimpleXML()->Цены->Цена as $one) {
                    $typecost = Typecost::loadByWhere([
                        'site_id' => SiteManager::getSiteId(),
                        'xml_id' => $one->ИдТипаЦены,
                    ]);
                    $currency = Currency::loadByWhere([
                        'site_id' => SiteManager::getSiteId(),
                        'title' => (string)$one->Валюта,
                    ]);
                    $pricedata[$typecost['id']] = [
                        'znak' => '=',
                        'original_value' => (string)$one->ЦенаЗаЕдиницу,
                        'unit' => $currency['id'],
                    ];
                }
            }

            $product_offer['pricedata_arr'] = ['price' => $pricedata];

            // Записываем сериализованные Характеристики в propsdata
            $props_nodes = $this->getSimpleXML()->ХарактеристикиТовара->ХарактеристикаТовара;
            if ($props_nodes != null) {
                $props = [];
                foreach ($props_nodes as $one) {
                    $props[Tools::toEntityString((string)$one->Наименование)] = Tools::toEntityString((string)$one->Значение);
                }
                $product_offer['propsdata'] = serialize($props);

                if ($config['offer_name_from_props']) {
                    $product_offer['title'] = implode(', ', $props);
                }
            }

            // Если схема больше 2.07 и Характеристики хранятся в файле import.xml и отсуствуют в offers.xml
            if (($props_nodes == null) && isset($_SESSION[ImporterCatalogProduct::SESS_KEY_NO_OFFER_PARAMS_FLAG]) && $_SESSION[ImporterCatalogProduct::SESS_KEY_NO_OFFER_PARAMS_FLAG]) {
                // Флаг, что характеристики не используются и нужно нумеровать все характеристики от 0
                $product_offer->cml_207_no_offer_params = true;
            }

            if ($config['dont_delete_stocks']) {
                $real_offer = ProductOffer::loadByWhere(['xml_id' => $product_offer['xml_id']]);
                $stock_num = $real_offer->fillStockNum();

                //Обнулим все остатки складов, у которых есть xml_id (которые созданы через 1С)
                foreach($stock_num as $warehouse_id => $stocks) {
                    $warehouse = new \Catalog\Model\Orm\WareHouse($warehouse_id);
                    if ($warehouse['id'] && $warehouse['xml_id']) {
                        $stock_num[$warehouse_id] = 0;
                    }
                }

            } else {
                $stock_num = [];
            }
            // Записываем сведения о количества товара на складе, если сведения об этом присутствуют
            // Для версии 2.07
            $stock_node = $this->getSimpleXML()->Склад;
            if (!count($stock_node)) {
                //Для версии 2.05
                $stock_node = $this->getSimpleXML()->КоличествоНаСкладах->КоличествоНаСкладе;
            } else {
                $product_offer->cml_207_no_offer_params = true;
            }

            if (!empty($stock_node)) {
                $this->log->write(t("Импорт остатков по складам для торгового предложения: ") . (string)$this->getSimpleXML()->Наименование, LogExchange::LEVEL_OFFER_IMPORT);
                foreach ($stock_node as $one) {
                    $warehouse_xml_id = (string)($one->ИдСклада ?: $one['ИдСклада']);
                    $warehouse_stock = (float)preg_replace("/[^\d\.,^-]/", '', ($one->Количество ?: $one['КоличествоНаСкладе']));
                    $warehouse_id = $this->getWarehouseByXMLId($warehouse_xml_id);
                    $stock_num[$warehouse_id] = $warehouse_stock;
                }
            } else {
                $stock_num[$default_warehouse['id']] = (float)$product_offer['num'];
            }
            $this->log->write(t("Для товара %product загружены следующие остатки(ID склада RS => Остаток): %stocks", [
                    'product' => (string)$this->getSimpleXML()->Наименование,
                    'stocks' => json_encode($stock_num)
                ]), LogExchange::LEVEL_OFFER_IMPORT_DETAIL);

            $product_offer['stock_num'] = $stock_num;

            $summary_num = 0;
            foreach ($stock_num as $warehouse_stock) {
                $summary_num += $warehouse_stock;
            }
            $product_offer['num'] = $summary_num;

            $this->importImages($product, $product_offer);

            EventManager::fire('exchange.offer.after', [
                'product' => $product,
                'product_offer' => $product_offer,
                'xml_id' => $xml_id,
                'importer' => $this
            ]);

            $dont_update_offer_fields = (array)$config['dont_update_offer_fields'];
            if ($product['exchange_dont_update_price']) {
                $dont_update_offer_fields[] = 'pricedata';
            }

            //Поля которые, будут обновлены при совпадении строки
            $on_duplicate_update_fields = array_diff(['title', 'sku', 'barcode', 'pricedata', 'propsdata', 'num', 'processed', 'import_hash', 'sku'], $dont_update_offer_fields);

            // Вставка _ИЛИ_ обновление товарного предложения (комплектации)
//              $product['num'] += $product_offer->num;
            $product_offer->dont_reset_hash = true;
            $product_offer->insert(false, $on_duplicate_update_fields, ['site_id', 'xml_id']);

            // Если это основная комплектация - обновим цены продукта
            $main_offer_id = (new OrmRequest())
                ->select('id')
                ->from(new ProductOffer())
                ->where(['product_id' => $product_offer['product_id']])
                ->orderby('sortn asc')
                ->limit(1)
                ->exec()->getOneField('id');

            if ($product_offer['id'] == $main_offer_id) {
                $this->log->write(t("Импортируем цены в таблицу product_x_cost для товара [id=%0]", [$product['id']]), LogExchange::LEVEL_PRODUCT_IMPORT_DETAIL);

                // Импортируем цены в таблицу product_x_cost
                if (!$product['exchange_dont_update_price']) {
                    if ($config['dont_delete_costs']) {
                        $product->fillCost();
                        $excost_array = $product['excost'];
                    } else {
                        $excost_array = [];
                    }

                    $cost_short_for_log = [];
                    if (isset($this->getSimpleXML()->Цены->Цена)) {
                        foreach ($this->getSimpleXML()->Цены->Цена as $one) {
                            $typecost = Typecost::loadByWhere(
                                [
                                    'xml_id' => $one->ИдТипаЦены,
                                    'site_id' => SiteManager::getSiteId(),
                                ]
                            );

                            $currency = Currency::loadByWhere(
                                [
                                    'site_id' => SiteManager::getSiteId(),
                                    'title' => (string)$one->Валюта,
                                ]
                            );

                            $excost_array[$typecost['id']] = [
                                'cost_original_val' => (string)$one->ЦенаЗаЕдиницу,
                                'cost_original_currency' => $currency['id'],
                            ];

                            $cost_short_for_log[$typecost['id']] = (string)$one->ЦенаЗаЕдиницу;
                        }
                    }

                    $product['excost'] = $excost_array;

                    $this->log->write(t("Для товара %product загружены следующие цены(ID цены RS => Цена): %costs", [
                        'product' => (string)$this->getSimpleXML()->Наименование,
                        'costs' => json_encode($cost_short_for_log)
                    ]), LogExchange::LEVEL_OFFER_IMPORT_DETAIL);
                }
                if (!empty($product_offer['barcode'])) {
                    $product['barcode'] = $product_offer['barcode'];
                }
                if (!empty($product_offer['sku'])) {
                    $product['sku'] = $product_offer['sku'];
                }

                $product->setFlag(Product::FLAG_DONT_UPDATE_DIR_COUNTER);
                $product->is_exchange_action = true;
                $product->processed = 1;
                $product->update();
            } else {
                OrmRequest::make()
                    ->update($product)
                    ->set(['processed' => 1])
                    ->where([
                        'id' => $product_offer['product_id']
                    ])
                    ->exec();
            }
        }
    }

    /**
     * Импортирует изображения, привязанные к комплектации.
     * Если в offers.xml есть тэг Предложения->Предложение->Картинка, то будет происходить обработка.
     * В случае, если в offers.xml нет седений об изображениях, устанавливать связь фото с комплектациями нужно вручную в админке
     *
     * @param $product
     * @param $product_offer
     */
    private function importImages($product, $product_offer)
    {
        $xml_id = $this->getProductXMLId();

        if (isset($this->getSimpleXML()->Картинка)) {
            $exists_photos_id = [];
            foreach($this->getSimpleXML()->Картинка as $one) {
                $path = ExchangeApi::getInstance()->getDir() . DS . $one;
                //Проверим с каким расширением передан файл
                $path_parts = pathinfo($path);
                $extention = $path_parts['extension'];

                if (!in_array(strtolower($extention), ['png', 'jpg', 'jpeg', 'gif', 'tiff'])) {
                    continue;
                }

                //Проверяем, присутствует ли данное фото у товара
                $image = OrmRequest::make()
                    ->from(new Image())
                    ->where([
                        'site_id' => SiteManager::getSiteId(),
                        'extra' => $xml_id,
                        'filename' => basename($path),
                        'linkid' => $product['id'],
                    ])
                    ->object();

                //Если фото существует и оно ещё не было загружено
                if (file_exists($path) && !$image) {

                    // Привязываем новую картинку
                    $photoapi = new PhotoApi();
                    try {
                        $image = $photoapi->addFromUrl($path, 'catalog', $product['id'], true, $xml_id, true, true);
                        if (!$image) {
                            throw new ImgException(implode(", ", $photoapi->getUploadError()));
                        }
                    } catch (ImgException $e) {
                        $this->log->write(t('Ошибка при загрузке изображения "%0" для товара с артикулом "%1", Ид "%2"', [
                            $e->getMessage(),
                            $product['barcode'],
                            $xml_id,
                        ]), LogExchange::LEVEL_PRODUCT_IMPORT_DETAIL);
                    }
                }
                //Если фото удачно загружено
                if ($image) {
                    $this->log->write(t('Загружено изображение для комплектации "%2" с артикулом "%0", Ид "%1"', [
                        $product['barcode'],
                        $xml_id,
                        $product_offer['title']."(".$product_offer['xml_id'].")"
                    ]), LogExchange::LEVEL_PRODUCT_IMPORT_DETAIL);

                    $exists_photos_id[] = $image['id'];
                }
            }

            $product_offer['photos_arr'] = $exists_photos_id;
        }
    }

    /**
     * Импорт единиц измерения
     *
     * @param ProductOffer $offer - комплектация
     */
    private function getBaseUnit(ProductOffer $offer)
    {
        if (!(string)$this->getSimpleXML()->БазоваяЕдиница) {   // Если единицы нет, ничего не делаем
            return;
        }
        $code = $this->getSimpleXML()->БазоваяЕдиница['Код'];
        $inter_sokr = $this->getSimpleXML()->БазоваяЕдиница['МеждународноеСокращение'];
        $full_title = $this->getSimpleXML()->БазоваяЕдиница['НаименованиеПолное'];
        $short_title = trim((string)$this->getSimpleXML()->БазоваяЕдиница);

        if (empty($full_title)) { //Если полного наименования не указано.
            $full_title = $short_title;
        }

        // Получаем идентификатор единицы изменерия по коду
        if (!empty($code)) {
            $unit_id = ImporterCatalogProduct::getUnitIdByCode($code);
        } elseif (!empty($short_title)) { // Получаем идентификатор единицы изменерия по полному наименованию
            $unit_id = ImporterCatalogProduct::getUnitIdByName($short_title);
        } else {
            $unit_id = false;
        }

        if ($unit_id === false) {
            // Если единицы измерения еще нет - вставляем
            $unit = new Unit();
            $unit['code'] = $code;
            $unit['icode'] = $inter_sokr;
            $unit['title'] = $full_title;
            $unit['stitle'] = $short_title;
            $unit->insert();
            $unit_id = $unit['id'];
        }
        $offer['unit'] = $unit_id;
    }

    /**
     * Возвращает артикул товара к которой привязана комплектация
     *
     * @param string|bool $xml_id - внешний идентификатор
     * @return string
     */
    private function getProductBarcodeByXMLId($xml_id = false)
    {
        if (!$xml_id) {
            $xml_id = $this->getProductXMLId();
        }
        if (!isset(self::$product_barcode_by_xml_id[$xml_id])) {
            self::$product_barcode_by_xml_id[$xml_id] = OrmRequest::make()
                ->select('barcode')
                ->from(new Product())
                ->where([
                    'site_id' => SiteManager::getSiteId(),
                    'xml_id' => $xml_id
                ])->exec()
                ->getOneField('barcode', '');
        }
        return self::$product_barcode_by_xml_id[$xml_id];
    }

    /**
     * Получает XML_ID товара из XML в файле
     */
    private function getProductXMLId()
    {
        // Получаем XML-идентификатор товара (первую часть до решетки)
        $xml_id = (string)$this->getSimpleXML()->Ид;
        $xml_id_arr = explode("#", $xml_id);
        return $xml_id_arr[0];
    }

    /**
     * Получает ID склада из базы и помещает значение в сессию
     *
     * @param string $xml_id - XML_ID склада
     * @return int
     */
    private function getWarehouseByXMLId($xml_id)
    {
        if (!isset($_SESSION[ImporterWarehouse::SESS_KEY_WAREHOUSE_IDS][$xml_id])) {
            $id = OrmRequest::make()
                ->from(new OrmWareHouse())
                ->where([
                    'site_id' => SiteManager::getSiteId(),
                    'xml_id' => $xml_id,
                ])
                ->exec()
                ->getOneField('id', 0);

            $_SESSION[ImporterWarehouse::SESS_KEY_WAREHOUSE_IDS][$xml_id] = $id;
        }
        return $_SESSION[ImporterWarehouse::SESS_KEY_WAREHOUSE_IDS][$xml_id];
    }
}
