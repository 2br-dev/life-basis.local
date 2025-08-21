<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Statistic\Config;
use \RS\Orm\Type;

/**
* Патчи к модулю
*/
class Patches extends \RS\Module\AbstractPatches
{
    /**
    * Возвращает массив имен патчей.
    */
    function init()
    {
        return [
            '304',
        ];
    }

    /**
     * Добавляем для пользователей отсутвующее поле
     */
    function afterUpdate304()
    {
        //Обновим пользователя
        $user = new \Users\Model\Orm\User();
        $user->getPropertyIterator()->append([
            'source_id' => new Type\Integer([
                'description' => t('Источник перехода'),
                'template' => '%statistic%/form/user/source_id.tpl',
                'default' => "0"
            ]),
            'date_arrive' => new Type\Datetime([
                'description' => t('Дата первого посещения'),
            ])
        ]);
        $user->dbUpdate();

        //Обновим заказ
        $order = new \Shop\Model\Orm\Order();
        $order->getPropertyIterator()->append([
            'source_id' => new Type\Integer([
                'description' => t('Источник перехода пользователя'),
                'infoVisible' => true,
                'default' => 0,
                'template' => '%statistic%/form/source/source_id.tpl'
            ]),
            //Параметры UTM меток
            'utm_source' => new Type\Varchar([
                'description' => t('Рекламная система UTM_SOURCE'),
                'maxLength' => 50,
            ]),
            'utm_medium' => new Type\Varchar([
                'description' => t('Тип трафика UTM_MEDIUM'),
                'maxLength' => 50,
            ]),
            'utm_campaign' => new Type\Varchar([
                'description' => t('Рекламная кампания UTM_COMPAING'),
                'maxLength' => 50,
            ]),
            'utm_term' => new Type\Varchar([
                'description' => t('Ключевое слово UTM_TERM'),
                'maxLength' => 50,
            ]),
            'utm_content' => new Type\Varchar([
                'description' => t('Различия UTM_CONTENT'),
                'maxLength' => 50,
            ]),
            'utm_dateof' => new Type\Date([
                'description' => t('Дата события'),
            ])
        ]);
        $order->dbUpdate();

        //Обновим заказать
        $reserve = new \Shop\Model\Orm\Reservation();
        $reserve->getPropertyIterator()->append([
            'source_id' => new Type\Integer([
                'description' => t('Источники прихода'),
                'default' => 0,
                'template' => '%statistic%/form/source/source_id.tpl'
            ]),
            //Параметры UTM меток
            'utm_source' => new Type\Varchar([
                'description' => t('Рекламная система UTM_SOURCE'),
                'maxLength' => 50,
            ]),
            'utm_medium' => new Type\Varchar([
                'description' => t('Тип трафика UTM_MEDIUM'),
                'maxLength' => 50,
            ]),
            'utm_campaign' => new Type\Varchar([
                'description' => t('Рекламная кампания UTM_COMPAING'),
                'maxLength' => 50,
            ]),
            'utm_term' => new Type\Varchar([
                'description' => t('Ключевое слово UTM_TERM'),
                'maxLength' => 50,
            ]),
            'utm_content' => new Type\Varchar([
                'description' => t('Различия UTM_CONTENT'),
                'maxLength' => 50,
            ]),
            'utm_dateof' => new Type\Date([
                'description' => t('Дата события'),
            ]),
        ]);
        $reserve->dbUpdate();

        //Обновим купить в один клик
        $click = new \Catalog\Model\Orm\OneClickItem();
        $click->getPropertyIterator()->append([
            'source_id' => new Type\Integer([
                'description' => t('Источник перехода пользователя'),
                'default' => 0,
                'template' => '%statistic%/form/source/source_id.tpl'
            ]),
            //Параметры UTM меток
            'utm_source' => new Type\Varchar([
                'description' => t('Рекламная система UTM_SOURCE'),
                'maxLength' => 50,
            ]),
            'utm_medium' => new Type\Varchar([
                'description' => t('Тип трафика UTM_MEDIUM'),
                'maxLength' => 50,
            ]),
            'utm_campaign' => new Type\Varchar([
                'description' => t('Рекламная кампания UTM_COMPAING'),
                'maxLength' => 50,
            ]),
            'utm_term' => new Type\Varchar([
                'description' => t('Ключевое слово UTM_TERM'),
                'maxLength' => 50,
            ]),
            'utm_content' => new Type\Varchar([
                'description' => t('Различия UTM_CONTENT'),
                'maxLength' => 50,
            ]),
            'utm_dateof' => new Type\Date([
                'description' => t('Дата события'),
            ]),
        ]);
        $click->dbUpdate();

        //Проимпортируем типы источников по умолчанию
        $install = new \Statistic\Config\Install();
        $install->insertDemoData();
    }
}