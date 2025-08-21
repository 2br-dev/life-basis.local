<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Mobilesiteapp\Model\Orm;
use \RS\Orm\Type;

/**
 * OnBoarding
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название
 * @property string $alias Псевдоним
 * @property string $image Изображение
 * @property integer $sortn Порядок сортировки
 * --\--
 */
class OnBoarding extends \RS\Orm\OrmObject
{
    protected static
        $table = 'mobilesiteapp_onboarding';

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'title' => new Type\Varchar([
                'description' => t('Название'),
                'attr' => [[
                    'data-autotranslit' => 'alias'
                ]],
            ]),
            'alias' => new Type\Varchar([
                'description' => t('Псевдоним'),
                'Checker' => ['chkalias', null],
                'maxLength' => 100,
            ]),
            'image' => new Type\Image([
                'max_file_size' => 10000000,
                'allow_file_types' => ['image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'],
                'description' => t('Изображение'),
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Порядок сортировки'),
                'visible' => false
            ])
        ]);

        $this->addIndex(['site_id', 'alias'], self::INDEX_UNIQUE);
    }

    function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = \RS\Orm\Request::make()
                    ->select('MAX(sortn) as max')
                    ->from($this)
                    ->where([
                        'site_id' => $this->__site_id->get()
                    ])
                    ->exec()->getOneField('max', 0) + 1;
        }
    }
}