<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Exchange\Model\Importers;

use Exchange\Model\Log\LogExchange;
use RS\Helper\Tools as Tools;
use RS\Helper\Transliteration;
use RS\Site\Manager as SiteManager;

/**
 * Импорт склада из файла с предложениями
 */
class Warehouse extends AbstractImporter
{
    const SESS_KEY_WAREHOUSE_IDS = "warehouse_ids"; //Сессионный ключ для массива c складами

    static public $pattern = '/Склад$/i';
    static public $title = 'Импорт складов';

    public function import(\XMLReader $reader)
    {
        $this->log->write(t("Импорт склада: ") . $this->getSimpleXML()->Наименование, LogExchange::LEVEL_WAREHOUSE_IMPORT);

        // Получаем xml_id
        $xml_id = (string)$this->getSimpleXML()->Ид;

        $warehouse = new \Catalog\Model\Orm\WareHouse();
        $warehouse->title = Tools::toEntityString($this->getSimpleXML()->Наименование);
        $warehouse->alias = Transliteration::str2url($warehouse->title);
        $warehouse->xml_id = $xml_id;
        $warehouse->site_id = SiteManager::getSiteId();


        // Вставка _ИЛИ_ обновление склада
        $warehouse->insert(false, ['xml_id'], ['site_id', 'xml_id']);

        //Запишем в сессию id склада по XML_ID
        if (!isset($_SESSION[self::SESS_KEY_WAREHOUSE_IDS][$warehouse['xml_id']])) {
            $_SESSION[self::SESS_KEY_WAREHOUSE_IDS][$warehouse['xml_id']] = $warehouse['id'];
        }
    }
}
