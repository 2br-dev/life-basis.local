<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Links\Type;

use Feedback\Model\Orm\ResultItem;
use Feedback\Model\ormtype\SelectFeedback;
use Feedback\Model\ResultApi;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Router\Manager;

/**
 * Связь объекта с результатом формы обратной связи
 */
class LinkTypeFeedback extends AbstractType
{
    public $linked_object_id;
    /**
     * @var ResultItem
     */
    public $linked_object;
    protected $last_objects_template = '%crm%/admin/links/lastobjects/feedback.tpl';

    /**
     * Возвращает имя закладки, характеризующей данную связь
     *
     * @return string
     */
    public function getTabName()
    {
        return t('Форма обратной связи');
    }

    /**
     * Возвращает объект формы, который следует отобразить для указания параметров связывания
     *
     * @return FormObject
     */
    public function getTabForm()
    {
        $form = new FormObject(new PropertyIterator([
            'result_item_id' => new SelectFeedback([
                'description' => t('Поиск по результатам формы обратной связи'),
                'crossMultisite' => true,
                'checker' => ['ChkEmpty', t('Результатам формы обратной связи не выбран')],
                'attr' => [[
                    'placeholder' => t('id, название')
                ]]
            ])
        ]));

        $form->setFormTemplate($this->getId());

        return $form;
    }


    /**
     * Возвращает ID связываемого объекта, опираясь на данные заполненного объекта формы
     *
     * @param FormObject $tab_form
     * @return integer
     */
    public function getLinkIdByTabFormObject($tab_form)
    {
        return $tab_form['result_item_id'];
    }

    /**
     * Инициализирует связь объекта с одним конкретным заказом
     * После данного метода можно вызывать методы визуализации
     *
     * @param $source_object
     * @param $linked_object_id
     */
    public function init($linked_object_id)
    {
        $this->linked_object_id = $linked_object_id;
        $this->linked_object = new ResultItem($this->linked_object_id);
    }

    /**
     * Возвращает текст, который нужно отобразить при визуализации связи
     *
     * @return mixed
     */
    public function getLinkText()
    {
        if ($this->linked_object['id']) {
            return t('%title от %date', [
                'title' => $this->linked_object['title'],
                'date' => date('d.m.Y', strtotime($this->linked_object['dateof']))
            ]);
        } else {
            return t('Результат формы обратной связи не найден (ID: %id)', [
                'id' => $this->linked_object_id
            ]);
        }
    }

    /**
     * Возвращает true, если объект находится на другом сайте
     *
     * @return bool
     */
    public function isObjectOtherSite()
    {
        $current_site_id = \RS\Site\Manager::getSiteId();
        return $this->linked_object['id'] && ($this->linked_object['site_id'] != $current_site_id);
    }

    /**
     * Возвращает ссылку, которую нужно установить к тексту, при визуализации связи
     *
     * @return mixed
     */
    public function getLinkUrl()
    {
        if ($this->linked_object['id']) {
            $url = Manager::obj()->getAdminUrl('edit', ['id' => $this->linked_object->id], 'feedback-resultctrl');
            return $url;
        }
    }

    /**
     * Возвращает последние $limit объектов, с которыми возможно установить связь
     *
     * @param integer $limit
     * @return []
     */
    public function getLastObjects($limit = null)
    {
        if (!$limit) {
            $limit = 10;
        }
        $api = new ResultApi();
        return $api->getList(1, $limit, 'id DESC');
    }
}