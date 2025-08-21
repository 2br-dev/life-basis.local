<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Config;

use Ai\Model\ButtonAi;
use Ai\Model\ButtonGroupEditAi;
use Ai\Model\CsvSchema\Prompt;
use Ai\Model\Log\AiLog;
use Ai\Model\Orm\ChatSettings;
use Ai\Model\TaskApi;
use Ai\Model\Transformer\Object\ArticleTransformer;
use Ai\Model\Transformer\Object\ProductDirTransformer;
use Ai\Model\Transformer\Object\ProductTransformer;
use Ai\Model\Transformer\Object\SupportTransformer;
use Article\Model\Orm\Article;
use Catalog\Model\Orm\Dir;
use Catalog\Model\Orm\Product;
use RS\AccessControl\Rights;
use RS\Application\Application;
use RS\Application\Auth;
use RS\Controller\Admin\Front as AdminFront;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Event\HandlerAbstract;
use RS\Html\Toolbar\Button\Button;
use RS\Orm\AbstractObject;
use RS\Router\Manager;
use Support\Model\Orm\Support;
use RS\Module\Item as ModuleItem;

/**
 * Класс содержит обработчики системных событий
 */
class Handlers extends HandlerAbstract
{
    /**
     * Здесь должна происходить подписка на события
     */
    public function init()
    {
        $this
            ->bind('getmenus')
            ->bind('getlogs')
            ->bind('controller.beforewrap')
            ->bind('controller.exec.article-admin-ctrl.index')
            ->bind('controller.exec.catalog-admin-ctrl.index')
            ->bind('tinymce.getExternalPlugins')
            ->bind('cron')

            //Добавляем AI-кнопки к объектам
            ->bind('orm.init.catalog-product')
            ->bind('orm.init.catalog-dir')
            ->bind('orm.init.article-article')
            ->bind('controller.afteraction.support-admin-supportctrl');

        if (\Setup::$INSTALLED) {
            $this->bind('orm.afterwrite.site-site', $this, 'onSiteCreate');
        }
    }

    /**
     * Обработчик события запуска планировщика
     *
     * @param $params
     * @return void
     */
    public static function cron($params)
    {
        $config = File::config();
        $task_api = new TaskApi();

        //Последовательно будут обработаны все задачи со всех мультисайтов
        $task_api->runGenerationStep($config['timeout_sec']);
    }

    /**
     * Добавляем шаблоны промптов при создании нового сайта
     *
     * @param $params
     */
    public static function onSiteCreate($params)
    {
        if ($params['flag'] == AbstractObject::INSERT_FLAG) {
            $site = $params['orm'];

            $module = new ModuleItem('ai');
            $installer = $module->getInstallInstance();
            $installer->importCsv(new Prompt(), 'prompts', $site['id']);
        }
    }

    /**
     * Добавляет JS-скрипт, описывающий плагин для Tiny для подключения
     *
     * @param array $list
     * @return array
     */
    public static function tinymceGetExternalPlugins($list)
    {
        $list['rsaigenerate'] = \Setup::$FOLDER.\Setup::$MODULE_FOLDER.'/ai'
            .\Setup::$MODULE_TPL_FOLDER.'/js/tinymce/rs-ai-plugin.js';

        return $list;
    }

    /**
     * Расширяет страницу со списком новостей в админ.панели
     *
     * @param CrudCollection $helper
     * @return void
     */
    public static function controllerExecArticleAdminCtrlIndex(CrudCollection $helper)
    {
        $helper->getBottomToolbar()->addItem(
            new ButtonGroupEditAi('article-article', 'btn-alt btn-primary'), 'ai-fill', 1
        );
    }

    /**
     * Расширяет страницу со списком товаров админ.панели.
     *
     * @param CrudCollection $helper
     * @return void
     */
    public static function controllerExecCatalogAdminCtrlIndex(CrudCollection $helper)
    {
        $helper->getBottomToolbar()->addItem(
            new ButtonGroupEditAi('catalog-product', 'btn-alt btn-primary'), 'ai-fill', 1
        );

        $helper->getTreeBottomToolbar()->addItem(
            new ButtonGroupEditAi('catalog-dir', 'btn-alt btn-primary'), 'ai-fill', 1
        );
    }

