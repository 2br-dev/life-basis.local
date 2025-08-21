<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm;
use Shop\Config\ModuleRights;
use Users\Model\Api as UserApi;
use \RS\Orm\Type;
use Users\Model\Orm\User;

/**
 * Предварительный заказ товара
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $product_id ID товара
 * @property string $product_barcode Артикул товара
 * @property string $product_title Название товара
 * @property string $offer Название комплектации товара
 * @property integer $offer_id Комплектация товара
 * @property string $currency Валюта на момент оформления заявки
 * @property string $multioffer Многомерная комплектация товара
 * @property array $multioffers 
 * @property float $amount Количество
 * @property string $phone Телефон пользователя
 * @property string $email E-mail пользователя
 * @property string $is_notify Уведомлять о поступлении на склад
 * @property string $dateof Дата заказа
 * @property integer $user_id ID пользователя
 * @property string $status Статус
 * @property string $comment Комментарий администратора
 * @property integer $affiliate_id Выбранный филиал на момент оформления предварительного заказа
 * --\--
 */
class Reservation extends \RS\Orm\OrmObject
{
    const STATUS_OPEN = 'open';
    const STATUS_CLOSE = 'close';

    const STATUS_COLOR_OPEN = '#ffa545';
    const STATUS_COLOR_CLOSE = '#71b903';
    
    protected static
        $table = 'product_reservation';
        
