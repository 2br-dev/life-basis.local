<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Model\FilesType;

use Catalog\Controller\Front\Product;
use Files\Model\Orm\File;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

/**
* Базовый класс, описывающий тип связи файлов и объектов
*/
abstract class AbstractType
{
    const ACCESS_TYPE_HIDDEN = 'hidden';
    const ACCESS_TYPE_VISIBLE = 'visible';

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
     * Возвращает уровень доступа, устанавливаемый сразу после загрузки файла
     */
    public function getDefaultAccessType()
    {
        return self::ACCESS_TYPE_HIDDEN;
    }
    
    /**
    * Возвращает массив с допустимыми разрешениями для загрузки. 
    * Если возвращен пустой массив, то это означает, что нет ограничений на 
    * загружаемые расширения
    * 
    * @return []
    */
    public function getAllowedExtensions()
    {
        return [];
    }

    /**
     * Возвращает допустимые расширения файлов в виде строки ".jpg,.png"
     *
     * @return string
     */
    public function getAllowExtensionsString()
    {
        $extensions = array_map(function($value) {
            return '.'.$value;
        }, $this->getAllowedExtensions());

        return implode(',', $extensions);
    }


    /**
    * Возвращает массив с возможными уровнями доступа
    * [id => пояснение, id => пояснение, ...]
    * или
    * [id => ['title' => 'пояснение', 'hint' => 'подробная подсказка']]
    * 
    * @return []
    */
    public static function getAccessTypes()
    {
        return [
            'hidden' => t('скрытый'),
            'visible' => t('публичный')
        ];
    }
    
    /**
    * Возвращает ID связанного объекта, если находимся на странице просмотра товара
    * 
    * @return integer | false
    */
    public function getLinkObjectId()
    {
        $router = \RS\Router\Manager::obj();
        $route = $router->getCurrentRoute();
        if ($route->getId() == 'catalog-front-product') {
            if ($product = $route->getExtra(Product::ROUTE_EXTRA_PRODUCT)) {
                return $product->id;
            }
        }
        return false;
    }
    
    /**
    * Проверяет права на загрузку файла в систему
    * Возвращает текст ошибки или false - в случае отсутствия ошибки
    * 
    * @return string | false
    */
    public function checkUploadRightErrors($file_arr)
    {
        //Стандартный механизм проверки прав - проверяются права на запись у модуля,
        //к которому принадлежит текущий класс связи
        return Rights::CheckRightError($this, DefaultModuleRights::RIGHT_CREATE);
    }
    
    /**
    * Проверяет права на скачивание файла
    * Возвращает текст ошибки или false - в случае отсутствия ошибки
    * 
    * @param File $file
    * @return string | false
    */
    public function checkDownloadRightErrors(File $file)
    {
        return false;
    }
    
    /**
    * Возвращает true, если для скачивания $access требуется авторизация
    * 
    * @param File $file - Файл, который скачивается
    * @return bool
    */
    public function getNeedGroupForDownload(File $file)
    {
        return false;
    }
    
    /**
    * Возвращает короткий идентификатор текущего класса
    * 
    * @return string
    */
    public static function getShortName()
    {
        $class = strtolower(trim(str_replace('\\', '-', get_called_class()),'-'));
        return str_replace('model-filestype-', '', $class);
    }


    /**
     * Возвращает true, если пользователь может загружать файлы из клиентской части.
     * Если false, значит только администратор сможет воспользоваться загрузкой файла.
     *
     * @return bool
     */
    function canClientUploadFiles()
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
     * Возвращает поле - уникальный идентификатор (ID) объекта, с которым связываются файлы
     *
     * @return string
     */
    public function getLinkIdField()
    {
        return 'id';
    }

    /**
     * Возвращает ID объекта, с которым связываются файлы сразу при их загрузке на сервер.
     * По умолчанию = 0, файл не связан ни с каким объектом, будет связан только при сохранении основного объекта.
     *
     * @return integer|string
     */
    public function getDefaultLinkId()
    {
        return 0;
    }
}
