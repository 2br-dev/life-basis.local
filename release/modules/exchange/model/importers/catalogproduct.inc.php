<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Exchange\Model\Importers;

use Catalog\Model\OfferApi;
use Catalog\Model\Orm\Brand;
use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Offer as OrmOffer;
use Catalog\Model\Orm\Product;
use Catalog\Model\Orm\Property\Item as PropertyItem;
use Catalog\Model\Orm\Property\ItemValue as PropertyItemValue;
use Catalog\Model\Orm\Property\Link as PropertyLink;
use Catalog\Model\Orm\Unit;
use Catalog\Model\ProductDimensions;
use Exchange\Config\File;
use Exchange\Model\Api as ExchangeApi;
use Exchange\Model\Importers\CatalogProperty as ImporterCatalogProperty;
use Exchange\Model\Log\LogExchange;
use Photo\Model\Orm\Image;
use Photo\Model\PhotoApi;
use RS\Config\Loader;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Helper\Tools as Tools;
use RS\Helper\Transliteration;
use RS\Img\Exception as ImgException;
use RS\Orm\Exception as OrmException;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Tax;
use Shop\Model\Orm\TaxRate;
use Shop\Model\RegionApi;

class CatalogProduct extends AbstractImporter
{
    const SESSION_KEY        = "property_ids"; //Сессионный ключ для массива в сессии со свойствами, для последуеющей замены типа на список
    const SESS_KEY_GOODS_IDS = "products_ids"; //Сессионный ключ для массива c товарами которые редактировались
    const SESS_KEY_NO_OFFER_PARAMS_FLAG = "no_offer_param"; //Сессионный ключ флаг, который будет говорить, о том, что нельзя использовать характистики товара(нужно для некоторых версий CommerceML)
    const SEES_KEY_207_GROUP_ID_CACHE = '207_group_id_cache';

    static public $pattern = '/Товар$/i';
    static public $title   = 'Импорт Товаров';

    static private $_cache_brands           = [];
    static private $_cache_categories       = [];
    static private $_cache_properties       = [];
    static private $_cache_properties_types = [];
    static private $_cache_taxes            = [];
    static private $_cache_units_by_code    = []; //Кэш по коду единицы измерения
    static private $_cache_units_by_name    = []; //Кэш по наименованию единицы измерения
    static private $_cache_import_hash      = null; //Кэш по хэшам импорта

    public function init()
    {
        if (self::$_cache_import_hash == null) {
            self::$_cache_import_hash = OrmRequest::make()
                ->select('id, xml_id, import_hash')
                ->from(new Product())
                ->where('xml_id > "" and import_hash > ""')
                ->where(['site_id' => SiteManager::getSiteId()])
                ->exec()->fetchSelected('xml_id');
        }
    }

