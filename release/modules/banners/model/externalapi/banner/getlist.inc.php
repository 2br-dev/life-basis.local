<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Banners\Model\ExternalApi\Banner;

use Banners\Model\ApiUtils;
use Banners\Model\Orm\Banner;
use Banners\Model\Orm\Zone;
use Banners\Model\ZoneApi;
use ExternalApi\Model\AbstractMethods\AbstractFilteredList;
use ExternalApi\Model\Utils;
use RS\Orm\Type;

/**
* Возвращает список баннеров для определённой зоны
*/
class GetList extends AbstractFilteredList
{
    const
        RIGHT_LOAD = 1;
    
    protected
        $token_require = false,
        $list;
    
    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Загрузка списка объектов')
        ];
    }
    
    /**
    * Возвращает объект, который позволит производить выборку товаров
    * 
    * @return ZoneApi
    */
    public function getDaoObject()
    {
        if ($this->dao === null) {
            $this->dao = new ZoneApi();
        }
        return $this->dao;
    }

    
    /**
    * Добавляет секцию с изображениями к баннерам
    *
    * @return void
    */
    protected function addImageData()
    {
        //Загружаем изображения
        if (!empty($this->list)) {
            foreach ($this->list as $index=>$orm_object){
                /**
                 * @var Banner $orm_object
                 */
                if (!empty($orm_object['file'])){
                    $orm_object['file'] = ApiUtils::prepareImagesSection($orm_object);
                    $this->list[$index] = $orm_object;
                }
            }
        }
    }


    
    /**
    * Возвращает список товаров
    * 
    * @param string $token Авторизационный токен
    * @param string $zone Полнотекстовый поиск по товарам. Использует стандартные настройки поиска в системе
    * 
    * @example GET /api/methods/banner.getlist?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de4868&zone=main
    * Ответ:
    * <pre>
    * {
    *     "response": {
    *         "list": [
    *             {
    *              "id": "9",
    *              "title": "IPhone",
    *              "use_original_file": "0",
    *              "info": "Ноутбуки с оптимальным соотношением цены и возможностей. ...",
    *              "link": "",
    *              "targetblank": "0",
    *              "use_original_file" : "0",
    *              "public": "1",
    *              "xzone": NULL,
    *              "weight": "100",
    *              "mobile_banner_type": "Menu",
    *              "mobile_link": "",
    *              "mobile_menu_id": "1",
    *              "mobile_product_id": "0",
    *              "mobile_category_id": "0",
    *              "file": [ //Файл с баннером, может быть null
    *                   {
    *                     "original_url": "http://full.readyscript.local/storage/photo/original/a/46s7ye2cobjx5j6.jpg",
    *                      "big_url": "http://full.readyscript.local/storage/photo/resized/xy_1000x1000/a/46s7ye2cobjx5j6_ded27759.jpg",
    *                      "small_url": "http://full.readyscript.local/storage/photo/resized/xy_300x300/a/46s7ye2cobjx5j6_7aa365e2.jpg"
    *                  }
    *              ]
    *             }
    *             , ...
    *         ],
    *     }
    * }
    * </pre>
    * 
    * @return array Возвращает список объектов и связанные с ним сведения.
    */
    protected function process($token = null, $zone = '')
    {
        $api = new ZoneApi();

        /**
         * @var Zone $banner_zone
         */
        $banner_zone = $api->getById($zone);
        if (!$banner_zone){ //Если зона не найдена
            $banner_zone = new Zone();
        }
        //Укажим какие дополнительные поля показывать
        $banner = new Banner();
        $banner->getPropertyIterator()->append([
            'mobile_banner_type' => new Type\Varchar([
                'description' => t('Тип баннера'),
                'appVisible' => true
            ]),
            'mobile_link' => new Type\Varchar([
                'description' => t('Страницы для показа пользователю'),
                'appVisible' => true
            ]),
            'mobile_menu_id' => new Type\Integer([
                'description' => t('Страницы для показа пользователю'),
                'appVisible' => true
            ]),
            'mobile_product_id' => new Type\Integer([
                'description' => t('Товар для показа пользователю'),
                'appVisible' => true
            ]),
            'mobile_category_id' => new Type\Integer([
                'description' => t('Категория для показа пользователю'),
                'appVisible' => true
            ])
        ]);

        $this->list = $banner_zone->getBanners();
        $this->addImageData();
        $result['response']['list'] = Utils::extractOrmList($this->list);

        return $result;
    }
}