<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Affiliate\Model\CsvSchema;
use \RS\Csv\Preset,
    \Affiliate\Model\Orm\Affiliate as AffiliateOrm;
    
/**
* Схема импорта-экспорта филиалов
*/
class Affiliate extends \RS\Csv\AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new AffiliateOrm(),
            'excludeFields' => ['site_id', 'id', 'parent_id'],
            'multisite' => true,
            'searchFields' => ['title','parent_id'],
            'selectRequest' => \RS\Orm\Request::make()
                ->from(new AffiliateOrm())
                ->where([
                    'site_id' => \RS\Site\Manager::getSiteId(),
                ])
                ->orderby('parent_id')
        ]), [
            new Preset\TreeParent([
                'ormObject' => new AffiliateOrm(),
                'titles' => [
                    'title' => t('Родитель')
                ],
                'idField' => 'id',
                'parentField' => 'parent_id',
                'treeField' => 'title',
                'rootValue' => 0,
                'multisite' => true,                
                'linkForeignField' => 'parent_id',
                'linkPresetId' => 0
            ])
        ]);
    }
}