    /**
     * @param \XMLReader $reader
     * @throws DbException
     * @throws EventException
     * @throws OrmException
     * @throws RSException
     */
    public function import(\XMLReader $reader)
    {
        $config = $this->getConfig();
        $import_hash = md5($this->getSimpleXML()->asXML());
        $xml_id = $this->getProductXMLId();

        if ($config['import_scheme'] == 'offers_in_import' && $config['import_offers_in_one_product'] && preg_match('/^(.*?)#/', $xml_id, $matches)) {
            $xml_id = $matches[1];
        }

        if (isset(self::$_cache_import_hash[$xml_id]) && self::$_cache_import_hash[$xml_id]['import_hash'] == $import_hash) {
            $this->log->write(t("Нет изменений в товаре: ") . (string)$this->getSimpleXML()->Наименование . t(" Артикул:") . (string)$this->getSimpleXML()->Артикул, LogExchange::LEVEL_PRODUCT_IMPORT);
            OrmRequest::make()
                ->update(new Product())
                ->set(['processed' => 1])
                ->where([
                    'xml_id' => $xml_id,
                    'site_id' => SiteManager::getSiteId(),
                ])
                ->exec();

            // Заносим товар в сессию для манипуляций по завершению импорта
            $_SESSION[self::SESS_KEY_GOODS_IDS][self::$_cache_import_hash[$xml_id]['id']] = self::$_cache_import_hash[$xml_id]['id'];
        } else {
            $this->log->write(t("Импорт товара: ") . (string)$this->getSimpleXML()->Наименование . t(" Артикул:") . (string)$this->getSimpleXML()->Артикул, LogExchange::LEVEL_PRODUCT_IMPORT);

            $barcode = Tools::toEntityString($this->getSimpleXML()->Артикул);
            $title = Tools::toEntityString($this->getSimpleXML()->Наименование);
            if ($config['remove_offer_from_product_title'] && preg_match('/(.*?) \(.*?\)$/', $title, $matches)) {
                $title = $matches[1];
            }

            // Если включена настройка "Идентифицировать товары по артикулу" - обновим xml_id товара
            if ($config['product_uniq_field'] != 'xml_id') {
                $q = OrmRequest::make()
                    ->update(new Product())
                    ->set(['xml_id' => $xml_id])
                    ->where(['site_id' => SiteManager::getSiteId()])
                    ->limit(1);

                switch ($config['product_uniq_field']) {
                    case 'barcode':
                        if ($barcode) {
                            $q->where(['barcode' => $barcode]);
                        }
                        break;
                    case 'title':
                        if ($title) {
                            $q->where(['title' => $title]);
                        }
                        break;
                }
                $q->exec();
            }

            $categories = [];
            if ($this->getSimpleXML()->Группы->Ид != null) {
                foreach ($this->getSimpleXML()->Группы->Ид as $one) {
                    $catid = self::getCategoryIdByXmlId((string)$one);
                    if ($catid) {
                        $categories[] = $catid;
                    }
                }
            }
            if (empty($categories)) {
                $cat_for_catless_porducts = new Dir($config['cat_for_catless_porducts']);
                if (!empty($cat_for_catless_porducts['id'])) {
                    $categories[] = $cat_for_catless_porducts['id'];
                }
            }

            // Создаем продукт и заполняем поля, которые будут обновлены в любом случае
            $product = new Product();
            $product['site_id'] = SiteManager::getSiteId();
            $product['xml_id'] = $xml_id;
            $product['sku'] = (string)$this->getSimpleXML()->Штрихкод;
            $product['processed'] = 1; //Флаг обработанного товара
            $product['import_hash'] = $import_hash;
            $product['dateof'] = date('c');
            if ($this->getSimpleXML()->Статус == 'Удален' || $this->getSimpleXML()->attributes()->Статус == 'Удален') {
                $product['public'] = 0;
            } elseif (!$config['hide_new_products']) {
                $product['public'] = 1;
            }

            if ($config['import_scheme'] == 'offers_in_import' && !$config['import_offers_in_one_product']) {
                $group_id = $product['xml_id'];
                if (preg_match('/^(.*?)#/', $group_id, $matches)) {
                    $group_id = $matches[1];
                }
                if (isset($_SESSION[self::SEES_KEY_207_GROUP_ID_CACHE][$group_id])) {
                    $product['group_id'] = $group_id;
                    if (!$_SESSION[self::SEES_KEY_207_GROUP_ID_CACHE][$group_id]['updated']) {
                        (new OrmRequest())
                            ->update(Product::_getTable())
                            ->set(['group_id' => $group_id])
                            ->where(['xml_id' => $_SESSION[self::SEES_KEY_207_GROUP_ID_CACHE][$group_id]['id']])
                            ->exec();
                        $_SESSION[self::SEES_KEY_207_GROUP_ID_CACHE][$group_id]['updated'] = true;
                    }
                } else {
                    $_SESSION[self::SEES_KEY_207_GROUP_ID_CACHE][$group_id] = [
                        'id' => $product['xml_id'],
                        'updated' => false,
                    ];
                }
            }

            // Заполняем бренд товара, если он присутствует в выгрузке
            if ($config['import_brand'] && $this->getSimpleXML()->Изготовитель) {
                $brand_xml_id = Tools::toEntityString($this->getSimpleXML()->Изготовитель->Ид);
                $brand_title = Tools::toEntityString($this->getSimpleXML()->Изготовитель->Наименование);
                $product['brand_id'] = self::getBrandIdByXmlId($brand_xml_id, $brand_title);
            }

            // Выбираем поля продукта, обновление которых может быть отключено через настройки модуля
            $product_data = [];
            $product_data['title'] = $title;
            $product_data['barcode'] = $barcode;
            $product_data['description'] = nl2br(Tools::toEntityString($this->getSimpleXML()->Описание));
            if ($config['import_short_description']) {
                $product_data['short_description'] = Tools::toEntityString($this->getSimpleXML()->Описание);
            }

            $rekvisits = []; // Совокупные реквизиты

            // Стандарное располжение реквизитов
            if ($this->getSimpleXML()->ЗначенияРеквизитов->ЗначениеРеквизита != null) {
                foreach ($this->getSimpleXML()->ЗначенияРеквизитов->ЗначениеРеквизита as $one) {
                    $rekvisits[] = $one;
                }
            }

            // В некоторых версиях некоторые реквизиты выпадают из родительского тэга и находятся непосредственно в теге <Товар>
            if ($this->getSimpleXML()->ЗначениеРеквизита != null) {
                foreach ($this->getSimpleXML()->ЗначениеРеквизита as $one) {
                    $rekvisits[] = $one;
                }
            }

            // Перебираем реквизиты
            foreach ($rekvisits as $one) {
                // Заполняем "Вес"
                if ($one->Наименование == "Вес") {
                    $product_data['weight'] = Tools::toEntityString($one->Значение);
                }
                // Заполняем "Краткое описание"
                if ($one->Наименование == "Полное наименование") {
                    switch($config['full_name_to_description']) {
                        case File::FULL_NAME_TO_DESCRIPTION:
                            $product_data['description'] = nl2br( Tools::toEntityString($one->Значение) );
                            break;
                        case File::FULL_NAME_TO_SHORT_DESCRIPTION:
                            $product_data['short_description'] = Tools::toEntityString($one->Значение);
                            break;
                        case File::FULL_NAME_TO_TITLE:
                            $product_data['title'] = Tools::toEntityString($one->Значение);
                            break;
                    }
                }
                // Если передано описание в формате HTML, то используем его
                if ($one->Наименование == "ОписаниеВФорматеHTML") {
                    $product_data['description'] = htmlspecialchars_decode($one->Значение);
                }
            }
            // Заполняем данными продукт
            $product->getFromArray($product_data);

            $product['xdir'] = $categories;

            //Обнуляем кэш данные о комплектациях
            $product['offers_json'] = null;

            // В случает product_update_dir = 0, категория у товара обновлятся не будет
            $product->keepUpdateProductCategory($config['product_update_dir']);

            // В случае catalog_keep_spec_dirs = 1, все прежние связи со спец-категориями будут сохранены
            $product->keepSpecDirs($config['catalog_keep_spec_dirs']);

            // Настройка "Транслитерировать символьный код из названия при _добавлении_ элемента или раздела"
            if ($config['catalog_translit_on_add']) {
                $uniq_postfix = hexdec(substr(md5($xml_id), 0, 4));
                $product['alias'] = Transliteration::str2url(Tools::unEntityString($product['title']), true, 140) . "-" . $uniq_postfix;
                $product['alias'] = preg_replace('/\(\)/', '', $product['alias']);
            }

            // Список полей, которые будут обновлены, если товар уже существует в нашей базе
            $on_duplicate_update_fields = ['xml_id', 'sku', 'title', 'barcode', 'description', 'short_description', 'processed', 'import_hash', 'weight', 'offers_json'];
            if (!$config['hide_new_products']) {
                $on_duplicate_update_fields[] = 'public';
            }
            if ($config['import_brand'] && $this->getSimpleXML()->Изготовитель) {
                $on_duplicate_update_fields[] = 'brand_id';
            }

            // Если обновлять категории
            if ($config['product_update_dir']) {
                $on_duplicate_update_fields[] = 'maindir';
            }

            // Исключаем поля, которые помечены как "не обновлять" в настройках модуля
            $on_duplicate_update_fields = array_diff($on_duplicate_update_fields, (array)$config['dont_update_fields']);

            // Настройка "Транслитерировать символьный код из названия при _обновлении_ элемента или раздела"
            if ($config['catalog_translit_on_update']) {
                $on_duplicate_update_fields[] = 'alias';
            }


            // Загрузка базовой единицы (штуки, килограммы и т.п.)
            if ($this->importBaseUnit($product)) {
                $on_duplicate_update_fields[] = 'unit';
            }

            // Загрузка налогов
            if ($this->importTaxes($product)) {
                $on_duplicate_update_fields[] = 'tax_ids';
            }

            $product['dont_save_offers'] = true; // флаг, предотвращающий перезапись комплектацию
            $product->setFlag(Product::FLAG_DONT_UPDATE_DIR_COUNTER); //Не обновляем счетчики у категорий. Обновим их один раз в конце импорта
            $product->setFlag(Product::FLAG_DONT_RESET_IMPORT_HASH);

            EventManager::fire('exchange.catalogproduct.before', [
                'product' => $product,
                'importer' => $this
            ]);

            // Вставка _ИЛИ_ обновление товара
            $product->insert(false, $on_duplicate_update_fields, ['site_id', 'xml_id']);

            // Если во время вставки произошла ошибка, то бросаем исключение
            if ($product->hasError()) {
                throw new RSException(join(", ", $product->getErrors()));
            }

            $is_product_meets_first = !isset($_SESSION[self::SESS_KEY_GOODS_IDS][$product['id']]);
            // Заносим товар в сессию для манипуляций по завершению импорта
            $_SESSION[self::SESS_KEY_GOODS_IDS][$product['id']] = $product['id'];

            // Загрузка изображения товара
            $this->importImages($product, $is_product_meets_first);

            // Очистка свойств
            $prop_delete = OrmRequest::make()
                ->from(new PropertyLink())
                ->where([
                    'product_id' => $product['id'],
                ])
                ->delete();
            // Если стоит настройка не удалять характеристики созданные на сайте
            if ($config['dont_delete_prop']) {
                $prop_delete->where('xml_id !=""');
            }
            $prop_delete->exec();


            // Загрузка Характеристик товара
            $this->importCharacteristics($product); // Это будет работать только при выгрузке из старой 1C. В новой версии характеристики находятся в offers.xml

            // Загрузка Свойств товара
            $this->importProperties($product, $on_duplicate_update_fields, $is_product_meets_first);
            $this->importDimensions($product, $rekvisits);

            if ($config['product_uniq_field'] != 'xml_id') {
                $offer_api = new OfferApi();
                $offer_api->setFilter([
                    'product_id' => $product['id'],
                    'xml_id:is' => 'NULL',
                ]);
                foreach ($offer_api->getList() as $offer) {
                    $offer->delete();
                }
            }

            EventManager::fire('exchange.catalogproduct.after', [
                'product' => $product,
                'importer' => $this
            ]);
        }
    }

