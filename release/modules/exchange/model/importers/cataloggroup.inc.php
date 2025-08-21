<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Exchange\Model\Importers;

use Catalog\Model\Dirapi;
use Catalog\Model\Orm\Dir;
use Exchange\Model\Log\LogExchange;
use RS\Helper\Tools as Tools;
use RS\Helper\Transliteration;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

/**
 * Импорт группы товаров
 */
class CatalogGroup extends AbstractImporter
{
    protected $dir_list;
    protected $dir_list_from_xml;

    static public $pattern = '/Классификатор\/Группы\/Группа$/i';
    static public $title = 'Импорт Групп';

    public function import(\XMLReader $reader)
    {
        $api = new Dirapi();
        $tree = $api->getTreeList();
        if ($this->getConfig()->is_unic_dirname) {
            $this->dir_list = $this->getStringListId($tree);
            $this->dir_list_from_xml = $this->getStringListIdFromXml($this->getSimpleXML());
        }
        $this->log->write(t("Импорт корневой группы: ") . $this->getSimpleXML(), LogExchange::LEVEL_CATEGORY_IMPORT);
        $this->recursiveImport($this->getSimpleXML());
    }

    private function recursiveImport(\SimpleXMLElement $group, Dir $parent_dir = null)
    {
        $dir = $this->importOneGroup($group, $parent_dir);
        if ($group->Группы->Группа) {
            foreach ($group->Группы->Группа as $one) {
                $this->recursiveImport($one, $dir);
            }
        }
    }

    private function importOneGroup(\SimpleXMLElement $group, Dir $parent_dir = null)
    {
        $config = $this->getConfig();
        $this->log->write(t("Импорт группы: ") . (string)$group->Наименование, LogExchange::LEVEL_CATEGORY_IMPORT);
        $dir = new Dir();
        $dir->no_update_levels = true;
        $dir->dont_check_parent = true;

        // Если включена настройка "Идентифицировать категории по наименованию" - обновим xml_id категории
        if ($config['is_unic_dirname']) {
            if (isset($this->dir_list[$this->dir_list_from_xml[(string)$group->Ид]]))
                OrmRequest::make()
                    ->update(new Dir())
                    ->set(['xml_id' => $group->Ид])
                    ->where([
                        'id' => $this->dir_list[$this->dir_list_from_xml[(string)$group->Ид]],
                        'site_id' => SiteManager::getSiteId(),
                    ])
                    ->limit(1)
                    ->exec();
        }

        $dir['site_id'] = SiteManager::getSiteId();
        $dir['parent'] = $parent_dir ? $parent_dir['id'] : $config['cat_for_import'];
        $dir['public'] = ($config['hide_new_dirs']) ? 0 : 1;
        $dir['name'] = Tools::toEntityString($group->Наименование);
        $dir['xml_id'] = $group->Ид;
        $dir['processed'] = 1;

        // Настройка "Транслитерировать символьный код из названия при _добавлении_ элемента или раздела"
        if ($config['catalog_translit_on_add']) {
            $uniq_postfix = hexdec(substr(md5((string)$group->Ид), 0, 4));
            $dir['alias'] = Transliteration::str2url($dir['name']) . "-" . $uniq_postfix;
        }

        $on_duplicate_update_fields = ['xml_id', 'name', 'public', 'processed'];

        // Исключаем поля, которые помечены как "не обновлять" в настройках модуля
        $dont_update_group_fields = $config['dont_update_group_fields'] ?? [];
        $on_duplicate_update_fields = array_diff($on_duplicate_update_fields, $dont_update_group_fields);

        if ($config['catalog_update_parent']) {
            $on_duplicate_update_fields[] = 'parent';
        }

        // Настройка "Транслитерировать символьный код из названия при _обновлении_ элемента или раздела"
        if ($config['catalog_translit_on_update']) {
            $on_duplicate_update_fields[] = 'alias';
        }

        $dir->insert(false, $on_duplicate_update_fields, ['xml_id', 'site_id']);

        if (!$dir['id']) {
            throw new \Exception("no id");
        }

        return $dir;
    }

    /**
     *  Возвращает пути к каждому элементу дерева (категории) в виде склеенных названий элемента и всех его родителей в xml.
     *
     * @param \SimpleXMLElement $tree - элемент дерева
     * @param string $path - путь к элементу
     * @return string[]
     */
    function getStringListIdFromXml($tree, $path = '')
    {
        $result = [];
        $current_path = $path . (string)$tree->Наименование;
        $result[(string)$tree->Ид] = $current_path;
        if ($tree->Группы) {
            foreach ($tree->Группы->Группа as $group) {
                $result = array_merge($result, $this->getStringListIdFromXml($group, $current_path));
            }
        }
        return $result;
    }

    /**
     *  Возвращает пути к каждому элементу дерева (категории) в виде склеенных названий элемента и всех его родителей.
     *
     * @param array $tree - элемент дерева
     * @param string $path - путь к элементу
     * @return string[]
     */
    public function getStringListId($tree, $path = '')
    {
        $result = [];
        foreach ($tree as $node) {
            $current_path = $path . $node['fields']['name'];
            $result[$current_path] = $node['fields']['xml_id'];
            if (!empty($node['child'])) {
                $result = array_merge($result, $this->getStringListId($node['child'], $current_path));
            }
        }
        return $result;
    }
}
