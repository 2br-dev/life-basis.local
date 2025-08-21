<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\PrintForm;

use RS\Exception;
use RS\Router\Manager as RouterManager;
use Shop\Model\Orm\Order;

/**
* Абстрактный класс печатной формы заказа.
*/
abstract class AbstractPrintForm
{
    protected
        $order;
    
    /**
    * Конструктор печатной формы
    * 
    * @param \Shop\Model\Orm\Order $order - заказ, который должен быть использован для формирования печатной формы
    * @return AbstractPrintForm
    */
    function __construct($order = null)
    {
        $this->setOrder($order);
    }

    /**
     * Устанавливает заказ, с которым будет работать печатная форма
     *
     * @param Order $order
     * @return void
     */
    public function setOrder($order = null)
    {
        $this->order = $order;
    }
    
    /**
    * Возвращает объект печатной формы по символьному идентификатору
    * 
    * @param mixed $id
    * @param \Shop\Model\Orm\Order $order
    * @return AbstractPrintForm | false
    */
    public static function getById($id, $order = null)
    {
        $all = self::getList();
        if (isset($all[$id])) {
            $item = $all[$id];
            $item->setOrder($order);
            return $item;
        }
        return false;
    }
    
    /**
    * Возвращает список всех печатных форм, имеющихся в системе
    * 
    * @return array
    */
    public static function getList()
    {
        $result = [];
        $event_result = \RS\Event\Manager::fire('printform.getlist', []);
        $list = (array)$event_result->getResult();
        foreach($list as $print_form) {
            $result[$print_form->getId()] = clone $print_form;
            
        }
        return $result;
    }
    
    /**
    * Возвращает краткий символьный идентификатор печатной формы
    * 
    * @return string
    */
    abstract function getId();
    
    /**
    * Возвращает название печатной формы
    * 
    * @return string
    */
    abstract function getTitle();
    
    /**
    * Возвращает шаблон формы
    * 
    * @return string
    */
    abstract function getTemplate();
    
    /**
    * Возвращает HTML готовой печатной формы
    * 
    * @return string
    */
    function getHtml() 
    {
        $view = new \RS\View\Engine();
        $view->assign([
            'order' => $this->order
        ]);
        $view->assign(\RS\Module\Item::getResourceFolders($this));
        return $view->fetch($this->getTemplate());
    }

    /**
     * Возвращает URL, по которому можно открыть документ без авторизации
     * (для мобильного приложения)
     *
     * @param bool $absolute Если true, то будет возвращен абсолютный URL
     * @return string
     */
    function getPublicUrl($absolute = true)
    {
        if (!$this->order) {
            throw new Exception(t('Печатная форма не связана с заказом'));
        }

        $router = RouterManager::obj();
        $params = [
            'order_hash' => $this->order['hash'],
            'doc_type' => $this->getId()
        ];

        return $router->getUrl('shop-front-printdocs', $params + [
            'sign' => self::signParams($params)
        ], $absolute);
    }

    /**
     * Возвращает подпись параметров запроса
     *
     * @param string $order_hash
     * @return string
     */
    public static function signParams($params)
    {
        return hash_hmac('sha1', json_encode(array_values($params)), \Setup::$SECRET_KEY. sha1(\Setup::$SECRET_SALT));
    }
}