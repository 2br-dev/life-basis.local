<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Exchange\Model\Importers;

use Catalog\Model\Orm\Typecost;
use Exchange\Model\Log\LogExchange;
use RS\Helper\Tools as Tools;
use RS\Site\Manager as SiteManager;

/**
 * Импорт типа цены
 */
class PriceType extends AbstractImporter
{
    static public $pattern = '/ТипыЦен\/ТипЦены$/i';
    static public $title = 'Импорт Типов цен';

    public function import(\XMLReader $reader)
    {
        $this->log->write(t("Импорт типа цены: ") . $this->getSimpleXML()->Наименование, LogExchange::LEVEL_TYPE_COST_IMPORT);

        $typecost = new Typecost();
        $typecost['site_id'] = SiteManager::getSiteId();
        $typecost['xml_id'] = $this->getSimpleXML()->Ид;
        $typecost['title'] = Tools::toEntityString($this->getSimpleXML()->Наименование);
        $typecost['type'] = 'manual';
        $typecost->insert(false, ['xml_id', 'title', 'type'], ['xml_id', 'site_id']);
    }
}
