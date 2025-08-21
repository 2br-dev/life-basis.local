<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;

use Catalog\Model\Orm\Offer;
use Catalog\Model\Orm\Product;
use RS\Helper\Tools;
use RS\Orm\Request;
use Shop\Config\File;

/**
 * Класс отвечает за импорт/экспорт товаров в корзине на стороне пользователя
 */
class CartExchange
{
    const CHARSET_WIN1251 = 'windows-1251';
    const CHARSET_UTF8 = 'utf-8';

    const REPORT_MISSING_PRODUCTS = 'missing_products';
    const REPORT_INTERNAL_ERROR = 'internal_error';
    const REPORT_ADDED_COUNT = 'added_count';

    protected Cart $cart;
    protected File $config;
    protected string $delimiter;
    protected string $enclosure;
    protected string $charset;
    protected array $report = [];

    /**
     * Конструктор
     *
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->config = File::config();
        $this->cart = $cart;
        $this->delimiter = (string)$this->config->exchange_cart_delimiter;
        $this->charset = (string)$this->config->exchange_cart_charset;
        $this->enclosure = '"';
    }

    /**
     * Экспортирует состав корзины в CSV
     *
     * @param string $filepath
     */
    public function export(string $filepath)
    {
        $items = $this->cart->getProductItems();
        $fp = fopen($filepath, 'w');

        $this->writeHeader($fp);
        foreach($items as $item) {
            $this->writeRow($fp, $item);
        }
        fclose($fp);
    }

    /**
     * Записывает строку с заголовками колонок
     *
     * @param resource $fp Указатель на файл
     * @return int|false
     */
    protected function writeHeader($fp)
    {
        $headers = [
            t('Артикул'),
            t('Название товара'),
            t('Количество')
        ];

        return fputcsv($fp, $this->convertArrayToCharset($headers), $this->delimiter, $this->enclosure);
    }

    /**
     * Конвертирует кодировку массива из UTF-8 в заданную
     *
     * @param array $data
     * @return void[]
     */
    protected function convertArrayToCharset($data)
    {
        $charset = $this->charset;
        if ($charset != self::CHARSET_UTF8) {
            $data = array_map(function ($value) use ($charset) {
                return iconv('utf-8', $charset.'//IGNORE', (string)$value);
            }, $data);
        }

        return $data;
    }

    /**
     * Конвертирует кодировку массива в UTF-8
     *
     * @param array $data
     */
    protected function convertArrayFromCharset($data)
    {
        $charset = $this->charset;
        if ($charset != self::CHARSET_UTF8) {
            $data = array_map(function ($value) use ($charset) {
                return iconv($charset, 'utf-8' .'//IGNORE', (string)$value);
            }, $data);
        }

        return $data;
    }

    /**
     * Записывает строку с данными
     *
     * @param resource $fp Указатель на файл
     * @param array $item Массив со сведениями по одному товару в корзине
     */
    protected function writeRow($fp, $item)
    {
        $cartitem = $item[Cart::CART_ITEM_KEY];
        $product = $item[Cart::TYPE_PRODUCT];
        $data = [
            $product->getBarCode($cartitem['offer']),
            Tools::unEntityString($cartitem['title']),
            $cartitem['amount'],
        ];
        fputcsv($fp, $this->convertArrayToCharset($data), ";");
    }

    /**
     * Добавляет товары в корзину
     *
     * @param $filepath
     * @return bool
     */
    public function import($filepath)
    {
        @ini_set('auto_detect_line_endings', true);
        $this->report = [
            self::REPORT_MISSING_PRODUCTS => [],
            self::REPORT_INTERNAL_ERROR => '',
            self::REPORT_ADDED_COUNT => 0
        ];

        $fp = fopen($filepath, 'r');
        if (!$fp) {
            $this->report[self::REPORT_INTERNAL_ERROR] = t('Не удалось открыть файл');
            return false;
        }

        $n = 0;
        while($row = fgetcsv($fp, 0, $this->delimiter)) {
            $n++;
            if ($n == 1) continue;
            $row = $this->convertArrayFromCharset($row);
            $sku = $row[0];
            $title = $row[1];
            $amount = $row[2] ?: 1;

            list($product_id, $offer_id) = $this->findProduct($sku, $title);

            if ($product_id) {
                if ($this->cart->addProduct($product_id, $amount, $offer_id)) {
                    $this->report[self::REPORT_ADDED_COUNT]++;
                    continue;
                }
            }
            $this->report[self::REPORT_MISSING_PRODUCTS][] = $sku ?: $title;
        }
        fclose($fp);

        return empty($this->report[self::REPORT_INTERNAL_ERROR])
            && empty($this->report[self::REPORT_MISSING_PRODUCTS]);
    }

    /**
     * Находит product_id, offer_id по Артикулу или названию товара
     *
     * @param string $sku Артикул товара
     * @param string $title Название товара
     * @return array
     */
    function findProduct($sku, $title)
    {
        $offer_id = 0;

        //Ищем по артикулу
        $offer = Request::make()
            ->from(new Offer())
            ->where([
                'barcode' => $sku
            ])
            ->where('sortn > 0')
            ->object();

        if ($offer && ($product = $offer->getProduct())
            && $product['id'] && $product['public'])
        {
            $product_id = $offer['product_id'];
            $offer_id = $offer['id'];
        } else {
            $product_id = Request::make()
                ->from(new Product())
                ->where([
                    'barcode' => $sku,
                    'public' => 1
                ])
                ->exec()->getOneField('id', 0);
        }

        //Ищем товар по наименованию
        if (!$product_id) {
            $product_id = Request::make()
                ->from(new Product())
                ->where([
                    'title' => Tools::toEntityString($title),
                    'public' => 1
                ])
                ->exec()->getOneField('id', 0);
        }

        return [$product_id, $offer_id];
    }

    /**
     * Проверяет файл, который предстоит импортировать и возвращает текст ошибки
     *
     * @param string $filepath Полный путь к файлу
     * @return string|bool(false) - Возвращает false в случае отсутствия ошибок, иначе текст ошибки
     */
    public function checkPreImportError($filepath, $filename)
    {
        if (!preg_match('/\.csv$/i', $filename)) {
            return t('Файл должен быть в формате CSV');
        }

        $fp = fopen($filepath, 'r');
        if (!$fp) {
            return t('Не удалось открыть файл');
        }

        $row = fgetcsv($fp, 0, $this->delimiter);
        if (!$row) {
            return t('В файле не найдена ни одна строка данных');
        }

        if (count($row) < 3) {
            return t('В файле должно быть не менее 3х колонок: Артикул, Название товара, Количество');
        }

        fclose($fp);

        return false;
    }

    /**
     * Возвращает информацию об ошибках во время импорта корзины
     *
     * @return array Возвращает массив со следующими ключами:
     * [
     *      self::REPORT_MISSING_PRODUCTS => [],
     *      self::REPORT_INTERNAL_ERROR => '',
     *      self::REPORT_ADDED_COUNT => 0
     * ]
     */
    public function getImportReport()
    {
        return $this->report;
    }
}