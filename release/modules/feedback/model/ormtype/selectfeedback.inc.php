<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Feedback\Model\ormtype;

use Feedback\Model\Orm\ResultItem;
use RS\Orm\Type\User;

/**
 * Поле - поиск ID резултата формы обратной связи по строке
 */
class SelectFeedback extends User
{
    protected
        $cross_multisite = false; //Искать на всех мультисайтах

    function __construct(array $options = null)
    {
        $this->view_attr = ['size' => 40, 'placeholder' => t('id, название')];
        parent::__construct($options);
    }

    /**
     * Устанавливает, нужно ли выбирать заказы без учета мультисайтовости
     *
     * @param $bool
     */
    function setCrossMultisite($bool)
    {
        $this->cross_multisite = $bool;
    }

    /**
     * Возвращает, нужно ли выбирать заказы
     *
     * @return bool
     */
    function isCrossMultisite()
    {
        return $this->cross_multisite;
    }

    /**
     * Возвращает выбранный объект
     *
     * @return ResultItem
     */
    function getSelectedObject()
    {
        $deal_id = ($this->get()>0) ? $this->get() : null;
        if ($deal_id>0) {
            if (!isset(self::$cache[$deal_id])) {
                $deal = new ResultItem($deal_id);
                self::$cache[$deal_id] = $deal;
            }
            return self::$cache[$deal_id];
        }
        return new ResultItem();
    }

    /**
     * Возвращает URL, который будет возвращать результат поиска
     *
     * @return string
     */
    function getRequestUrl()
    {
        return $this->request_url ?: \RS\Router\Manager::obj()->getAdminUrl('ajaxSearchResultItem', [
            'cross_multisite' => (int)$this->isCrossMultisite()
        ], 'feedback-tools');
    }

    /**
     * Возвращает наименование найденного объекта
     *
     * @return string
     */
    function getPublicTitle()
    {
        $result_item = $this->getSelectedObject();

        return t('%title от %date', [
            'title' => $result_item['title'],
            'date' => date('d.m.Y', strtotime($result_item['dateof']))
        ]);
    }

    /**
     * Возвращает класс иконки zmdi
     *
     * @return string
     */
    function getIconClass()
    {
        return 'assignment';
    }
}