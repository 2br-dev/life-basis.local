<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Model\Orm;

use RS\Helper\Tools;
use RS\Orm\AbstractObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager as SiteManager;
use RS\Theme\Manager as ThemeManager;

/**
 * Конфигурационный файл одного сайта.
 * --/--
 * @property integer $site_id ID сайта
 * @property string $admin_email E-mail администратора(ов)
 * @property string $admin_phone Телефон администратора
 * @property string $theme Тема
 * @property string $favicon Иконка сайта 16x16 (PNG, ICO)
 * @property string $favicon_svg Иконка сайта в формате SVG
 * @property string $logo Логотип
 * @property string $logo_inverse Логотип (инверсия)
 * @property string $logo_sm Квадратная версия логотипа
 * @property string $logo_xs Квадратная версия логотипа(микро)
 * @property string $slogan Лозунг
 * @property string $firm_name Наименование организации
 * @property string $firm_inn ИНН организации
 * @property string $firm_kpp КПП организации
 * @property string $firm_ogrn ОГРН/ОГРНИП
 * @property string $firm_bank Наименование банка
 * @property string $firm_bik БИК
 * @property string $firm_rs Расчетный счет
 * @property string $firm_ks Корреспондентский счет
 * @property string $firm_director Фамилия, инициалы руководителя
 * @property string $firm_accountant Фамилия, инициалы главного бухгалтера
 * @property string $firm_v_lice Компания представлена в лице ...
 * @property string $firm_deistvuet действует на основании ...
 * @property string $firm_address Фактический адрес компании
 * @property string $firm_legal_address Юридический адрес компании
 * @property string $firm_email Официальный Email компании
 * @property string $notice_from Будет указано в письме в поле  'От'
 * @property string $notice_reply Куда присылать ответные письма? (поле Reply)
 * @property integer $smtp_is_use Использовать SMTP для отправки писем
 * @property string $smtp_host SMTP сервер
 * @property string $smtp_port SMTP порт
 * @property string $smtp_secure Тип шифрования
 * @property integer $smtp_auth Требуется авторизация на SMTP сервере
 * @property string $smtp_username Имя пользователя SMTP
 * @property string $smtp_password Пароль SMTP
 * @property integer $dkim_is_use Устанавливать DKIM подпись с помощью ReadyScript
 * @property string $dkim_domain DKIM домен
 * @property string $dkim_private Приватный ключ DKIM
 * @property string $dkim_selector Селектор DKIM записи в доменной зоне
 * @property string $dkim_passphrase Пароль для приватного ключа (если есть)
 * @property string $facebook_group Ссылка на группу в Facebook
 * @property string $vkontakte_group Ссылка на группу ВКонтакте
 * @property string $twitter_group Ссылка на страницу в Twitter
 * @property string $instagram_group Ссылка на страницу в Instagram
 * @property string $youtube_group Ссылка на страницу в YouTube
 * @property string $viber_group Ссылка на Viber
 * @property string $telegram_group Ссылка на Telegram
 * @property string $whatsapp_group Ссылка на WhatsApp
 * @property string $policy_personal_data Политика обработки персональных данных (ссылка /policy/)
 * @property string $agreement_personal_data Соглашение на обработку персональных данных (ссылка /policy-agreement/)
 * @property string $agreement_cookie Соглашение на использование cookie (ссылка /cookie-agreement/)
 * @property integer $enable_agreement_personal_data Вариант подтверждения согласия на обработку персональных данных в формах
 * @property string $agreement_personal_data_phrase Фраза подтверждения согласия на обработку персональных данных
 * @property string $manifest_name Название приложения
 * @property string $manifest_short_name Короткое название
 * @property string $manifest_icon Иконка 1024x1024 (PNG)
 * @property string $manifest_display Предпочитаемый вид отображения приложения
 * @property string $manifest_background_color Цвет фона приложения до загрузки стилей
 * @property string $manifest_theme_color Цвет темы
 * --\--
 */
class Config extends AbstractObject
{
    const AGREEMENT_PERSONAL_DATA_DISABLE = 0;
    const AGREEMENT_PERSONAL_DATA_PHRASE = 1;
    const AGREEMENT_PERSONAL_DATA_CHECKBOX = 2;

    protected static 
        $table = 'site_options',
        $activeSiteInstance;
    
