<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model;

/**
 * Класс позволяет передавать поток данных с сервера в браузер
 */
class StreamOutput
{
    /**
     * @param \Traversable $stream
     */
    function __construct(private \Traversable $stream)
    {}

    /**
     * Отправляет потоковые данные в браузер в формате,
     * необходимом JS-скриптам ReadyScript
     *
     * @return void
     */
    function output()
    {
        $start = true;
        foreach ($this->stream as $response) {
            if (connection_aborted()) break;

            if ($start) {
                ob_end_clean();
                //Отправляем заголовок Content-type только после успешного начала перебора $this->stream
                header('Content-type: application/stream+json');
                header('X-Accel-Buffering: no'); //Отключаем буферизацию в nginx
                flush();
                $start = false;
            }

            echo json_encode([
                'text' => $response->getDeltaText(),
                ...$response->getExtraData()
            ], JSON_UNESCAPED_UNICODE)."\n";
            flush();
        }
    }
}