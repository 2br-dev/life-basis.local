<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Partnership\Model\Orm;

use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Typecost;
use Catalog\Model\ProductDialog;
use RS\Cache\Manager as CacheManager;
use RS\Exception;
use RS\Http\Request as HttpRequest;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\Theme\Item as ThemeItem;
use Shop\Model\Orm\Delivery;
use Site\Model\Orm\Site;
use Users\Model\Orm\User;

/**
 * ORM объект - партнер
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id Сайт
 * @property string $title Название партнера
 * @property integer $is_reg_user Регистрировать нового пользователя?
 * @property integer $user_id Пользователь
 * @property string $reg_user_name Имя
 * @property string $reg_user_surname Фамилия
 * @property string $reg_user_phone Телефон
 * @property string $reg_user_e_mail E-mail
 * @property string $reg_user_openpass Пароль
 * @property string $logo Логотип
 * @property string $logo_sm Квадратная версия логотипа
 * @property string $logo_xs Квадратная версия логотипа(микро)
 * @property string $favicon Иконка сайта 16x16 (PNG, ICO)
 * @property string $favicon_svg Иконка сайта в формате SVG
 * @property string $slogan Слоган
 * @property string $short_contacts Короткая контактная информация
 * @property string $contacts Контактная информация
 * @property integer $price_base_id Базовая цена для расчета
 * @property integer $price_inc_value Увеличения стоимости, %
 * @property integer $cost_type_id Привязанная к партнеру цена
 * @property string $domains Доменные имена (через запятую или с новой строки)
 * @property array $coordinates Местонахождение
 * @property integer $city_id Город из справочника
 * @property string $address Адрес партнёра
 * @property double $coordinate_lat Координата широты партнёра
 * @property double $coordinate_lng Координата долготы партнёра
 * @property integer $redirect_to_https Перенаправлять на https
 * @property string $theme Тема оформления партнера
 * @property array $products Товары, доступные на сайте партнера
 * @property string $_products 
 * @property integer $is_closed Закрыть доступ к сайту
 * @property string $close_message Причина закрытия сайта
 * @property string $notice_from Будет указано в письме в поле  'От'
 * @property string $notice_reply Куда присылать ответные письма? (поле Reply)
 * --\--
 */
class Partner extends OrmObject
{
    protected static $table = 'partnership';