    /**
     * Импорт характеристик (для старой версии 1с, где характеристики содержались в Товаре а не в Предложении)
     * Для новой схемы начиная с 2.07 проскакиеваем импорт этих комплектаций
     *
     * @param Product $product
     */
    private function importCharacteristics(Product $product)
    {
        $config = $this->getConfig();
        //Если нет тегов Характеристика товара
        $props_nodes = $this->getSimpleXML()->ХарактеристикиТовара->ХарактеристикаТовара;
        if($props_nodes == null){
            return;
        }
        //Проверим, если есть <Ид> у характеристик товара, то ничего не импортируем
        //Изменение внесено в связи с выходом CommerceML2 2.07 
        //В некоторых версиях(новых) при 2.07 значение характеристик невозможно определить
        //Поэтому и использовать их тоже нельзя
        if (isset($this->getSimpleXML()->ХарактеристикиТовара->ХарактеристикаТовара[0]->Ид)){
            $_SESSION[self::SESS_KEY_NO_OFFER_PARAMS_FLAG] = true; //Включим флаг показыващий этот особый тип CommerceML
            return;
        }
        
        $props = [];
        foreach($props_nodes as $one){
            $props[Tools::toEntityString((string)$one->Наименование)] = Tools::toEntityString((string)$one->Значение);
        } 
        // Сохнаняем характеристики в дефолтное товарное предложение в таблицу "product_offer"
        $offer = new OrmOffer();
        $offer['site_id']     = SiteManager::getSiteId();
        $offer['product_id']  = $product['id'];
        $offer['title']       = Tools::toEntityString($this->getSimpleXML()->Наименование);
        $offer['barcode']     = Tools::toEntityString($this->getSimpleXML()->Артикул);
        $offer['xml_id']      = $this->getProductXMLId();  // В этом случае xml_id предложения совпадает с xml_id товара
        $offer['propsdata']   = serialize($props);       // Характеристики хранятся в сериализованном виде в поле propsdata
        $offer['processed']   = 1; //Флаг обработанной комплектации

        //Если установлен флаг уникализировать артикулы у комплектаций. Сложим артикул товара и уникальный "хвост"
        if ($config['unique_offer_barcode']){
           $uniq_tail = strtoupper(mb_substr(md5($offer['barcode'].$offer['title'].$offer['xml_id']), 0, 6));
           $offer['barcode'] = $product['barcode']."-".$uniq_tail;
        }
        
        //Поля которые, будут обновлены при совпадении строки
        $on_duplicate_update_fields = array_diff_key(['title', 'barcode', 'pricedata', 'propsdata','processed'], $config['dont_update_offer_fields']);
        
        $offer->insert(false, $on_duplicate_update_fields, ['xml_id', 'site_id']);
    }

