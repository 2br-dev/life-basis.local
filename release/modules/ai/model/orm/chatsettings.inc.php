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
 * ORM-объект, содержит индивидуальные настройки чата в рамках пользователя
 * --/--
 * @property integer $user_id Пользователь
 * @property integer $chat_top Отступ сверху диалогового окна
 * @property integer $chat_right Отступ справа диалогового окна
 * @property integer $chat_width Ширина диалогового окна
 * @property integer $chat_height Высота диалогового окна
 * @property string $chat_stick Сторона привязки окна
 * @property integer $trigger_bottom Позиция по вертикали триггера
 * --\--
 */
class ChatSettings extends OrmObject
{
    protected static $table = 'ai_chat_settings';

    protected function _init()
    {
        $this->getPropertyIterator()->append([
            'user_id' => (new Type\User())
                ->setDescription(t('Пользователь'))
                ->setPrimaryKey(true),
            'chat_top' => (new Type\Integer())
                ->setDescription(t('Отступ сверху диалогового окна')),
            'chat_right' => (new Type\Integer())
                ->setDescription(t('Отступ справа диалогового окна')),
            'chat_width' => (new Type\Integer())
                ->setDescription(t('Ширина диалогового окна')),
            'chat_height' => (new Type\Integer())
                ->setDescription(t('Высота диалогового окна')),
            'chat_stick' => (new Type\Enum(['none', 'right']))
                ->setDescription(t('Сторона привязки окна')),
            'trigger_bottom' => (new Type\Integer())
                ->setDescription(t('Позиция по вертикали триггера'))
        ]);
    }

    /**
     * Возвращает имя свойства, которое помечено как первичный ключ.
     *
     * @return string
     */
    public function getPrimaryKeyProperty()
    {
        return 'user_id';
    }
}