<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model;

use Alerts\Model\Types\AbstractNotice;
use Alerts\Model\Types\InterfaceDesktopApp;
use Alerts\Model\Types\InterfaceEmail;
use Alerts\Model\Types\InterfaceSms;
use RS\Helper\IdnaConvert;
use RS\Helper\Mailer;
use RS\Event\Manager as EventManager;
use RS\Img\Core as ImgCore;
use RS\Language\Core as LanguageCore;

/**
 * Менеджер уведомлений
 */
class Manager
{
    public static $timeout_image_request = 5;
    /**
     * Отправляет уведомление по классу событий
     *
     * @param $notice object событие
     * @throws
     */
    public static function send(AbstractNotice $notice)
    {
        //Отключаем генерацию изображений в webp для уведомлений
        ImgCore::switchFormat(ImgCore::FORMAT_WEBP, false);

        //Сменить язык для уведомлений, если язык админ. части отличается от языка сайта
        $temporary_language = $notice->getLanguage();
        $language_has_changed = false;
        if ( $temporary_language ) {
            $actual_language = LanguageCore::getCurrentLang();
            if ( $temporary_language != $actual_language ) {
                $language_has_changed = LanguageCore::setCurrentLang($temporary_language);
            }
        }

        $send_data = self::getSendData($notice);

        $event = EventManager::fire('alerts.beforenoticesend', $send_data);
        if ($event->getEvent()->isStopped()) return;
        $send_data = $event->getResult();

        // Отправка E-Mail уведомления
        if (isset($send_data['email_notice_data'])) {
            $notice_data = $send_data['email_notice_data'];
            if ($send_data['notice_config']->additional_recipients && $notice_data ) {
                $notice_data->email .= ','.$send_data['notice_config']->additional_recipients;
            }
            if($notice_data){
                $mailer = new Mailer();
                $mailer->Subject = $notice_data->subject;
                if ($notice_data->email) {
                    $mailer->addEmails($notice_data->email);
                    $mailer->renderBody($send_data['email_template'], $notice_data->vars);
                    $mailer->Body = Manager::prepareBodyImages($mailer);
                    $mailer->setEventParams('alerts', ['notice' => $notice]);
                    $mailer->send();
                }
            }
        }

        // Отправка SMS уведомления
        if (isset($send_data['sms_notice_data'])) {
            $notice_data = $send_data['sms_notice_data'];
            if($notice_data && $notice_data->phone){
                SMS\Manager::send(
                    $notice_data->phone,
                    $send_data['sms_template'],
                    $notice_data->vars,
                    true,
                    $notice_data->other
                );
            }
        }

        //Отправка уведомления в Desktop приложение ReadyScript
        if (isset($send_data['desktop_notice_data'])) {
            NoticeItemsApi::cleanOldNoticeItems();
            NoticeItemsApi::addNoticeItem($notice, $send_data['desktop_template']);
        }

        //Восстановить язык
        if ( $language_has_changed ) {
            LanguageCore::setCurrentLang($actual_language);
        }
    }

    /**
     * Подготавливает данные для предстоящей отправки уведомления
     *
     * @param Types\AbstractNotice $notice
     * @return array
     * @throws \Exception
     */
    public static function getSendData(AbstractNotice $notice)
    {
        $notice_config = Api::getNoticeConfig(get_class($notice));
        $send_data = [
            'notice_config' => $notice_config,
            'notice' => $notice,
        ];

        if($notice instanceof InterfaceEmail && $notice_config['enable_email']) {
            $send_data += [
                'email_notice_data' => $notice->getNoticeDataEmail(),
                'email_template' => $notice_config->template_email ?: $notice->getTemplateEmail()
            ];
        }

        if ($notice instanceof InterfaceSms && $notice_config['enable_sms']) {
            $send_data += [
                'sms_notice_data' => $notice->getNoticeDataSms(),
                'sms_template' => $notice_config->template_sms ?: $notice->getTemplateSms()
            ];
        }

        if ($notice instanceof InterfaceDesktopApp && $notice_config['enable_desktop']) {
            $send_data += [
                'desktop_notice_data' => $notice->getNoticeDataDesktopApp(),
                'desktop_template' => $notice_config->template_desktop ?: $notice->getTemplateDesktopApp()
            ];
        }

        return $send_data;
    }

    /**
     * Подготавливает тело письма к отправке. Делает изображения встроенными в письмо.
     *
     * @param \RS\Helper\Mailer $mailer - объект письма
     * @param string $body - тело письма, если не укзано - извлекается из объекта письма
     * @return string
     */
    public static function prepareBodyImages(Mailer $mailer, $body = null)
    {
        if ($body === null) {
            $body = $mailer->Body;
        }

        $replace_function = function($matches) use ($mailer) {

            $src = trim($matches[2],"'\"");
            $cid = md5($src);
            if (preg_match('/^data:/', $src)) {
                return $matches[0];
            }

            $host = parse_url($src, PHP_URL_HOST);
            $root = \RS\Site\Manager::getSite()->getRootUrl(true, false);
            $relative_path = \Setup::$ROOT.parse_url($src, PHP_URL_PATH);

            if (preg_match('/\.(gif|jpg|jpeg|svg|png|webp)$/', $relative_path)
                && (!$host || stripos($root, $host))
                && file_exists($relative_path)) {
                //Будем подгружать файл локально, если файл расположен на текущем хосте и это изображение
                $filename = $relative_path;
            } else {
                //Будем выкачивать файл для вложения
                if (strpos($src, '://') === false) {
                    //Если путь относительный, значит фото локальное
                    $filename = $root . ltrim($src, '/');
                } else {
                    $filename = $src;
                }
                $filename = IdnaConvert::getInstance()->encodeUri($filename);
            }

            //Все фото загружаем через запрос, чтобы они генерировались в случае их отсутствия
            $context = [
                'http'=> [
                    'method'=>"GET",
                    'timeout' => \Alerts\Model\Manager::$timeout_image_request
                ],
                "ssl"=> [
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ]
            ];

            $image_data = @file_get_contents($filename, false, stream_context_create($context));
            if ($image_data) {
                $mailer->addStringEmbeddedImage($image_data, $cid, basename($src));
            }

            return $matches[1]."cid:$cid".$matches[3];
        };

        $body = preg_replace_callback('/(<img[^>]*src=["\'])(.*?)(["\'][^>]*>)/i', $replace_function, $body);
        $body = preg_replace_callback('/(style=["\'][^>]*url\()(.*?)(\))/i', $replace_function, $body);
        $body = preg_replace_callback('/(background=["\'])(.*?)(["\'])/i', $replace_function, $body);

        return $body;
    }
}