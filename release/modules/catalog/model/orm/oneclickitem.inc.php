<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Orm;

use Alerts\Model\Manager as AlertsManager;
use Catalog\Config\ModuleRights;
use Catalog\Model\CurrencyApi;
use Catalog\Model\Notice as CatalogNotice;
use Catalog\Model\OneClickItemApi;
use Feedback\Model\Orm\FormItem;
use RS\Application\Auth;
use RS\Config\Loader;
use RS\Config\UserFieldsManager;
use RS\Helper\Tools;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use Users\Model\Api as UserApi;
use Users\Model\Orm\User;
use RS\Config\Loader as ConfigLoader;

/**
 * Класс ORM-объектов "Добавить в 1 клик". Объект добавить в 1 клик
 * Наследуется от объекта \RS\Orm\OrmObject, у которого объявлено свойство id
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $user_id Пользователь
 * @property string $number Номер
 * @property string $user_fio Ф.И.О. пользователя
 * @property string $user_phone Телефон пользователя
 * @property string $title Номер сообщения
 * @property string $dateof Дата отправки
 * @property string $status Статус
 * @property string $ip IP Пользователя
 * @property string $currency Трехсимвольный идентификатор валюты на момент покупки
 * @property array $clickfields Дополнительные сведения
 * @property string $sext_fields Дополнительными сведения
 * @property array $products Массив со сведениями о товарах
 * @property string $stext Cведения о товарах
 * --\--
 */
class OneClickItem extends OrmObject
{
    const STATUS_NEW = 'new';
    const STATUS_VIEWED = 'viewed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COLOR_NEW = '#ffa545';
    const STATUS_COLOR_CANCELLED = '#f1542f';
    const STATUS_COLOR_VIEWED = '#71b903';

    protected static $table = 'one_click'; //Имя таблицы в БД

    /** @var FormItem */
    protected $form;  //Текущая форма

