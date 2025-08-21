<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Feedback\Controller\Admin;

use Feedback\Model\FormApi;
use Feedback\Model\ResultApi;
use RS\Controller\Admin\Front;

class Tools extends Front
{
    /**
     * Выполняет поиск ID результатам формы обратной связи по строке
     *
     * @return string
     */
    public function actionAjaxSearchResultItem()
    {
        $api = new ResultApi();

        $term = $this->url->request('term', TYPE_STRING);
        $cross_multisite = $this->url->request('cross_multisite', TYPE_INTEGER);

        if ($cross_multisite) {
            //Устанавливаем поиск по всем мультисайтам
            $api->setMultisite(false);
        }

        $json = [];
        $api->setFilter([
            'title:%like%' => $term,
            '|stext:%like%' => $term,
        ]);
        $list = $api->getList(1, 8);
        foreach ($list as $result_item) {
            $forms = FormApi::staticSelectList();

            $json[] = [
                'label' => t('%title от %date', [
                    'title' => $result_item['title'],
                    'date' => date('d.m.Y', strtotime($result_item['dateof']))
                ]),
                'id' => $result_item['id'],
                'desc' => t('Форма: %form', [
                    'form' => $forms[$result_item['form_id']]
                ])
            ];
        }

        return json_encode($json);
    }
}
