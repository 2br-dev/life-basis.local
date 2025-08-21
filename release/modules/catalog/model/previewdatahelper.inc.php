<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use Users\Model\Api as UserApi;

/**
 * Помощник формирования данных для предпросмотра. Используется во внешних API.
 * У каждого объекта в списках - есть краткая таблица наиболее важных значений,
 * которая формируется на основе данных, подготавливаемых этим классом
 */
class PreviewDataHelper
{
    const TYPE_TEXT = 'text';
    const TYPE_LINK = 'link';
    const TYPE_PHONE = 'phone';
    const TYPE_EMAIL = 'email';

    protected $preview_data = [];

    /**
     * Добавляет текстовую строку данных
     *
     * @param string $title Название параметра
     * @param string $value Значение параметра
     * @return $this
     */
    public function addTextRow($title, $value)
    {
        $this->preview_data[] = [
            'title' => $title,
            'value' => $value,
            'type' => self::TYPE_TEXT
        ];

        return $this;
    }

    /**
     * Добавляет строку со ссылкой
     *
     * @param string $title Название параметра
     * @param string $value Значение параметра
     * @param string $link Ссылка, при клике на значение параметра
     * @return $this
     */
    public function addLinkRow($title, $value, $link)
    {
        $this->preview_data[] = [
            'title' => $title,
            'value' => $value,
            'type' => self::TYPE_LINK,
            'data' => [
                'link' => $link
            ]
        ];

        return $this;
    }

    /**
     * Добавляет строку с телефоном
     *
     * @param string $title Название параметра
     * @param string $phone Телефон
     * @return $this
     */
    public function addPhoneRow($title, $phone)
    {
        $this->preview_data[] = [
            'title' => $title,
            'value' => $phone,
            'type' => self::TYPE_PHONE,
            'data' => [
                'link' => 'tel:'.UserApi::normalizePhoneNumber($phone)
            ]
        ];

        return $this;
    }

    /**
     * Добавляет строку с email
     *
     * @param string $title Название параметра
     * @param string $email Почта
     * @return $this
     */
    public function addEmailRow($title, $email)
    {
        $this->preview_data[] = [
            'title' => $title,
            'value' => $email,
            'type' => self::TYPE_EMAIL,
            'data' => [
                'link' => 'mailto:'.$email
            ]
        ];

        return $this;
    }

    /**
     * Возвращает массив данных для предпросмотра
     *
     * @return array
     */
    public function getPreviewData()
    {
        return $this->preview_data;
    }

    /**
     * Очищает все ранее добавленные строки
     *
     * @return $this
     */
    public function clean()
    {
        $this->preview_data = [];
        return $this;
    }
}