    /**
     * Импорт габаритов товарв в характеристику из реквизитов
     *
     * @param Product $product
     */
    private function importDimensions(Product $product, $rekvisits)
    {
        $catalog_config = $this->getCatalogConfig();
        $exchange_config = $this->getConfig();

        foreach ($rekvisits as $one) {
            if (in_array($one->Наименование, ['Длина', 'Ширина', 'Высота'])) {
                $value = (float)$one->Значение;
                $value = $this->convertDimension($exchange_config->dimensions_unit, $catalog_config->dimensions_unit, $value);
                if ($one->Наименование == 'Длина' && $catalog_config['property_product_length']) {
                    $this->insertProperty($product['id'], $product['xml_id'], $catalog_config['property_product_length'], $value);
                }
                if ($one->Наименование == 'Ширина' && $catalog_config['property_product_width']) {
                    $this->insertProperty($product['id'], $product['xml_id'], $catalog_config['property_product_width'], $value);
                }
                if ($one->Наименование == 'Высота' && $catalog_config['property_product_height']) {
                    $this->insertProperty($product['id'], $product['xml_id'], $catalog_config['property_product_height'], $value);
                }
            }
        }
    }

    /**
     * Конвертирует значение из одной единицы измерения в другую
     *
     * @param string $from_unit Значение константы ProductDimensions::DIMENSION_...
     * @param string $to_unit Значение константы ProductDimensions::DIMENSION_...
     * @param $value Значение
     * @return float|int
     */
    private function convertDimension($from_unit, $to_unit, $value)
    {
        if (isset(ProductDimensions::DIMENSION_COEFFICIENT[$to_unit])) {
            $value = $value * (ProductDimensions::DIMENSION_COEFFICIENT[$from_unit] / ProductDimensions::DIMENSION_COEFFICIENT[$to_unit]);
        }
        return $value;
    }

