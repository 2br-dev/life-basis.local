<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Mobilesiteapp\Model;

/**
 * API для работы с OnBoarding
 */
class OnBoardingApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\OnBoarding(), [
            'nameField' => 'title',
            'sortField' => 'sortn',
            'defaultOrder' => 'sortn',
            'multisite' => true,
        ]);
    }
}