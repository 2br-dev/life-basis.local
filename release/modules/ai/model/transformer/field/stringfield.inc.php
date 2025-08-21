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
 * Тип автозаполняемого поля - строка
 */
class StringField extends AbstractField
{
    protected $max_length = 180;

    /**
     * Возвращает строковое название типа
     *
     * @return string
     */
    public function getTypeTitle()
    {
        return t('Строка');
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
            ->setMaxLength($this->getMaxLength())
            ->setAttr([
                'rows' => 3,
                'class' => 'w-100'
            ]);
    }
}