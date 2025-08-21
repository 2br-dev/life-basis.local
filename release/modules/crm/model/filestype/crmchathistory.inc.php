<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\FilesType;

use Crm\Config\ModuleRights;
use Crm\Model\Orm\ChatHistory;
use Files\Model\FilesType\AbstractType;
use Files\Model\Orm\File;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Config\Loader;

/**
 * Тип файлов - "Файлы для чата"
 */
class CrmChatHistory extends AbstractType
{
    /**
     * Возвращает название типа
     * @return string
     */
    function getTitle()
    {
        return t('Файлы для чата');
    }

    /**
     * Проверяет права на скачивание файла
     * Возвращает текст ошибки или false - в случае отсутствия ошибки
     *
     * Скачивать файл может только пользователь, которому принадлежит тема переписки
     * или администратор
     *
     * @param File $file
     * @return string | false
     */
    function checkDownloadRightErrors(File $file)
    {
        if ($file['access'] == static::ACCESS_TYPE_VISIBLE) {
            if ($file['link_id']) {
                $chat_history = new ChatHistory($file['link_id']);
                $allow_user_id = $chat_history->user_id;
            } else {
                $allow_user_id = $file['user_id'];
            }

            $current_user = Auth::getCurrentUser();
            if ($current_user->isAdmin()
                || $current_user->id == $allow_user_id) {
                return false; //Ошибок нет, можно скачивать
            }
        }
        return t('Доступ к файлу запрещен');
    }

    /**
     * Проверяет права на загрузку файла в систему
     * Возвращает текст ошибки или false - в случае отсутствия ошибки
     *
     * @return string | false
     */
    public function checkUploadRightErrors($file_arr)
    {
        return Rights::CheckRightError($this, ModuleRights::TASK_CHAT_ADD_FILES);
    }

    /**
     * Возвращает максимальный размер допустимого для загрузки файла в байтах
     *
     * @param string $unit
     * @return integer | bool(false) false - если нет ограничений
     */
    protected function getMaxFilesizeBytes()
    {
        return 50 * 1024 * 1024;
    }

    /**
     * Возвращает уровень доступа, устанавливаемый сразу после загрузки файла
     */
    public function getDefaultAccessType()
    {
        return self::ACCESS_TYPE_VISIBLE;
    }
}
