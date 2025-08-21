<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;
use Crm\Model\FilesType\CrmChatHistory;
use Files\Model\FileApi;
use Files\Model\Orm\File;
use Files\Model\OrmType\Files;
use RS\Orm\OrmObject;
use \RS\Orm\Type;
use Support\Model\FilesType\SupportFiles;

class ChatHistory extends OrmObject
{
    const TYPE_MESSAGE = 'message';
    const TYPE_SYSTEM = 'system';

    protected static
        $table = 'crm_chat_history';

    function _init()
    {
        parent::_init()->append([
            'task_id' => new Type\Integer([
                'description' => 'ID задачи'
            ]),
            'autotask_id' => new Type\Integer([
                'description' => 'ID автозадачи'
            ]),
            'user_id' => new Type\Integer([
                'description' => 'ID пользователя'
            ]),
            'reply_to_id' => new Type\Integer([
                'description' => 'ID сообщения'
            ]),
            'autotask_root_id' => new Type\Integer([
                'description' => t('Идентификатор корневой автозадачи'),
                'visible' => false,
            ]),
            'type' => new Type\Enum([self::TYPE_MESSAGE, self::TYPE_SYSTEM], [
                'description' => 'Тип'
            ]),
            'message' => new Type\Richtext([
                'description' => t('Текст сообщения'),
            ]),
            'date_of_create' => new Type\Datetime([
                'description' => t('Дата создания')
            ]),
            'extra' =>  new Type\Richtext([
                'maxLength' => '255',
                'description' => t('Дополнительное поле для данных'),
            ]),
            'attachments' => (new Files())
                ->setDescription(t('Файлы'))
                ->setLinkType(CrmChatHistory::getShortName())
        ]);
    }

    /**
     * Возвращает вложения, связанные с сообщением
     *
     * @return File[]
     */
    public function getAttachments()
    {
        return FileApi::getLinkedFiles(CrmChatHistory::getShortName(),
            $this['id'],
            CrmChatHistory::ACCESS_TYPE_VISIBLE);
    }

    /**
     * Обработчик после сохранения объектоа
     *
     * @param string $flag
     */
    function afterWrite($flag)
    {
        //Привязывает вложения к сообщению
        if ($this->isModified('attachments')) {
            foreach($this['attachments'] as $public_hash) {
                $file = File::loadByUniq($public_hash);
                if ($file['id'] && $file['link_id'] != $this['id']) {
                    $file['link_id'] = $this['id'];
                    $file->update();
                }
            }
        }
    }
}