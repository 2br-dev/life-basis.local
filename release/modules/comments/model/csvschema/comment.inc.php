<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Comments\Model\CsvSchema;
use Comments\Model\CsvPreset\ObjectColumns;
use RS\Csv\AbstractSchema;
use \RS\Csv\Preset,
    \Comments\Model\Orm\Comment as CommentOrm;
    
/**
* Схема импорта-экспорта комментариев
*/
class Comment extends AbstractSchema
{
    function __construct()
    {
        parent::__construct(new Preset\Base([
            'ormObject' => new CommentOrm(),
            'excludeFields' => ['site_id'],
            'searchFields' => ['id'],
            'multisite' => true,
        ]), [
            new ObjectColumns([]),
        ]);
    }
}
