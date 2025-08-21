<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Photo\Model\Orm;
use Photo\Model\PhotoApi;
use Photo\Model\PhotoType\AbstractType;
use Photo\Model\PhotoType\Exception;
use \RS\Orm\Type;

/**
 * Фотография объекта
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $servername Имя файла на сервере
 * @property string $filename Оригинальное имя файла
 * @property integer $view_count Количество просмотров
 * @property integer $size Размер файла
 * @property string $mime Mime тип изображения
 * @property integer $sortn Порядковый номер
 * @property string $title Подпись изображения
 * @property string $type Название объекта, которому принадлежат изображения
 * @property integer $linkid Идентификатор объекта, которому принадлежит изображение
 * @property string $extra Дополнительный символьный идентификатор изображения
 * @property string $hash Хэш содержимого файла
 * @property string $uniq Уникальный идентификатор
 * --\--
 */
class Image extends \RS\Orm\OrmObject
{
    protected static $table = 'images';
    
    protected $img_core;
    protected $srcFolder = '/storage/photo/original';
    protected $dstFolder = '/storage/photo/resized';
    
    function __construct($id = null, $cache = true)
    {
        $this->srcFolder = \Setup::$FOLDER.$this->srcFolder;
        $this->dstFolder = \Setup::$FOLDER.$this->dstFolder;        
        
        parent::__construct($id, $cache);
        $this->img_core = new \RS\Img\Core(\Setup::$ROOT, $this->srcFolder, $this->dstFolder);
    }
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'servername' => new Type\Varchar([
                'description' => t('Имя файла на сервере'),
                'maxLength' => 25,
                'index' => true
            ]),
            'filename' => new Type\Varchar([
                'description' => t('Оригинальное имя файла'),
                'maxLength' => 255
            ]),
            'view_count' => new Type\Integer([
                'description' => t('Количество просмотров')
            ]),
            'size' => new Type\Integer([
                'description' => t('Размер файла'),
                'hint' => t('В байтах')
            ]),
            'mime' => new Type\Varchar([
                'description' => t('Mime тип изображения'),
                'maxLength' => 20
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Порядковый номер'),
                'allowempty' => false
            ]),
            'title' => new Type\Text([
                'description' => t('Подпись изображения')
            ]),
            'type'  => new Type\Varchar([
                'description' => t('Название объекта, которому принадлежат изображения'),
                'maxLength' => 80
            ]),
            'linkid' => new Type\Integer([
                'description' => t('Идентификатор объекта, которому принадлежит изображение')
            ]),
            'extra' => new Type\Varchar([
                'description' => t('Дополнительный символьный идентификатор изображения'),
                'maxLength' => 255
            ]),
            'hash' => new Type\Varchar([
                'description' => t('Хэш содержимого файла'),
                'maxLength' => 50
            ]),
            'uniq' => new Type\Varchar([
                'description' => t('Уникальный идентификатор'),
                'visible' => false,
                'maxLength' => 32,
                'unique' => true
            ]),
        ]);
        
        $this
            ->addIndex(['servername', 'type', 'linkid'], self::INDEX_UNIQUE)
            ->addIndex(['linkid', 'type'])
            ->addIndex(['linkid', 'sortn']);
    }
    
    function getFolders()
    {
    	return [
    		'srcFolder' => $this->srcFolder,
    		'dstFolder' => $this->dstFolder
        ];
	}
    
    /**
    * При создании записи sortn - ставим максимальный, т.е. добавляем фото в конец.
    */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG)
        {
            $this['uniq'] = self::generateUniq();
            $this['sortn'] = \RS\Orm\Request::make()
                ->select('MAX(sortn)+1 as next_sort')
                ->from($this)
                ->where([
                    'linkid' => $this['linkid'],
                    'type' => $this['type']
                ])
                ->exec()->getOneField('next_sort', 0);
        }
        return true;
    }

    /**
     * Генерирует уникальный 32 байтный идентификатор для файла
     *
     * @return string
     */
    public static function generateUniq()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * Загружает текущий объект по уникальному хэшу
     *
     * @param string $uniq Публичный уникальный идентификатор
     * @return self
     */
    public static function loadByUniq(string $uniq)
    {
        return self::loadByWhere([
            'uniq' => $uniq
        ]);
    }

    /**
     * Возвращает относительный URL к картинке
     *
     * @param int $width - ширина
     * @param int $height - высота
     * @param string $img_type (xy|cxy|axy) $img_type - тип картинки
     * @param bool $absolute - если True, то будет возвращен абсолютный путь, иначе относительный
     * @param bool $force_create - если true, то изображение будет гарантировано создано на диске в
     * момент вызова этой функции, если такого изображения еще нет.
     * @return string URL
     * @throws \RS\Img\Exception
     */
    function getUrl($width, $height, $img_type = 'xy', $absolute = false, $force_create = false)
    {
        //Пользуемся общей системой отображения картинок этой CMS.
        $url = $this->img_core->getImageUrl($this['servername'], $width, $height, $img_type, $absolute);

        if ($force_create) {
            $this->img_core->buildImage($this['servername'], $width, $height, $img_type, null, false);
        }

        return $url;
    }

    /**
     * Возвращает реальные ширину и высоту изображения с учетом запрошенных размеров.
     *
     * @param int $width
     * @param int $height
     * @param string $img_type
     * @return array Возвращает массив с ключами width, height
     */
    function getImageSize($width = 0, $height = 0, $img_type = 'xy')
    {
        if (!$width && !$height) {
            $relative_url = $this->getOriginalUrl();
        } else {
            $relative_url = $this->getUrl($width, $height, $img_type, false, true);
        }

        $path = \Setup::$ROOT.$relative_url;
        if ($info = getimagesize($path)) {
            list($width, $height) = $info;
        } else {
            $width = 0;
            $height = 0;
        }
        return [
            'width' => $width,
            'height' => $height
        ];
    }
    
    /**
    * Возвращает объект системы картинок для этой CMS
    * @return \RS\Img\Core
    */
    function getImageCore()
    {
        return $this->img_core;
    }
    
    /**
    * Возвращает URL файла оригинала
    * 
    * @param boolean $absolute - флаг отвечает за, то какую ссылку отображать абсолютную или относительную
    * 
    * @return string
    */
    function getOriginalUrl($absolute = false)
    {
        $url = $this->srcFolder.'/'.$this['servername'];
        return $absolute ?  \RS\Site\Manager::getSite()->getAbsoluteUrl($url) : $url;
    }

    /**
     * Возвращает объект типа связи
     *
     * @return AbstractType
     * @throws Exception
     */
    function getLinkType()
    {
        static
        $cache_type = [];

        $type = $this['type'];
        if (!isset($cache_type[$type])) {
            $cache_type[$type] = PhotoApi::getTypeClassInstance($type);
        }
        return $cache_type[$type];
    }
    
    function delete()
    {
        $remain = \RS\Orm\Request::make()->from($this)->where(['servername' => $this['servername']])->count();
        if ($result = parent::delete()) {
            if ($remain<2) {
                $img = new \RS\Img\Core(\Setup::$ROOT, $this->srcFolder, $this->dstFolder);
                $img->removeFile($this['servername']);
            }
            
            // Исправление порядковых номеров сортировки сестринских изображений
            $photo_api = new PhotoApi;
            $photo_api->fixSortNumbers($this['linkid'], $this['type']);
        }
        return $result;
    }
    
    /**
    * Перемещает элемент на новую позицию. 0 - первый элемент
    * 
    * @param mixed $new_position
    */
    public function moveToPosition($new_position)
    {
        if ($this->noWriteRights()) return false;
        
        $q = \RS\Orm\Request::make()
            ->update($this)
            ->where([
                'linkid' => $this['linkid'],
                'type' => $this['type']
            ]);
        
        //Определяем направлене перемещения 
        if ($this['sortn'] < $new_position) {
            //Вниз
            $q->set('sortn = sortn - 1')
            ->where("sortn > '#cur_pos' AND sortn <= '#new_pos'", ['cur_pos' => $this['sortn'], 'new_pos' => $new_position]);
        } else { 
            //Вверх
            $q->set('sortn = sortn + 1')
                ->where("sortn >= '#new_pos' AND sortn < '#cur_pos'", ['cur_pos' => $this['sortn'], 'new_pos' => $new_position]);
        }
        $q->exec();
        
        \RS\Orm\Request::make()
            ->update($this)
            ->set(['sortn' => $new_position])
            ->where([
                'id' => $this['id']
            ])
            ->exec();
        return true;
    }

    /**
     * Переворачивает изображение на 90 градусов.
     *
     * @param string $angle cw | ccw $direction направление переворота. cw - по часовой стралке, ccw - против часовой стрелки
     * @throws \RS\Img\Exception
     */
    function rotate($angle)
    {
        $img = new \RS\Img\Core(\Setup::$PATH, $this->srcFolder, $this->dstFolder);
        $img->rotate($this['servername'], $angle);
    }

    /**
     * Отражает изображение по горизонтали или по вертикали.
     *
     * @param string $direction horizontal | vertical $direction направление отображения. horizontal - по горизонтали, vertical - по вертикали
     * @throws \RS\Img\Exception
     */
    function flip($direction)
    {
        $img = new \RS\Img\Core(\Setup::$PATH, $this->srcFolder, $this->dstFolder);
        $img->flip($this['servername'], $direction);
    }
}

