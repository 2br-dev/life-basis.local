<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Affiliate\Model\Orm;

use Catalog\Model\Orm\WareHouse;
use RS\Debug\Action as DebugAction;
use RS\Http\Request as HttpRequest;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Request;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager;
use Shop\Model\Orm\Region;
use Site\Model\Orm\Site;

/**
 * Объект - филиал
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Наименование(регион или город)
 * @property string $alias URL имя
 * @property integer $parent_id Родитель
 * @property integer $clickable Разрешить выбор данного филиала
 * @property integer $cost_id Тип цен
 * @property string $short_contacts Краткая контактная информация
 * @property string $contacts Контактная информация
 * @property float $coord_lng Долгота
 * @property float $coord_lat Широта
 * @property integer $skip_geolocation Не выбирать данный филиал с помощью геолокации
 * @property integer $sortn Порядк. №
 * @property integer $is_default Филиал по умолчанию
 * @property integer $is_highlight Выделить филиал визуально
 * @property integer $public Публичный
 * @property integer $linked_region_id Связанный регион
 * @property integer $xzone Зона
 * @property string $domain Связанный домен (или субдомен), без http
 * @property string $meta_title Заголовок
 * @property string $meta_keywords Ключевые слова
 * @property string $meta_description Описание
 * @property string $javascript_code JavaScript-код (для связанного домена)
 * @property string $head_code Произвольный HTML-код для секции HEAD
 * @property string $robots_txt Содержимое robots.txt (для связанного домена)
 * @property array $variables Переменные для замены на страницах
 * @property string $_variables Переменные для замены на страницах
 * --\--
 */
