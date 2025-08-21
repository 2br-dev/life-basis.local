<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Controller\Admin;

use Export\Model\Api;
use Export\Model\ExportType\Vkontakte\Utils\VkTools;
use Export\Model\Orm\ExportProfile;
use Export\Model\Orm\ExternalProductLink;
use Export\Model\Orm\Vk\VkCategoryLink;
use RS\Controller\Admin\Front;
use RS\Orm\Request;

/**
 * Контроллер, необходимый для реализации экспорта в ВКонтакте
 */
class VkCtrl extends Front
{
    /**
     * Загружает в базу данных список категорий ВК
     *
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     */
    public function actionGetVkCategoryList()
    {
        $profile_id = $this->url->get('profile_id', TYPE_INTEGER);
        $dir_id = $this->url->get('dir_id', TYPE_INTEGER);

        $export_api = new Api();
        $profile = $export_api->getOneItem($profile_id);

        if (!$profile) $this->e404();

        $vk_tools = new VkTools();
        $result = $vk_tools->loadCategoryForProfile($profile);

        if ($result !== true) {
            //Возвращаем сообщение об ошибке, если таковое имеется
            return $this->result->setSuccess(false)->addEMessage($result);
        }

        $export_vk_id = VkCategoryLink::getVkId($profile_id, $dir_id);

        //Возвращаем HTML со строкой данных
        $this->view->assign([
            'export_vk_id' => $export_vk_id,
            'profile_data' => $vk_tools->getCategoryLinksData($dir_id, $profile_id)
        ]);

        return $this->result
                        ->setSuccess(true)
                        ->setTemplate('vk/vk_cat_line.tpl');
    }

    /**
     * Обнуляет связь товаров RS с товарами ВК.
     * Может использоваться для повторной выгрузки всех товаров
     */
    function actionResetLinks()
    {
        $profile_id = $this->url->get('profile_id', TYPE_INTEGER);
        Request::make()
            ->delete()
            ->from(new ExternalProductLink())
            ->where([
                'profile_id' => $profile_id
            ])->exec();

        return $this->result->setSuccess(true)->addSection('closeDialog', true);
    }
}