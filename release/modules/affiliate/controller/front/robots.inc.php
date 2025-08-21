<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Affiliate\Controller\Front;

use Affiliate\Model\AffiliateApi;
use RS\Controller\Front;
use RS\Helper\Tools;

/**
 * Контроллер отвечает за отдачу виртуального содержимого robots.txt
 * для геоподдоменов
 */
class Robots extends Front
{
    function actionIndex()
    {
        $this->wrapOutput(false);
        $this->app->headers->addHeader('Content-type: text/plain; charset=utf-8');
        $affiliate = AffiliateApi::getCurrentAffiliate();
        if ($affiliate['robots_txt']) {
            return Tools::unEntityString($affiliate['robots_txt']);
        }

        return '';
    }
}