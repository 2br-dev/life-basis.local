<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Controller\Front\Policy;

use RS\Config\Loader;
use RS\Controller\Front;

class Cookie extends Front  {

    function actionIndex()
    {
        $this->app->title->addSection(t('Согласие на использование cookie'));
        $this->app->breadcrumbs->addBreadCrumb(t('Согласие на использование cookie'));

        $site_config = Loader::getSiteConfig();
        $this->view->assign([
            'document' => $site_config['agreement_cookie']
                            ?: $site_config['policy_personal_data']
        ]);

        return $this->result->setTemplate('policy/policy_wrapper.tpl');
    }
}