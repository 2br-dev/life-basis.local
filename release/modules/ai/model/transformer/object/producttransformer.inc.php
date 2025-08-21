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
use Ai\Model\Transformer\Field\TextField;
use Ai\Model\Transformer\MainField;
use Ai\Model\Transformer\ReplaceVariable;
use Catalog\Controller\Admin\Ctrl;
use Catalog\Model\Api;
use Catalog\Model\Orm\Product;
use RS\Module\AbstractModel\EntityList;
use RS\Router\Manager;

/**
 * Класс обеспечивает автоматическое заполнение полей товара с помощью ИИ
 */
class ProductTransformer extends AbstractTransformer
{
    /**
     * Возвращает идентификатор транформера
     *
     * @return string
     */
    public static function getId()
    {
        return 'catalog-product';
    }

    /**
     * Возвращает название транформера
     *
     * @return string
     */
    public static function getTitle()
    {
        return t('Товары');
    }

    /**
     * Возвращает список полей, которые могут автоматически заполняться с помощью ИИ
     *
     * @return array
     */
    protected function initFields()
    {
        return [
            new TextField($this, 'short_description', t('Короткое описание')),
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
        return (new MainField($this, 'title', t('Короткое название')))
            ->setGenerateFieldsOrder([
               ['short_description'],
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
        return $this->getVariablesFromOrmObject($this->source_object ?? new Product(), [
            'sale_status', 'format', 'last_id', 'group_id', 'xml_id', 'import_hash', 'offers_json', 'video_link',
            'market_sku', 'payment_subject', 'payment_method', 'tax_ids', 'marked_class', 'country_maker', 'tn_ved_codes',
            'gtd', 'offer_caption'
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
        $product = new Product();
        $product->getFromArray($post_array);
        $this->setSourceData($product);
    }

    /**
     * Устанавливает исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @param Product $object
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
        $this->source_object = new Product($id);
    }

    /**
     * Возвращает название исходного объекта
     *
     * @return string
     */
    public function getSourceObjectTitle()
    {
        return $this->source_object['title'];
    }

    /**
     * Возвращает ссылку на просмотр/редактирование объекта в административной панели
     *
     * @return string
     */
    public function getSourceObjectAdminUrl()
    {
        $router = Manager::obj();
        return $router->getAdminUrl('edit', [
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
        return new Api();
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
        $api = $this->getDaoObject();
        $request_object = $api->getSavedRequest(Ctrl::class . '_list');

        if ($is_select_all_pages && $request_object !== null) {
            return $api->getIdsByRequest($request_object);
        }
        return $ids;
    }
}