    /**
     * Импорт свойств товаров
     *
     * @param Product $product
     * @param string[] $on_duplicate_update_fields
     * @param bool $is_product_meets_first - товар встречается впервые за время импорта
     * @throws EventException
     * @throws RSException
     */
    private function importProperties(Product $product, $on_duplicate_update_fields, bool $is_product_meets_first)
    {
        $config = $this->getConfig();

        $props_nodes = $this->getSimpleXML()->ЗначенияСвойств->ЗначенияСвойства;
        if ($props_nodes == null) {
            return;
        }

        // Смотреть ли на разделитель в тексте (это флаг для множественного свойства)
        // Для текстовых полей с символом радлителя указанном в настройках (Например ";")
        $separator = $config['multi_separator_fields'];

        // Для каждого свойства товара
        foreach ($props_nodes as $one) {
            $prop_id = self::getPropertyIdByXmlId((string)$one->Ид);

            foreach ($one->Значение as $one_value) { // Может быть указано несколько списковых значений

                $value = (string)$one_value;
                /**
                 * Элемент "Значение" может содержать как само значение, так и xml_id значения.
                 * На данном этапе мы не знаем что именно в нем находится.
                 * Будем предполагать, что в нем находится xml_id.
                 * Если данного xml_id в справочникике не найдется, значит это само значение
                 */

                $value_by_xml_id = ImporterCatalogProperty::getPropertyAllowedValueByXmlId($value);

                // Если элемент "Значение" содержит xml_id
                if ($value_by_xml_id !== null) {
                    // Перезаписываем значение
                    $value = $value_by_xml_id;
                }

                // Пустые значения свойств игнорируются
                if (trim($value) === "") {
                    continue;
                }

                // Импортируем вес из характеристики
                if ($config['weight_property'] && $config['weight_property'] == $prop_id) {
                    $product['weight'] = floatval(str_replace([','], ['.'], str_replace(["\xc2\xa0", " ", "\r","\n"], '', $value)));
                    $product->insert(false, $on_duplicate_update_fields, ['site_id', 'xml_id']);
                }

                if (!empty($separator)) { //Если флаг разделителя в настройках указан делаем проверку на множественное свойство
                    $value = trim($value, $separator);
                    if (strpos($value, $separator) !== false) { //Если разделитель найден
                        $values = explode($separator, $value);
                        foreach ($values as $val) {
                            $this->insertProperty($product['id'], $product['xml_id'], $prop_id, $val);
                        }
                        $this->setUpdatableProperty($prop_id); //Устанавливает свойства, для обновления типа
                    } else {
                        $this->insertProperty($product['id'], $product['xml_id'], $prop_id, $value);
                    }
                } else { //Если флаг разделителя в настройках не указан, то вставляем
                    $this->insertProperty($product['id'], $product['xml_id'], $prop_id, $value);
                }
            }
        }
    }

