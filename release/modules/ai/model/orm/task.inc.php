<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Orm;

use Ai\Model\Transformer\AbstractTransformer;
use Ai\Model\TransformerApi;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;

/**
 * ORM-объект, описывающий задачу на дозаполнение контента объектов через ИИ
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $user_id Создатель задачи
 * @property string $date_of_create Дата создания
 * @property string $date_of_update Дата обновления
 * @property string $transformer_id ID трансформера
 * @property string $settings Параметры задачи
 * @property array $entity_ids Список ID обновляемых объектов
 * @property string $status Статус задачи
 * @property integer $total_count Количество объектов для обновления
 * @property integer $generated_count Количество обработанных объектов
 * @property integer $approved_count Обновлено объектов
 * @property integer $skipped_count Пропущено объектов
 * @property integer $errors_count Сгенерировано с ошибками
 * --\--
 */
class Task extends OrmObject
{
    const STATUS_NEW = 'new';
    const STATUS_GENERATING = 'generating';
    const STATUS_READY = 'ready';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FINISHED = 'finished';

    protected static $table = 'ai_task';

    function _init()
    {
        parent::_init()->append([
            'site_id' => (new Type\CurrentSite()),
            'user_id' => (new Type\User())
                ->setDescription(t('Создатель задачи')),
            'date_of_create' => (new Type\Datetime())
                ->setDescription(t('Дата создания')),
            'date_of_update' => (new Type\Datetime())
                ->setDescription(t('Дата обновления')),
            'transformer_id' => (new Type\Varchar())
                ->setDescription(t('ID трансформера'))
                ->setList(['Ai\Model\TransformerApi', 'staticSelectList']),
            'settings' => (new Type\Text())
                ->setDescription(t('Параметры задачи'))
                ->setChecker([__CLASS__, 'checkSettings']),
            'entity_ids' => (new Type\ArrayList())
                ->setDescription(t('Список ID обновляемых объектов')),
            'status' => (new Type\Varchar())
                ->setDescription(t('Статус задачи'))
                ->setList([__CLASS__, 'getStatusTitles']),
            'total_count' => (new Type\Integer())
                ->setDescription(t('Количество объектов для обновления'))
                ->setAllowEmpty(false),
            'generated_count' => (new Type\Integer())
                ->setDescription(t('Количество обработанных объектов'))
                ->setAllowEmpty(false),
            'approved_count' => (new Type\Integer())
                ->setDescription(t('Обновлено объектов'))
                ->setAllowEmpty(false),
            'skipped_count' => (new Type\Integer())
                ->setDescription(t('Пропущено объектов'))
                ->setAllowEmpty(false),
            'errors_count' => (new Type\Integer())
                ->setDescription(t('Сгенерировано с ошибками'))
                ->setAllowEmpty(false),
        ]);
    }

    /**
     * Проверяет заполнение параметров задачи
     *
     * @param $_this
     * @param $value
     * @return bool|string
     */
    public static function checkSettings($_this, $value)
    {
        $settings = $_this->getSettings();
        $enable = count(array_filter($settings['fields'] ?? [], function ($item) {
            return !empty($item['enable']);
        })) > 0;

        if (!$enable) {
            return t('Выберите хотя бы одно поле для заполнения');
        }

        return true;
    }

    /**
     * Возвращает настройки задачи
     *
     * @return array
     */
    function getSettings()
    {
        return json_decode((string)$this['settings'], true) ?: [];
    }

    /**
     * Устанавливает настройки задачи
     *
     * @param $settings
     * @return void
     */
    function setSettings($settings)
    {
        $this['settings'] = json_encode($settings, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Обработчик перед сохранением
     *
     * @param bool $flag
     * @return void
     */
    public function beforeWrite($flag)
    {
        $now = date('Y-m-d H:i:s');
        if ($flag == self::INSERT_FLAG) {
            $this['date_of_create'] = $now;
            $this['status'] = self::STATUS_NEW;
            $this['total_count'] = count($this['entity_ids'] ?: []);
        }

        if ($flag == self::UPDATE_FLAG) {
            $this['date_of_update'] = $now;
        }
    }

    /**
     * Обработчик после сохранения
     *
     * @param $flag
     * @return void
     */
    public function afterWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this->makeTaskResult();
        }
    }

    /**
     * Создает объекты результатов заполнения
     *
     * @param array $entity_ids
     * @return void
     */
    protected function makeTaskResult()
    {
        foreach($this['entity_ids'] ?: [] as $n => $id) {
            $task_result = new TaskResult();
            $task_result['task_id'] = $this['id'];
            $task_result['transformer_id'] = $this['transformer_id'];
            $task_result['entity_id'] = $id;
            $task_result['status'] = TaskResult::STATUS_NEW;
            $task_result['number'] = $n+1;
            $task_result->insert();
        }
    }

