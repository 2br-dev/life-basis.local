<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Config;
use Shop\Model\Orm\Delivery;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Payment;
use RS\Orm\Type;

/**
 * Патчи к модулю
 */
class Patches extends \RS\Module\AbstractPatches
{
    /**
     * Возвращает массив патчей.
     */
    function init()
    {
        return [
            '3017',
            '6050',
        ];
    }

    /**
     * Добавляем отсутствующее поле
     */
    function beforeUpdate6050()
    {
        $delivery = new Delivery();
        $delivery->getPropertyIterator()->append([
            'mobilesiteapp_hide' => new Type\Integer([
                'description' => t('Скрыть в мобильном приложении?'),
                'checkboxView' => [1, 0],
                'default' => 0,
            ])
        ]);

        $payment = new Payment();
        $payment->getPropertyIterator()->append([
            'mobilesiteapp_hide' => new Type\Integer([
                'description' => t('Скрыть в мобильном приложении?'),
                'checkboxView' => [1, 0],
                'default' => 0,
            ])
        ]);

        $delivery->dbUpdate();
        $payment->dbUpdate();
    }

    /**
     * Сделаем цвет по умолчанию
     */
    function afterUpdate3017()
    {
        if (class_exists('Shop\Model\Orm\Order')) {
            \RS\Orm\Request::make()
                ->update(new Order())
                ->set([
                    'mobile_background_color' => '#E0E0E0'
                ])
                ->where([
                    'mobile_background_color' => '#fff'
                ])->exec();
        }
    }

}