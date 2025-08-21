<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\Orm\Delivery;

use RS\Orm\AbstractObject;
use RS\Orm\Type;

/**
 * ORM объект описывает один регион из справочника СДЭК.
 * Справочник регионов доставки СДЭК теперь находится в БД, он будет обновляться либо
 * по кнопке в настройках модуля Магазин, либо по крону
 * --/--
 * @property integer $code Код населенного пункта СДЭК
 * @property integer $region_code Код региона СДЭК
 * @property string $city Название населенного пункта
 * @property string $fias_guid Уникальный идентификатор ФИАС населенного пункта
 * @property string $kladr_code Код КЛАДР населенного пункта
 * @property string $country Название страны населенного пункта
 * @property string $country_code Идентификатор страны
 * @property string $region Название региона населенного пункта
 * @property string $sub_region Название района региона населенного пункта
 * @property integer $processed Флаг "обработан"
 * --\--
 */
class CdekRegion extends AbstractObject
{
    protected static $table = 'delivery_cdek_regions';

    function _init()
    {
        $this->getPropertyIterator()->append([
            'code' => (new Type\Integer())
                ->setDescription(t('Код населенного пункта СДЭК')),
            'region_code' => (new Type\Integer())
                ->setDescription(t('Код региона СДЭК')),
            'city' => (new Type\Varchar())
                ->setDescription(t('Название населенного пункта'))
                ->setMaxLength(100),
            'fias_guid' => (new Type\Varchar())
                ->setDescription(t('Уникальный идентификатор ФИАС населенного пункта'))
                ->setMaxLength(36),
            'kladr_code' => (new Type\Varchar())
                ->setDescription(t('Код КЛАДР населенного пункта')),
            'country' => (new Type\Varchar())
                ->setDescription(t('Название страны населенного пункта'))
                ->setMaxLength(50),
            'country_code' => (new Type\Varchar())
                ->setDescription(t('Идентификатор страны'))
                ->setIndex(true)
                ->setMaxLength(3),
            'region' => (new Type\Varchar())
                ->setDescription(t('Название региона населенного пункта'))
                ->setMaxLength(50),
            'sub_region' => (new Type\Varchar())
                ->setDescription(t('Название района региона населенного пункта'))
                ->setMaxLength(50),
            'processed' => (new Type\Integer())
                ->setDescription(t('Флаг "обработан"'))
                ->setVisible(false),
        ]);

        $this->addIndex(['code'], self::INDEX_KEY);
        $this->addIndex(['country', 'region', 'sub_region', 'city'], self::INDEX_UNIQUE);
        $this->addIndex(['kladr_code'], self::INDEX_KEY);
    }
}
