<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * ORM-объект статистики расходования токенов в запросах к ИИ
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $user_id Пользователь
 * @property string $date_of_create Дата и время
 * @property integer $service_id GPT-сервис
 * @property string $transformer_id Трансформер
 * @property string $type Тип генерации
 * @property string $field Поле
 * @property integer $prompt_id ID запроса
 * @property integer $task_id ID задачи
 * @property integer $task_result_id ID результата задачи
 * @property integer $input_text_tokens Токенов в запросе
 * @property integer $completion_tokens Токенов в ответе
 * @property integer $total_tokens Всего токенов
 * --\--
 */
class Statistic extends OrmObject
{
    const TYPE_FIELD_FILL = 'field_fill';
    const TYPE_TASK_FILL = 'task_fill';
    const TYPE_CHAT = 'chat';
    const TYPE_OTHER = 'other';

    protected static $table = 'ai_statistic';

    function _init()
    {
        parent::_init()->append([
            'site_id' => (new Type\CurrentSite()),
            'user_id' => (new Type\User())
                ->setDescription(t('Пользователь')),
            'date_of_create' => (new Type\Datetime())
                ->setDescription(t('Дата и время'))
                ->setIndex(true),
            'service_id' => (new Type\Integer())
                ->setDescription(t('GPT-сервис'))
                ->setList(['Ai\Model\ServiceApi', 'staticSelectList'], [Service::SERVICE_READYSCRIPT_ID => 'ReadyScript'])
                ->setIndex(true),
            'transformer_id' => (new Type\Varchar())
                ->setDescription(t('Трансформер'))
                ->setList(['Ai\Model\TransformerApi', 'staticSelectList'])
                ->setIndex(true),
            'type' => (new Type\Varchar())
                ->setDescription(t('Тип генерации'))
                ->setList([__CLASS__, 'getTypeTitles'])
                ->setIndex(true)
                ->setMaxLength(50),
            'field' => (new Type\Varchar())
                ->setDescription(t('Поле')),
            'prompt_id' => (new Type\Integer())
                ->setDescription(t('ID запроса')),
            'task_id' => (new Type\Integer())
                ->setDescription(t('ID задачи')),
            'task_result_id' => (new Type\Integer())
                ->setDescription(t('ID результата задачи')),
            'input_text_tokens' => (new Type\Integer())
                ->setDescription(t('Токенов в запросе')),
            'completion_tokens' => (new Type\Integer())
                ->setDescription(t('Токенов в ответе')),
            'total_tokens' => (new Type\Integer())
                ->setDescription(t('Всего токенов')),
        ]);
    }

    /**
     * Возвращает список возможных название типов
     *
     * @param array $first
     * @return array
     */
    public static function getTypeTitles(array $first = [])
    {
        return $first + [
            self::TYPE_FIELD_FILL => t('Заполнение полей'),
            self::TYPE_TASK_FILL => t('Массовое заполнение'),
            self::TYPE_CHAT => t('Чат'),
            self::TYPE_OTHER => t('Другое')
        ];
    }
}