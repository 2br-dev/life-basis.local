<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\Push;

/**
 * Произвольное текстовое Push - уведомление, не ведущее ни на какую страницу приложения.
 */
class TextMessage extends AbstractPushToAdmin
{
    public $push_title;
    public $push_body;
    public $push_data = [];
    public $user_id;

    /**
     * Инициализирует Push-уведомление
     *
     * @param string $push_title
     * @param string $push_body
     * @param array $push_data
     * @param integer|null $user_id Если null, То сообщение будет отправлено всем администраторам
     *
     * @return void
     */
    public function init($push_title, $push_body, array $push_data, $user_id = null)
    {
        $this->push_title = $push_title;
        $this->push_body = $push_body;
        $this->user_id = $user_id;
        $this->push_data = $push_data;
    }

    /**
     * Возвращает описание уведомления для внутренних нужд системы и
     * отображения в списках админ. панели
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Другие уведомления');
    }

    /**
     * Возвращает одного или нескольких получателей
     *
     * @return array
     */
    public function getRecipientUserIds()
    {
        if ($this->user_id) {
            return [$this->user_id];
        } else {
            return parent::getRecipientUserIds();
        }
    }

    /**
     * Возвращает Заголовок для Push уведомления
     *
     * @return string
     */
    public function getPushTitle()
    {
        return $this->push_title;
    }

    /**
     * Возвращает текст Push уведомления
     *
     * @return string
     */
    public function getPushBody()
    {
        return $this->push_body;
    }

    /**
     * Возвращает произвольные данные ключ => значение, которые должны быть переданы с уведомлением
     *
     * @return array
     */
    public function getPushData()
    {
        return $this->push_data + [
                'user_id' => $this->user_id
            ];
    }
}