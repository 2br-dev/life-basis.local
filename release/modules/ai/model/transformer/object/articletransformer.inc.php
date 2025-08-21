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
use Article\Model\Orm\Article;
use Article\Controller\Admin\Ctrl;
use Article\Model\Api;
use RS\Module\AbstractModel\EntityList;
use RS\Router\Manager;

/**
 * Класс обеспечивает автоматическое заполнение полей статьи с помощью ИИ
 */
class ArticleTransformer extends AbstractTransformer
{
    /**
     * Возвращает идентификатор транформера
     *
     * @return string
     */
    public static function getId()
    {
        return 'article-article';
    }

    /**
     * Возвращает название транформера
     *
     * @return string
     */
    public static function getTitle()
    {
        return t('Статьи');
    }

    /**
     * Возвращает список полей, которые могут автоматически заполняться с помощью ИИ
     *
     * @return array
     */
    protected function initFields()
    {
        return [
            new HtmlField($this, 'content', t('Текст статьи')),
            new HtmlField($this, 'short_content', t('Краткий текст')),
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
        return (new MainField($this, 'title', t('Название')))
            ->setGenerateFieldsOrder([
                ['content'],
                ['short_content'],
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
        return $this->getVariablesFromOrmObject($this->source_object ?? new Article(), [
            'parent', 'dont_show_before_date', 'image', 'user_id', 'public'
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
        $article = new Article();
        $article->getFromArray($post_array);
        $this->setSourceData($article);
    }

    /**
     * Устанавливает исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @param Article $object
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
        $this->source_object = new Article($id);
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
        ], 'article-ctrl');
    }

    /**
     * Возвращает исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @return Article
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