<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\FilesType;

use Files\Model\FilesType\AbstractType;
use Files\Model\Orm\File;
use RS\Application\Auth;
use RS\Config\Loader;
use Support\Model\Orm\Support;

/**
 * Тип файлов - "Файлы для товаров"
 */
class SupportFiles extends AbstractType
{
    protected $config;
    protected $allowed_extension;

    function __construct()
    {
        $this->config = Loader::byModule($this);
    }

    /**
     * Возвращает название типа
     * @return string
     */
    function getTitle()
    {
        return t('Файлы для сообщений поддержки');
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
                $message = new Support($file['link_id']);
                $allow_user_id = $message->getTopic()->user_id;
            } else {
                //Если файл еще не связан с сообщением, то скачать
                //его может только загрузивший его пользователь
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
     * Возвращает true, если пользователь может загружать файлы из клиентской части
     *
     * @return bool
     */
    function canClientUploadFiles()
    {
        return $this->config['allow_attachments'] == 1;
    }

    /**
     * Возвращает массив из допустимых расширений файлов для загрузки
     *
     * @return array
     */
    function getAllowedExtensions()
    {
        if ($this->allowed_extension !== null) {
            return $this->allowed_extension;
        } else {
            if ($this->config['attachment_allow_extensions']) {
                return explode(',', $this->config['attachment_allow_extensions']);
            } else {
                return [];
            }
        }
    }

    /**
     * Возвращает массив из допустимых расширений файлов для загрузки
     *
     * @param array $extensions
     * @return array
     */
    function setAllowedExtensions(array $extensions)
    {
        $this->allowed_extension = $extensions;
    }

    /**
     * Возвращает максимальный размер допустимого для загрузки файла в байтах
     *
     * @param string $unit
     * @return integer | bool(false) false - если нет ограничений
     */
    protected function getMaxFilesizeBytes()
    {
        return $this->config['attachment_max_filesize'] * 1024 * 1024;
    }

    /**
     * Возвращает уровень доступа, устанавливаемый сразу после загрузки файла
     */
    public function getDefaultAccessType()
    {
        return self::ACCESS_TYPE_VISIBLE;
    }
}
