<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Controller\Admin;

use RS\Controller\Admin\Crud;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;
use RS\Html\Table;
use RS\Html\Toolbar;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Router\Manager as RouterManager;
use Shop\Model\CdekRegionApi;
use Shop\Model\DeliveryType\Cdek\CdekApi;
use Shop\Model\Exception as ShopException;
use RS\Orm\Type;

class CdekRegionCtrl extends Crud
{
    /** @var CdekRegionApi $api */
    public $api;

    /**
     * ReturnsCtrl constructor.
     */
    function __construct()
    {
        parent::__construct(new CdekRegionApi());
    }

    /**
     * Хелпер для страницы регионов СДЭК
     *
     * @return CrudCollection
     */
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Загруженные регионы СДЭК'));
        $helper->setTopHelp(t('В данном разделе отображается список регионов из базы СДЭК. Список обновляется автоматически по крону или при нажатии на кнопку.'));

        $helper->setTopToolbar(new Toolbar\Element([
            'Items' => [
                new Toolbar\Button\Button(RouterManager::obj()->getAdminUrl('updateCdekRegions'), t('Загрузить список регионов'), [
                    'attr' => [
                        'class' => 'btn-success crud-add crud-sm-dialog',
                    ],
                ]),
            ],
        ]));
        $helper->setBottomToolbar(null);

        $helper -> setTable(new Table\Element([
            'Columns' => [
                new TableType\Text('code', t('Код СДЭК'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('region_code', t('Код региона СДЭК'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('city', t('Название населённого пункта'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('fias_guid', t('Уникальный идентификатор ФИАС'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('kladr_code', t('Код КЛАДР'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('country', t('Название страны'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('region', t('Название региона'), ['Sortable' => SORTABLE_BOTH]),
                new TableType\Text('sub_region', t('Название района региона'), ['Sortable' => SORTABLE_BOTH]),
            ],
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' =>  [
                    new Filter\Line([
                        'Items' => [
                            new Filter\Type\Text('code', t('Код СДЭК')),
                            new Filter\Type\Text('region_code', t('Код региона СДЭК')),
                            new Filter\Type\Text('city', t('Название населённого пункта'), ['searchType' => '%like%']),
                            new Filter\Type\Text('fias_guid', t('Уникальный идентификатор ФИАС'), ['searchType' => '%like%']),
                            new Filter\Type\Text('kladr_code', t('Код КЛАДР'), ['searchType' => '%like%']),
                            new Filter\Type\Text('country', t('Название страны'), ['searchType' => '%like%']),
                            new Filter\Type\Text('region', t('Название региона'), ['searchType' => '%like%']),
                            new Filter\Type\Text('sub_region', t('Название района региона'), ['searchType' => '%like%']),
                        ],
                    ]),
                ],
            ]),
            'Caption' => t('Поиск'),
        ]));

        return $helper;
    }

    /**
     * Диалог обновления регионов
     *
     * @return \RS\Controller\Result\Standard
     */
    public function actionUpdateCdekRegions()
    {
        $form_object = new FormObject(new PropertyIterator([
            'country_code' => (new Type\Varchar())
                ->setDescription(t('Страна'))
                ->setHint(t('Регионы будут загружены в рамках выбранной страны'))
                ->setList(['\Shop\Model\DeliveryType\Cdek\CdekApi', 'staticGetCountries'])
        ]));

        $form_object['country_code'] = 'RU';

        $helper = new CrudCollection($this);
        $helper->viewAsForm();
        $helper->setTopTitle(t('Загрузка регионов СДЭК'));
        $helper->setBottomToolbar($this->buttons(['save', 'cancel']));
        $helper->setFormObject($form_object);

        if ($this->url->isPost()) {
            if ($form_object->checkData()) {
                try {
                    $delivery = CdekApi::getDefaultCdekDelivery();
                    $page = $this->url->request('page', TYPE_INTEGER, 0);

                    $delivery_type = $delivery->getTypeObject();
                    $new_page = $delivery_type->api->updateCdekRegionsStep($page, $form_object['country_code']);

                    if ($new_page === null) {
                        $this->result->setSuccess(true)
                            ->addMessage(t('База регионов СДЭК успешно обновлена'));
                    } else {
                        $this->result->addSection('repeat', true)
                            ->addSection('queryParams', [
                                'url' => $this->url->getSelfUrl(),
                                'data' => [
                                    'page' => $new_page,
                                    'country_code' => $form_object['country_code']
                                ],
                            ]);
                    }

                    return $this->result;
                } catch (ShopException $e) {
                    $form_object->addError($e->getMessage());
                    return $this->result->setSuccess(false)->setErrors($form_object->getErrors());
                }
            }
        }

        return $this->result->setTemplate($helper->getTemplate());
    }
}