    function _init()
    {
        $router = RouterManager::obj();
        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite([
                'primaryKey' => true
            ]),
            t('Основные'),
                'admin_email' => new Type\Varchar([
                    'maxLength' => '250',
                    'description' => t('E-mail администратора(ов)'),
                    'hint' => t('На этот ящик будут приходить уведомления о событиях в системе(заказы, покупки в 1 клик, обращениях пользователей, и т.д.). <br>Допустимо указывать несколько E-mail адресов через запятую.<br> Например: admin@example.com или admin@example.com,manager@example.com'),
                ]),
                'admin_phone' => new Type\Varchar([
                    'maxLength' => '150',
                    'description' => t('Телефон администратора'),
                    'hint' => t('На этот телефон будут приходить SMS уведомления(опционально). <br>Допустимо указывать несколько телефонов через запятую.<br> Например: +79112223334 или +79112223334,+79222333411'),
                ]),
                'theme' => new Type\Varchar([
                    'Attr' => [['readonly' => 'readonly']],
                    'maxLength' => '150',
                    'description' => t('Тема'),
                    'template' => '%site%/form/options/theme.tpl'
                ]),
                'favicon' => new Type\File([
                    'description' => t('Иконка сайта 16x16 (PNG, ICO)'),
                    'hint' => t('Отображается возле заголовка страницы в браузерах'),
                    'allow_file_types' => ['image/png', 'image/x-icon'],
                    'storage' => [\Setup::$ROOT, \Setup::$FOLDER.'/storage/favicon/']
                ]),
                'favicon_svg' => new Type\File([
                    'description' => t('Иконка сайта в формате SVG'),
                    'hint' => t('Отображается возле заголовка страницы в браузерах'),
                    'allow_file_types' => ['image/svg+xml'],
                    'storage' => [\Setup::$ROOT, \Setup::$FOLDER.'/storage/favicon/']
                ]),
            t('Организация'),
                'logo' => new Type\Image([
                    'maxLength' => '200',
                    'description' => t('Логотип'),
                    'hint' => t('Основной логотип, будет использоваться по умолчанию'),
                    'max_file_size' => 10000000,
                    'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'],
                    'PreviewSize' => [275,80],
                ]),
                'logo_inverse' => new Type\Image([
                    'maxLength' => '200',
                    'description' => t('Логотип (инверсия)'),
                    'hint' => t('Основной логотип, который имеет инверсированный цвет, чтобы его можно было размещать на фоне противоположного цвета.'),
                    'max_file_size' => 10000000,
                    'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'],
                    'PreviewSize' => [275,80],
                ]),
                'logo_sm' => new Type\Image([
                    'maxLength' => '200',
                    'description' => t('Квадратная версия логотипа'),
                    'hint' => t('Может использоваться в некоторых темах оформления, в мобильных версиях'),
                    'max_file_size' => 10000000,
                    'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'],
                    'PreviewSize' => [96,96],
                ]),
                'logo_xs' => new Type\Image([
                    'maxLength' => '200',
                    'description' => t('Квадратная версия логотипа(микро)'),
                    'hint' => t('Может использоваться в некоторых темах оформления, в мобильном таб-баре'),
                    'max_file_size' => 10000000,
                    'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'],
                    'PreviewSize' => [48,48],
                ]),
                'slogan' => new Type\Varchar([
                    'description' => t('Лозунг'),
                    'hint' => t('Обычно отображается под логотипом')
                ]),
                'firm_name' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Наименование организации'),
                ]),
                'firm_inn' => new Type\Varchar([
                    'maxLength' => '12',
                    'description' => t('ИНН организации'),
                    'Attr' => [['size' => 20]],
                ]),
                'firm_kpp' => new Type\Varchar([
                    'maxLength' => '12',
                    'description' => t('КПП организации'),
                    'Attr' => [['size' => 20]],
                ]),
                'firm_ogrn' => new Type\Varchar([
                    'maxLength' => '15',
                    'description' => t('ОГРН/ОГРНИП'),
                ]),
                'firm_bank' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Наименование банка'),
                ]),
                'firm_bik' => new Type\Varchar([
                    'maxLength' => '10',
                    'description' => t('БИК'),
                ]),
                'firm_rs' => new Type\Varchar([
                    'maxLength' => '20',
                    'description' => t('Расчетный счет'),
                    'Attr' => [['size' => 25]],
                ]),
                'firm_ks' => new Type\Varchar([
                    'maxLength' => '20',
                    'description' => t('Корреспондентский счет'),
                    'Attr' => [['size' => 25]],
                ]),
                'firm_director' => new Type\Varchar([
                    'maxLength' => '70',
                    'description' => t('Фамилия, инициалы руководителя'),
                ]),
                'firm_accountant' => new Type\Varchar([
                    'maxLength' => '70',
                    'description' => t('Фамилия, инициалы главного бухгалтера'),
                ]),
                'firm_v_lice' => new Type\Varchar([
                    'description' => t('Компания представлена в лице ...'),
                    'hint' => t('например: директора Иванова Ивана Ивановича. Пустое поле означает - "в собственном лице"')
                ]),
                'firm_deistvuet' => new Type\Varchar([
                    'description' => t('действует на основании ...'),
                    'hint' => t('например: устава или свидетельства о регистрации физ.лица в качестве ИП, ОГРНИП:0000000000')
                ]),
                'firm_address' => new Type\Varchar([
                    'description' => t('Фактический адрес компании'),
                ]),
                'firm_legal_address' => new Type\Varchar([
                    'description' => t('Юридический адрес компании'),
                ]),
                'firm_email' => new Type\Varchar([
                    'description' => t('Официальный Email компании'),
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
                'smtp_is_use' => new Type\Integer([
                    'description' => t('Использовать SMTP для отправки писем'),
                    'checkboxView' => [1,0],
                    'template' => '%main%/form/sysoptions/smtp_is_use.tpl',
                    'hint' => t('Если включена - все поля из настроек сайта перекроют соответствующие поля настроек системы'),
                ]),
                'smtp_host' => new Type\Varchar([
                    'description' => t('SMTP сервер')
                ]),
                'smtp_port' => new Type\Varchar([
                    'description' => t('SMTP порт'),
                    'maxLength' => 10
                ]),
                'smtp_secure' => new Type\Varchar([
                    'description' => t('Тип шифрования'),
                    'listFromArray' => [[
                        '' => t('Нет шифрования'),
                        'ssl' => 'SSL',
                        'tls' => 'TLS'
                    ]]
                ]),
                'smtp_auth' => new Type\Integer([
                    'description' => t('Требуется авторизация на SMTP сервере'),
                    'checkboxView' => [1,0]
                ]),
                'smtp_username' => new Type\Varchar([
                    'description' => t('Имя пользователя SMTP'),
                    'maxLength' => 100
                ]),
                'smtp_password' => new Type\Varchar([
                    'description' => t('Пароль SMTP'),
                    'maxLength' => 100
                ]),
                'dkim_is_use' => new Type\Integer([
                    'description' => t('Устанавливать DKIM подпись с помощью ReadyScript'),
                    'checkboxView' => [1,0],
                    'template' => '%main%/form/sysoptions/dkim_is_use.tpl',
                ]),
                'dkim_domain' => new Type\Varchar([
                    'description' => t('DKIM домен'),
                    'hint' => t('Укажите его, если вы желаете генерировать DKIM подпись средствами ReadyScript. Оставьте пустое поле, если желаете передать эту функцию на сторону ПО для отправки почты.')
                ]),
                'dkim_private' => new Type\File([
                    'description' => t('Приватный ключ DKIM'),
                ]),
                'dkim_selector' => new Type\Varchar([
                    'description' => t('Селектор DKIM записи в доменной зоне'),
                    'hint' => t('То, что находится до _domainkey, например: селектор "dkim", если запись имеет ключ dkim._domainkey.ВАШ ДОМЕН.')
                ]),
                'dkim_passphrase' => new Type\Varchar([
                    'description' => t('Пароль для приватного ключа (если есть)')
                ]),
            t('Социальные ссылки'),
                'facebook_group' => new Type\Varchar([
                    'description' => t('Ссылка на группу в Facebook')
                ]),
                'vkontakte_group' => new Type\Varchar([
                    'description' => t('Ссылка на группу ВКонтакте')
                ]),
                'twitter_group' => new Type\Varchar([
                    'description' => t('Ссылка на страницу в Twitter')
                ]),
                'instagram_group' => new Type\Varchar([
                    'description' => t('Ссылка на страницу в Instagram')
                ]),
                'youtube_group' => new Type\Varchar([
                    'description' => t('Ссылка на страницу в YouTube')
                ]),
                'viber_group' => new Type\Varchar([
                    'description' => t('Ссылка на Viber')
                ]),
                'telegram_group' => new Type\Varchar([
                    'description' => t('Ссылка на Telegram')
                ]),
                'whatsapp_group' => new Type\Varchar([
                    'description' => t('Ссылка на WhatsApp')
                ]),
            t('Персональные данные'),
                'policy_personal_data' => new Type\Richtext([
                    'description' => t('Политика обработки персональных данных (ссылка /policy/)'),
                    'hint' => t('Разместите ссылку на данную политику, принятую в вашей органиции на вашем сайте, в меню или в подвале'),
                    'template' => '%site%/form/site/policy.tpl',
                    'loadDefaultUrl' => $router->getAdminUrl("LoadDefaultDocument", ["doc_id" => "policy_personal_data"], 'site-personaldata')
                ]),
                'agreement_personal_data' => new Type\Richtext([
                    'description' => t('Соглашение на обработку персональных данных (ссылка /policy-agreement/)'),
                    'hint' => t('Документ, который пользователь акцептует при сообщении своих персональных данных. '),
                    'template' => '%site%/form/site/policy.tpl',
                    'loadDefaultUrl' => $router->getAdminUrl("LoadDefaultDocument", ["doc_id" => "agreement_personal_data"], 'site-personaldata')
                ]),
                'agreement_cookie' => new Type\Richtext([
                    'description' => t('Соглашение на использование cookie (ссылка /cookie-agreement/)'),
                    'hint' => t('Документ, который пользователь акцептует при первом входе на сайт'),
                    'template' => '%site%/form/site/policy.tpl',
                    'loadDefaultUrl' => $router->getAdminUrl("LoadDefaultDocument", ["doc_id" => "agreement_cookie"], 'site-personaldata')
                ]),
                'enable_agreement_personal_data' => new Type\Integer([
                    'description' => t('Вариант подтверждения согласия на обработку персональных данных в формах'),
                    'list' => [[__CLASS__, 'getAgreementPersonalTitles']],
                    'allowEmpty' => false,
                    'radioListView' => true
                ]),
                'agreement_personal_data_phrase' => new Type\Text([
                    'description' => t('Фраза подтверждения согласия на обработку персональных данных'),
                    'template' => '%site%/form/site/policy.tpl',
                    'loadDefaultUrl' => $router->getAdminUrl("LoadDefaultDocument", ["doc_id" => "agreement_personal_data_phrase"], 'site-personaldata'),
                    'hint' => t('Если поле пустое, то будет использована стандартная фраза. Допустимо использовать переменные:<br>%send - Название нажимаемой кнопки<br>%policy_agreement - Ссылка на согласие на ОПД')
                ]),
            t('Web App'),
                'manifest_name' => new Type\Varchar([
                    'description' => t('Название приложения'),
                    'hint' => t('До 45 символов. Имеет значение, когда сайт добавляется на домашний экран мобильного устройства.')
                ]),
                'manifest_short_name' => new Type\Varchar([
                    'description' => t('Короткое название'),
                    'hint' => t('До 12 символов. Имеет значение, когда сайт добавляется на домашний экран мобильного устройства. Будет подписано под иконкой')
                ]),
                'manifest_icon' => new Type\Image([
                    'description' => t('Иконка 1024x1024 (PNG)'),
                    'allow_file_types' => ['image/png'],
                    'max_file_size' => 10000000,
                    'hint' => t('Будет являться иконкой Web-приложения на домашнем экране мобильного устройства')
                ]),
                'manifest_display' => new Type\Varchar([
                    'description' => t('Предпочитаемый вид отображения приложения'),
                    'listFromArray' => [[
                        'fullscreen' => t('Полный экран'),
                        'standalone' => t('Как полноэкранное приложение'),
                        'minimal-ui' => t('Минимальный набор навигации'),
                        'browser' => t('В браузере')
                    ]],
                    'hint' => t('Настройка имеет значение, когда приложение запущено через иконку на домашнем экране устройства')
                ]),
                'manifest_background_color' => new Type\Color([
                    'description' => t('Цвет фона приложения до загрузки стилей'),
                    'hint' => t('Влияет на цвет фона, до загрузки стилей приложения')
                ]),
                'manifest_theme_color' => new Type\Color([
                    'description' => t('Цвет темы'),
                    'hint' => t('Некоторые браузеры могут подкрашивать свои элементы управления в цвет темы')
                ])
        ]);
    }

    /**
     * Возвращает список вариантов предоставления согласия на обработку персональных данных
     *
     * @param array $first
     */
    public static function getAgreementPersonalTitles($first = [])
    {
        return $first + [
            self::AGREEMENT_PERSONAL_DATA_DISABLE => t('Не выводить подтверждение'),
            self::AGREEMENT_PERSONAL_DATA_PHRASE => t('Выводить надпись перед кнопкой действия'),
            self::AGREEMENT_PERSONAL_DATA_CHECKBOX => t('Выводить надпись и чекбокс перед кнопкой действия')
        ];
    }

    /**
     * Возвращает идентификатор первичного ключа
     *
     * @return string
     */
    function getPrimaryKeyProperty()
    {
        return 'site_id';
    }
    
    function _initDefaults()
    {
        $this['theme'] = 'amazing';
        $this['manifest_background_color'] = '#ffffff';
        $this['manifest_theme_color'] = '#ffffff';
        $this['manifest_display'] = 'standalone';
        $this['enable_agreement_personal_data'] = 2;
    }

    public static function getActualInstance()
    {
        if (!isset(self::$activeSiteInstance)) {
            self::$activeSiteInstance = new self();
            self::$activeSiteInstance->load(SiteManager::getSiteId());
        }
        return self::$activeSiteInstance;
    }
    
    function getThemeName()
    {
        $theme_data = ThemeManager::parseThemeValue($this['theme']);
        return $theme_data['theme'];
    }
}
