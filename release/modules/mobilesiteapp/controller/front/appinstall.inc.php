<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Controller\Front;

use Main\Model\LicenseApi;
use MobileSiteApp\Model\AppApi;
use MobileSiteApp\Model\PlatformDetector;
use RS\Controller\Front;
use RS\Site\Manager;

/**
 * Контроллер выполняет автоматический редирект в маркетплейс приложений,
 * в соответствии с ОС устройства. Контроллер необходим для создания единой ссылки
 * (например на QR-коде для установки приложения)
 */
class AppInstall extends Front
{
    function actionIndex()
    {
        $app_api = new AppApi();
        $license_api = new LicenseApi();
        $platform = PlatformDetector::getPlatform();

        $site = Manager::getSite();
        $domain = $site->getMainDomain().$site->getRootUrl();

        $main_license_hash = defined('CLOUD_UNIQ') ? CLOUD_UNIQ :  $license_api->getMainLicenseHash();
        $main_license_data_hash = $license_api->getMainLicenseDataHash();

        $info = $app_api->getAppSubscribeInfo($domain, $main_license_hash, $main_license_data_hash);

        if ($platform == PlatformDetector::OS_IOS && !empty($info['app']['url_appstore'])) {
            $this->app->redirect($info['app']['url_appstore']);
        }

        if ($platform == PlatformDetector::OS_ANDROID) {
            $markets = [
                'googleplay' => $info['app']['url_googleplay'] ?? false,
                'rustore' => $info['app']['url_rustore'] ?? false,
            ];

            if (isset($info['app']['android_priority_market'])
                && !empty($markets[$info['app']['android_priority_market']])) {
                $this->app->redirect($markets[$info['app']['android_priority_market']]);
            }

            foreach($markets as $url) {
                if ($url) {
                    $this->app->redirect($url);
                }
            }
        }

        $this->app->redirect(); //На главную, если устройство не определено
    }
}