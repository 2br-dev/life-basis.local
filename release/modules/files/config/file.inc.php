<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Config;
use RS\Orm\ConfigObject;
use RS\Orm\Type;

/**
* Класс конфигурации модуля
*/
class File extends ConfigObject
{
    function init()
    {
        parent::init()->append([
            'expired_files_days' => (new Type\Integer)
                ->setDescription(t('Через какое количество дней удалять не связанные с объектом файлы?'))
                ->setHint(t('0 - не удалять. Пользователь мог загрузить файл, но несохранить объект, к которому данный файл предназначался. В этом случае файл остается в системе не связанным. Каждый день в 2:00 планировщик будет удалять файлы, которые были добавлены ранее указанного количества дней и еще не привязаны ни к одному объекту.'))
        ]);
    }
}