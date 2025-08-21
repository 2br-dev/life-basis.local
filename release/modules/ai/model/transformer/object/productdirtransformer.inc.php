<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Transformer\Object;

use Ai\Model\Transformer\AbstractTransformer;
use Ai\Model\Transformer\Field\HtmlField;
use Ai\Model\Transformer\Field\StringField;
use Ai\Model\Transformer\MainField;
use Ai\Model\Transformer\ReplaceVariable;
use Catalog\Model\DirApi;
use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Product;
use RS\Module\AbstractModel\EntityList;
use RS\Router\Manager;

/**
 * Класс обеспечивает автоматическое заполнение полей товара с помощью ИИ
 */
class ProductDirTransformer extends AbstractTransformer
{
    /**
     * Возвращает идентификатор транформера
     *
     * @return string
     */
    public static function getId()
    {
        return 'catalog-dir';
    }

    /**
     * Возвращает название транформера
     *
     * @return string
     */
    public static function getTitle()
    {
        return t('Категории товаров');
    }

    /**
     * Возвращает список полей, которые могут автоматически заполняться с помощью ИИ
     *
     * @return array
     */
    protected function initFields()
    {
        return [
            new HtmlField($this, 'description', t('Полное описание')),
            new StringField($this, 'meta_title', t('Мета-заголовок')),
            new StringField($this, 'meta_keywords', t('Мета-ключевые слова')),
            new StringField($this, 'meta_description', t('Мета-описание')),
        ];
    }

    /**
     * Возвращает объект главного поля, которое может быть источником генерации всех остальных полей
     *
     * @return MainField
     */
    public function getMainField()
    {
        return (new MainField($this, 'name', t('Название')))
            ->setGenerateFieldsOrder([
                ['description'],
                ['meta_title', 'meta_keywords', 'meta_description']
            ]);
    }

    /**
     * Возвращает список объектов, содержащих идентификатор, название и значение переменной
     *
     * @return ReplaceVariable[]
     */
    public function getVariables()
    {
        return $this->getVariablesFromOrmObject($this->source_object ?? new Dir(), [
            'xml_id', 'tax_ids', 'mobile_background_color', 'mobile_background_icon_color', 'mobile_background_title_color'
        ]);
    }

    /**
     * Заполняет объект товара данными из POST.
     * Данный товар будет необходим для подстановки переменных в запрос к ИИ
     *
     * @param array $post_array
     * @return void
     */
    public function fillSourceObjectPromPost(array $post_array)
    {
        $dir = new Dir();
        $dir->getFromArray($post_array);
        $this->setSourceData($dir);
    }

    /**
     * Устанавливает исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @param Dir $object
     * @return void
     */
    public function setSourceData($object)
    {
        $this->source_object = $object;
    }

    /**
     * Загружает по ID исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @param integer $id
     * @return void
     */
    public function setSourceDataById($id)
    {
        $this->source_object = new Dir($id);
    }

    /**
     * Возвращает название исходного объекта
     *
     * @return string
     */
    public function getSourceObjectTitle()
    {
        return $this->source_object['name'];
    }

    /**
     * Возвращает ссылку на просмотр/редактирование объекта в административной панели
     *
     * @return string
     */
    public function getSourceObjectAdminUrl()
    {
        $router = Manager::obj();
        return $router->getAdminUrl('treeEdit', [
            'id' => $this->source_object['id']
        ], 'catalog-ctrl');
    }

    /**
     * Возвращает исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @return Product
     */
    public function getSourceObject()
    {
        return $this->source_object;
    }

    /**
     * Возвращает объект класса для выборки объектов, заполняемых данным трансформером
     *
     * @return EntityList
     */
    public function getDaoObject()
    {
        return new DirApi();
    }

    /**
     * Возвращает список ID объектов с учетом установленных в административной панели фильтров.
     *
     * @param array $ids
     * @param bool $is_select_all_pages Если true, то означает, что был выбран флаг "Выбрать элементы на всех страницах",
     * и нужно выбрать элементы с учетом всех установленных фильтров
     *
     * @return array
     */
    public function modifySelectAll($ids, $is_select_all_pages)
    {
        return $ids;
    }
}