<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model;

use Ai\Model\Orm\ChatMessage;
use Ai\Model\Orm\ChatSettings;
use Ai\Model\Orm\Statistic;
use Ai\Model\ServiceType\BalanceInterface;
use RS\Application\Auth;
use RS\Orm\Request;

/**
 * Класс соержит методы по работе с перепиской с ИИ
 */
class ChatApi
{
    /**
     * @param integer $user_id ID пользователя, чат с которым ведется
     */
    function __construct(protected $user_id)
    {}

    /**
     * Получает массив объектов ChatMessage
     *
     * @return ChatMessage[]
     */
    public function getLastMessages()
    {
        return Request::make()
            ->from(new ChatMessage())
            ->where([
                'user_id' => $this->user_id
            ])
            ->orderby('id')
            ->objects();
    }

    /**
     * Очищает чат пользователя
     *
     * @return bool
     */
    public function cleanChat()
    {
        Request::make()
            ->delete()
            ->from(new ChatMessage())
            ->where([
                'user_id' => $this->user_id,
            ])
            ->exec();

        return true;
    }

    /**
     * Сохраняет настройки чата
     *
     * @param $data
     * @return bool
     */
    public function saveChatSettings($data)
    {
        $chatSettings = new ChatSettings($this->user_id);
        $chatSettings->getFromArray($data);

        if ($chatSettings['user_id']) {
            $save_result = $chatSettings->update();
        } else {
            $chatSettings['user_id'] = $this->user_id;
            $save_result = $chatSettings->insert();
        }

        return $save_result;
    }

    /**
     * Сохраняет в базе данных вопрос
     *
     * @param mixed $text
     * @return ChatMessage
     */
    public function saveQuestion(mixed $text)
    {
        $message = new ChatMessage();
        $message['user_id'] = $this->user_id;
        $message['message'] = $text;
        $message['role'] = ChatMessage::ROLE_USER;
        $message->insert();

        return $message;
    }

    /**
     * Возвращает данные, необходимые для старта чата
     *
     * @return array
     */
    public function getChatStartData()
    {
        $messages = $this->getLastMessages();
        try {
            $balance = $this->getUserBalance();
        } catch (\Exception $e) {
            $balance = [
                'balance' => null,
                'balanceRefillUrl' => null,
            ];
        }

        return [
            'chat' => [
                'messages' => array_map(function($value) {
                    return $value->getValues();
                }, $messages),
                ...$balance
            ]
        ];
    }

    /**
     * Выполняет запрос к ИИ на генерацию ответа на вопрос
     *
     * @param bool $repeat Если true, значит нужно обновить последний ответ
     * @return \Traversable
     */
    public function generateAnswer($repeat = false)
    {
        $now = date('Y-m-d H:i:s');
        $service = ServiceApi::getDefaultChatService();

        $params = [
            'messages' => []
        ];
        $messages = $this->getLastMessages();
        foreach($messages as $index => $message) {

            if ($index + 1 == count($messages) && $repeat) {
                if ($message['role'] == ChatMessage::ROLE_ASSISTANT) {
                    $message->delete();
                    continue;
                }
            }

            if ($message['role'] != ChatMessage::ROLE_SYSTEM
                && $message['message'] != '') {
                $params['messages'][] = [
                    'role' => $message['role'],
                    'content' => $message['message']
                ];
            }
        }

        try {
            $stream = $service
                ->getServiceTypeObject()
                ->setStatisticParams([
                    'type' => Statistic::TYPE_CHAT,
                    'user_id' => Auth::getCurrentUser()->id,
                ])
                ->createChatStream($params);

            foreach ($stream as $data) {
                if ($data->isFinish()) {
                    //Записываем ответ в базу данных
                    $answer = new ChatMessage();
                    $answer['user_id'] = $this->user_id;
                    $answer['date_of_create'] = $now;
                    $answer['role'] = ChatMessage::ROLE_ASSISTANT;
                    $answer['message'] = $data->getFullText();
                    $answer->insert();
                }
                yield $data;
            }
        } catch (\Exception $e) {
            //Сохраняем ошибку как системное сообщение
            $answer = new ChatMessage();
            $answer['user_id'] = $this->user_id;
            $answer['date_of_create'] = $now;
            $answer['role'] = ChatMessage::ROLE_SYSTEM;
            $answer['message'] = $e->getMessage();
            $answer->insert();

            throw $e;
        }
    }

    /**
     * Возвращает баланс пользователя, если выбран GPT-сервис ReadyScript
     *
     * @return array
     */
    public function getUserBalance()
    {
        $service = ServiceApi::getDefaultChatService();
        $service_type = $service->getServiceTypeObject();

        if ($service_type instanceof BalanceInterface) {
            $balance = $service_type->getBalance();
            $balance_refill_url = $service_type->getBalanceRefillUrl();
        }

        return [
            'balance' => $balance ?? null,
            'balanceRefillUrl' => $balance_refill_url ?? null,
        ];
    }
}