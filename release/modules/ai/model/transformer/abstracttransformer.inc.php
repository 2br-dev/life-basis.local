<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Transformer;

use Ai\Config\ModuleRights;
use Ai\Model\Orm\Prompt;
use Ai\Model\Orm\Statistic;
use Ai\Model\Transformer\Object\ArticleTransformer;
use Ai\Model\Transformer\Object\ProductDirTransformer;
use Ai\Model\Transformer\Object\ProductTransformer;
use Ai\Model\Transformer\Object\SupportTransformer;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Event\Manager;
use RS\Exception;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\AbstractObject;
use RS\Orm\Request;
use RS\Orm\Type\Text;
use RS\Orm\Type\Varchar;
use RS\Site\Manager as SiteManager;

/**
 * Базовый класс транформера.
 * Транформер должен описывать поля у объекта, которые можно автоматически наполнять с помощью ИИ,
 * а также переменные, которые можно применять в запросах к ИИ для данного объекта.
 */
abstract class AbstractTransformer
{
    /**
     * @var AbstractField[]
     */
    private array $fields = [];
    protected static $prompts = [];
    protected $source_object;
    protected $site_id;

    function __construct()
    {
        foreach($this->initFields() as $field) {
            $this->fields[$field->getFieldName()] = $field;
        }
    }

    /**
     * Устанавливает идентификатор мультисайта, с которым работает данный трансформер
     *
     * @param integer $site_id
     * @return void
     */
    public function setSiteId($site_id)
    {
        $this->site_id = $site_id;
    }

    /**
     * Возвращает идентификатор мультисайта, с которым работает данный трансформер
     *
     * @return integer
     */
    public function getSiteId()
    {
        return $this->site_id ?: SiteManager::getSiteId();
    }

    /**
     * Возвращает все возможные поля
     *
     * @return AbstractField[]
     */
    public function getFields($with_prompts = false)
    {
        if ($with_prompts) {
            return array_filter($this->fields, function($value) {
                return count($value->getPrompts()) > 0;
            });
        }

        return $this->fields;
    }

    /**
     * Возвращает объект главного поля, которое может быть источником генерации всех остальных полей,
     * возле такого поля будет отображена кнопка "сгенерировать остальные поля"
     *
     * @return MainField
     */
    public function getMainField()
    {
        return null;
    }

    /**
     * Возвращает объект поля по идентификатору
     *
     * @return AbstractField
     */
    public function getFieldByName($name)
    {
        return $this->fields[$name] ?? throw new Exception(t('Поле `%0` не найдено', [$name]));
    }

    /**
     * Возвращает идентификатор транформера
     *
     * @return string
     */
    abstract public static function getId();

    /**
     * Возвращает название транформера
     *
     * @return string
     */
    abstract public static function getTitle();

    /**
     * Возвращает список полей, которые могут автоматически заполняться с помощью ИИ
     *
     * @return AbstractField[]
     */
    abstract protected function initFields();

    /**
     * Возвращает список объектов для замены переменных
     *
     * @return ReplaceVariable[]
     */
    abstract public function getVariables();

    /**
     * Возвращает объект класса для выборки объектов, заполняемых данным трансформером
     *
     * @return EntityList
     */
    abstract public function getDaoObject();

    /**
     * Устанавливает исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @param mixed $object
     * @return void
     */
    abstract public function setSourceData($object);

    /**
     * Загружает по ID исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @param integer $id
     * @return void
     */
    abstract public function setSourceDataById($id);

    /**
     * Возвращает исходный объект, из которого будут добываться переменные и/или который нужно обновлять
     *
     * @return mixed
     */
    abstract public function getSourceObject();

    /**
     * Возвращает ссылку на просмотр/редактирование объекта в административной панели
     *
     * @return string
     */
    abstract public function getSourceObjectAdminUrl();

    /**
     * Возвращает название исходного объекта
     *
     * @return string
     */
    abstract public function getSourceObjectTitle();

    /**
     * Возвращает исходный для заполнения объект из $post_array
     *
     * @param array $post_array
     * @return mixed
     */
    abstract public function fillSourceObjectPromPost(array $post_array);

