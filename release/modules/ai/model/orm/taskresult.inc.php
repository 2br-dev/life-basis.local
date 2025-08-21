<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Orm;

use Ai\Model\Transformer\AbstractTransformer;
use RS\Exception;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;
use RS\Orm\OrmObject;

/**
 * ORM-объект, описывающий результат автоматического заполнения одного объекта
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $task_id ID задачи на генерацию
 * @property string $transformer_id ID трансформера
 * @property integer $entity_id ID объекта
 * @property array $generated_data Сгенерированные данные
 * @property string $_generated_data Сгенерированные данные (JSON)
 * @property integer $number Порядковый номер результата внутри задачи
 * @property string $status Статус
 * @property string $error Ошибка
 * --\--
 */
class TaskResult extends OrmObject
{
    const STATUS_NEW = 'new';
    const STATUS_GENERATED = 'generated';
    const STATUS_GENERATED_WITH_ERRORS = 'generated_with_errors';
    const STATUS_APPROVED = 'approved';
    const STATUS_CANCELLED = 'cancelled';

    protected $cache_task;
    protected $cache_transformer;
    protected static $table = 'ai_task_result';

    function _init()
    {
        parent::_init()->append([
            'site_id' => (new Type\CurrentSite()),
            'task_id' => (new Type\Integer())
                ->setDescription(t('ID задачи на генерацию'))
                ->setIndex(true),
            'transformer_id' => (new Type\Varchar())
                ->setMaxLength(100)
                ->setDescription(t('ID трансформера'))
                ->setList(['Ai\Model\TransformerApi', 'staticSelectList']),
            'entity_id' => (new Type\Integer())
                ->setDescription(t('ID объекта')),
            'generated_data' => (new Type\ArrayList())
                ->setVisible(false)
                ->setDescription(t('Сгенерированные данные')),
            '_generated_data' => (new Type\Text())
                ->setDescription(t('Сгенерированные данные (JSON)')),
            'number' => (new Type\Integer())
                ->setDescription(t('Порядковый номер результата внутри задачи')),
            'status' => (new Type\Varchar())
                ->setDescription(t('Статус'))
                ->setList([__CLASS__, 'getStatusTitles']),
            'error' => (new Type\Varchar())
                ->setDescription(t('Ошибка'))
        ]);
    }