    /**
     * Возвращает названия статусов
     *
     * @return array
     */
    public static function getStatusTitles($first = [])
    {
        return $first + [
            self::STATUS_NEW => t('Новая'),
            self::STATUS_GENERATING => t('Идет генерация'),
            self::STATUS_READY => t('Генерация завершена'),
            self::STATUS_FINISHED => t('Полностью завершена'),
            self::STATUS_CANCELLED => t('Отменена')
        ];
    }

    /**
     * Возвращает текстовое представление статуса
     *
     * @return string
     */
    public function getStatusTitle()
    {
        $statuses = self::getStatusTitles();
        return $statuses[$this['status']];
    }

    /**
     * Удаляет задачу
     *
     * @return bool
     */
    public function delete()
    {
        if ($result = parent::delete()) {

            //Удаляем дочерние элементы
            Request::make()
                ->delete()
                ->from(new TaskResult())
                ->where([
                    'task_id' => $this['id']
                ])->exec();
        }
        return $result;
    }

    /**
     * Возвращает генератор для элементов TaskResult, которые находтся в статусах, требующих генерацию
     *
     * @return \Generator
     */
    public function getTaskResultForGeneration()
    {
        $ids = Request::make()
            ->select('id')
            ->from(new TaskResult())
            ->where([
                'task_id' => $this['id'],
                'status' => TaskResult::STATUS_NEW
            ])
            ->exec()
            ->fetchSelected(null, 'id');

        foreach($ids as $id) {
            $task_result = new TaskResult($id);
            $task_result->setTask($this);
            yield $task_result;
        }
    }

    /**
     * Обновляет счетчики по данной задаче
     *
     * @return void
     */
    function updateCounts()
    {
        $rows = Request::make()
            ->select('status, COUNT(*) as cnt')
            ->from(new TaskResult())
            ->where([
                'task_id' => $this['id'],
            ])
            ->groupBy('status')
            ->exec()->fetchAll();

        $counts = [
            'generated_count' => 0,
            'approved_count' => 0,
            'skipped_count' => 0,
            'errors_count' => 0
        ];
        foreach($rows as $row) {
            if ($row['status'] == TaskResult::STATUS_NEW) {
                continue;
            }

            if ($row['status'] == TaskResult::STATUS_APPROVED) {
                $counts['approved_count'] += $row['cnt'];
            }

            if ($row['status'] == TaskResult::STATUS_CANCELLED) {
                $counts['skipped_count'] += $row['cnt'];
            }

            if ($row['status'] == TaskResult::STATUS_GENERATED_WITH_ERRORS) {
                $counts['errors_count'] += $row['cnt'];
            }

            $counts['generated_count'] += $row['cnt'];
        }

        $this->getFromArray($counts);
        $this->update();
    }

    /**
     * Перезапускает текущую задачу
     *
     * @return bool
     */
    public function restart()
    {
        Request::make()
            ->update(new TaskResult())
            ->set([
                '_generated_data' => null,
                'status' => TaskResult::STATUS_NEW,
                'error' => ''
            ])
            ->where([
                'task_id' => $this['id'],
            ])->exec();

        $this['status'] = self::STATUS_NEW;
        $this['generated_count'] = 0;
        $this['approved_count'] = 0;
        $this['skipped_count'] = 0;
        $this['errors_count'] = 0;

        return $this->update();
    }

    /**
     * Переводит задачу в статус Отменено
     *
     * @return bool
     */
    public function stop()
    {
        $this['status'] = self::STATUS_CANCELLED;
        return $this->update();
    }

    /**
     * Возвращает объект трансформера
     *
     * @return AbstractTransformer
     * @throws \RS\Exception
     */
    public function getTransformer()
    {
        return AbstractTransformer::getTransformerById($this['transformer_id']);
    }

    /**
     * Возвращает поля, выбранные для заполнения в данной задаче
     *
     * @return array
     */
    public function getSelectedFields()
    {
        $result = [];
        $settings = $this->getSettings();
        foreach($this->getTransformer()->getFields() as $field) {
            $field_settings = $settings['fields'][$field->getFieldName()] ?? [];
            if (!empty($field_settings['enable'])) {
                $form_object = $field->getSettingFormObject();
                $form_object->getFromArray($field_settings);

                $result[] = [
                    'field' => $field,
                    'form_object' => $form_object
                ];
            }
        }
        return $result;
    }

    /**
     * Возвращает true, если задачу можно отменить
     *
     * @return bool
     */
    public function canCancel()
    {
        return in_array($this['status'], [self::STATUS_NEW, self::STATUS_GENERATING]);
    }
}