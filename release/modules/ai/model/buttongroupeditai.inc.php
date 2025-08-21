<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model;

use RS\Html\Toolbar\Button\Button;
use RS\Router\Manager;

/**
 * Класс кнопки массового заполнения через AI
 */
class ButtonGroupEditAi extends Button
{
    /**
     * Конструктор кнопки
     *
     * @param string $transformer_id - ID трансформера
     * @param string $class - HTML-класс у кнопки
     */
    function __construct($transformer_id, $class)
    {
        $router = Manager::obj();
        $href = $router->getAdminUrl('wizardWelcome', [
            'transformer_id' => $transformer_id,
            'skip' => 'auto'
        ], 'ai-taskctrl');

        parent::__construct($href, '<i class="zmdi zmdi-graphic-eq"></i>', [
            'attr' => [
                'title' => t('Заполнить через ИИ'),
                'class' => 'crud-multiaction btn '.$class,
            ]
        ]);
    }
}