    /**
     * Добавляет ИИ-кнопки возле форм в административную панель
     *
     * @param $params
     * @return void
     */
    public static function controllerBeforeWrap($params)
    {
        if (Manager::obj()->isAdminZone() && Auth::isAuthorize()) {
            $controller = $params['controller'];
            if ($controller instanceof AdminFront) {
                $ai_config = File::config();
                $router = Manager::obj();
                $app = Application::getInstance();
                $app->addCss('%ai%/admin/ai-admin.css');

                if (Rights::hasRight('ai', ModuleRights::RIGHT_FIELD_COMPLETION)) {
                    $app->addJs('%ai%/admin/streamfetcher.js');
                    $app->addJs('%ai%/admin/jquery.ai-button.js');
                    $app->addJs('%ai%/admin/jquery.ai-richtext.js');
                    $app->addJs('%ai%/admin/jquery.ai-main-button.js');
                }

                if (Rights::hasRight('ai', ModuleRights::RIGHT_CHAT)
                    && $ai_config['chat_enable']) {
                    $app->addJsVar('ai', [
                        'generateUrl' => $router->getAdminUrl('generate', [], 'ai-gate'),
                        'chatUrl' => $router->getAdminUrl(false, [], 'ai-chat'),
                        'chatSettings' => self::getChatSettings()
                    ]);

                    $app->addJs('%ai%/assistant/dist/assistant.js');
                    $app->addCss(\Setup::$FOLDER . \Setup::$MODULE_FOLDER . '/ai/view/js/assistant/dist/assistant.css', null, BP_ROOT);
                }
            }
        }
    }

    /**
     * Возвращает настройки чата для текущего пользователя
     *
     * @return array
     */
    protected static function getChatSettings()
    {
        $user_id = Auth::getCurrentUser()->id;
        if ($user_id > 0) {
            $chat_settings = new ChatSettings($user_id);
            return array_diff_key(array_map(
                function($value) {
                    return is_numeric($value) ? (int)$value : $value;
                }, $chat_settings->getValues()
            ), array_flip(['user_id']));
        }
        return [];
    }

    public static function controllerAfterActionSupportAdminSupportCtrl($params)
    {
        if ($params['action'] == 'index') {
            $transformer = new SupportTransformer();
            $transformer->addAiToFields(new Support());
        }
    }

    /**
     * Добавляет метки к полям, которые должны будут заполняться с помощью ИИ
     *
     * @param Product $product
     * @return void
     */
    public static function ormInitCatalogProduct(Product $product)
    {
        $transformer = new ProductTransformer();
        $transformer->addAiToFields($product);
    }

    /**
     * Добавляет метки к полям, которые должны будут заполняться с помощью ИИ
     *
     * @param Dir $dir
     * @return void
     */
    public static function ormInitCatalogDir(Dir $dir)
    {
        $transformer = new ProductDirTransformer();
        $transformer->addAiToFields($dir);
    }

    /**
     * Добавляет метки к полям, которые должны будут заполняться с помощью ИИ
     *
     * @param Article $article
     * @return void
     */
    public static function ormInitArticleArticle(Article $article)
    {
        $transformer = new ArticleTransformer();
        $transformer->addAiToFields($article);
    }

    /**
     * Добавляет пункты меню в административной панели
     *
     * @param array $items
     * @return mixed
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('AI-ассистент'),
            'alias' => 'ai-helper',
            'parent' => 'modules',
            'sortn' => 3,
            'typelink' => 'link',
            'link' => '%ADMINPATH%/ai-promptctrl/',
        ];
        $items[] = [
            'title' => t('Шаблоны запросов к ИИ'),
            'alias' => 'ai-prompts',
            'parent' => 'ai-helper',
            'sortn' => 0,
            'typelink' => 'link',
            'link' => '%ADMINPATH%/ai-promptctrl/',
        ];
        $items[] = [
            'title' => t('Задачи на генерацию'),
            'alias' => 'ai-task',
            'parent' => 'ai-helper',
            'sortn' => 0,
            'typelink' => 'link',
            'link' => '%ADMINPATH%/ai-taskctrl/',
        ];
        $items[] = [
            'title' => t('Статистика запросов'),
            'alias' => 'ai-statistic',
            'parent' => 'ai-helper',
            'sortn' => 0,
            'typelink' => 'link',
            'link' => '%ADMINPATH%/ai-statisticctrl/',
        ];


        return $items;
    }

    /**
     * Привносит в систему класс логирования модуля
     *
     * @param array $list
     * @return mixed
     */
    public static function getLogs($list)
    {
        $list[] = AiLog::getInstance();
        return $list;
    }
}