    /** @var self */
    protected $before_partner;

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\Integer([
                'description' => t('Сайт'),
                'list' => [['\Site\Model\Api', 'staticSelectList']]
            ]),
            'title' => new Type\Varchar([
                'description' => t('Название партнера'),
                'checker' => ['chkEmpty', t('Укажите наименование партнера')],
            ]),
            'is_reg_user' => new Type\Integer([
                'description' => t('Регистрировать нового пользователя?'),
                'visible' => false,
                'runtime' => true
            ]),
            'user_id' => new Type\User([
                'description' => t('Пользователь'),
                'template' => '%partnership%/form/partner/user.tpl',
                'checker' => [function ($object, $value, $error) {
                    return ($object['is_reg_user'] == 0 && empty($value)) ? $error : true;

                }, t('Партнер должен быть привязан к пользователю системы')],
                'attr' => [[
                    'placeholder' => t('Логин или Фамилия')
                ]]
            ]),
            'reg_user_name' => new Type\Varchar([
                'description' => t('Имя'),
                'visible' => false,
                'runtime' => true
            ]),
            'reg_user_surname' => new Type\Varchar([
                'description' => t('Фамилия'),
                'visible' => false,
                'runtime' => true
            ]),
            'reg_user_phone' => new Type\Varchar([
                'description' => t('Телефон'),
                'visible' => false,
                'runtime' => true
            ]),
            'reg_user_e_mail' => new Type\Varchar([
                'description' => t('E-mail'),
                'visible' => false,
                'runtime' => true
            ]),
            'reg_user_openpass' => new Type\Varchar([
                'description' => t('Пароль'),
                'visible' => false,
                'runtime' => true
            ]),
            'logo' => new Type\Image([
                'description' => t('Логотип'),
                'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'],
                'PreviewSize' => [275,80]
            ]),
            'logo_sm' => new Type\Image([
                'maxLength' => '200',
                'description' => t('Квадратная версия логотипа'),
                'hint' => t('Может использоваться в некоторых темах оформления, в мобильных версиях'),
                'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'],
                'PreviewSize' => [96,96],
            ]),
            'logo_xs' => new Type\Image([
                'maxLength' => '200',
                'description' => t('Квадратная версия логотипа(микро)'),
                'hint' => t('Может использоваться в некоторых темах оформления, в мобильном таб-баре'),
                'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'],
                'PreviewSize' => [48,48],
            ]),
            'favicon' => new Type\File([
                'description' => t('Иконка сайта 16x16 (PNG, ICO)'),
                'hint' => t('Отображается возле заголовка страницы в браузерах'),
                'max_file_size' => 100000, //100 Кб
                'allow_file_types' => ['image/png', 'image/x-icon'],
                'storage' => [\Setup::$ROOT, \Setup::$FOLDER.'/storage/favicon/']
            ]),
            'favicon_svg' => new Type\File([
                'description' => t('Иконка сайта в формате SVG'),
                'hint' => t('Отображается возле заголовка страницы в браузерах'),
                'max_file_size' => 100000, //100 Кб
                'allow_file_types' => ['image/svg+xml'],
                'storage' => [\Setup::$ROOT, \Setup::$FOLDER.'/storage/favicon/']
            ]),
            'slogan' => new Type\Varchar([
                'description' => t('Слоган')
            ]),
            'short_contacts' => new Type\Varchar([
                'description' => t('Короткая контактная информация'),
            ]),
            'contacts' => new Type\Richtext([
                'description' => t('Контактная информация')
            ]),
            'price_base_id' => new Type\Integer([
                'description' => t('Базовая цена для расчета'),
                'list' => [['\Catalog\Model\CostApi', 'getManualCostList']]
            ]),
            'price_inc_value' => new Type\Integer([
                'description' => t('Увеличения стоимости, %')
            ]),
            'cost_type_id' => new Type\Integer([
                'description' => t('Привязанная к партнеру цена'),
                'visible' => false
            ]),
            'domains' => new Type\Text([
                'description' => t('Доменные имена (через запятую или с новой строки)')
            ]),
            'coordinates' => new Type\Coordinates([
                'description' => t('Местонахождение'),
            ]),
            'city_id' => new Type\Integer([
                'description' => 'Город из справочника',
                'tree' => [['\Shop\Model\RegionApi', 'staticTreeList']],
            ]),
            'address' => new Type\Varchar([
                'description' => t('Адрес партнёра'),
                'visible' => false,
            ]),
            'coordinate_lat' => new Type\Real([
                'description' => t('Координата широты партнёра'),
                'visible' => false,
            ]),
            'coordinate_lng' => new Type\Real([
                'description' => t('Координата долготы партнёра'),
                'visible' => false,
            ]),
            'redirect_to_https' => new Type\Integer([
                'description' => t('Перенаправлять на https'),
                'hint' => t('При изменении данной опции, необходимо очистить кэш браузера'),
                'allowEmpty' => false,
                'checkboxView' => [1, 0]
            ]),
            'theme' => new Type\Varchar([
                'description' => t('Тема оформления партнера'),
                'checker' => ['chkEmpty', t('Укажите тему оформления')],
                'attr' => [[
                    'readonly' => 'readonly'
                ]],
                'template' => '%partnership%/form/partner/theme.tpl'
            ]),
            'products' => new Type\ArrayList([
                'description' => t('Товары, доступные на сайте партнера'),
                'template' => '%partnership%/form/partner/products.tpl'
            ]),
            '_products' => new Type\Text([
                'visible' => false
            ]),
            'is_closed' => new Type\Integer([
                'description' => t('Закрыть доступ к сайту'),
                'hint' => t('Используйте данный флаг, чтобы закрыть доступ к сайту на время его разработки. Администраторы будут иметь доступ как на сайт, так и в административную панель.'),
                'template' => '%partnership%/form/partner/is_closed.tpl',
                'checkboxView' => [1, 0]
            ]),
            'close_message' => new Type\Varchar([
                'description' => t('Причина закрытия сайта'),
                'hint' => t('Будет отображена пользователям'),
                'attr' => [[
                    'placeholder' => t('Причина закрытия сайта')
                ]],
                'visible' => false
            ]),
            t('Уведомления'),
                'notice_from' => new Type\Varchar([
                    'maxLength' => 255,
                    'description' => t("Будет указано в письме в поле  'От'"),
                    'hint' => t('Например: "Магазин ReadyScript&lt;robot@ваш-магазин.ru&gt;" или просто "robot@ваш-магазин.ru"'),
                    'checker' => ['chkPattern', t('Неправильно указан email<br>Например: "Магазин ReadyScript&lt;robot@ваш-магазин.ru&gt;" или просто "robot@ваш-магазин.ru"'), '/^.+\&lt\;{1}[a-z0-9_.-]+\@[a-z0-9_.-]+.[a-z.]{2,6}\&gt\;{1}$|^[a-z0-9_.-]+\@[a-z0-9_.-]+.[a-z.]{2,6}$|^$/'],

                ]),
                'notice_reply' => new Type\Varchar([
                    'maxLength' => 255,
                    'description' => t("Куда присылать ответные письма? (поле Reply)"),
                    'hint' => t('Например: "Магазин ReadyScript&lt;support@ваш-магазин.ru&gt;" или просто "support@ваш-магазин.ru"')
                ]),
        ]);
    }

    public function afterObjectLoad()
    {
        $this['products'] = @unserialize((string)$this['_products']);

        if ($this['address'] || $this['coordinate_lat'] || $this['coordinate_lng']) {
            $coordinates = [
                'address' => $this['address'],
                'lat' => $this['coordinate_lat'],
                'lng' => $this['coordinate_lng'],
            ];
            $this['coordinates'] = $coordinates;
        }
    }

    function beforeWrite($flag)
    {
        if ($this['is_reg_user']) {
            //Регистрируем пользователя
            $user = new User();
            $user->getFromArray($this->getValues(), 'reg_user_');
            if (!$user->validate()) {
                foreach ($user->getErrorsByForm() as $key => $errors) {
                    $this->addErrors($errors, 'reg_user_' . $key);
                }
                return false;
            }
            if ($user->insert()) {
                $this['user_id'] = $user['id'];
            } else {
                throw new Exception(t('Пользователь не был создан'));
            }
        }

        if ($flag == self::INSERT_FLAG) {
            //Создаем тип цен партнера
            $cost_type = new Typecost();
            $cost_type['title'] = t('Цена партнера %0', $this['title']);
            $cost_type['type'] = 'auto';
            $cost_type['val_znak'] = '+';
            $cost_type['val'] = $this['price_inc_value'];
            $cost_type['val_type'] = '%';
            $cost_type['depend'] = $this['price_base_id'];
            $cost_type['round'] = 1;
            $cost_type->insert();
            $this['cost_type_id'] = $cost_type['id'];
        }

        if ($flag == self::UPDATE_FLAG) {
            $this->before_partner = new self($this['id']);

        }

        $this['_products'] = serialize($this['products']);
        if ($this->isModified('coordinates')) {
            $this['address'] = (isset($this['coordinates']['address'])) ? $this['coordinates']['address'] : '';
            $this['coordinate_lat'] = (isset($this['coordinates']['lat'])) ? $this['coordinates']['lat'] : 0;
            $this['coordinate_lng'] = (isset($this['coordinates']['lng'])) ? $this['coordinates']['lng'] : 0;
        }

        return null;
    }

    function afterWrite($flag)
    {
        if ($flag == self::UPDATE_FLAG) {
            //Обновляем тип цен партнера
            $cost_type = new Typecost($this['cost_type_id']);
            $cost_type['val'] = $this['price_inc_value'];
            $cost_type['depend'] = $this['price_base_id'];
            $cost_type->update();
        }

        if ($flag == self::INSERT_FLAG || $this->before_partner->getTheme() != $this->getTheme()) {
            //создаем контекст для темы и устанавливаем её
            $theme = new ThemeItem($this->getTheme());
            $theme->setThisTheme([], null, null, false);
        }
    }

    /**
     * Возвращает список доменов, которые относятся к текущему сайту
     *
     * @return array
     */
    function getDomainsList()
    {
        return preg_split('/[,\n\s]/', (string)$this['domains'], -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Возвращает главное доменное имя сайта
     *
     * @return string
     */
    function getMainDomain()
    {
        $domains = $this->getDomainsList();
        return trim($domains[0]);
    }

    /**
     * Возвращает основной сайт текущего партнерского сайта.
     *
     * @return Site
     */
    function getSite()
    {
        return new Site($this['site_id']);
    }

    /**
     * Возвращает ссылку на корень партнёрского сайта
     *
     * @param bool $absolute - Если true, то будет возвращена абсолютная ссылка, иначе относительная
     * @param bool $add_root_folder - Если true, то приписывает в конце папку, в которой находится скрипт
     * @param bool $force_https - Если true, то всегда возвращается с https, иначе в зависимости от текущего протокола
     *
     * @return string
     */
    function getRootUrl($absolute = false, $add_root_folder = true, $force_https = false)
    {
        $domain = $this->getMainDomain();

        $folder = ($add_root_folder) ? \Setup::$FOLDER . '/' : '/';

        if ($force_https) {
            $protocol = 'https';
        } else {
            $protocol = HttpRequest::commonInstance()->getProtocol();
        }

        return $absolute ? $protocol . '://' . $domain . $folder : $folder;
    }

    /**
     * Формирует абсолютный URL из относительного применительно к текущему сайту
     *
     * @param string $relative_uri
     * @return string
     */
    function getAbsoluteUrl($relative_uri)
    {
        return $this->getRootUrl(true, false) . ltrim($relative_uri, '/');
    }

    function getProductsDialog()
    {
        return new ProductDialog('products', false, $this['products'], true);
    }

    function delete()
    {
        if ($this['cost_type_id'] < 1) {
            $this->load($this['id']);
        }
        //Удаляем тип цены партнера
        OrmRequest::make()
            ->delete()
            ->from(new Typecost)
            ->where(['id' => $this['cost_type_id']])
            ->exec();

        //Удаляем идентификатор схемы блоков
        try {
            $theme = new ThemeItem($this->getTheme());
            $theme->removeContext();
        } catch (\RS\Theme\Exception $e) {
        }

        return parent::delete();
    }

    /**
     * Возвращает контекст темы для данного партнера
     *
     * @return string
     */
    function getThemeContext()
    {
        return 'partner-' . $this['id'];
    }

    /**
     * Возвращает полный идентификатор темы
     *
     * @return string
     */
    function getTheme()
    {
        return $this['theme'] . ';' . $this->getThemeContext();
    }

    /**
     * Возращает объект пользователя
     * @return \Users\Model\Orm\User
     */
    function getUser()
    {
        return new User($this['user_id']);
    }

    /**
     * Возвращает список id категорий(вместе с их родителями), которые разрешены для показа на данном партнерском сайте
     *
     * @param bool $cache - испоьлзовать кэш
     * @return array
     */
    function getAllowFolderList($cache = true)
    {
        if ($cache) {
            return CacheManager::obj()
                ->watchTables($this)
                ->request([$this, 'getAllowFolderList'], false, $this['id']);

        } else {
            $groups = isset($this['products']['group']) ? (array)$this['products']['group'] : [];
            if (!$groups) $groups = [0];
            $ids = array_combine($groups, $groups);
            //Находим всех детей у выбранных категорий
            $find_childs = $groups;
            $childs = [];
            while (count($find_childs)) {
                $find_childs = OrmRequest::make()
                    ->select('id')
                    ->from(new Dir())
                    ->whereIn('parent', $find_childs)
                    ->exec()->fetchSelected('id', 'id');
                $childs += $find_childs;
            }

            $find_ids = $groups;
            //Находим все родительские элементы
            while (count($find_ids)) {
                $parents = OrmRequest::make()
                    ->select('parent')
                    ->from(new Dir())
                    ->whereIn('id', $find_ids)
                    ->exec()
                    ->fetchSelected('parent', 'parent');
                $ids += $parents;
                $find_ids = [];
                foreach ($parents as $item) {
                    if ($item > 0) {
                        $find_ids[] = $item;
                    }
                }
            };
            unset($ids[0]);
            $ids += $childs;

            return $ids;
        }
    }

    /**
     * Возвращает клонированный объект доставки
     * @return Delivery
     */
    function cloneSelf()
    {
        /** @var Delivery $clone */
        $clone = parent::cloneSelf();

        //Клонируем фото, если нужно
        if ($clone['logo']) {
            /**
             * @var \RS\Orm\Type\Image
             */
            $clone['logo'] = $clone['__logo']->addFromUrl($clone['__logo']->getFullPath());
        }
        unset($clone['domains']);
        return $clone;
    }
}
