<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Config;

use Catalog\Model\Dirapi;
use Catalog\Model\Orm\Dir;
use Main\Model\Widgets as WidgetsApi;
use RS\Config\Loader as ConfigLoader;
use RS\Module\AbstractInstall;
use RS\Orm\AbstractObject;
use RS\Site\Manager as SiteManager;
use Templates\Model\PageApi;
use Users\Model\Orm\User;
use Users\Model\Orm\UserGroup;

/**
 * Класс отвечает за установку и обновление модуля
 */
class Install extends AbstractInstall
{
    function install()
    {
        $result = parent::install();
        if ($result) {
            //Вставляем в таблицы данные по-умолчанию, в рамках нового сайта, вызывая принудительно обработчик события
            Handlers::onSiteCreate([
                'orm' => SiteManager::getSite(),
                'flag' => AbstractObject::INSERT_FLAG
            ]);
        }

        return $result;
    }

    /**
     * Добавляет демонстрационные данные
     *
     * @param array $params - произвольные параметры.
     * @return boolean|array
     */
    function insertDemoData($params = [])
    {
        return $this->importCsvFiles([
            ['\Ai\Model\CsvSchema\Prompt', 'prompts'],
        ], 'utf-8', $params);
    }

    /**
     * Возвращает true, если модуль может вставить демонстрационные данные
     *
     * @return bool
     */
    function canInsertDemoData()
    {
        return true;
    }
}
