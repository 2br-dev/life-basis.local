<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\CommentType;

use Catalog\Controller\Front\Product as ControllerProduct;

/**
* Тип комментария - комментарий к товару
*/
class Product extends \Comments\Model\Abstracttype
{
    protected $product;

    /**
    * Возвращает тип комментария
    */
    function getTitle()
    {
        return t('Комментарий к товарам');
    }
    
    /**
    * Возвращает id товара, к которому необходимо привязать комментарий
    * 
    * @return integer
    */
    function getLinkId()
    {
        if ($id = parent::getLinkId()) {
            return $id;
        }

        if ($this->comment) {
            return $this->comment['aid'];
        }

        $route = \RS\Router\Manager::obj()->getCurrentRoute();
        if ($route->getId() == 'catalog-front-product') {
            if ($product = $route->getExtra(ControllerProduct::ROUTE_EXTRA_PRODUCT)) {
                return $product['id'];
            }
        }
        return false;
    }
    
    /**
    * Возвращает ссылку на объект в административной части
    * 
    * @return string
    */
    function getAdminUrl($absolute = false)
    {
        return \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->comment['aid']], 'catalog-ctrl', $absolute);
    }
    
    /**
    * Возвращает связанный с комментарием товар
    * 
    * @return \Catalog\Model\Orm\Product
    */
    function getLinkedObject()
    {
        if (!isset($this->product)) {
            $this->product = new \Catalog\Model\Orm\Product($this->getLinkId());
        }
        return $this->product;
    }
    
    /**
    * Возвращает ссылку в клиентской части на связанный с комментарием товар
    * 
    * @param bool $absolute Если true, то возвращать абсолютную ссылку, иначе относительную
    * @return string
    */
    function getLinkedObjectUrl($absolute = false)
    {
        return $this->getLinkedObject()->getUrl($absolute);
    }
    
    /**
    * Возвращает название связанного товара 
    * 
    * @return string
    */
    function getLinkedObjectTitle()
    {
        return $this->getLinkedObject()->title;
    }
    
    /**
    * Вызывается при добавлении комментария
    * Обновляет поле "рейтинг" у товара
    * 
    * @return bool
    */
    function onAdd()
    {
        $api = new \Comments\Model\Api(); 
        $api->recountItemRatingByComment($this->getLinkedObject(), $this->comment);
        return true;
    }
    
    /**
    * Действие при удалении комментария
    * 
    * @return bool
    */
    function onDelete()
    {
        $api = new \Comments\Model\Api(); 
        $api->recountItemRatingByComment($this->getLinkedObject(), $this->comment);
    }
}

