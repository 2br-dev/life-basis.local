<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Guzzle;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

/**
 * Класс-декоратор, позволяет выполнять $callback при достижении конца потока SSE (Server Sent Events).
 * Необходим для записи лога после завершения получения потока
 */
class StreamFinishCallback implements StreamInterface
{
    use StreamDecoratorTrait;

    private $stream;
    private $callback;
    private $full_result = '';

    public function __construct(StreamInterface $stream, callable $callback)
    {
        $this->stream = $stream;
        $this->callback = $callback;
    }

    /**
     * Читает данные из потока
     *
     * @param int $length Читает максимум $length байтов из потока и возвращает
     *     их. Менее $length байтов может быть возвращено, если
     *     соответствующий вызов потока возвращает меньшее количество байтов.
     * @return string Возвращает данные, прочитанные из потока, или пустую строку,
     *     если доступных байтов нет.
     * @throws \RuntimeException if an error occurs.
     */
    public function read(int $length): string
    {
        $result = $this->stream->read($length);
        $this->full_result .= $result;

        if (str_contains($this->full_result, "\n\ndata: [DONE]") || $this->stream->eof()) {
            call_user_func($this->callback, $this->full_result);
            $this->full_result = '';
        }

        return $result;
    }
}