    /**
     * Инициализирует свойства ORM объекта
     *
     * @return void
     */
    function _init()
    {
        $config = Loader::byModule($this);

        parent::_init()->append([
            'site_id' => new Type\CurrentSite(), //Создаем поле, которое будет содержать id текущего сайта
            'user_id' => new Type\Bigint([
                'description' => t('Пользователь'),
                'visible' => false,
                'appVisible' => true,
            ]),
            'number' => new Type\Varchar([
                'description' => t('Номер'),
                'maxLength' => 50,
                'unique' => true
            ]),
            'user_fio' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Ф.И.О. пользователя'),
                'checker' => [function($_this, $value) use ($config) {
                    if ($config['oneclick_name_required'] && $value == '') {
                        return t("Поле 'Имя' является обязательным");
                    }
                    return true;
                }]
            ]),
            'user_phone' => new Type\Varchar([
                'maxLength' => '50',
                'description' => t('Телефон пользователя'),
                'checker' => ['chkPhone', t('Некорректно указан телефон')]
            ]),
            'title' => new Type\Varchar([
                'maxLength' => '150',
                'description' => t('Номер сообщения')
            ]),
            'dateof' => new Type\Datetime([
                'maxLength' => '150',
                'description' => t('Дата отправки')
            ]),
            'status' => new Type\Enum(array_keys(self::getStatusTitles()), [
                'maxLength' => '1',
                'allowEmpty' => false,
                'default' => self::STATUS_NEW,
                'list' => [[__CLASS__, 'getStatusTitles']],
                'description' => t('Статус')
            ]),
            'ip' => new Type\Varchar([
                'maxLength' => '150',
                'description' => t('IP Пользователя')
            ]),
            'currency' => new Type\Varchar([
                'maxLength' => '5',
                'description' => t('Трехсимвольный идентификатор валюты на момент покупки')
            ]),
            'clickfields' => new Type\ArrayList([
                'description' => t('Дополнительные сведения'),
                'checker' => [[__CLASS__, 'checkCustomFields']],
                'visible' => false,
            ]),
            'sext_fields' => new Type\Text([
                'description' => t('Дополнительными сведения'),
                'Template' => 'form/field/sext_fields.tpl',
                'listenPost' => false
            ]),
            'products' => new Type\ArrayList([
                'description' => t('Массив со сведениями о товарах'),
                'visible' => false,
                'listenPost' => false
            ]),
            'stext' => new Type\Text([
                'description' => t('Cведения о товарах'),
                'Template' => 'form/field/stext.tpl',
                'listenPost' => false
            ]),
            'kaptcha' => new Type\Captcha([
                'enable' => false,
                'context' => '',
            ]),
        ]);
    }

    /**
     * Проверяет валидность произвольных полей
     *
     * @param $_this
     * @param $value
     * @return bool|null
     */
    public static function checkCustomFields($_this, $value)
    {
        //Сохраняем дополнительные сведения о пользователе
        $fields = $_this->getFieldsManager();
        $ok = $fields->check($value);

        if (!$ok) {
            foreach ($fields->getErrors() as $form => $error_text) {
                $_this->addError($error_text, $form);
            }
            return null; //Не устанавливать ошибку полю data
        }

        return true;
    }

    /**
     * Возвращает объект - менеджер произвольных полей
     *
     * @return UserFieldsManager
     */
    public function getFieldsManager()
    {
        if (!$this->field_manager) {
            $config = ConfigLoader::byModule($this);
            $this->field_manager = $config->getClickFieldsManager()
                ->setErrorPrefix('clickfield_')
                ->setArrayWrapper('clickfields');
        }

        $this->field_manager->setValues((array)$this['clickfields']);
        return $this->field_manager;
    }

    /**
     * Возращает масстив сохранённых данных рассериализованными
     *
     * @param string $field - поле для десеарелизации
     * @return mixed
     */
    function tableDataUnserialized($field = 'stext')
    {
        return @unserialize((string)$this[$field]);
    }

    /**
     * Возвращает пользователя, оформившего заказ
     *
     * @return User
     */
    function getUser()
    {
        if ($this['user_id'] > 0) {
            return new User($this['user_id']);
        }
        $user = new User();
        $fio = explode(" ", $this['user_fio']);
        if (isset($fio[0])) {
            $user['surname'] = $fio[0];
        }
        if (isset($fio[1])) {
            $user['name'] = $fio[1];
        }
        if (isset($fio[2])) {
            $user['midname'] = $fio[2];
        }
        $user['phone'] = $this['user_phone'];
        return $user;
    }

    /**
     * Событие срабатывает перед записью объекта в БД
     *
     * @param string $flag - Флаг вставки обновления, либо удаления insert или update
     * @return void
     */
    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) { //Флаг вставки
            if (empty($this['stext']) && !empty($this['products'])) {
                $api = new OneClickItemApi();
                $this['stext'] = $api->prepareSerializeTextFromProducts($this['products']);
            }
            $this['user_id'] = Auth::getCurrentUser()->id;
        }

        if (empty($this['currency'])) {
            //Если Валюта не задана, то укажем базовую
            $default_currency = CurrencyApi::getDefaultCurrency();
            $this['currency'] = $default_currency['title'];
        }

        $this['number'] = $this->generateNumber();
        $this['user_phone'] = UserApi::normalizePhoneNumber($this['user_phone']);
        $this['title'] = t("Покупка №") . $this['number'] . " " . $this['user_fio'] . " (" . $this['user_phone'] . ")"; //Обновим название
        $this['ip'] = $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR'];
        $this['dateof'] = date("Y.m.d H:i:s");

        if ($this->isModified('clickfields')) {
            $this['sext_fields'] = serialize($this->getFieldsManager()->getStructure());
        }
    }

    /**
     * возвращает уникальный номер покупки
     *
     * @param int $length
     * @return string
     */
    function generateNumber($length = 6)
    {
        return Tools::generatePassword($length, range(0,9));
    }

    /**
     * Событие срабатывает после записи объекта в БД
     *
     * @param string $flag - Флаг вставки обновления, либо удаления
     * @return void
     */
    function afterWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) { //Флаг вставки

            $notice = new CatalogNotice\OneClickUser();
            $notice->init($this);
            //Отсылаем sms пользователю
            AlertsManager::send($notice);

            $notice = new CatalogNotice\OneClickAdmin();
            $notice->init($this);
            //Отсылаем письмо администратору
            AlertsManager::send($notice);
        }
    }

    /**
     * Возвращает массив с названиями статусов
     *
     * @return array
     */
    public static function getStatusTitles()
    {
        return [
            self::STATUS_NEW => t('Новый'),
            self::STATUS_VIEWED => t('Закрыт'),
            self::STATUS_CANCELLED => t('Отменен')
        ];
    }

    /**
     * Возвращает название текущего статуса
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
    function getStatusColor()
    {
        switch($this['status']) {
            case self::STATUS_NEW: return self::STATUS_COLOR_NEW;
            case self::STATUS_VIEWED: return self::STATUS_COLOR_VIEWED;
            case self::STATUS_CANCELLED: return self::STATUS_COLOR_CANCELLED;
        }
    }

    /**
     * Возвращает идентификатор права на удаление для данного объекта
     *
     * @return string
     */
    public function getRightDelete()
    {
        return ModuleRights::RIGHT_ONECLICK_DELETE;
    }


    /**
     * Возвращает идентификатор права на изменение для данного объекта
     *
     * @return string
     */
    public function getRightUpdate()
    {
        return ModuleRights::RIGHT_ONECLICK_CHANGING;
    }

    /**
     * Возвращает true, если из этой покупки в 1 клик можно создать заказ
     *
     * @return bool
     */
    public function canCreateOrder()
    {
        return $this['status'] == self::STATUS_NEW;
    }
}
