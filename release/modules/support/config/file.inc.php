<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Config;

use RS\Orm\ConfigObject;
use \RS\Orm\Type;

/**
 * Конфигурационный файл модуля
 */
class File extends ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'number_mask_digits' => (new Type\Integer)
                    ->setDescription(t('Количество цифр в генерируемом номере темы переписки'))
                    ->setHint(t('Произвольный номер, не позволит отследить какое количество обращений вы получаете, а также позволит легко идентифицировать обращения клиентов')),
                'manager_group' => (new Type\Varchar)
                    ->setDescription(t('Группа, пользователи которой считаются менеджерами поддержки'))
                    ->setHint(t('Пользователей из данной группы, вы сможете указать в качестве менеджеров тикетов'))
                    ->setList(['Users\Model\GroupApi', 'staticSelectList'], [0 => t('- Не выбрано -')]),
                'platforms_on_site' => (new Type\ArrayList())
                    ->setDescription(t('Отображать на сайте переписку со следующих платформ, помимо платформы Сайт'))
                    ->setRuntime(false)
                    ->setHint(t('Например, если желаете, вы можете не отображать в личном кабинете пользователя вашу переписку через Email или другие платформы'))
                    ->setList(['Support\Model\Platform\Manager', 'getAllowOnSitePlatfromTitles'], true, ['all' => t('Все')])
                    ->setCheckboxListView(true)
                    ->setTemplate('%support%/admin/config/platforms_on_site.tpl'),
                'enable_autoupdate_tickets' => (new Type\Integer())
                    ->setDescription(t('Включить автообновление ленты сообщений для администратора'))
                    ->setHint(t('Раз в 5 секунд будет запрашивать у сервера новые сообщения'))
                    ->setCheckboxView(1, 0),
                'enable_new_message_sound' => (new Type\Integer())
                    ->setDescription(t('Включить звук для новых сообщений'))
                    ->setCheckboxView(1, 0),
            t('Вложения'),
                'allow_attachments' => (new Type\Integer)
                    ->setDescription(t('Разрешить прикреплять файлы к сообщениям клиентам'))
                    ->setCheckboxView(1, 0),
                'attachment_allow_extensions' => (new Type\Varchar)
                    ->setDescription(t('Допустимые расширения файлов, через запятую. Например: jpg,png,pdf. Пустое поле - без ограничений')),
                'attachment_max_filesize' => (new Type\Integer)
                    ->setDescription(t('Максимально допустимый размер загружаемых файлов, в Мб. Если 0, то не будет проверяться')),
                'attachment_allow_email_any_extensions' => (new Type\Integer)
                    ->setDescription('Принимать файлы любых расширений через почту')
                    ->setHint(t('Используйте данный флажок, если на сайте вы желаете ограничить файлы по типам, а через сборку почты - нет'))
                    ->setCheckboxView(1, 0),
            t('Сборка почты'),
                'crawlers' => (new Type\MixedType())
                    ->setVisible(true)
                    ->setDescription('')
                    ->setTemplate('%support%/admin/config/crawlers.tpl'),
                'one_fetch_limit' => (new Type\Integer())
                    ->setDescription(t('Лимит на загрузку писем за один шаг от одного профиля'))
                    ->setHint(t('Оптимальный лимит - 30 писем. Загрузка писем занимает время у планировщика. Желательно ограничивать загрузку писем за одну итерацию.'))
        ]);
    }
}