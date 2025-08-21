<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;

use RS\Application\Application;
use RS\Config\Loader;
use RS\Event\Manager as EventManager;
use RS\Img\Core as ImgCore;
use RS\Site\Manager as SiteManager;

/**
 * Класс содержит методы для генерации manifest.json
 */
class ManifestApi
{
    protected
        $folder = '/storage/manifest',
        $path;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->path = \Setup::$PATH.$this->folder.'/manifest.json';
    }

    /**
     * Отдает актуальный файл manifest.json на вывод
     *
     * @return void
     */
    function manifestToOutput()
    {
        $data = $this->getManifestSiteData();

        $app = Application::getInstance();
        if (!empty($data['name'])) {
            $app->headers
                ->addHeader('Content-Type', 'application/json')
                ->sendHeaders();

            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }else {
            $app->showException(404, t('Не задано имя Web приложения'));
        }

    }

    /**
     * Возвращает массив со сведениями по сайту для manifest.json
     * Работает только в контексте текущего сайта, из-за подписчиков на события
     *
     * @return array
     */
    public function getManifestSiteData()
    {
        $site = SiteManager::getSite();
        $site_config = Loader::getSiteConfig();;

        $data = [
            'name' => $site_config->manifest_name,
            'display' => $site_config->manifest_display,
            'start_url' => $site->getRootUrl(true)
        ];

        if ($site_config->manifest_short_name) {
            $data['short_name'] = $site_config->manifest_short_name;
        }

        if ($site_config->manifest_background_color) {
            $data['background_color'] = $site_config->manifest_background_color;
        }

        if ($site_config->manifest_theme_color) {
            $data['theme_color'] = $site_config->manifest_theme_color;
        }

        if ($site_config->manifest_icon) {
            ImgCore::switchFormat(ImgCore::FORMAT_WEBP, false);
            $data['icons'] = [[
                'src' => $site_config->__manifest_icon->getUrl(1024, 1024),
                'sizes' => '1024x1024',
                'type' => getimagesize($site_config->__manifest_icon->getFullPath())['mime'],
            ]];
        }

        $event_result = EventManager::fire('main.getmanifestinfo', [
            'data' => $data
        ]);
        $result = $event_result->getResult();

        return $result['data'];
    }
}