    /**
     * Добывает переменные из ORM объекта
     *
     * @param AbstractObject $orm
     * @param array|null $exclude_fields
     * @param array|null $include_fields
     * @return ReplaceVariable[]
     */
    protected function getVariablesFromOrmObject(AbstractObject $orm, array $exclude_fields = null, array $include_fields = null)
    {
        $vars = [];
        $orm_type = str_replace('\\', '/', strtolower(get_class($orm)));

        foreach($orm->getProperties() as $key => $property) {
            if (($property instanceof Varchar || $property instanceof Text)
                && $property->isVisible('ai')) {

                $var = new ReplaceVariable();
                $orm_name = basename($orm_type);
                $var->name = $orm_name.'.'.$key;
                $var->key = $key;
                $var->title = $property->getDescription();
                $var->value = (string)$orm[$key];
                if ($exclude_fields === null || !in_array($var->key, $exclude_fields)) {
                    if ($include_fields === null || in_array($var->key, $include_fields)) {
                        $vars[$var->name] = $var;
                    }
                }

            }
        }

        return $vars;
    }

    /**
     * Возвращает полный список трансформеров (классов, отвечающих за транформацию(заполнение через ИИ) полей какого-либо объекта)
     *
     * @return AbstractTransformer::class[]
     */
    public static function getAllTransformers()
    {
        $list = [
            ProductDirTransformer::class,
            ProductTransformer::class,
            ArticleTransformer::class,
            SupportTransformer::class,
        ];

        return Manager::fire('ai.getTransformers', $list)->getResult();
    }

    /**
     * Возвращает трансформер по ID
     *
     * @param string $id
     * @return AbstractTransformer
     */
    public static function getTransformerById($id)
    {
        foreach(self::getAllTransformers() as $transformer) {
            if ($transformer::getId() === $id) {
                return new $transformer;
            }
        }

        throw new Exception(t('Класс трансформера `%0` не найден', [$id]));
    }

    /**
     * Загружает сразу все промпты, чтобы потом быстро с ними работать
     *
     * @param $site_id
     * @return array
     */
    public static function loadPrompts($site_id)
    {
        $transformer_id = static::getId();
        if (!isset(self::$prompts[$site_id][$transformer_id])) {
            $prompts = Request::make()
                ->from(new Prompt())
                ->orderby('sortn')
                ->where([
                    'transformer_id' => $transformer_id,
                    'site_id' => $site_id
                ])
                ->objects();

            foreach($prompts as $prompt) {
                self::$prompts[$site_id][$transformer_id][$prompt['field']][$prompt['id']] = $prompt;
            }
        }
        return self::$prompts[$site_id];
    }

    /**
     * Возвращает все имеющиеся промпты для поля
     *
     * @param string $field Имя поля ORM объекта
     *
     * @return Prompt[]
     */
    public function getPromptsByField($field)
    {
        $prompts = self::loadPrompts($this->getSiteId());
        return $prompts[static::getId()][$field] ?? [];
    }

    /**
     * Возвращает промпт по его ID
     *
     * @param string $field Имя поля ORM объекта
     * @param integer $id
     * @return Prompt|null
     */
    public function getPromptById($field, $id)
    {
        $prompts = self::loadPrompts($this->getSiteId());
        return $prompts[static::getId()][$field][$id] ?? null;
    }


    /**
     * Метод добавляет необходимые атрибуты к полям, чтобы в административной панели
     * возле этих полей появились кнопки заполнения с помощью ИИ
     *
     * @param $orm_object
     * @return void
     */
    public function addAiToFields($orm_object)
    {
        if (!Rights::hasRight('ai', ModuleRights::RIGHT_FIELD_COMPLETION)) {
            return;
        }

        foreach($this->getFields() as $field) {
            $field->addFieldAttributes($orm_object);
        }

        if ($main_field = $this->getMainField()) {
            $main_field->addFieldAttributes($orm_object);
        }
    }

    /**
     * Генерирует данные для одного поля по конкретному промпту
     *
     * @param Prompt $prompt
     * @return \Traversable
     */
    public function requestGenerationByPrompt(Prompt $prompt)
    {
        $field = $this->getFieldByName($prompt['field']);
        $field->setPrompt($prompt);
        return $field->makeCompletionRequest([
            'type' => Statistic::TYPE_FIELD_FILL,
            'user_id' => Auth::getCurrentUser()->id,
            'site_id' => $prompt['site_id']
        ]);
    }
}