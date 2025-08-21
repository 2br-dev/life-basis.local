<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model\Platform;

/**
 * Класс - заглушка, используется в случае,
 * если по идентификатору не удалось найти ни одну платформу.
 */
class PlatformUnknown extends AbstractPlatform
{
    private $id;

    function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Возвращает идентификатор платформы
     *
     * @return string
     */
    function getId()
    {
        return $this->id;
    }

    /**
     * Возвращает название платформы
     *
     * @return string
     */
    function getTitle()
    {
        return t('Неизвестная платформа');
    }
}