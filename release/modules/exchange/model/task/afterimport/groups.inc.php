<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Exchange\Model\Task\AfterImport;

use Catalog\Model\Dirapi;
use Catalog\Model\Orm\Dir;
use Exchange\Config\File as ExchangeConfig;
use Exchange\Model\Importers;
use Exchange\Model\Log\LogExchange;
use Exchange\Model\Task\AbstractTask;
use RS\Config\Loader as ConfigLoader;
use RS\Event\Manager as EventManager;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

/**
 * Объект этого класса хранится в сессии, соответственно все свойства объекта доступны
 * не только до окончания выполнения скрипта, но и в течение всей сессии
 */
class Groups extends AbstractTask
{
    protected $filename;

    public function __construct($filename)
    {
        parent::__construct();
        $this->filename = $filename;
    }

    public function exec($max_exec_time = 0)
    {
        //Вызовем хук
        EventManager::fire('exchange.task.afterimport.groups', [
            'filename' => $this->filename
        ]);

        // Только для import.xml
        if (!preg_match('/import/iu', $this->filename)) {
            $this->log->write(t("Ничего не делаем, так как это не import.xml"), LogExchange::LEVEL_CATEGORY_IMPORT);
            return true;
        }

        //Обновляем кэш сведения о вложенности элементов
        Dirapi::updateLevels();

        // Если классификатор содержит только изменения, то ничего не делаем
        if (Importers\Catalog::containsOnlyChanges()) {
            $this->log->write(t("Ничего не делаем, так как классификатор содержит только изменения"), LogExchange::LEVEL_CATEGORY_IMPORT);
            return true;
        }

        $config = ConfigLoader::byModule($this);

        // Если установлена настройка "Что делать с разделами, отсутствующими в файле импорта -> Ничего не делать"
        if ($config->catalog_section_action == ExchangeConfig::ACTION_NOTHING) {
            $this->log->write(t("Ничего не делаем, так как установлена настройка Ничего не делать c элементами, отсутствующими в файле импорта"), LogExchange::LEVEL_CATEGORY_IMPORT);
            return true;
        }

        // Если установлена настройка "Что делать с разделами, отсутствующими в файле импорта -> Удалять"
        if ($config->catalog_section_action == ExchangeConfig::ACTION_REMOVE) {
            $this->log->write(t("Удаление категорий, которые не участвуют в файле импорта..."), LogExchange::LEVEL_CATEGORY_IMPORT);
            while (true) {
                // Удалению подлежат только категории импортированные ранее, не являющиеся "спец-категориями"
                $dir = Dir::loadByWhere('site_id = #site_id and is_spec_dir = "N" and processed is null and xml_id > ""', ['site_id' => SiteManager::getSiteId()]);

                // Если не осталось больше объектов для удаления
                if (!$dir->id) {
                    $this->log->write(t("Нет больше категорий для удаления"), LogExchange::LEVEL_CATEGORY_IMPORT);
                    return true;
                }

                // Если привышено время выполнения
                if ($this->isExceed()) {
                    return false;
                }

                $this->log->write(t("Удаление категории ") . $dir->name, LogExchange::LEVEL_CATEGORY_IMPORT);
                $dir->delete();
            }
        }

        // Если установлена настройка "Что делать с разделами, отсутствующими в файле импорта -> Деактивировать"
        if ($config->catalog_section_action == ExchangeConfig::ACTION_DEACTIVATE) {
            $this->log->write(t("Деактивация категорий, которые не участвуют в файле импорта..."), LogExchange::LEVEL_CATEGORY_IMPORT);

            // Скрываем категории
            $affected = OrmRequest::make()
                ->update(new Dir())
                ->set(['public' => 0])
                ->where([
                    'site_id' => SiteManager::getSiteId(),
                    'processed' => null,
                ])
                ->exec()->affectedRows();
            $this->log->write(t("Дактивировано категорий: ") . $affected, LogExchange::LEVEL_CATEGORY_IMPORT);
            return true;
        }

        throw new \Exception('Impossible 1!');
    }
}
