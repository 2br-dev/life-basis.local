<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Controller\Admin;

use Ai\Model\ChatApi;
use Ai\Model\StreamOutput;
use RS\Controller\Admin\Front;
use RS\Controller\Result\Standard;

/**
 * Front-контроллер чата ИИ в административной панели
 */
class Chat extends Front
{
    protected ChatApi $api;

    function init()
    {
        $this->wrapOutput(false);
        $this->api = new ChatApi($this->user->id);
    }

    /**
     * Возвращает параметры, необходимые для старта чата.
     *
     * @return Standard
     */
    public function actionStartChat()
    {
        $start_data = $this->api->getChatStartData();

        return $this->result
            ->setSuccess(true)
            ->addSection($start_data);

    }

    /**
     * Сохраняет сообщение-вопрос в базе
     *
     * @return Standard
     */
    public function actionSaveQuestion()
    {
        $text = $this->url->post('text', TYPE_STRING);
        $message = $this->api->saveQuestion($text);
        $this->result->setSuccess($message['id'] > 0);

        return $this->result;
    }

    /**
     * Генерирует ответ по всей предыдущей переписке
     *
     * @return mixed
     */
    public function actionGetAnswer()
    {
        try {
            $repeat = $this->url->post('repeat', TYPE_INTEGER);
            $stream = $this->api->generateAnswer($repeat);

            $stream_output = new StreamOutput($stream);
            $stream_output->output();

        } catch (\Exception $e) {
            return json_encode([
               'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Очищает последнюю переписку
     *
     * @return Standard
     */
    public function actionCleanChat()
    {
        return $this->result->setSuccess($this->api->cleanChat());
    }

    /**
     * Сохраняет позицию
     *
     * @return Standard
     */
    public function actionSavePosition()
    {
        $data = [
            'chat_width' => $this->url->post('width', TYPE_INTEGER),
            'chat_height' => $this->url->post('height', TYPE_INTEGER),
            'chat_right' => $this->url->post('right', TYPE_INTEGER),
            'chat_top' => $this->url->post('top', TYPE_INTEGER),
            'chat_stick' => $this->url->post('stick', TYPE_STRING),
        ];

        $save_result = $this->api->saveChatSettings($data);
        return $this->result->setSuccess($save_result);
    }

    /**
     * Сохраняет положение кнопки
     *
     * @return Standard
     */
    public function actionSaveButtonPosition()
    {
        $data = [
            'trigger_bottom' => $this->url->post('bottom', TYPE_INTEGER),
        ];

        $save_result = $this->api->saveChatSettings($data);
        return $this->result->setSuccess($save_result);
    }

    /**
     * Возвращает баланс запросов к ИИ для текущего пользователя
     *
     * @return Standard
     */
    public function actionGetBalance()
    {
        $balance = $this->api->getUserBalance();

        return $this->result
            ->setSuccess(true)
            ->addSection($balance);
    }
}