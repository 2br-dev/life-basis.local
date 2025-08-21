<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Model\OrmType;

use Files\Model\FileApi;
use Files\Model\FilesType\AbstractType;
use Files\Model\Orm\File;
use RS\Orm\Request;
use RS\Orm\Type\UserTemplate;
use RS\Router\Manager;
use RS\View\Engine;

/**
 * Тип поля "файлы", должен располагаться на отдельной вкладке ORM объекта
 */
class Files extends UserTemplate
{
    protected $link_type;
    protected $front_template = '%files%/ormtype/files_front.tpl'; //Шаблон для клиентской части сайта
    protected $upload_url;
    protected $remove_url;
    protected $dropzone_options = [];

    /**
     * Конструктор класса
     * @param null|array $options - массив дополнительных параметров
     */
    public function __construct($options = null)
    {
        parent::__construct('%files%/ormtype/files.tpl', '', $options);
    }

    /**
     * Устанавливает тип связи файлов
     *
     * @param string $link_type Строковый идентификатор типа связи
     * @return self
     */
    public function setLinkType($link_type)
    {
        $this->link_type = $link_type;
        return $this;
    }

    /**
     * Возвращает тип связи файлов
     * @return string
     */
    public function getLinkType()
    {
        return $this->link_type;
    }

    /**
     * Возвращает объект связи
     *
     * @return AbstractType
     */
    public function getLinkObject()
    {
        return FileApi::getTypeClassInstance($this->link_type);
    }

    /**
     * Возвращает форму в отредеренном шаблоне
     *
     * @param null|array $view_options - массив дополнительных аттрибутов в форме
     * @param null|object $orm_object - объект, которому принадлежит поле
     * @return string
     */
    public function formView($view_options = null, $orm_object = null)
    {
        $sm = new Engine();
        $sm -> assign([
            'field' => $this,
            'view_options' => $view_options !== null ? array_combine($view_options, $view_options) : null
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
    public function setFrontTemplate($template)
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
    public function setFrontUploadUrl($url)
    {
        $this->upload_url = $url;
        return $this;
    }

    /**
     * Возвращает URL для загрузки файлов в клиентской части
     *
     * @return string
     */
    public function getFrontUploadUrl()
    {
        if ($this->upload_url) {
            return $this->upload_url;
        }

        $router = Manager::obj();
        return $router->getUrl('files-front-upload', ['linkType' => $this->getLinkType(), 'Act' => 'upload']);
    }

    /**
     * Устанвливает URL для удаления файлов
     *
     * @param string $url
     * @return Files
     */
    public function setFrontRemoveUrl($url)
    {
        $this->remove_url = $url;
        return $this;
    }

    /**
     * Возвращает базовый URL для удаления файлов
     *
     * @return string
     */
    public function getFrontRemoveUrl()
    {
        if ($this->remove_url) {
            return $this->remove_url;
        }

        $router = Manager::obj();
        return $router->getUrl('files-front-upload', ['linkType' => $this->getLinkType(), 'Act' => 'remove']);
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
                ->from(new File())
                ->whereIn('uniq', $uniq_list)
                ->objects();
        } else {
            return [];
        }
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