<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Filter;

use Catalog\Model\Orm\Offer;
use RS\Exception;
use RS\Html\Filter\Type\AbstractType;
use RS\Http\Request;

/**
 * Фильтр по списку артикулов в административной панели.
 */
class BarcodeListFilter extends AbstractType
{
    public
        $tpl = '%catalog%/filter/textarea.tpl';

    /**
     * Модифицирует запрос для реализации фильтра. Можно использовать, когда для
     * фильтрации нужно подключать дополнительные таблицы
     *
     * @param \RS\Orm\Request $q
     * @return false|mixed|\RS\Orm\Request
     */
    function modificateQuery(\RS\Orm\Request $q)
    {
        if ($this->getValue() != '') {
            $barcode_text = $this->getValue();
            $barcode_list = preg_split("/\r?\n/", $barcode_text, -1, PREG_SPLIT_NO_EMPTY);
            $q->leftjoin(new Offer(), 'BARCODE_LIST_OFFER.product_id = A.id', 'BARCODE_LIST_OFFER');
            $q->openWGroup();
                $q->whereIn('A.barcode', $barcode_list);
                $q->whereIn('BARCODE_LIST_OFFER.barcode', $barcode_list, 'OR');
            $q->closeWGroup();

            parent::modificateQuery($q);
        }
        return $q;
    }

    /**
     * Возвращает массив с данными, об установленых фильтрах для визуального отображения частиц
     *
     * @param array $current_filter_values - значения установленных фильтров
     * @param array $exclude_keys массив ключей, которые необходимо исключить из ссылки на сброс параметра
     * @return array of array ['title' => string, 'value' => string, 'href_clean']
     * @throws Exception
     */
    public function getParts($current_filter_values, $exclude_keys = [])
    {
        $parts = [];
        if ($this->getNonEmptyValue() !== null) {

            $without_this = $current_filter_values;
            unset($without_this[$this->getKey()]);

            $exclude = array_combine($exclude_keys, array_fill(0, count($exclude_keys), null));

            $barcodes_count = $this->getCountListByText($this->getTextValue());

            $parts[] = [
                'title' => t('Отобрано артикулов'),
                'value' => $barcodes_count,
                'href_clean' => Request::commonInstance()->replaceKey([$this->wrap_var => $without_this] + $exclude) //Url, для очистки данной части фильтра
            ];
        }
        return $parts;
    }

    /**
     * Возвращает количество артикулов в строке
     *
     * @param $text - список элементов в виде строки
     * @return int
     */
    function getCountListByText($text)
    {
        $barcode_list = preg_split("/\r?\n/", $text, -1, PREG_SPLIT_NO_EMPTY);
        return count($barcode_list) ?? 0;
    }

    /**
     * Возвращает условия для строки Where
     *
     * @return string
     */
    function getWhere()
    {
        return '';
    }
}

