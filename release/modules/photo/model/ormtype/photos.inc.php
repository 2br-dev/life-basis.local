<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photo\Model\OrmType;

use Photo\Model\Orm\Image;
use Photo\Model\PhotoApi;
use Photo\Model\PhotoType\AbstractType;
use Photo\Model\PhotoType\Exception;
use RS\Orm\Request;
use RS\Orm\Type\UserTemplate;
use RS\Router\Manager;
use RS\View\Engine;
use SmartyException;

/**
 * Тип поля "Фотографии", должен располагаться на отдельной вкладке ORM объекта
 */
class Photos extends UserTemplate
{
    protected $link_type;
    protected $front_template = '%photo%/ormtype/photos_front.tpl'; //Шаблон для клиентской части сайта
    protected $upload_url;
    protected $remove_url;
    protected $options = [];
    protected $dropzone_options = [];

    /**
     * Конструктор класса
     * @param null|array $options - массив дополнительных параметров
     */
    public function __construct($options = null)
    {
        $this->options = $options;
        $this->dropzone_options = [
            'dictDefaultMessage' => t('Перетащите сюда Ваши изображения')
        ];
        parent::__construct('%photo%/ormtype/photos.tpl', '', $options);
    }

    /**
     * Устанавливает тип связи файлов
     *
     * @param string $link_type Объект типа связи
     * @return self
     */
    public function setLinkType(string $link_type)
    {
        $this->link_type = $link_type;
        return $this;
    }

    /**
     * Возвращает тип связи
     *
     * @return AbstractType
     */
    public function getLinkType()
    {
        return $this->link_type;
    }

    /**
     * Возвращает объект связи
     *
     * @return AbstractType
     * @throws Exception
     */
    public function getLinkObject()
    {
        return PhotoApi::getTypeClassInstance($this->link_type);
    }

    /**
     * Возвращает форму в определённом шаблоне
     *
     * @param null|array $view_options - массив дополнительных аттрибутов в форме
     * @param null|object $orm_object - объект, которому принадлежит поле
     * @return string
     * @throws SmartyException
     */
    public function formView($view_options = null, $orm_object = null)
    {
        $sm = new Engine();
        $sm -> assign([
            'field' => $this,
            'view_options' => $view_options !== null ? array_combine($view_options, $view_options) : null,
            'options' => $this->options
        ]);

        if (Manager::obj()->isAdminZone() && empty($view_options['force_client_view'])) {
            $template = $this->getRenderTemplate();
        } else {
            $template = $this->getFrontTemplate();
        }

        return $sm -> fetch($template);
    }


    /**
     * Устанавливает шаблон, который будет использоваться для отображения формы в клиентской части сайта
     *
     * @param string $template
     * @return self
     */
    public function setFrontTemplate(string $template)
    {
        $this->front_template = $template;
        return $this;
    }

    /**
     * Возвращает шаблон, который будет использоваться для отображения формы в клиентской части сайта
     *
     * @return string
     */
    public function getFrontTemplate()
    {
        return $this->front_template;
    }

    /**
     * Устанавливает URL для загрузки файлов в клиентской части
     *
     * @param string $url
     * @return self
     */
    public function setFrontUploadUrl(string $url)
    {
        $this->upload_url = $url;
        return $this;
    }

    /**
     * Возвращает URL для загрузки файлов в клиентской части
     *
     * @return string
     * @throws Exception
     */
    public function getFrontUploadUrl()
    {
        if ($this->upload_url) {
            return $this->upload_url;
        }

        $router = Manager::obj();
        return $router->getUrl('photo-front-upload', ['linkType' => $this->getLinkType(), 'Act' => 'upload']);
    }

    /**
     * Устанавливает URL для удаления файлов
     *
     * @param string $url
     * @return Photos
     */
    public function setFrontRemoveUrl(string $url)
    {
        $this->remove_url = $url;
        return $this;
    }

    /**
     * Возвращает базовый URL для удаления файлов
     *
     * @return string
     * @throws Exception
     */
    public function getFrontRemoveUrl()
    {
        if ($this->remove_url) {
            return $this->remove_url;
        }

        $router = Manager::obj();
        return $router->getUrl('photo-front-upload', ['linkType' => $this->getLinkType(), 'Act' => 'remove']);
    }

    /**
     * Возвращает еще не привязанные к объекту файлы,
     * в случае когда объект еще не сохранен
     *
     * @return array
     */
    public function getDraftFiles()
    {
        $uniq_list = $this->get();
        if (is_array($uniq_list) && $uniq_list) {
            return Request::make()
                ->from(new Image())
                ->whereIn('uniq', $uniq_list)
                ->objects();
        } else {
            return [];
        }
    }

    /**
     * Возвращает список разрешённых расширений для загрузки.
     *
     * @param string $separator Если пустое значение, то возвращает массив, иначе - строку
     * @return array|string
     */
    public function getAcceptExtensions($separator = '')
    {
        $ext = ['.jpg', '.jpeg', '.png', '.gif'];
        return $separator ? implode($separator, $ext) : $ext;
    }

    /**
     * Возвращает дополнительные параметры для JS-плагина DropZone
     *
     * @param $json
     * @return false|string
     */
    public function getDropZoneOptions($json = false)
    {
        return $json ? json_encode($this->dropzone_options, JSON_UNESCAPED_UNICODE) : $this->dropzone_options;
    }

    /**
     * Устанавливает дополнительные параметры для JS-плагина DropZone
     *
     * @param array $options
     * @return self
     */
    public function setDropZoneOptions(array $options)
    {
        $this->dropzone_options = $options;
        return $this;
    }
}