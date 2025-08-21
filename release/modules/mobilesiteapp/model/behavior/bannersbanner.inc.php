<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Model\Behavior;

use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Product;
use RS\Behavior\BehaviorAbstract;

/**
 * Расширяет объект баннера
 */
class BannersBanner extends BehaviorAbstract
{
    /**
     * Возвращает название связанной категории с баннером в мобильном приложении
     *
     * @return string
     */
    function getMobileCategoryTitle()
    {
        if ($this->owner->mobile_category_id) {
            $category = new Dir($this->owner->mobile_category_id);
            return $category['name'];
        }
        return '';
    }

    /**
     * Возвращает название связанного товара с баннером в мобильном приложении
     *
     * @return string
     */
    function getMobileProductTitle()
    {
        if ($this->owner->mobile_product_id) {
            $product = new Product($this->owner->mobile_product_id);
            return $product['title'];
        }
        return '';
    }
}