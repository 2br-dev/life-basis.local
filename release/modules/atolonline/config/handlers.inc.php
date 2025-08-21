<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace AtolOnline\Config;
use \RS\Orm\Type as OrmType;

/**
* Класс предназначен для объявления событий, которые будет прослушивать данный модуль и обработчиков этих событий.
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    function init()
    {
        $this->bind('cashregister.gettypes')
             ->bind('orm.init.catalog-unit');
    }

    public static function ormInitCatalogProduct($product)
    {}

    /**
     * Обработчик события "Инициализация ORM объекта Товар".
     * Не забудьте переустановить модуль каталог через меню Веб-сайт->Настройка модулей. Каталог товаров -> переустановить
     *
     * @param \Catalog\Model\Orm\Unit
     * @return void
     */
    public static function ormInitCatalogUnit(\Catalog\Model\Orm\Unit $orm)
    {
        $orm->getPropertyIterator()->append(array(
            'measure_value' => new \RS\Orm\Type\Varchar(array(
                'description' => 'Единицы измерения количества предмета расчета',
                'hint' => 'Будет использоваться для интеграции с АТОЛ начиная с версии 1.2',
                'listFromArray' => [[
                    0 => "Поштучно или единицами",
                    10 => "Грамм",
                    11 => "Килограмм",
                    12 => "Тонна",
                    20 => "Сантиметр",
                    21 => "Дециметр",
                    22 => "Метр",
                    30 => "Квадратный сантиметр",
                    31 => "Квадратный дециметр",
                    32 => "Квадратный метр",
                    40 => "Миллилитр",
                    41 => "Литр",
                    42 => "Кубический метр",
                    50 => "Киловатт час",
                    51 => "Гигакалория",
                    70 => "Сутки (день)",
                    71 => "Час",
                    72 => "Минута",
                    73 => "Секунда",
                    80 => "Килобайт",
                    81 => "Мегабайт",
                    82 => "Гигабайт",
                    83 => "Терабайт",
                    255 => "Иная единица измерения"
                ]]
            ))
        ));
    }

    /**
     * Возвращает процессоры(типы) доставки, присутствующие в текущем модуле
     *
     * @param array $list - массив из передаваемых классов доставок
     * @return array
     */
    public static function cashRegisterGetTypes($list)
    {
        $list[] = new \AtolOnline\Model\CashRegisterType\AtolOnline();
        return $list;
    }
    
}