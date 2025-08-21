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
 * @property string $dateof Дата события
 * @property string $component Компонент
 * @property string $type Тип события
 * @property string $file Путь к файлу
 * @property string $details Детали проблемы/уязвимости
 * @property integer $viewed Флаг просмотра события администратором
 * --\--
 */
class Event extends \RS\Orm\OrmObject
{
    const COMPONENT_INTEGRITY = 'integrity';
    const COMPONENT_ANTIVIRUS = 'antivirus';
    const COMPONENT_PROACTIVE = 'proactive';

    const TYPE_INFO     = 'info';
    const TYPE_PROBLEM  = 'problem';
    const TYPE_FIXED    = 'fixed';

    protected static
        $table = 'antivirus_events';
        
    function _init()
    {
        parent::_init()->append([
            'dateof' => new Type\Datetime([
                'description' => t('Дата события'),
            ]),
            'component' => new Type\Varchar([
                'description' => t('Компонент'),
                'attr' => [['size' => 1]],
                'list' => [[__CLASS__, 'getComponentList']],
                'meVisible' => false
            ]),
            'type' => new Type\Varchar([
                'description' => t('Тип события'),
                'attr' => [['size' => 1]],
                'list' => [[__CLASS__, 'getTypeList']],
                'meVisible' => false
            ]),
            'file' => new Type\Varchar([
                'maxLength' => 2048,
                'description' => t('Путь к файлу'),
            ]),
            'details' => new Type\Mediumblob([
                'description' => t('Детали проблемы/уязвимости'),
            ]),
            'viewed' => new Type\Integer([
                'description' => t('Флаг просмотра события администратором'),
                'maxLength' => 1,
                'allowEmpty' => false,
            ]),

        ]);
        
        //$this->addIndex(array('site_id', 'route_id'), self::INDEX_UNIQUE);
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
            self::COMPONENT_PROACTIVE => t('Проактивная защита'),
        ];
    }

    /**
     * Возвращает список типов событий
     * @return array
     */
    public static function getTypeList()
    {

        return [
            self::TYPE_INFO     => t('Информация'),
            self::TYPE_PROBLEM  => t('Проблема'),
            self::TYPE_FIXED    => t('Исправлено'),
        ];
    }


    public function getDetails()
    {
        return @unserialize((string)$this['details']);
    }


    public function setDetails($object)
    {
        $this['details'] = serialize($object);
    }


}