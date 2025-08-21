<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Transformer\Field;

use Ai\Model\Transformer\AbstractField;
use RS\Orm\Type;

/**
 * Тип автозаполняемого поля - текст
 */
class TextField extends AbstractField
{
    protected $max_length = 65535;

    /**
     * Возвращает строковое название типа
     *
     * @return string
     */
    public function getTypeTitle()
    {
        return t('Небольшой текст');
    }

    /**
     * Возвращает объект свойства ORM объекта, с помощью которого можно
     * отобразить форму с генерируемым значением
     *
     * @return Type\Text
     */
    public function getOrmProperty()
    {
        return (new Type\Text())
            ->setAttr([
                'rows' => 5,
                'class' => 'w-100'
            ]);
    }
}