    /**
     * Вставляет в БД значение импортированного свойства для товара
     *
     * @param integer $product_id - id товара
     * @param string $xml_id - внешний идентификатор
     * @param integer $prop_id - id свойства
     * @param string $value - значение свойства
     * @throws EventException
     * @throws RSException
     */
    private function insertProperty($product_id, $xml_id, $prop_id, $value)
    {
        $value_escaped = Tools::toEntityString((string)$value);
        $type = self::getPropertyTypeById($prop_id);
        if (in_array($type, PropertyItem::getListTypes())) {
            //Если это списковая характеристика, то добавляем возможное значение характеристики
            $val_list_id = PropertyItemValue::getIdByValue($prop_id, $value_escaped);
        } else {
            $val_list_id = null;
        }

        $prop_link = new PropertyLink();
        $prop_link['product_id'] = $product_id;
        $prop_link['prop_id'] = $prop_id;
        $prop_link['val_str'] = $value_escaped;
        $prop_link['val_int'] = ($value == 'true') ? 1 : floatval(str_replace([','], ['.'], str_replace(["\xc2\xa0", " ", "\r","\n"], '', $value)));
        $prop_link['val_list_id'] = $val_list_id;
        $prop_link['xml_id'] = $xml_id;
        if ($type == PropertyItem::TYPE_TEXT) {
            $prop_link['val_text'] = $value_escaped;
        }

        $prop_link->insert();
    }

    /**
     * Устанавливает в массив свойства у которых будет изменён тип в дальнейшем
     *
     * @param integer $property_id - id свойства для последубщего обновления типа
     */
    private function setUpdatableProperty($property_id)
    {
        if (!isset($_SESSION[self::SESSION_KEY][$property_id])) {
            $_SESSION[self::SESSION_KEY][$property_id] = true;
        }
    }

    /**
     * Возращает массив со свойствами для обновления
     *
     * @return array - возвращает массив со свойствами
     */
    public static function getPropertiesToUpdate()
    {
        if (!empty($_SESSION[self::SESSION_KEY])) {
            return array_keys($_SESSION[self::SESSION_KEY]);
        }
        return [];
    }

    /**
     * Импорт изображений товра
     *
     * @param Product $product - товар
     * @param bool $is_product_meets_first - товар встречается впервые за время импорта
     * @throws DbException
     * @throws EventException
     * @throws RSException
     */
    protected function importImages(Product $product, bool $is_product_meets_first)
    {
        $xml_id = (string)$this->getProductXMLId();

        if (!$this->getConfig()->force_delete_images
            && !(string)$this->getSimpleXML()->Картинка) {
            return;
        }

        // Для каждого изображения
        $exists_photos_id = [];
        if ($this->getSimpleXML()->Картинка) {
            foreach ($this->getSimpleXML()->Картинка as $one) {

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
                    $this->log->write(t('Загружено изображение для товара с артикулом "%0", Ид "%1"', [
                        $product['barcode'],
                        $xml_id,
                    ]), LogExchange::LEVEL_PRODUCT_IMPORT_DETAIL);

                    $exists_photos_id[] = $image['id'];
                }
            }
        }

