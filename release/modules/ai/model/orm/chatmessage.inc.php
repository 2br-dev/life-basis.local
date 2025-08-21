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
 * ORM-объект, содержит последнюю переписку в рамках каждого пользователя.
 * Необходим для загрузки последней переписки после перезагрузки страницы
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $user_id Пользователь
 * @property string $date_of_create Дата создания
 * @property string $role Источник сообщения
 * @property string $message Сообщение
 * --\--
 */
class ChatMessage extends OrmObject
{
    const ROLE_USER = 'user';
    const ROLE_ASSISTANT = 'assistant';
    const ROLE_SYSTEM = 'system';

    protected static $table = 'ai_chat_messages';

    function _init()
    {
        parent::_init()->append([
            'user_id' => (new Type\User())
                ->setDescription(t('Пользователь')),
            'date_of_create' => (new Type\DateTime())
                ->setIndex(true)
                ->setDescription(t('Дата создания')),
            'role' => (new Type\Enum(array_keys(self::getRoleTitles())))
                ->setDescription(t('Источник сообщения')),
            'message' => (new Type\Text())
                ->setDescription(t('Сообщение'))
        ]);
    }

    /**
     * Обработчик перед сохранением объекта
     *
     * @param $flag
     * @return void
     */
    public function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['date_of_create'] = date('Y-m-d H:i:s');
        }
    }

    /**
     * Возвращает список названий ролей
     *
     * @return array
     */
    public static function getRoleTitles()
    {
        return [
            self::ROLE_ASSISTANT => t('ИИ-ассистент'),
            self::ROLE_USER => t('Пользователь'),
            self::ROLE_SYSTEM => t('Системное сообщение')
        ];
    }
}