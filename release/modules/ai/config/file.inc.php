<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Config;

use RS\Orm\ConfigObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;

/**
 * Класс конфигурации модуля
 */
class File extends ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'chat_enable' => (new Type\Integer())
                    ->setDescription(t('Отображать кнопку чата с ИИ'))
                    ->setCheckboxView(1, 0),
            t('GPT-сервисы'),
                'max_tokens' => (new Type\Integer())
                    ->setDescription(t('Максимальное количество токенов в ответе'))
                    ->setHint(t('Вы можете ограничивать количество символов в ответе прямо в тексте промпта(запроса), однако эта настройка имеет более высокий приоритет и распространяется на все запросы в системе.')),
                'timeout_sec' => (new Type\Integer())
                    ->setDescription(t('Таймаут ожидания ответа в секундах'))
                    ->setHint(t('Запрос к внешнему сервису будет прерван, если он будет отвечать более указанного значения времени')),
                'cron_step_timeout_sec' => (new Type\Integer())
                    ->setDescription(t('Время выполнения одного шага массовой генерации данных'))
                        ->setHint(t('Необходимо ограничивать время работы одного шага генерации, чтобы на фоне у вас смогли выполняться и другие запланированные задачи. Оптимальный лимит - является 20 секунд за 1 шаг. Т.е. при ежеминутном запуске планировщика 20 секунд будет выделяться на обработку задач по генерации контента. Вы можете увеличить этот лимит на время, если вам нужно сгенерировать большое количество данных в короткие сроки.')),
                'default_service' => (new Type\Varchar())
                    ->setDescription(t('Сервис по умолчанию для автозаполнения'))
                    ->setList(['Ai\Model\ServiceApi', 'staticSelectList'], ['-1' => t('ReadyScript')]),
                'default_chat_service' => (new Type\Varchar())
                    ->setDescription(t('Сервис по умолчанию для чата'))
                    ->setList(['Ai\Model\ServiceApi', 'staticSelectList'], ['-1' => t('ReadyScript')]),
        ]);
    }

    /**
     * Конфигурация общая для всех мультисайтов
     *
     * @return bool
     */
    public function isMultisiteConfig()
    {
        return false;
    }

    /**
     * Возвращает значения свойств по-умолчанию
     *
     * @return array
     */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => RouterManager::obj()->getAdminUrl(false, [], 'ai-servicectrl'),
                    'title' => t('Дополнительные сервисы GPT'),
                    'description' => t('Здесь вы можете настроить профили прямого подключения к другим GPT-сервисам.'),
                    'class' => ' '
                ]
            ]
        ];
    }
}
