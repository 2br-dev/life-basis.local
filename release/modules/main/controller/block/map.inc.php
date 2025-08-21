<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Block;

use RS\Controller\StandartBlock;
use RS\Orm\Type;
use RS\Orm\Type\VariableList\TextAreaVariableListField;
use RS\Orm\Type\VariableList\TextVariableListField;

/**
* Блок - карта
*/
class Map extends StandartBlock
{
    protected static $controller_title = 'Карта';
    protected static $controller_description = 'Отображает карту с точками на ней';

    protected $default_params = [
        'indexTemplate' => 'blocks/map/map.tpl',
        'width' => '400',
        'height' => '300',
        'zoom' => '11',
    ];
    
    /**
     * Возвращает ORM объект, содержащий настриваемые параметры или false в случае, 
     * если контроллер не поддерживает настраиваемые параметры
     * @return \RS\Orm\ControllerParamObject | false
     */
    public function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'width' => new Type\Integer([
                'description' => t('Ширина карты, px'),
                'hint' => t('Если пустое поле, то будет на всю ширину'),
            ]),
            'height' => new Type\Integer([
                'description' => t('Высота карты, px'),
            ]),
            'points' => new Type\VariableList([
                'description' => t('Координаты точек'),
                'tableFields' => [[
                    new TextVariableListField('lat', t('Широта')),
                    new TextVariableListField('lon', t('Долгота')),
                    new TextAreaVariableListField('balloonContent', t('Текст точки')),
                ]],
            ]),
            'zoom' => new Type\Integer([
                'description' => t('Масштаб карты'),
                'hint' => t('Если несколько точек на карте, то зум будет определять автоматически'),
            ]),
        ]);
    }

    public function actionIndex()
    {
        $this->view->assign([
            'width' => $this->getParam('width'),
            'height' => $this->getParam('height', $this->default_params['height'], true),
            'zoom' => $this->getParam('zoom'),
        ]);
        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
    
    /**
     * Возвращает JSON массив точек
     * 
     * @return string
     */
    public function getPointsJSON()
    {
        return json_encode(array_values($this->getParam('points', TYPE_ARRAY)), JSON_UNESCAPED_UNICODE);
    }
}
