<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Antivirus\Model\Orm;
use \RS\Orm\Type;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $dateof Дата добавления
 * @property string $component Компонент
 * @property string $file Путь к файлу
 * --\--
 */
class ExcludedFile extends \RS\Orm\OrmObject
{
    const COMPONENT_INTEGRITY = 'integrity';
    const COMPONENT_ANTIVIRUS = 'antivirus';
    
    protected static
        $table = 'antivirus_excluded_files';
        
    function _init()
    {
        parent::_init()->append([
            'dateof' => new Type\Datetime([
                'description' => t('Дата добавления'),
            ]),
            'component' => new Type\Varchar([
                'description' => t('Компонент'),
                'attr' => [['size' => 1]],
                'list' => [['Antivirus\Model\Orm\ExcludedFile', 'getComponentList']],
                'meVisible' => false
            ]),
            'file' => new Type\Varchar([
                'maxLength' => 2048,
                'description' => t('Путь к файлу'),
                'hint' => t('Путь должен быть задан относительно корня сайта, например, modules/export/config/file.inc.php')
            ]),
        ]);
        
        $this->addIndex(['file'], self::INDEX_KEY);
    }
    
    /**
    * Возвращает список идентификаторов компонентов
    * @return array
    */
    public static function getComponentList()
    {
        return [
            self::COMPONENT_INTEGRITY => t('Проверка целостности'),
            self::COMPONENT_ANTIVIRUS => t('Антивирус'),
        ];
    }    

}