<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photo\Model\PhotoType;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
use RS\Exception;

/**
* Базовый класс, описывающий тип связи файлов и объектов
*/
abstract class AbstractType
{
    const SIZE_UNIT_BYTE = 'b';
    const SIZE_UNIT_KILOBYTE = 'k';
    const SIZE_UNIT_MEGABYTE = 'm';
    const SIZE_UNIT_GIGABYTE = 'g';
    
    /**
    * Возвращает название типа 
    * @return string
    */
    abstract function getTitle();

    /**
     * Возвращает массив с допустимыми разрешениями для загрузки.
     * Если возвращен пустой массив, то это означает, что нет ограничений на
     * загружаемые расширения
     *
     * @return array
     */
    public function getAllowedExtensions()
    {
        return [];
    }

    /**
     * Возвращает массив с допустимыми типами файлов для загрузки.
     * Если возвращен пустой массив, то это означает, что нет ограничений на
     * загружаемые расширения
     *
     * @return array
     */
    public function getAllowedMimePhotoType()
    {
        return ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'];
    }

    /**
     * Проверяет права на загрузку файла в систему.
     * Возвращает текст ошибки или false - в случае отсутствия ошибки
     *
     * @return string | false
     * @throws Exception
     */
    public function checkUploadRightErrors($file_arr)
    {
        //Стандартный механизм проверки прав - проверяются права на запись у модуля,
        //к которому принадлежит текущий класс связи
        return Rights::CheckRightError($this, DefaultModuleRights::RIGHT_CREATE);
    }

    /**
     * Возвращает true, если пользователь может загружать файлы из клиентской части.
     * Если false, значит только администратор сможет воспользоваться загрузкой файла.
     *
     * @return bool
     */
    function canClientUploadPhotos()
    {
        return false;
    }

    /**
     * Возвращает максимальный размер допустимого для загрузки файла в байтах
     *
     * @param string $unit
     * @return integer 0 - без ограничений
     */
    protected function getMaxFilesizeBytes()
    {
        return 0;
    }

    /**
     * Возвращает максимальный размер допустимого для загрузки файла
     *
     * @param string $unit
     * @return integer
     */
    public function getMaxFilesize($unit = self::SIZE_UNIT_BYTE)
    {
        $bytes = $this->getMaxFilesizeBytes();
        if ($bytes) {
            $dividers = [
                self::SIZE_UNIT_BYTE => 1,
                self::SIZE_UNIT_KILOBYTE => 1024,
                self::SIZE_UNIT_MEGABYTE => pow(1024, 2),
                self::SIZE_UNIT_GIGABYTE => pow(1024, 3),
            ];

            return $bytes / $dividers[$unit];
        }
        return $bytes;
    }

    /**
     * Возвращает ID объекта, с которым связываются файлы сразу при их загрузке на сервер.
     * По умолчанию = 0, файл не связан ни с каким объектом, будет связан только при сохранении основного объекта.
     *
     * @return integer
     */
    public function getDefaultLinkId()
    {
        return 0;
    }
}