    protected
        $product;
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'product_id' => new Type\Integer([
                'description' => t('ID товара'),
                'checker' => ['chkEmpty', t('Не задан товар')],
                'meVisible' => false
            ]),
            'product_barcode' => new Type\Varchar([
                'description' => t('Артикул товара'),
                'meVisible' => false
            ]),
            'product_title' => new Type\Varchar([
                'description' => t('Название товара'),
                'meVisible' => false
            ]),
            'offer' => new Type\Varchar([
                'description' => t('Название комплектации товара'), 
                'visible' => false
            ]),
            'offer_id' => new Type\Integer([
                'description' => t('Комплектация товара'), 
                'Template' => 'form/reservationfield/offer_id.tpl',
                'meVisible' => false
            ]),
            'currency' => new Type\Varchar([
                'description' => t('Валюта на момент оформления заявки')
            ]),
            'multioffer' => new Type\Varchar([
                'description' => t('Многомерная комплектация товара'),
                'Template' => 'form/reservationfield/multioffer.tpl',
                'meVisible' => false,
            ]),
            'multioffers' => new Type\ArrayList([
                'descirption' => t('Многомерная комплектация товара - массив'),
                'visible' => false
            ]),
            'amount' => new Type\Decimal([
                'description' => t('Количество'),
                'maxLength' => 11,
                'decimal' => 3,
                'meVisible' => false
            ]),
            'phone' => new Type\Varchar([
                'maxLength' => 50,
                'description' => t('Телефон пользователя')
            ]),
            'email' => new Type\Varchar([
                'description' => t('E-mail пользователя'),
                'checker' => [[__CLASS__, 'checkContacts'], t('Укажите телефон или E-mail')]
            ]),
            'is_notify' => new Type\Enum(['0', '1'], [
                'allowempty' => false,
                'default' => '0',
                'description' => t('Уведомлять о поступлении на склад'),
                'listFromArray' => [[
                    '1' => t('Уведомлять'),
                    '0' => t('Не уведомлять')
                ]]
            ]),
            'dateof' => new Type\Datetime([
                'description' => t('Дата заказа')
            ]),
            'user_id' => new Type\User([
                'description' => t('ID пользователя'),
                'meVisible' => false
            ]),
            'kaptcha' => new Type\Captcha([
                'description' => t('Защитный код'),
                'enable' => false,
                'context' => '',
            ]),
            'status' => new Type\Enum(array_keys(self::getStatusTitles()), [
                'allowempty' => false,
                'description' => t('Статус'),
                'list' => [[__CLASS__, 'getStatusTitles']]
            ]),
            'comment' => new Type\Text([
                'description' => t('Комментарий администратора')
            ]),
            'affiliate_id' => new Type\Integer([
                'description' => t('Выбранный филиал на момент оформления предварительного заказа'),
                'hint' => t('В случае, если будет включена опция в настройках модуля Каталог "Ограничить остатки товара остатками связанных с филиалом складов", то уведомление о поступление товара будет приходить только, если товар поступил именно на связанные с филиалом склады.'),
                'visible' => false
            ])
        ]);
    }    
    
    public static function checkContacts($_this, $value, $error_text)
    {
        if ($_this['phone'] || $_this['email']) return true;
        return $error_text;
    }

    /**
     * Действия перед записью в БД
     *
     * @param string $flag - insert или update
     * @return void
     */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['dateof'] = date('c');
            $this['user_id'] = \RS\Application\Auth::getCurrentUser()->id;
        }
        
        $this['multioffer'] = serialize($this['multioffers']);
        $this['product_title'] = $this->getProduct()->title;

        $this['phone'] = UserApi::normalizePhoneNumber($this['phone']);
    }

    /**
     * Действия после записи в БД
     *
     * @param string $flag - insert или update
     * @return void
     */
    function afterWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $notice = new \Shop\Model\Notice\Reservation;
            $notice->init($this);
            \Alerts\Model\Manager::send($notice); 
        }        
    }
    
    function afterObjectLoad()
    {
        $this['multioffers'] = @unserialize($this['multioffer']);
        
        // Приведение типов
        $this['amount'] = (float)$this['amount'];
    }

    /**
     * Возвращает общую стоимость предварительного заказа
     *
     * @param null $cost_id ID типа цен
     * @return float
     */
    function getTotalCost($cost_id = null, $format = false, $in_base_currency = false)
    {
        $single_cost = $this->getProductSingleCost($cost_id, $format, $in_base_currency);
        return $single_cost * ($this['amount'] ?: 1);
    }

    /**
     * Возвращает стоимость одного товара
     *
     * @param integer|null $cost_id ID типа цен, null - цена по умолчанию
     * @param bool $format
     * @param bool $in_base_currency
     * @return float
     */
    function getProductSingleCost($cost_id = null, $format = false, $in_base_currency = false)
    {
        $product = $this->getProduct();
        return $product->getCost($cost_id, $this['offer_id'], $format, $in_base_currency);
    }

    /**
     * Возвращает связанный с данным заказом товар
     *
     * @return \Catalog\Model\Orm\Product
     */
    function getProduct()
    {
        if (empty($this->product)){
          $this->product =  new \Catalog\Model\Orm\Product($this['product_id']);
        }   
        return $this->product;
    }
    
    /**
    * Возвращает массив многомерных комплектаций
    *
    * @return array
    */
    function getArrayMultiOffer()
    {
       $arr = [];
       if (!empty($this['multioffers'])){
           $product = $this->getProduct();
           if ($product->isMultiOffersUse()) {
               foreach($product['multioffers']['levels'] as $level) {
                   if (isset($this['multioffers'][$level['prop_id']])) {
                       $property_title = $level['title'] ?: $level['prop_title'];
                       $arr[$property_title] = $this['multioffers'][$level['prop_id']];
                   }
               }
           }
       }
       elseif ($this['offer_id'] > 0) {
           $offer = $this->getOffer();
           $arr = $offer['propsdata_arr'];
       }
       
       return $arr;
    }

    /**
     * Возвращает комплектацию товара
     *
     * @return \Catalog\Model\Orm\Offer
     */
    function getOffer()
    {
        $offer = new \Catalog\Model\Orm\Offer($this['offer_id']);
        return $offer;
    }

    /**
     * Возвращает объект пользователя, для которого оформлен предзаказ
     *
     * @return User
     */
    function getUser()
    {
        return new User($this['user_id']);
    }

    /**
     * Возвращает массив названий статусов
     *
     * @return array
     */
    public static function getStatusTitles()
    {
        return [
            self::STATUS_OPEN => t('Открыт'),
            self::STATUS_CLOSE => t('Закрыт')
        ];
    }

    /**
     * Возвращает название статуса текущего предзаказа
     *
     * @return string
     */
    public function getStatusTitle()
    {
        return self::getStatusTitles()[$this['status']];
    }

    /**
     * Возращает идентификатор цвета текущего статуса
     *
     * @return string
     */
    public function getStatusColor()
    {
        switch($this['status']) {
            case self::STATUS_OPEN: return self::STATUS_COLOR_OPEN;
            case self::STATUS_CLOSE: return self::STATUS_COLOR_CLOSE;
        }
    }

    /**
     * Возвращает идентификатор права на удаление для данного объекта
     *
     * @return string
     */
    public function getRightDelete()
    {
        return ModuleRights::RIGHT_RESERVATION_DELETE;
    }

    /**
     * Возвращает идентификатор права на изменение для данного объекта
     *
     * @return string
     */
    public function getRightUpdate()
    {
        return ModuleRights::RIGHT_RESERVATION_CHANGING;
    }

    /**
     * Возвращает true, если из этой покупки в 1 клик можно создать заказ
     *
     * @return bool
     */
    public function canCreateOrder()
    {
        return $this['status'] == self::STATUS_OPEN;
    }
}
