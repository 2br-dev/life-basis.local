<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Exchange\Model\Importers;

use Exchange\Model\Log\LogExchange;
use Exchange\Model\XMLTools as ExchangeXMLTools;

/**
 * Импорт типа цены
 */
class Catalog extends AbstractImporter
{
    static public $pattern = '/КоммерческаяИнформация\/Каталог$/i';
    static public $title = 'Импорт аттрибутов каталога';

    const CONTAINS_ONLY_CHANGES_SESSION_KEY = 'import_contains_only_changes';

    public function import(\XMLReader $reader)
    {
        $attributes = ExchangeXMLTools::getAttributes($reader);

        $lines = [];
        foreach ($attributes as $key => $val) {
            $lines[] = "{$key}={$val}";
        }
        $attributes_str = '[' . join(', ', $lines) . ']';

        $this->log->write(t('Импорт аттрибутов каталога: ') . $attributes_str, LogExchange::LEVEL_CATALOG_IMPORT);

        // Если существует аттрибут "СодержитТолькоИзменения". Атрибут существует только начиная с версии схемы 2.04
        // В более ранних версиях флаг "СодержитТолькоИзменения" импортируется классом CatalogContainsOnlyChanges
        if (isset($attributes['СодержитТолькоИзменения'])) {
            // Сохраняем содержит ли классификатор только изменения. Сохраняем в сессию, так как импорт идет во много шагов, и переменные будут потеряны
            if ($attributes['СодержитТолькоИзменения'] == 'true') {
                $this->log->write(t('Запись в сессию флага, что выгрузка содержит только изменения'), LogExchange::LEVEL_CATALOG_IMPORT);
                $_SESSION[self::CONTAINS_ONLY_CHANGES_SESSION_KEY] = true;
            } else {
                $this->log->write(t('Запись в сессию флага, что это полная выгрузка'), LogExchange::LEVEL_CATALOG_IMPORT);
                $_SESSION[self::CONTAINS_ONLY_CHANGES_SESSION_KEY] = false;
            }
        }

        $this->log->write(t('Конец импорта аттрибутов каталога'), LogExchange::LEVEL_CATALOG_IMPORT);
    }

    static public function containsOnlyChanges()
    {
        if (!isset($_SESSION[self::CONTAINS_ONLY_CHANGES_SESSION_KEY])) {
            throw new \Exception(t("Переменная сессии %0 не установлена", [self::CONTAINS_ONLY_CHANGES_SESSION_KEY]));
        }
        return $_SESSION[self::CONTAINS_ONLY_CHANGES_SESSION_KEY];
    }
}
