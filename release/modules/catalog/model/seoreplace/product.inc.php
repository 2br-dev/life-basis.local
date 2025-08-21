<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\SeoReplace;
  
use Affiliate\Model\AffiliateApi;
use Partnership\Model\Api;
use Partnership\Model\Orm\Partner;
use RS\Config\Loader;
use RS\Module\Manager;

/**
* Класс сео генератора для товара
*/
class Product extends \RS\Helper\AbstractSeoGen
{
    public
        $hint_fields   = [    //Массив имён свойств которым будет обновлена подсказка (hint)
            'meta_title',
            'meta_keywords',
            'meta_description',
    ];
        
      
    /**
    * Конструктор класса
    * 
    * @param array $real_replace - массив автозамены, 
    * в котором ключи которые совпадают с внутренним 
    * массивом struct будут заменены.
    * использоватся будет массив struct описанный внутри 
    * этой функции
    * 
    * @return \Catalog\Model\SeoReplace\Product
    */
    function __construct(array $real_replace = [])
    {
         
        $this->struct = [                        //Структурный массив автозамены
            new \Catalog\Model\Orm\Product(),
            'cat_'  => new \Catalog\Model\Orm\Dir(),
            'brand_' => new \Catalog\Model\Orm\Brand(),
            'price' => t('Цена товара (по умолчанию)'),
        ];
        
        $this->include_array  = [ //Массив с ключами включения, какие элементы в массиве автозамены участвуют
            'id',
            'title',
            'short_description',
            'description',
            'barcode',
            'cat_name',
            'cat_itemcount',
            'brand_title'
        ];
        
        // Если есть партнёрский модуль
        if (Manager::staticModuleEnabled('partnership')) {
            $partner = Api::getCurrentPartner();
            if (!$partner) {
                $partner_config = Loader::byModule('partnership');
                $partner = new Partner();
                $partner['title'] = $partner_config['main_title'];
            }
            
            $this->struct['partner_'] = new Partner();
            $this->include_array[] = 'partner_title';
            $real_replace['partner_'] = $partner;
        }

        // Если есть модуль филиальная сеть
        if (Manager::staticModuleEnabled('affiliate')) {
            $affiliate = AffiliateApi::getCurrentAffiliate();
            if ($affiliate['id']) {
                $real_replace['affiliate_'] = $affiliate;

                foreach($affiliate['variables'] ?: [] as $data) {
                    $real_replace['affiliate_var_'.$data['name']] = $data['value'];
                }
            }

            $this->struct['affiliate_'] = $affiliate;
            $this->include_array[] = 'affiliate_title';

            $this->struct['affiliate_var_ваша переменная'] = t('Значение заданной у филиала переменной');
        }
        
        parent::__construct($real_replace);
    } 
     
     
    /**
    * Функция срабатывает перед заменой текста
    * Заменяет характеристики, если таковые присутствуют 
    * 
    * @param string $text        - Текст в котором будет произведена замена
    * @param array $real_struct  - массив со структурой значений, для замены
    */
    function beforeReplace($text,$real_struct)
    {
        /**
        * @var \Catalog\Model\Orm\Product
        */
        $product = $real_struct[0];
        
        //Если нашли синтаксис характеристик, то заменим их
        $text = preg_replace_callback('/\{prop.(\d+?)}/si', function($match) use ($product) {
            /**
            * @var \Catalog\Model\Orm\Product
            */
            $property = $product->getPropertyValueById($match[1]);
            return strip_tags($property);
            
        },$text);
        
        return $text;
    }
}
