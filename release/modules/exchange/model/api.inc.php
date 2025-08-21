<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Exchange\Model;

use Exchange\Model\Log\LogExchange;
use RS\Config\Loader as ConfigLoader;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Exception as RSException;
use RS\File\Tools;
use RS\Helper\Tools as HelperTools;
use RS\Orm\Exception as OrmException;
use RS\Module\AbstractModel\BaseModel;
use RS\Site\Manager as SiteManager;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\OrderItem;
use Shop\Model\Orm\UserStatus;
use RS\HashStore\Api as HashStoreApi;
use Shop\Model\TaxApi;

class Api extends BaseModel
{
    const LAST_EXCHANGE_DATE_KEY = 'last_exchange_date';

    static private $inst = null;

    static public $session_file = "session.lock"; //Файл в котором хранится сессия

    private $basedir;       // Корневая папка модуля
    private $dir;           // Массив директорий для импорта/экспорта
    private $config;
    private $matcher;

    /** @var LogExchange */
    protected $log;

    static public function getInstance()
    {
        if (self::$inst == null) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    private function __construct()
    {
        $this->log = LogExchange::getInstance();

        $this->matcher = \Exchange\Model\Matcher::getInstance();
        $this->config = \RS\Config\Loader::byModule($this);
        $this->basedir = \Setup::$ROOT . \Setup::$STORAGE_DIR . DS . 'exchange' . DS . SiteManager::getSiteId();
        if (!is_dir($this->getDir('import'))) {
            @mkdir($this->getDir('import'), \Setup::$CREATE_DIR_RIGHTS, true);
            Tools::makePrivateDir($this->basedir);
        }
        if (!is_dir($this->getDir('export'))) {
            @mkdir($this->getDir('export'), \Setup::$CREATE_DIR_RIGHTS, true);
            Tools::makePrivateDir($this->basedir);
        }
    }

    /**
     * Получить корневую дерикторию модуля для хранения файлов
     * @return string
     */
    public function getBaseDir()
    {
        return $this->basedir;
    }

    /**
     * Очистка папки для импорта/экспорта
     *
     * @param string $type - тип файлов
     * @return void
     */
    public function clearExchangeDirectory($type = 'import')
    {
        if ($this->config->history_depth > 0) {
            $history_dir = $this->getDir($type, true);
            if (!is_dir($history_dir)) {
                @mkdir($history_dir, \Setup::$CREATE_DIR_RIGHTS, true);
            }
            // Очищаем папку с историей
            self::deleteFolder($history_dir . DS . $this->config->history_depth);

            // Переносим папку import в папку с историей
            for ($i = $this->config->history_depth; $i > 1; $i--) {
                @rename($history_dir . DS . ($i - 1), $history_dir . DS . $i);
            }
            @rename($this->getDir($type), $history_dir . DS . '1');
        } else {
            self::deleteFolder($this->getDir($type));
        }
        @mkdir($this->getDir($type), \Setup::$CREATE_DIR_RIGHTS, true);
    }

    /**
     * Является ли папка для импорта пустой
     *
     * @return bool
     */
    public function isExchangeDirectoryEmpty()
    {
        return count(scandir($this->getDir())) == 2;
    }

    /**
     * Сохранить файл в папку для импорта
     *
     * @param string $filename Имя файла
     * @param string $filedata Содержимое файла
     * @param string $type - тип файлов
     * @return void
     */
    public function saveUploadedFile($filename, $filedata, $type = 'import')
    {
        $dir = dirname($this->getDir($type) . DS . $filename);
        if (!is_dir($dir)) {
            mkdir($dir, \Setup::$CREATE_DIR_RIGHTS, true);
        }
        if (file_exists($this->getDir($type) . DS . $filename)) {
            file_put_contents($this->getDir($type) . DS . $filename, $filedata, FILE_APPEND);
        } else {
            file_put_contents($this->getDir($type) . DS . $filename, $filedata);
        }
    }

    /**
     * Импортировать XML-файл каталога товаров (offers.xml или import.xml)
     *
     * @param mixed $filename Имя XML-файла
     * @param mixed $offset Смещение в файле для импорта в xml-нодах
     * @param mixed $max_exec_time Максимальное время выполнения в секундах
     * @return boolean
     * @throws EventException
     * @throws RSException
     */
    public function catalogImport($filename, $offset, $max_exec_time = 0)
    {
        $full_filename = $this->getDir('import') . DS . $filename;
        if (!file_exists($full_filename)) {
            $this->extractAllArchives();
        }

        if (!file_exists($full_filename)) {
            throw new RSException(t("Файл %0 не найден", [$filename]));
        }

        // Здесь импортируем данные из XML файла, смотрим по имени файла
        if (preg_match('/import/iu', $filename)) { //import.xml
            $importers = [
                '\Exchange\Model\Importers\Catalog',
                '\Exchange\Model\Importers\CatalogGroup',
                '\Exchange\Model\Importers\CatalogProduct',
                '\Exchange\Model\Importers\CatalogProperty',
                '\Exchange\Model\Importers\CatalogContainsOnlyChanges', // Для старой версии схемы: 2.03. В этой версии флаг "СодержитТолькоИзменения" передается через отдельный тэг внутри тэга "Каталог", а не через аттрибут тэга "Каталог"
            ];
        } elseif (preg_match('/offers/iu', $filename)) { //offers.xml
            //Очистим сессию с id складов для получения 
            $_SESSION[\Exchange\Model\Importers\Warehouse::SESS_KEY_WAREHOUSE_IDS] = [];
            $importers = [
                '\Exchange\Model\Importers\CatalogProperty',
                '\Exchange\Model\Importers\PriceType',
                '\Exchange\Model\Importers\Warehouse',
                '\Exchange\Model\Importers\Offer',
            ];
        } else {
            throw new RSException(t('Недопустимое имя файла "%0"', [$filename]));
        }

        $event_result = \RS\Event\Manager::fire('exchange.catalogimport.importers', [
            'importers' => $importers,
            'filename' => $filename,
        ]);
        $result_params = $event_result->getResult();
        $importers = $result_params['importers'];

        return $this->matcher->applyImporters($full_filename, $importers, $offset, $max_exec_time);
    }

    /**
     * Импортировать заказы
     *
     * @param $filename
     * @param $offset
     * @param int $max_exec_time
     * @return boolean
     * @throws RSException
     */
    public function saleImport($filename, $offset = 0, $max_exec_time = 0)
    {
        $full_filename = $this->getDir('orders') . DS . $filename;
        list($name, $ext) = Tools::parseFileName($filename, true);

        if (!file_exists($full_filename) || $ext == 'zip') {
            $this->extractAllArchives('orders');
        }

        if ($this->config['import_on_sale_file'] ) {
            //Ищем XML файл после распаковки в спрефиксом "orders"
            $files = glob($this->getDir('orders'). DS . 'orders*.xml');
            if ($files) {
                $full_filename = $files[0];
            }
        }

        if (!file_exists($full_filename)) {
            throw new RSException(t("Не найден XML-файл, содержащий заказы"));
        }

        // Здесь импортируем заказы из XML файла
        return $this->matcher->applyImporters($full_filename,
            [
                '\Exchange\Model\Importers\Document'
            ], $offset, $max_exec_time);
    }

    /**
     * Создать XML со списком заказов
     *
     * @param mixed $statuses
     * @return string
     * @throws DbException
     * @throws RSException
     * @throws OrmException
     */
    public function createSalesXML(array $statuses)
    {
        // Получаем список заказов
        $q = \RS\Orm\Request::make()
            ->select('O.*')
            ->from(new \Shop\Model\Orm\Order, 'O');
        $q->where([
            'O.site_id' => SiteManager::getSiteId(), // С текущего сайта
            'O.is_exported' => 0                          // Которые еще не были экспортированы
        ]);
        // Если передан список статусов, выбираем заказы только с этими статусами
        if (!empty($statuses)) {
            $q->whereIn('O.status', $statuses);
        }
        // Если установлена настройка "Выгружать только оплаченные заказы", выбираем только оплаченные заказы
        if ($this->config->sale_export_only_payed) {
            $q->where([
                'O.is_payed' => 1
            ]);
        }

        //Вызовем хук
        // todo описать событие в документации
        \RS\Event\Manager::fire('exchange.orderexport.selectorders', [
            'orm_request' => $q,
            'statuses' => $statuses,
        ]);

        $q->limit($this->config['one_step_export_limit']);
        $orders = $q->objects();

        if (empty($orders)) {
            $this->log->write(t("Изменения заказов не зарегистрированы"), Log\LogExchange::LEVEL_ORDER_EXPORT);
        } else {
            $this->log->write(t("Начинаем выгрузку ") . count($orders) . t(" заказов"), Log\LogExchange::LEVEL_ORDER_EXPORT);
        }

        // Скорректируем время формирования файла
        $exporttime = date('Y-m-d\TH:i:s');
        if (!empty($this->config['export_timezone']) && $this->config['export_timezone'] != 'default') {
            $exporttime = date('Y-m-d\TH:i:s', strtotime($exporttime) + $this->getExportTimeOffset());
        }

        $sxml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><КоммерческаяИнформация />');
        $sxml['ВерсияСхемы'] = "2.04";
        $sxml['ДатаФормирования'] = $exporttime;

        foreach ($orders as $one) {
            // Преобразуем заказ в XML
            XMLTools::sxmlAppend($sxml, $this->orderToXml($one));

            // Помечаем заказ как "выгруженный"
            $one['is_exported'] = 1;
            $one->update();
        }

        $this->log->write(t("xml заказов сформирован"), Log\LogExchange::LEVEL_ORDER_EXPORT);

        return $sxml->asXML();
    }

    /**
     * Получает xml_id товарного предложения по товару
     *
     * @param \Shop\Model\Orm\OrderItem $offer_product - объект товара в заказе
     * @param \Catalog\Model\Orm\Product $product - объект связанного товара
     * @return string
     * @throws DbException
     * @throws RSException
     * @throws OrmException
     */
    private function getOfferXmlIdByOfferProduct(OrderItem $offer_product, $product)
    {
        if (!isset($offer_product['offer'])) {
            $offer_product['offer'] = 0;
        }

        $product->fillOffers();
        if (!empty($product['offers']['items'][$offer_product['offer']]['xml_id'])) {
            return $product['offers']['items'][$offer_product['offer']]['xml_id'];
        }
        return $product['xml_id'];
    }

    /**
     * Преобразовать один заказ в XML. Возвращает SimpleXMLElement
     *
     * @param Order $order
     * @return \SimpleXMLElement
     * @throws DbException
     * @throws RSException
     * @throws OrmException
     */
    function orderToXml(Order $order = null)
    {
        $sxml = new \SimpleXMLElement('<Документ/>');
        //Вызовем хук
        \RS\Event\Manager::fire('exchange.orderexport.before', [
            'order' => $order,
            'xml' => $sxml,
        ]);

        $order_items = $order->getCart()->getProductItems();

        // Скорректируем время оформления заказа
        $exportdateof = $order->dateof;
        if (!empty($this->config['export_timezone']) && $this->config['export_timezone'] != 'default') {
            $exportdateof = date('Y-m-d H:i:s', strtotime($exportdateof) + $this->getExportTimeOffset());
        }

        $this->log->write(t("Формируем xml для заказа id - {$order->id}"), Log\LogExchange::LEVEL_ORDER_EXPORT);
        $sxml->Ид           = $order->id;
        $sxml->Номер        = $order->order_num;
        $sxml->Дата         = date('Y-m-d', strtotime($exportdateof));
        $sxml->Время        = date('H:i:s', strtotime($exportdateof));
        $sxml->ХозОперация  = 'Заказ товара';
        $sxml->Роль         = 'Продавец';
        $sxml->Сумма        = $order->totalcost;
        $sxml->Валюта       = $this->config->sale_replace_currency ? $this->config->sale_replace_currency : $order->currency;
        $sxml->Курс         = 1;
        $sxml->Кратность    = 1;
        $sxml->Контрагенты->Контрагент->Ид = $order->user_id.'#'.$order->getUser()->login.'#'.$order->getUser()->getFio();
        $sxml->Контрагенты->Контрагент->Роль = 'Покупатель';
        if($order->getUser()->is_company){
            // Если юридическое лицо или ИП
            $sxml->Контрагенты->Контрагент->ИНН = $order->getUser()->company_inn;
            $sxml->Контрагенты->Контрагент->Наименование = htmlspecialchars_decode($order->getUser()->company);

            if (strlen($order->getUser()->company_inn) == 12 && !$this->config['export_ip_in_official_name']) {
                //Для ИП
                $sxml->Контрагенты->Контрагент->ПолноеНаименование = htmlspecialchars_decode($order->getUser()->company);
            } else {
                //Если присутствует тег ОфициальноеНаименование, то контрагент автоматически становится юр.лицом в 1С
                $sxml->Контрагенты->Контрагент->ОфициальноеНаименование = htmlspecialchars_decode($order->getUser()->company);
            }

            $sxml->Контрагенты->Контрагент->Адрес->Представление = $order->getAddress()->getLineView();
            $sxml->Контрагенты->Контрагент->Адрес->АдресноеПоле[0]->Тип      = 'Индекс';
            $sxml->Контрагенты->Контрагент->Адрес->АдресноеПоле[0]->Значение = $order->getAddress()->zipcode;
            $sxml->Контрагенты->Контрагент->Адрес->АдресноеПоле[1]->Тип      = 'Страна';
            $sxml->Контрагенты->Контрагент->Адрес->АдресноеПоле[1]->Значение = $order->getAddress()->country;
            $sxml->Контрагенты->Контрагент->Адрес->АдресноеПоле[2]->Тип      = 'Город';
            $sxml->Контрагенты->Контрагент->Адрес->АдресноеПоле[2]->Значение = $order->getAddress()->city;
            $sxml->Контрагенты->Контрагент->Адрес->АдресноеПоле[3]->Тип      = 'Улица';
            $sxml->Контрагенты->Контрагент->Адрес->АдресноеПоле[3]->Значение = $order->getAddress()->address;

            $sxml->Контрагенты->Контрагент->ЮридическийАдрес->Представление = $order->getAddress()->getLineView();
            $sxml->Контрагенты->Контрагент->ЮридическийАдрес->АдресноеПоле[0]->Тип      = 'Индекс';
            $sxml->Контрагенты->Контрагент->ЮридическийАдрес->АдресноеПоле[0]->Значение = $order->getAddress()->zipcode;
            $sxml->Контрагенты->Контрагент->ЮридическийАдрес->АдресноеПоле[1]->Тип      = 'Страна';
            $sxml->Контрагенты->Контрагент->ЮридическийАдрес->АдресноеПоле[1]->Значение = $order->getAddress()->country;
            $sxml->Контрагенты->Контрагент->ЮридическийАдрес->АдресноеПоле[2]->Тип      = 'Город';
            $sxml->Контрагенты->Контрагент->ЮридическийАдрес->АдресноеПоле[2]->Значение = $order->getAddress()->city;
            $sxml->Контрагенты->Контрагент->ЮридическийАдрес->АдресноеПоле[3]->Тип      = 'Улица';
            $sxml->Контрагенты->Контрагент->ЮридическийАдрес->АдресноеПоле[3]->Значение = $order->getAddress()->address;
        }
        else{
            // Если физическое лицо
            $sxml->Контрагенты->Контрагент->Наименование = $order->getUser()->getFio();
            $sxml->Контрагенты->Контрагент->ПолноеНаименование = $order->getUser()->getFio();
            $sxml->Контрагенты->Контрагент->АдресРегистрации->Представление = $order->getAddress()->getLineView();
            $sxml->Контрагенты->Контрагент->АдресРегистрации->АдресноеПоле[0]->Тип      = 'Индекс';
            $sxml->Контрагенты->Контрагент->АдресРегистрации->АдресноеПоле[0]->Значение = $order->getAddress()->zipcode;
            $sxml->Контрагенты->Контрагент->АдресРегистрации->АдресноеПоле[1]->Тип      = 'Страна';
            $sxml->Контрагенты->Контрагент->АдресРегистрации->АдресноеПоле[1]->Значение = $order->getAddress()->country;
            $sxml->Контрагенты->Контрагент->АдресРегистрации->АдресноеПоле[2]->Тип      = 'Город';
            $sxml->Контрагенты->Контрагент->АдресРегистрации->АдресноеПоле[2]->Значение = $order->getAddress()->city;
            $sxml->Контрагенты->Контрагент->АдресРегистрации->АдресноеПоле[3]->Тип      = 'Улица';
            $sxml->Контрагенты->Контрагент->АдресРегистрации->АдресноеПоле[3]->Значение = $order->getAddress()->address;
        }
        $sxml->Контрагенты->Контрагент->Контакты->Контакт[0]->Тип      = 'Почта';
        $sxml->Контрагенты->Контрагент->Контакты->Контакт[0]->Значение = $order->getUser()->e_mail;

        $sxml->Контрагенты->Контрагент->Контакты->Контакт[1]->Тип      = 'ТелефонРабочий';
        $sxml->Контрагенты->Контрагент->Контакты->Контакт[1]->Значение = $order->getUser()->phone;

        $sxml->Комментарий = $order->comments;
        $sxml->Товары = "";

        // Товары, входящие в заказ
        $i = 0;
        foreach ($order_items as $one) {
            $product = $one['product'];
            $order_item = $one['cartitem'];

            $this->log->write(t("Формируем XML для товара id:%0", [$order_item['entity_id']]), Log\LogExchange::LEVEL_ORDER_EXPORT_DETAIL);

            $single_discount = $order_item['amount'] > 0 ? ($order_item['discount'] / $order_item['amount']) : 0;

            // Получаем идентификатор по товару
            $xml_id = $this->getOfferXmlIdByOfferProduct($one['cartitem'], $product);

            $sxml->Товары->Товар[$i]->БазоваяЕдиница = $product->getUnit()->stitle;
            $sxml->Товары->Товар[$i]->БазоваяЕдиница['Код'] = $product->getUnit()->code;
            $sxml->Товары->Товар[$i]->БазоваяЕдиница['НаименованиеПолное'] = $product->getUnit()->title;
            $sxml->Товары->Товар[$i]->БазоваяЕдиница['МеждународноеСокращение'] = $product->getUnit()->icode;
            $sxml->Товары->Товар[$i]->Ид = $xml_id;
            $sxml->Товары->Товар[$i]->Артикул = trim($order_item['barcode']);
            $sxml->Товары->Товар[$i]->Наименование = HelperTools::unEntityString($order_item['model'] ? $order_item['title'] . ", " . $order_item['model'] : $order_item['title']);
            $sxml->Товары->Товар[$i]->ЦенаЗаЕдиницу = $order_item['single_cost'] - $single_discount;
            $sxml->Товары->Товар[$i]->Количество = $order_item['amount'];
            $sxml->Товары->Товар[$i]->Сумма = $order_item['price'] - $order_item['discount'];
            $sxml->Товары->Товар[$i]->ЗначенияРеквизитов->ЗначениеРеквизита[0]->Наименование = 'ВидНоменклатуры';
            $sxml->Товары->Товар[$i]->ЗначенияРеквизитов->ЗначениеРеквизита[0]->Значение = 'Товар';
            $sxml->Товары->Товар[$i]->ЗначенияРеквизитов->ЗначениеРеквизита[1]->Наименование = 'ТипНоменклатуры';
            $sxml->Товары->Товар[$i]->ЗначенияРеквизитов->ЗначениеРеквизита[1]->Значение = 'Товар';

            $taxes = TaxApi::getTaxesByIds($order_item->getExtraParam('tax_ids', []), $order->getUser(), $order->getAddress());

            foreach($taxes as $k => $tax) {
                $tax_rate = (float)$tax->getRate($order->getAddress());
                $tax_part = ($tax->isIncluded()) ? ($tax_rate / (100 + $tax_rate)) : ($tax_rate / 100);
                $tax_value = round(($order_item['price'] - $order_item['discount']) * $tax_part, 2);

                $sxml->Товары->Товар[$i]->Налоги->Налог[$k]->Наименование = $tax['title'];
                $sxml->Товары->Товар[$i]->Налоги->Налог[$k]->УчтеноВСумме = $tax->isIncluded() ? 'true' : 'false';
                $sxml->Товары->Товар[$i]->Налоги->Налог[$k]->Сумма = $tax_value;

                $sxml->Товары->Товар[$i]->СтавкиНалогов->СтавкаНалога[$k]->Наименование = $tax['title'];
                $sxml->Товары->Товар[$i]->СтавкиНалогов->СтавкаНалога[$k]->СтавкаНалога = $tax_rate;
            }

            //Вызовем хук
            \RS\Event\Manager::fire('exchange.orderitemexport.after', [
                'order' => $order,
                'productIndex' => $i,
                'orderItem' => $one,
                'product' => $product,
                'xml' => $sxml
            ]);

            $i++;
        }

        $order_taxes = \RS\Orm\Request::make()
            ->from(new \Shop\Model\Orm\OrderItem)
            ->where([
                'order_id' => $order->id,
                'type'     => 'tax'
            ])
			->objects();

        if ($order_taxes){
            foreach ($order_taxes as $n => $one){
                /*
                * @var \Shop\Model\Orm\Tax $tax
                */
                $tax = new \Shop\Model\Orm\Tax($one->entity_id, false);

                if ($one['price'] > 0) {
                    $sxml->Налоги->Налог[$n]->Наименование = $tax->title;
                    $sxml->Налоги->Налог[$n]->УчтеноВСумме = $tax->isIncluded() ? 'true' : 'false';
                    $sxml->Налоги->Налог[$n]->Сумма = $one['price'];
                }
            }
        }


        if (($delivery = $order->getDelivery())
            && ($order->getDeliveryCost()>0 || $this->config['export_free_delivery'])) {
            $delivery_id = 'ORDER_DELIVERY';
            // МойСклад не распознаёт разные доставки с одним id - уникализируем id
            if ($this->config['uniq_delivery_id']) {
                $delivery_id .= "_{$delivery['id']}";
            }
            $sxml->Товары->Товар[$i]->Ид           = $delivery_id;
            $sxml->Товары->Товар[$i]->Наименование = HelperTools::unEntityString($delivery->title);
            $sxml->Товары->Товар[$i]->БазоваяЕдиница                            = 'шт';
            $sxml->Товары->Товар[$i]->БазоваяЕдиница['Код']                     = 796;
            $sxml->Товары->Товар[$i]->БазоваяЕдиница['НаименованиеПолное']      = 'Штука';
            $sxml->Товары->Товар[$i]->БазоваяЕдиница['МеждународноеСокращение'] = 'PCE';
            $sxml->Товары->Товар[$i]->ЦенаЗаЕдиницу = $order->getDeliveryCost();
            $sxml->Товары->Товар[$i]->Количество    = 1;
            $sxml->Товары->Товар[$i]->Сумма         = $order->getDeliveryCost();
            $sxml->Товары->Товар[$i]->ЗначенияРеквизитов->ЗначениеРеквизита[0]->Наименование = 'ВидНоменклатуры';
            $sxml->Товары->Товар[$i]->ЗначенияРеквизитов->ЗначениеРеквизита[0]->Значение     = 'Услуга';
            $sxml->Товары->Товар[$i]->ЗначенияРеквизитов->ЗначениеРеквизита[1]->Наименование = 'ТипНоменклатуры';
            $sxml->Товары->Товар[$i]->ЗначенияРеквизитов->ЗначениеРеквизита[1]->Значение     = 'Услуга';
        }

        $status = $order->getStatus();
        $i = 0;
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i]->Наименование = 'Статус заказа';
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i++]->Значение   = HelperTools::unEntityString($status['title']);
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i]->Наименование = 'Дата изменения статуса';
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i++]->Значение   = date('d.m.Y H:i:s', strtotime($order->dateof));
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i]->Наименование = $this->config['order_flag_cancel_requisite_name'];
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i++]->Значение   = ($status['type'] == UserStatus::STATUS_CANCELLED || $status['copy_type'] == UserStatus::STATUS_CANCELLED) ? 'true' : 'false';
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i]->Наименование = 'Сайт';
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i++]->Значение   = HelperTools::unEntityString(SiteManager::getSite()->title);
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i]->Наименование = 'Метод оплаты';
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i++]->Значение   = HelperTools::unEntityString($order->getPayment()->title);
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i]->Наименование = 'Метод оплаты ИД';
        $sxml->ЗначенияРеквизитов->ЗначениеРеквизита[$i++]->Значение   = $order['payment'];

        //Вызовем хук
        \RS\Event\Manager::fire('exchange.orderexport.after', [
            'order' => $order,
            'xml' => $sxml
        ]);

        return $sxml;
    }

    /**
     * Распаковать все архивы в папке импорта
     * @return void
     */
    public function extractAllArchives($type = 'import')
    {
        $files = glob($this->getDir($type) . DS . "*.zip");
        foreach ($files as $one) {
            $zip = new \ZipArchive;
            if ($zip->open($one) === true) {
                $zip->extractTo($this->getDir($type));
                $zip->close();
            }
        }
    }

    /**
     * Распаковать архив папке импорта.
     * Возвращает список имен распакованных файлов
     *
     * @return array
     */
    public function extractArchive($zip_filename, $dir_to)
    {
        $extracted_files = [];
        $zip = new \ZipArchive;
        if ($zip->open($zip_filename) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $zip->extractTo($dir_to, [$zip->getNameIndex($i)]);
                $extracted_files[] = $zip->getNameIndex($i);
            }
            $zip->close();
        }
        return $extracted_files;
    }

    /**
     * Доступно ли сжатие ZIP в системе
     * @return bool
     */
    static public function isZipAvailable()
    {
        return class_exists('ZipArchive');
    }

    /**
     * Удалить папку полностью
     *
     * @param string $path
     * @return bool
     */
    static private function deleteFolder($path)
    {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $file) {
                self::deleteFolder(realpath($path) . '/' . $file);
            }
            return @rmdir($path);
        } else if (is_file($path) === true) {
            return @unlink($path);
        }
        return false;
    }

    /**
     * Получить дату последнего обмена данными
     *
     * @param integer $site_id ID сайта. Если null, То текущий
     * @return string
     */
    static public function getLastExchangeDate($site_id = null)
    {
        $site_id = $site_id ?? SiteManager::getSiteId();
        return HashStoreApi::get(self::LAST_EXCHANGE_DATE_KEY.'-'.$site_id);
    }

    /**
     * Установить дату последнего обмена данными
     *
     * @param integer $site_id ID текущего сайта
     * @return void
     */
    static public function setLastExchangeDate($site_id)
    {
        HashStoreApi::set(self::LAST_EXCHANGE_DATE_KEY.'-'.$site_id, date('Y-m-d H:i:s'));
    }

    /**
     * Возвращает список полей товара, обновление которых можно отключить из настроек модуля
     *
     * @param bool $exclude_exceptions Исключая поля, указанные как "не обновлять" в настройках модуля
     * @return array
     * @throws RSException
     */
    static public function getUpdatableProductFields($exclude_exceptions = false)
    {
        $fields = [];
        $fields['title']             = t('Наименование');
        $fields['barcode']           = t('Артикул');
        $fields['dateof']            = t('Дата создания');
        $fields['description']       = t('Описание');
        $fields['short_description'] = t('Краткое описание');
        $fields['public']            = t('Показывать товар');
        $fields['weight']            = t('Вес');
        $fields['sku']               = t('Штрихкод');
        $fields['brand_id']          = t('Бренд');

        if ($exclude_exceptions) {
            $dont_update_fields = (array)\RS\Config\Loader::byModule('exchange')->dont_update_fields;
            return array_diff_key($fields, array_flip($dont_update_fields));
        }

        return $fields;
    }


    /**
     * Возвращает список полей комплектаций, обновление которых можно отключить из настроек модуля
     *
     * @param bool $exclude_exceptions Исключая поля, указанные как "не обновлять" в настройках модуля
     * @return array
     * @throws RSException
     */
    static public function getUpdatableOfferFields($exclude_exceptions = false)
    {
        $fields = [
            'title' => t('Наименование'),
            'barcode' => t('Артикул'),
            'propsdata' => t('Свойства характеристик'),
            'sku' => t('Штрихкод')
        ];

        if ($exclude_exceptions) {
            $dont_update_fields = (array)\RS\Config\Loader::byModule('exchange')->dont_update_offer_fields;
            return array_diff_key($fields, array_flip($dont_update_fields));
        }

        return $fields;
    }

    /**
     * Возвращает список полей товара, обновление которых можно отключить из настроек модуля
     *
     * @param bool $exclude_exceptions Исключая поля, указанные как "не обновлять" в настройках модуля
     * @return array
     * @throws RSException
     */
    static public function getUpdatableGroupFields($exclude_exceptions = false)
    {
        $fields = [];
        $fields['name'] = t('Наименование');
        $fields['public'] = t('Публичность');

        if ($exclude_exceptions) {
            $dont_update_fields = (array)\RS\Config\Loader::byModule('exchange')->dont_update_group_fields;
            return array_diff_key($fields, array_flip($dont_update_fields));
        }

        return $fields;
    }

    /**
     * Возвращает список полей товара, обновление которых можно отключить из настроек модуля
     *
     * @param bool $exclude_exceptions Исключая поля, указанные как "не обновлять" в настройках модуля
     * @return array
     * @throws RSException
     */
    static public function getUpdatablePropFields($exclude_exceptions = false)
    {
        $fields = [
            'title' => t('Наименование'),
        ];

        if ($exclude_exceptions) {
            $config =  ConfigLoader::byModule('exchange');
            $dont_update_fields = (array)$config['dont_update_group_fields'];
            return array_diff_key($fields, array_flip($dont_update_fields));
        }

        return $fields;
    }

    /**
     * Возвращает путь к папке для сохранения файлов
     *
     * @param string $type - тип файлов
     * @param bool $history_dir - вернуть ссылку на папку "истории"
     * @return string
     */
    public function getDir($type = 'import', $history_dir = false)
    {
        $path = $this->basedir;
        if ($history_dir) {
            $path .= DS . 'history';
        }
        $path .= DS . $type;
        return $path;
    }

    /**
     * Получает имя текущего файла с сессионным id
     *
     */
    public static function getSessionFileName()
    {
        return \Setup::$ROOT . \Setup::$STORAGE_DIR . DS . 'exchange' . DS . self::$session_file;
    }

    /**
     * Получает id текущей сессии из файла или из текущей сессии
     *
     */
    public static function getSessionId()
    {
        $file = self::getSessionFileName();
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        return session_id();
    }

    /**
     * Сохраняет идентификатор сессии в файл
     *
     */
    public static function saveSessionIdIntoFile()
    {
        $file = self::getSessionFileName();
        file_put_contents($file, session_id());
    }

    /**
     * Проверяет наличие сессионного файла
     *
     */
    public static function checkSessionFile()
    {
        $file = self::getSessionFileName();
        return file_exists($file);
    }

    /**
     * Удаляет сессионый файл
     *
     */
    public static function removeSessionIdFile()
    {
        $file = self::getSessionFileName();
        @unlink($file);
    }

    /**
     * Получает бренды из характеристик и добавляет очередной новый бренд
     * И обновляет бренды
     *
     */
    public function updateBrandsFromProperties()
    {
        $config_import = \RS\Config\Loader::byModule($this);
        $brand_prop_id = $config_import['brand_property'];

        $this->updateAllBrands($brand_prop_id);
        $this->updateBrandsInProducts($brand_prop_id);
    }

    /**
     * Обновление всех брендов на основании значений характеристики
     * Функция сравнивает новые значения со старыми и если есть несовпадения, то удаляет их.
     *
     * @param integer $brand_prop_id - id характеристики "Производителя"
     * @throws RSException
     * @throws DbException
     * @throws EventException
     * @throws OrmException
     */
    private function updateAllBrands($brand_prop_id)
    {
        //Получим текущие бренды
        $brands = \RS\Orm\Request::make()
            ->select('title')
            ->from(new \Catalog\Model\Orm\Brand())
            ->where([
                'site_id' => SiteManager::getSiteId(),
            ])->exec()
            ->fetchSelected(null, 'title', false);


        //Получим доступные значения характеристики производителя
        $props = \RS\Orm\Request::make()
            ->select('DISTINCT (val_str) as val_str')
            ->from(new \Catalog\Model\Orm\Property\Link())
            ->where([
                'site_id' => SiteManager::getSiteId(),
                'prop_id' => $brand_prop_id,
            ])
            ->where("val_str != ''")
            ->exec()
            ->fetchSelected(null, 'val_str', false);

        $new_brands = array_diff($props, $brands);

        //Если есть различия, и появились новые бренды
        if (!empty($new_brands)) {
            foreach ($new_brands as $brand_title) {
                $brand = new \Catalog\Model\Orm\Brand();
                $brand['title'] = $brand_title;
                $brand['alias'] = \RS\Helper\Transliteration::str2url($brand_title);
                $brand['public'] = 1;
                $brand->insert();
            }
        }
    }

    /**
     * Обновляет id брендов у товаров
     *
     * @param integer $brand_prop_id - id характеристики "Производителя"
     * @throws RSException
     */
    private function updateBrandsInProducts($brand_prop_id)
    {
        $prop = new \Catalog\Model\Orm\Property\Link();
        $brand = new \Catalog\Model\Orm\Brand();

        try {
            $brand_query = '
             (SELECT B.id FROM ' . $prop->_getTable() . ' AS L
                INNER JOIN ' . $brand->_getTable() . ' AS B ON B.title = L.val_str
                WHERE prop_id=' . $brand_prop_id . ' AND L.val_str != "" AND L.product_id = P.id LIMIT 1)
            ';

            $q = \RS\Orm\Request::make()
                ->update()
                ->from(new \Catalog\Model\Orm\Product(), 'P')
                ->set('brand_id = ' . $brand_query)
                ->where([
                    'P.site_id' => SiteManager::getSiteId(),
                ])->exec();
        } catch (RSException $e) {
            if ($e->getCode() == 1242) {
                throw new RSException(t('Ошибка при обновлении брендов у товара! 
                Убедитесь, что характеристика бренда имеет строковый тип.')
                    , $e->getCode());
            } else {
                throw $e;
            }
        }
    }

    /**
     * Возвращает разницу в секундах между часовым поясом системы и часовым поясом для экспрта
     *
     * @return int
     * @throws \Exception
     */
    private function getExportTimeOffset()
    {
        $systemTimezone = new \DateTimeZone(\Setup::$TIMEZONE);
        $exportTimezone = new \DateTimeZone($this->config['export_timezone']);
        $timeoffset = $exportTimezone->getOffset(new \DateTime("now")) - $systemTimezone->getOffset(new \DateTime("now"));

        return $timeoffset;
    }
}