    /**
     * Обработчик перед сохранением
     *
     * @param $save_flag
     * @return void
     */
    public function beforeWrite($save_flag)
    {
        $this['_generated_data'] = json_encode($this['generated_data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Обработчик после сохранения
     *
     * @param $save_flag
     * @return void
     */
    public function afterWrite($save_flag)
    {
        if ($save_flag == self::UPDATE_FLAG) {
            $this->getTask()->updateCounts();
        }
    }

    /**
     * Обработчик загрузки объекта
     *
     * @return void
     */
    public function afterObjectLoad()
    {
        $this['generated_data'] = json_decode((string)$this['_generated_data'], true) ?: [];
    }

    /**
     * Возвращает объект Задачи на заполнение данных через ИИ
     *
     * @return Task
     */
    public function getTask()
    {
        if (!isset($this->cache_task)) {
            $this->cache_task = new Task($this['task_id']);
        }
        return $this->cache_task;
    }

    /**
     * Устанавливает в локальном кэше заданный объект основной задачи
     *
     * @param Task $task
     * @return void
     */
    public function setTask(Task $task)
    {
        $this->cache_task = $task;
        $this['task_id'] = $task['id'];
    }

    /**
     * Возвращает объект трансформера
     *
     * @return AbstractTransformer
     */
    public function getTransformer()
    {
        if (!isset($this->cache_transformer)) {
            $this->cache_transformer = AbstractTransformer::getTransformerById($this['transformer_id']);
            $this->cache_transformer->setSourceDataById($this['entity_id']);
        }
        return $this->cache_transformer;
    }

    /**
     * Возвращает названия статусов
     *
     * @param array $first Первый элемент
     * @return array
     */
    public static function getStatusTitles(array $first = [])
    {
        return $first + [
            self::STATUS_NEW => t('Новый'),
            self::STATUS_GENERATED => t('Сгенерировано'),
            self::STATUS_GENERATED_WITH_ERRORS => t('Есть ошибки'),
            self::STATUS_APPROVED => t('Применено'),
            self::STATUS_CANCELLED => t('Отменено')
        ];
    }

    /**
     * Возвращает полный список кодов для цветов
     *
     * @return string[]
     */
    public static function getStatusColors()
    {
        return [
            self::STATUS_NEW => '#d4e6f7',
            self::STATUS_GENERATED => '#e8daef',
            self::STATUS_GENERATED_WITH_ERRORS => '#fdebd0',
            self::STATUS_APPROVED => '#d5f5e3',
            self::STATUS_CANCELLED => '#fadbd8'
        ];
    }

    /**
     * Возвращает название статуса текущего результата
     *
     * @return string
     */
    public function getStatusTitle()
    {
        $titles = self::getStatusTitles();
        return $titles[$this['status']];
    }

    /**
     * Возвращает цвет статуса
     *
     * @return string
     */
    public function getStatusColor()
    {
        $colors = self::getStatusColors();
        return $colors[$this['status']];
    }

    /**
     * Генерирует данные для одного объекта
     *
     * @return bool
     */
    public function generate()
    {
        $task = $this->getTask();
        $settings = $task->getSettings();

        $data = [];
        $transformer = $this->getTransformer();
        $source_object = $transformer->getSourceObject();

        foreach($transformer->getFields() as $field) {
            $field_settings = $settings['fields'][$field->getFieldName()] ?? [];
            if (!empty($field_settings['enable'])) {
                $this['status'] = self::STATUS_GENERATED;

                if (empty($field_settings['overwrite']) && $source_object[$field->getFieldName()] != '') {
                    //Пропускаем, если поле не пустое
                    continue;
                }

                try {
                    if (!empty($field_settings['prompt_id'])) {
                        $prompt = new Prompt($field_settings['prompt_id']);
                        $field->setPrompt($prompt);
                    }
                    //Ожидаем, когда завершится поток данных генерации
                    $completions = $field->makeCompletionRequest([
                        'user_id' => $task['user_id'],
                        'type' => Statistic::TYPE_TASK_FILL,
                        'site_id' => $this['site_id'],
                        'task_id' => $this['task_id'],
                        'task_result_id' => $this['id'],
                    ]);
                    foreach ($completions as $completion) {
                        $full_text = $completion->getFullText();
                    }

                    if ($full_text == '') {
                        throw new Exception(t('Не удалось сгенерировать текст для поля `%field`', [
                            'field' => $field->getFieldName()
                        ]));
                    }

                    if (isset($full_text)) {
                        $data[$field->getFieldName()] = $full_text;
                    }
                } catch (\Exception $e) {
                    $this['status'] = self::STATUS_GENERATED_WITH_ERRORS;
                    $this['error'] = $e->getMessage();
                    break;
                }
            }
        }

        $this['generated_data'] = $data;
        return $this->update();
    }

    /**
     * Возвращает наименование объекта
     *
     * @return string
     */
    function getEntityTitle()
    {

        $transformer = $this->getTransformer();
        return $transformer->getSourceObjectTitle();
    }

    /**
     * Возвращает ссылку в административной панели на объект
     *
     * @return string
     */
    function getEntityAdminUrl()
    {
        $transformer = $this->getTransformer();
        return $transformer->getSourceObjectAdminUrl();
    }

    /**
     * Возвращает исходный объект, для которого генерируются данные в текущем результате
     *
     * @return mixed
     */
    function getEntityObject()
    {
        $transformer = $this->getTransformer();
        return $transformer->getSourceObject();
    }

    /**
     * Возвращает ORM-объект, позволяющий сформировать форму, отображающую результат
     *
     * @return FormObject
     */
    function getResultFormObject()
    {
        $task = $this->getTask();
        $settings = $task->getSettings();
        $transformer = $this->getTransformer();

        $transformer->setSourceDataById($this['entity_id']);
        $source_object = $transformer->getSourceObject();

        $properties = new PropertyIterator();
        $form_object = new FormObject($properties);

        //В поле source_object вернем исходный загруженный объект,
        //чтобы выводить его значения визуально в форме.
        $form_object['source_object'] = $source_object;
        $form_object['__source_object']->setVisible(false);

        foreach($transformer->getFields() as $field) {
            $field_settings = $settings['fields'][$field->getFieldName()] ?? [];
            if (!empty($field_settings['enable'])) {
                $properties[$field->getFieldName()] = $field
                        ->getOrmProperty()
                        ->setName($field->getFieldName())
                        ->setDescription($source_object->getProp($field->getFieldName())->getDescription());

                $form_object[$field->getFieldName()] = $this['generated_data'][$field->getFieldName()] ?? null;
            }
        }

        return $form_object;
    }

    /**
     * Сохраняет изменения в исходном объекте и переводит статус результата в Выполнено.
     *
     * @param array $post_data
     * @return bool
     */
    public function approve($post_data)
    {
        if ($this['status'] == self::STATUS_NEW) {
            return $this->addError(t('В этом статусе вы не можете применить изменения. Дождитесь генерации данных.'));
        }

        $data = array_filter($post_data, function($value) {
            return $value !== '';
        });

        if ($data) {
            $object = $this->getEntityObject();
            $object->getFromArray($data);
            $object->update();
        }

        $this['status'] = self::STATUS_APPROVED;
        return $this->update();
    }

    /**
     * Переводит статус результата в Отменено, без установки данных генерации объекту
     *
     * @return bool
     */
    public function cancel()
    {
        $this['status'] = self::STATUS_CANCELLED;
        return $this->update();
    }

    /**
     * Инициализирует для чтения поля, которые хранятся в объекте задачи
     *
     * @return void
     */
    public function initTaskFields()
    {
        $this['total_count'] = $this->getTask()->total_count;
        $this['number_of'] = t('%0 из %1', [$this['number'], $this['total_count']]);
    }

    /**
     * Возвращает true, если результат можно применить к исходному объекту
     *
     * @return bool
     */
    public function canApprove()
    {
        return in_array($this['status'],
            [
                self::STATUS_GENERATED,
                self::STATUS_GENERATED_WITH_ERRORS,
                self::STATUS_CANCELLED
            ]);
    }

    /**
     * Возвращает true, если результат можно отменить
     *
     * @return bool
     */
    public function canCancel()
    {
        return in_array($this['status'], [
            self::STATUS_GENERATED,
            self::STATUS_GENERATED_WITH_ERRORS
        ]);
    }
}