        if ($is_product_meets_first) {
            //Удаляем фото, не присутствующие в выгрузке
            $q = OrmRequest::make()
                ->from(new Image)
                ->where([
                    'site_id' => SiteManager::getSiteId(),
                    'extra' => $xml_id,
                ]);

            if ($exists_photos_id) {
                $q->where("id NOT IN (" . implode(",", $exists_photos_id) . ')');
            }
            foreach($q->objects() as $image) {
                $image->delete();
            }
        }
    }

    /**
     * Импорт налогов из тега <СтавкиНалогов>
     * Парсит список налогов товара
     * Вставляет налог в справочник order_tax (если его еще нет)
     * Вставляет ставку налога в таблицу связи order_tax_rate
     * Привязывает товар к списку налогов через поле tax_ids (перечисляя идентификаторы через запятую)
     *
     * @param Product $product
     * @return bool
     * @throws EventException
     * @throws OrmException
     */
    private function importTaxes(Product $product)
    {
        if (!(string)$this->getSimpleXML()->СтавкиНалогов->СтавкаНалога) {   // Если налогов нет, ничего не делаем
            return false;
        }

        $product_taxes = [];

        // Для каждого налога этого товара
        foreach ($this->getSimpleXML()->СтавкиНалогов->СтавкаНалога as $one) {
            $alias = Transliteration::str2url($one->Наименование . "-" . $one->Ставка);
            $tax_id = $this->getTaxIdByAlias($alias);
            // Если такого налога в системе ще нет
            if ($tax_id === false) {
                $default_region = RegionApi::getDefaultRegion();
                // Вставляем налог
                $tax = new Tax();
                $tax['alias'] = $alias;
                $tax['title'] = Tools::toEntityString($one->Наименование) . ', ' . $one->Ставка . '%';
                $tax['description'] = $tax['title'];
                $tax['user_type'] = 'all';
                $tax['included'] = 1;
                $tax['enabled'] = 1;
                $tax->insert();
                $tax_id = $tax['id'];

                // Вставляем процент налога
                $tax_rate = new TaxRate();
                $tax_rate['tax_id'] = $tax->id;
                $tax_rate['region_id'] = $default_region['id']; //Россия
                $tax_rate['rate'] = (string)$one->Ставка;
                $tax_rate['insert()'];

            }
            $product_taxes[] = $tax_id;
        }

        // Прикрепляем налоги к продукту
        $product['tax_ids'] = join(',', $product_taxes);
        return true;
    }

    /**
     * Импорт единиц измерения
     *
     * @param Product $product
     * @return bool
     */
    private function importBaseUnit(Product $product)
    {
        if (!(string)$this->getSimpleXML()->БазоваяЕдиница) {   // Если единицы нет, ничего не делаем
            return false;
        }
        $code = @$this->getSimpleXML()->БазоваяЕдиница['Код'];
        $inter_sokr = @$this->getSimpleXML()->БазоваяЕдиница['МеждународноеСокращение'];
        $full_title = $this->getSimpleXML()->БазоваяЕдиница['НаименованиеПолное'];
        $short_title = (string)$this->getSimpleXML()->БазоваяЕдиница;

        if (empty($full_title)) { //Если полного наименования не указано.
            $full_title = $short_title;
        }

        // Получаем идентификатор единицы изменерия по коду
        if (!empty($code)) {
            $unit_id = self::getUnitIdByCode($code);
        } elseif (!empty($short_title)) { // Получаем идентификатор единицы изменерия по полному наименованию
            $unit_id = self::getUnitIdByName($short_title);
        }

        if (empty($unit_id)) {
            // Если единицы измерения еще нет - вставляем
            $unit = new Unit();
            $unit['code'] = $code;
            $unit['icode'] = $inter_sokr;
            $unit['title'] = $full_title;
            $unit['stitle'] = $short_title;
            $unit->insert();
            $unit_id = $unit['id'];
        }

        $product['unit'] = $unit_id;
        return true;
    }

    /**
     * Получить XML_ID
     * @return string
     */
    protected function getProductXMLId()
    {
        $xml_id = (string)$this->getSimpleXML()->Ид;
        return $xml_id;
    }

    /**
     * Получить id бренда по xml_id. Результат кешируется
     * Если бренд отсутствует - он будет создан
     *
     * @param string $brand_xml_id - внешний идентификатор бренда
     * @param string $brand_title - название бренда
     * @return int
     */
    static private function getBrandIdByXmlId($brand_xml_id, $brand_title)
    {
        if (!array_key_exists($brand_xml_id, self::$_cache_brands)) {
            $brand = Brand::loadByWhere([
                'site_id' => SiteManager::getSiteId(),
                'xml_id' => $brand_xml_id,
            ]);
            if (!$brand['id']) {
                $brand['site_id'] = SiteManager::getSiteId();
                $brand['public'] = 1;
                $brand['xml_id'] = $brand_xml_id;
                $brand['title'] = $brand_title;
                $brand['alias'] = Transliteration::str2url($brand['title']);

                $same_aliases = OrmRequest::make()
                    ->select('alias')
                    ->from(new Brand())
                    ->where('alias like "#brand_alias%"', ['brand_alias' => $brand['alias']])
                    ->exec()->fetchSelected('alias', 'alias');
                if (in_array($brand['alias'], $same_aliases)) {
                    $counter = 2;
                    while (in_array($brand['alias'] . $counter, $same_aliases)) {
                        $counter++;
                    }
                    $brand['alias'] .= $counter;
                }

                $brand->insert();
            }
            self::$_cache_brands[$brand_xml_id] = $brand['id'];
        }
        return self::$_cache_brands[$brand_xml_id];
    }

    /**
     * Получить id категории по xml_id. Результат кешируется
     *
     * @param String $category_xml_id
     * @return int
     */
    static private function getCategoryIdByXmlId($category_xml_id)
    {
        if (!array_key_exists($category_xml_id, self::$_cache_categories)) {
            $dir = Dir::loadByWhere([
                'site_id' => SiteManager::getSiteId(),
                'xml_id' => $category_xml_id,
            ]);
            if (!$dir['id']) {
                LogExchange::getInstance()->write(t("Не найдена категория ") . $category_xml_id, LogExchange::LEVEL_PRODUCT_IMPORT);
                return false;
            }
            self::$_cache_categories[$category_xml_id] = $dir['id'];
        }
        return self::$_cache_categories[$category_xml_id];
    }

    /**
     * Получить id свойства по xml_id. Результат кешируется
     *
     * @param String $property_xml_id
     * @return int
     * @throws RSException
     */
    static private function getPropertyIdByXmlId($property_xml_id)
    {
        if (!array_key_exists($property_xml_id, self::$_cache_properties)) {
            $prop = PropertyItem::loadByWhere([
                'site_id' => SiteManager::getSiteId(),
                'xml_id' => $property_xml_id,
            ]);
            if (!$prop['id']) {
                throw new RSException(t("Не найдено свойство ") . $property_xml_id);
            }
            self::$_cache_properties[$property_xml_id] = $prop['id'];
            self::$_cache_properties_types[$prop['id']] = $prop['type'];
        }
        return self::$_cache_properties[$property_xml_id];
    }

    /**
     * Возвращает тип характеристики по её id
     *
     * @param int $property_id
     * @return string
     * @throws RSException
     */
    static private function getPropertyTypeById($property_id)
    {

        if (!isset(self::$_cache_properties_types[$property_id])) {
            $prop = new PropertyItem($property_id);
            if (!$prop['id']) {
                throw new RSException(t("Не найдено свойство по ID") . $property_id);
            }
            self::$_cache_properties_types[$property_id] = $prop['type'];
        }
        return self::$_cache_properties_types[$property_id];
    }

    /**
     * Возвращает id налоге по его английскому идентификатору
     *
     * @param string $tax_alias
     * @return bool|int
     */
    static private function getTaxIdByAlias($tax_alias)
    {
        if (!array_key_exists($tax_alias, self::$_cache_taxes)) {
            $tax = Tax::loadByWhere([
                'site_id' => SiteManager::getSiteId(),
                'alias' => $tax_alias,
            ]);
            if (!$tax['id']) {
                return false;
            }
            self::$_cache_taxes[$tax_alias] = $tax['id'];
        }
        return self::$_cache_taxes[$tax_alias];
    }

    /**
     * Получает единицу измерения по её коду
     *
     * @param string $unit_name - наименование единицы измерения
     * @return int
     */
    static function getUnitIdByName($unit_name)
    {
        $unit_name = (string)$unit_name;
        if (!array_key_exists($unit_name, self::$_cache_units_by_name)) {
            $unit = Unit::loadByWhere([
                'site_id' => SiteManager::getSiteId(),
                'stitle' => $unit_name
            ]);
            if (!$unit['id']) {
                return false;
            }
            self::$_cache_units_by_name[$unit_name] = $unit['id'];
        }
        return self::$_cache_units_by_name[$unit_name];
    }

    /**
     * Получает единицу измерения по её коду
     *
     * @param string $unit_code - код единицы измерения
     * @return int
     */
    static function getUnitIdByCode($unit_code)
    {
        $unit_code = (string)$unit_code;
        if (!array_key_exists($unit_code, self::$_cache_units_by_code)) {
            $unit = Unit::loadByWhere([
                'site_id' => SiteManager::getSiteId(),
                'code' => $unit_code
            ]);
            if (!$unit['id']) {
                return false;
            }
            self::$_cache_units_by_code[$unit_code] = $unit['id'];
        }
        return self::$_cache_units_by_code[$unit_code];
    }
}