class Affiliate extends OrmObject
{
    protected static $table = 'affiliate';

    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar([
                    'description' => t('Наименование(регион или город)'),
                    'checker' => ['ChkEmpty', t('Укажите URL имя')],
                    'attr' => [[
                        'data-autotranslit' => 'alias'
                    ]],
                    'meVisible' => false,
                    'index' => true
                ]),
                'alias' => new Type\Varchar([
                    'description' => t('URL имя'),
                    'maxLength' => 150,
                    'meVisible' => false,
                    'checker' => ['ChkEmpty', t('Укажите URL имя')]
                ]),
                'parent_id' => new Type\Integer([
                    'description' => t('Родитель'),
                    'list' => [['\Affiliate\Model\AffiliateApi', 'staticRootList']]
                ]),
                'clickable' => new Type\Integer([
                    'description' => t('Разрешить выбор данного филиала'),
                    'default' => 1,
                    'checkboxView' => [1, 0],
                    'hint' => t('Если снять флажок, то элемент будет считаться группой филиалов, которую нельзя выбрать'),
                ]),
                'cost_id' => new Type\Integer([
                    'description' => t('Тип цен'),
                    'list' => [['\Catalog\Model\CostApi', 'staticSelectList'], [0 => t('Не выбрано')]],
                    'hint' => t('Выбранный тип цен будет являться типом цен по-умолчанию, при выборе данного филиала'),
                ]),
                'short_contacts' => new Type\Text([
                    'description' => t('Краткая контактная информация')
                ]),
                'contacts' => new Type\Richtext([
                    'description' => t('Контактная информация')
                ]),
                'coord_lng' => new Type\Decimal([
                    'maxLength' => 10,
                    'decimal' => 6,
                    'description' => t('Долгота'),
                    'allowempty' => true,
                    'requestType' => 'string',
                    'visible' => false,
                    'appVisible' => true,
                ]),
                'coord_lat' => new Type\Decimal([
                    'maxLength' => 10,
                    'decimal' => 6,
                    'description' => t('Широта'),
                    'allowempty' => true,
                    'visible' => false,
                    'appVisible' => true,
                    'requestType' => 'string',
                ]),
                '_geo' => new Type\MixedType([
                    'description' => t('Расположение на карте'),
                    'visible' => true,
                    'template' => '%affiliate%/form/affiliate/geo.tpl'
                ]),
                'skip_geolocation' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Не выбирать данный филиал с помощью геолокации'),
                    'default' => 0,
                    'allowEmpty' => false,
                    'checkboxView' => [1, 0]
                ]),
                'sortn' => new Type\Integer([
                    'maxLength' => '11',
                    'description' => t('Порядк. №'),
                    'visible' => false,
                ]),
                'is_default' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Филиал по умолчанию'),
                    'meVisible' => false,
                    'checkboxView' => [1, 0],
                    'allowEmpty' => false,
                    'hint' => t('Будет выбран, если ни один филиал по геолокации не будет определен')
                ]),
                'is_highlight' => new Type\Integer([
                    'maxLength' => 1,
                    'description' => t('Выделить филиал визуально'),
                    'checkboxView' => [1, 0]
                ]),
                'public' => new Type\Integer([
                    'description' => t('Публичный'),
                    'default' => 1,
                    'checkboxView' => [1, 0]
                ]),
                'linked_region_id' => new Type\Integer([
                    'description' => t('Связанный регион'),
                    'hint' => t('Устанавливает связь между филиалом и регионом из справочника городов доставки. Является регионом доставки по умолчанию в оформлении заказа для текущего филиала.'),
                    'tree' => ['\Shop\Model\RegionApi::staticTreeList', 0, [0 => t('- Не выбран -')]]
                ]),
                'xzone' => new Type\Integer([
                    'description' => t('Зона'),
                    'hint' => t('При оформлении заказа можно будет выбрать город, только из указанной здесь зоны. Используйте, если желаете, что при выборе филиала например, Москва, ваши клиенты не могли сделать заказ в Краснодар.'),
                    'list' => [['\Shop\Model\ZoneApi', 'staticSelectList'], [0 => t(' - все - ')]],
                    'default' => 0,
                ]),
                'domain' => new Type\Varchar([
                    'description' => t('Связанный домен (или субдомен), без http'),
                    'hint' => t('Запись без точки, например "sochi" - будет означать поддомен вашего основного домена, т.е. "sochi.вашсайт.ру". Используйте, для создания геоподдоменной системы. Все домены предварительно должны быть настроены как псевдонимы к основному домену. Выбор такого филиала будет означать перемещение на другой домен. В случае прямого захода на указанный домен, данный филиал будет автоматически выбран.'),
                    'checker' => [function($_this, $value) {
                        $site = new Site($_this['__site_id']->get());
                        $site_main_domain = $site->getMainDomain();
                        if ($site_main_domain && $value == $site_main_domain) {
                            return t('Не указывайте основной домен у главного филиала, оставьте поле Домен пустым');
                        }
                        return true;
                    }]
                ]),
            t('Мета-тэги'),
                'meta_title' => new Type\Varchar([
                    'maxLength' => '1000',
                    'description' => t('Заголовок'),
                ]),
                'meta_keywords' => new Type\Varchar([
                    'maxLength' => '1000',
                    'description' => t('Ключевые слова'),
                ]),
                'meta_description' => new Type\Varchar([
                    'maxLength' => '1000',
                    'viewAsTextarea' => true,
                    'description' => t('Описание'),
                ]),
                'javascript_code' => new Type\Text([
                    'description' => t('JavaScript-код (для связанного домена)'),
                    'hint' => t('Будет добавлен перед закрытием тега body. Здесь можно задать, например, независимый код счетчика, в случае использования геоподдоменной системы. Код будет добавлен только при выбранном филиале.')
                ]),
                'head_code' => new Type\Text([
                    'description' => t('Произвольный HTML-код для секции HEAD'),
                    'hint' => t('Код будет добавлен в секцию HEAD. Код будет добавлен только при выбранном филиале.')
                ]),
                'robots_txt' => new Type\Text([
                    'description' => t('Содержимое robots.txt (для связанного домена)'),
                    'hint' => t('В случае использования геоподдоменной системы, вы можете отдавать различный robots.txt для каждого геоподдомена. Чтобы данная опция работала, необходимо удалить из корневой папки сайта реальный файл robots.txt, а также строку RewriteRule (robots.txt)$ $1 [L] из .htaccess файла')
                ]),
                'variables' => new Type\VariableList([
                    'description' => t('Переменные для замены на страницах'),
                    'hint' => t('Здесь вы можете определить переменные, которые будут заменяться в итоговом HTML-коде сайта. Ко всем идентификаторам переменных будет добавляться префикс "affiliate_var_", т.е. если вы здесь определите переменную title_rp, то заменяться в шаблонах будет {affiliate_var_title_rp} или #affiliate_var_title_rp. Замена будет работать только, если в настройках модуля Филиальная сеть включена опция `Заменять переменные...`'),
                    'tableFields' => [[
                        new Type\VariableList\TextVariableListField('name', t('Идентификатор')),
                        new Type\VariableList\TextVariableListField('value', t('Значение'))
                    ]]
                ]),
                '_variables' => new Type\TinyText([
                    'description' => t('Переменные для замены на страницах'),
                    'visible' => false,
                ])
        ]);

        $this->addIndex(['site_id', 'alias'], self::INDEX_UNIQUE);
    }

    /**
     * Возвращает отладочные действия, которые можно произвести с объектом
     *
     * @return DebugAction\AbstractAction[]
     */
    public function getDebugActions()
    {
        return [
            new DebugAction\Edit(RouterManager::obj()->getAdminPattern('edit', [':id' => '{id}'], 'affiliate-ctrl')),
            new DebugAction\Delete(RouterManager::obj()->getAdminPattern('del', [':chk[]' => '{id}'], 'affiliate-ctrl'))
        ];
    }

    /**
     * Возвращает объект связанного с филиалом региона
     *
     * @return Region
     */
    public function getLinkedRegion()
    {
        return new Region($this['linked_region_id']);
    }

    /**
     * Возвращает объект родительского филиала
     *
     * @return Affiliate
     */
    public function getParentAffiliate()
    {
        return new Affiliate($this['parent_id']);
    }

    /**
     * Выполняется перед сохранением объекта
     *
     * @param string $flag
     * @return void
     */
    function beforeWrite($flag)
    {
        if ($this['coord_lng'] === '') $this['coord_lng'] = null;
        if ($this['coord_lat'] === '') $this['coord_lat'] = null;

        if ($flag == self::INSERT_FLAG && !$this->isModified('sortn')) {
            $q = OrmRequest::make()
                ->select('MAX(sortn) max_sort')
                ->from($this)
                ->where([
                    'site_id' => $this['site_id'],
                    'parent_id' => $this['parent_id'],
                ]);

            $this['sortn'] = $q->exec()->getOneField('max_sort', -1) + 1;
        }

        if ($this->isModified('variables')) {
            $this['_variables'] = json_encode($this['variables'], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Выполняется после загрузки объекта
     *
     * @return void
     */
    function afterObjectLoad()
    {
        $this['variables'] = json_decode((string)$this['_variables'], true) ?: [];
    }

    /**
     * Выполняется после сохранения объекта
     *
     * @param string $flag
     */
    function afterWrite($flag)
    {
        if ($this['is_default']) {
            //Флаг "по умолчанию", может быть только у одного филиала в рамках сайта
            OrmRequest::make()
                ->update($this)
                ->set('is_default = 0')
                ->where([
                    'site_id' => $this['site_id']
                ])
                ->where("id != '#id'", ['id' => $this['id']])
                ->exec();
        }
    }

    /**
     * Удаляет объект
     *
     * @return bool
     */
    function delete()
    {
        if ($result = parent::delete()) {
            //Удаляем у складов ссылку на данный филиал
            OrmRequest::make()
                ->update(new WareHouse())
                ->set(['affiliate_id' => 0])
                ->where([
                    'affiliate_id' => $this['id']
                ])->exec();
        }
        return $result;
    }

    /**
     * Возвращает клон текущего объекта
     *
     * @return static
     */
    function cloneSelf()
    {
        /** @var self $clone */
        $clone = parent::cloneSelf();
        unset($clone['alias']);
        return $clone;
    }

    /**
     * Возвращает список ID связанных с филиалом складов,
     * а также складов связанных со всеми филиалами
     *
     * @return int[]
     */
    function getLinkedWarehouses()
    {
        $warehouse_ids = Request::make()
            ->select('id')
            ->from(new WareHouse())
            ->where([
                'site_id' => $this['site_id']
            ])
            ->whereIn('affiliate_id', [0, $this['id']])
            ->exec()->fetchSelected(null, 'id');

        return $warehouse_ids;
    }

    /**
     * Возвращает URL на страницу контактов филиала
     *
     * @param bool $absolute
     * @return string
     */
    function getContactPageUrl($absolute = false)
    {
        $affiliate_id = $this['alias'] ?: $this['id'];
        return RouterManager::obj()->getUrl('affiliate-front-contacts', ['affiliate' => $affiliate_id], $absolute);
    }

    /**
     * Возвращает URL на страницу изменения филиала на текущий
     *
     * @param string $referer URL для перенаправления после смены филиала
     * @param bool $absolute Если true, то будет возвращена абсолютная ссылка
     * @return string
     */
    function getChangeAffiliateUrl($referer, $absolute = false)
    {
        $http_request = \RS\Http\Request::commonInstance();

        if ($this['domain']) {
            $protocol = $http_request->getProtocol();
            $domain = static::processDomain($this['domain']);
            return $protocol.'://'.$domain.$referer;
        } else {
            $site = Manager::getSite();
            if (!$absolute && $http_request->getDomain() != $site->getMainDomain()) {
                $absolute = true;
            }

            $affiliate_id = $this['alias'] ?: $this['id'];
            return RouterManager::obj()->getUrl('affiliate-front-change', ['affiliate' => $affiliate_id, 'referer' => $referer], $absolute);
        }
    }

    /**
     * При необходимости дописывает к доменному имя основной домен
     *
     * @param string $domain
     * @return string
     */
    public static function processDomain($domain, $site_id = null)
    {
        if (strpos((string)$domain, '.') !== false) {
            return $domain;
        }

        $site = $site_id ? new Site($site_id) : Manager::getSite();
        return ($domain != '' ? $domain.'.' : '' ).$site->getMainDomain();
    }

    /**
     * Возвращает домен, связанный с данным филиалом
     *
     * @param bool $with_protocol Если true, то будет возвращет с протоколом http(s)://Доменное имя
     * @return string
     */
    public function getDomain($with_protocol = false)
    {
        $domain = self::processDomain($this['domain'], $this['site_id']);

        if ($with_protocol) {
            $protocol = HttpRequest::commonInstance()->getProtocol();
            $domain = $protocol.'://'.$domain;
        }

        return $domain;
    }
}
