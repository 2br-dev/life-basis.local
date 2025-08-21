<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Banners\Model\Orm;
use RS\Event\Manager;
use \RS\Orm\Type;
use RS\Site\Manager as SiteManager;

/**
 * Класс ORM Объектов, описывающих зону для баннеров
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название
 * @property string $alias Симв. идентификатор
 * @property integer $width Ширина области, px
 * @property integer $height Высота области, px
 * --\--
 */
class Zone extends \RS\Orm\OrmObject
{
    protected static
        $table = 'banner_zone';
    
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'title' => new Type\Varchar([
                'description' => t('Название')
            ]),
            'alias' => new Type\Varchar([
                'description' => t('Симв. идентификатор'),
                'uniq' => true
            ]),
            'width' => new Type\Integer([
                'description' => t('Ширина области, px')
            ]),
            'height' => new Type\Integer([
                'description' => t('Высота области, px')
            ]),
            'banners' => new Type\MixedType([
                'description' => t('Баннеры текущей зоны'),
                'visible' => false,
            ]),
        ]);
    }
    
    /**
    * Возвращает все баннеры, связанные с текущей зоной
    * 
    * @return Banner[]|\RS\Orm\AbstractObject[]
    */
    function getBanners()
    {
        if ($this['banners'] == null){
            $banners = \RS\Orm\Request::make()
                ->select('B.*')
                ->from(new Banner(), 'B')
                ->join(new Xzone(), 'X.banner_id = B.id', 'X')
                ->where("X.zone_id = '#zone_id'", ['zone_id' => $this['id']])
                ->where([
                    'public' => 1,
                    'site_id' => SiteManager::getSiteId(),
                ])
                ->orderby('weight desc')
                ->objects();

            //Отфильтруем по дате, если расписание включено
            if (!empty($banners)){
                foreach ($banners as $k=>$banner){
                    $cur_time = time();
                    if ($banner['use_schedule'] && ($cur_time < strtotime($banner['date_start']) || ($cur_time > strtotime($banner['date_end'])))){
                        unset($banners[$k]);
                    }
                }
            }

            $this['banners'] = $banners;
        }

        Manager::fire('zone.getBanners', [
            'zone' => $this
        ]);

        return $this['banners'] ;
    }
    
    /**
    * Возвращает баннер для текущей зоны с учетом всех параметров ротации
    * 
    * @return Banner
    */
    function getOneBanner()
    {
        $banners = $this->getBanners();
        $banner_index = 0;
        $max_weight = 0;
        foreach($banners as $n => $banner) {
            $weight_value = $banner['weight'] * rand(0, 1000);
            if ($max_weight < $weight_value) {
                $max_weight = $weight_value;
                $banner_index = $n;
            }
        }
        
        $banner = isset($banners[$banner_index]) ? $banners[$banner_index] : new Banner();

        $event_result = Manager::fire('zone.getOneBanner', [
            'zone' => $this,
            'banner' => $banner
        ]);

        return $event_result->getResult()['banner'];
    }
}