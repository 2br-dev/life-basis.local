<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Comments\Model\PhotoType;

use RS\Application\Auth;
use RS\Config\Loader;
use Photo\Model\PhotoType\AbstractType;

/**
 * Тип фото - "Фото для комментариев к товарам"
 */
class CommentPhotos extends AbstractType
{
    protected $config;

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
        return t('Фото для комментариев к товарам');
    }

    /**
     * Возвращает короткий идентификатор текущего класса
     *
     * @return string
     */
    public static function getLinkType()
    {
        $class = strtolower(trim(str_replace('\\', '-', get_called_class()),'-'));
        return str_replace('model-phototype-', '', $class);
    }

    /**
     * Возвращает true, если пользователь может загружать фото из клиентской части
     * Если false, значит только администратор сможет воспользоваться загрузкой файла.
     *
     * @return bool
     */
    function canClientUploadPhotos()
    {
        return $this->config['allow_attachments'] == 1;
    }

    /**
     * Возвращает максимальный размер допустимого для загрузки файла в байтах
     *
     * @return float|integer - false, если нет ограничений
     */
    protected function getMaxFilesizeBytes()
    {
        return $this->config['attachment_max_filesize'] * 1024 * 1024;
    }
}
