<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Config;

use RS\Module\AbstractPatches;
use RS\Orm\Request;
use Support\Model\Orm\Topic;
use Support\Model\Platform\PlatformSite;

class Patches extends AbstractPatches
{

    /**
     * Возвращает массив имен патчей.
     * В классе должны быть пределены методы:
     * beforeUpdate<ИМЯ_ПАТЧА> или
     * afterUPDATE<ИМЯ_ПАТЧА>
     *
     * @return array
     */
    function init()
    {
        return [
            '608'
        ];
    }

    function afterUpdate608()
    {
        //Генерируем всем тикетам номера
        $topic = new Topic();
        $topic_ids = Request::make()
            ->select('id')
            ->from($topic)
            ->where("number IS NULL OR number = ''")
            ->exec()->fetchSelected(null, 'id');

        foreach($topic_ids as $id) {
            Request::make()
                ->update($topic)
                ->set([
                    'number' => $topic->generateNumber()
                ])
                ->where([
                    'id' => $id
                ])->exec();
        }

        //Установим дату создания равную дате последнего обновления для старых тикетов
        Request::make()
            ->update(new Topic())
            ->set('created = updated')
            ->where('created IS NULL')
            ->exec();

        //Установим платформу Сайт всем тикетам по умолчанию
        Request::make()
            ->update(new Topic())
            ->set([
                'platform' => PlatformSite::PLATFORM_ID
            ])
            ->where('platform IS NULL')
            ->exec();
    }
}