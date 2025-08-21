<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Controller\Admin;

use Main\Model\LicenseApi;
use MobileSiteApp\Model\AppApi;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Helper\QrCode\QrCodeGenerator;

/**
 * Контроллер для отображения информации о мобильном приложении
 * Для отображения сведений требуется наличие установленной и активной лицензии на продукт
 */
class AppCtrl extends \RS\Controller\Admin\Front
{
    /**
     * @var AppApi
     */
    public $app_api;

    /**
     * @var LicenseApi
     */
    public $license_api;

    function init()
    {
        $this->license_api = new LicenseApi();
        $this->app_api = new AppApi();

        $this->view->assign([
            'app_api' => $this->app_api
        ]);
    }

    function actionIndex()
    {
        return $this->result->setTemplate('admin/loading.tpl');
    }

    /**
     * Загружает данные о подписке с сервера ReadyScript, кладет их в кэш.
     * В случае успеха далее отображается страница просмотра информации о подписке,
     * в случае неудачи отображается промо-страница с рекламной информацией
     */
    function actionLoadMsaData()
    {
        $refresh = $this->url->get('refresh', TYPE_BOOLEAN);
        $site = \RS\Site\Manager::getAdminCurrentSite();
        $domain = $site->getMainDomain().$site->getRootUrl();

        $main_license_hash = defined('CLOUD_UNIQ') ? CLOUD_UNIQ :  $this->license_api->getMainLicenseHash();
        $main_license_data_hash = $this->license_api->getMainLicenseDataHash();

        $info = $this->app_api->getAppSubscribeInfo($domain, $main_license_hash, $main_license_data_hash, !$refresh);
        if ($info) {
            //Успех, подписка на приложение для этого сайта создана
            $this->result
                    ->setSuccess(true)
                    ->setTemplate( $this->viewInfo($domain, $info) );

        } else {
            //Неудача, не удалось получить информацию или подписка не создана
            $this->result
                    ->setSuccess(false)
                    ->setTemplate($this->viewPromo($domain));

            if ($this->app_api->hasError()) {
                $this->result->addEMessage( $this->app_api->getErrorsStr() );
            }
        }

        return $this->result;
    }

    /**
     * Возвращает шаблон страницы с информацией о подписке
     *
     * @param $domain
     * @param $info
     * @return string
     */
    function viewInfo($domain, $info)
    {
        $this->view->assign([
            'info' => $info['app'],
            'domain' => $domain,
            'order_count' => $this->app_api->getAppOrderCount(),
        ]);

        return 'admin/view_app.tpl';
    }

    function actionShowQrCode()
    {
        $helper = new CrudCollection($this);
        $helper->viewAsAny();
        $helper->setTopTitle(t('Единый QR-код для установки мобильного приложения'));

        $app_install_url = $this->router->getUrl('mobilesiteapp-front-appinstall', [], true);
        $this->view->assign([
            'qr_img_url' => QrCodeGenerator::buildUrl($app_install_url, ['w' => 220, 'h' => 220, 's' => 'qr', 'p' => 0, 'wq' => 0]),
            'qr_big_img_url' => QrCodeGenerator::buildUrl($app_install_url, ['w' => 1024, 'h' => 1024, 's' => 'qr', 'p' => 0]),
            'app_install_url' => $app_install_url
        ]);

        $helper->setForm($this->view->fetch('%mobilesiteapp%/admin/show_qr.tpl'));
        return $this->result->setTemplate($helper->getTemplate());
    }


    /**
     * Возвращает шаблон для промо страницы
     *
     * @return string
     */
    function viewPromo()
    {
        return 'admin/promo.tpl';
    }
}