<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Affiliate\Controller\Front;

use Affiliate\Model\AffiliateApi;

class Affiliates extends \RS\Controller\Front
{
    const PAGE_SIZE_SEARCH = 7;
    /**
     * @var AffiliateApi
     */
    public $api;
        
    function init()
    {
        $this->api = new AffiliateApi();
        $this->api->setFilter('public', 1);
    }
    
    function actionIndex()
    {
        $referer = $this->url->request('referer', TYPE_STRING);
        
        $this->app->headers->addCookie(AffiliateApi::COOKIE_ALREADY_SELECT, 1, time() + 60*60*24*365*10, '/');
        
        $this->view->assign([
            'current_affiliate' => $this->api->getCurrentAffiliate(),
            'affiliates' => $this->api->getTreeList(0),
            'referer' => $referer
        ]);
        
        return $this->result->setTemplate('affiliates.tpl');
    }

    function actionAjaxSearch()
    {
        $term = $this->url->request('term', TYPE_STRING);
        $referer = $this->url->request('referer', TYPE_STRING);
        $contact_page = $this->url->request('contact_page', TYPE_INTEGER);

        $this->api->setFilter(['title:%like%' => $term]);
        $this->api->setOrder('title');

        $list = [];
        foreach($this->api->getList(1, self::PAGE_SIZE_SEARCH) as $affiliate) {
            $list[] = [
                'label' => $affiliate['title'],
                'url' => $contact_page ? $affiliate->getContactPageUrl() : $affiliate->getChangeAffiliateUrl($referer),
                'is_default' => $affiliate['is_default']
            ];
        }

        if (!$list) {
            $default = $this->api
                ->clearFilter()
                ->setFilter('is_default', 1)
                ->getFirst();

            if ($default) {
                $list[] = [
                    'label' => t('Не нашли своего города? Выберите <b>%0</b>', [$default['title']]),
                    'title' => $default['title'],
                    'url' => $contact_page ? $default->getContactPageUrl() : $default->getChangeAffiliateUrl($referer),
                    'is_default' => $default['is_default']
                ];
            }
        }

        return $this->result->addSection('list', $list);
    }
}
