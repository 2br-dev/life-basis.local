<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Mobilesiteapp\Controller\Admin;

use Mobilesiteapp\Model\OnBoardingApi;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Table;

/**
 * Контроллер OnBoarding
 */
class OnBoardingCtrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        parent::__construct(new OnBoardingApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('В данном разделе можно управлять слайдами onboarding для приложения. Если список слайдов пуст, то onboarding отображаться не будет.'));
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('Добавить')]));

        $helper->setTopTitle(t('Слайды для onboarding'));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'id', 'CurrentSort' => SORTABLE_ASC]),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),

                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]]),
                ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move')
            ]
        ]));

        return $helper;
    }

    /**
     * AJAX
     */
    function actionMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $direction = $this->url->request('flag', TYPE_STRING);
        return $this->result->setSuccess( $this->api->moveElement($from, $to, $direction) )->getOutput();
    }
}
