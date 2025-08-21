<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Transformer;

/**
 * Класс, описывает объект, содержащий сведения об одной переменной
 */
class ReplaceVariable
{
    /**
     * Идентификатор переменной вместе с идентификатором объекта (Например, product.title)
     *
     * @var string
     */
    public string $name;

    /**
     * Идентификатор ключа в объекте (Например, title)
     *
     * @var string
     */
    public string $key;

    /**
     * Название поля, которое описывает переменная
     *
     * @var string
     */
    public string $title;

    /**
     * Значение переменной для замены
     *
     * @var string
     */
    public string $value;
}