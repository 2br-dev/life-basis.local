<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Exchange\Config;

use Catalog\Model\Orm\Product;

/**
 * Класс отвечает за установку и обновление модуля
 */
class Install extends \RS\Module\AbstractInstall
{
    function update()
    {
        $result = parent::update();
        if ($result) {
            //Добавляем виджеты на рабочий стол
            $product = new Product();
            $product->dbUpdate();
        }
        return $result;
    }
}
