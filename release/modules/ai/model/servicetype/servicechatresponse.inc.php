<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\ServiceType;

/**
 * Объект содержит в себе сведения о результате выполнения одной итерации получения данных из потока (stream) GPT-сервиса.
 * Так как, каждый GPT-сервис может возвращать поток в разном формате. Его необходимно унифицировать для ReadyScript.
 * Все GPT сервисы в ReadyScript, должны возвращать поток объектов ServiceChatResponse.
 */
class ServiceChatResponse
{
    /**
     * Кусок текста, полученный от последней итерации
     * @var string
     */
    private string $delta_text;

    /**
     * Полный текст, полученный с начала потока
     *
     * @var string
     */
    private string $full_text;

    /**
     * Признак завершения потока. Если true, то это последнее сообщение
     *
     * @var bool
     */
    private bool $is_finish = false;

    /**
     * Любые произвольные данные, которые необходимо передать в браузер вместе с данным сообщением
     *
     * @var array
     */
    private array $extra_data = [];

    /**
     * Добавляет произвольные данные к данному сообщению
     *
     * @param string $key Произвольный ключ
     * @param mixed $value Произвольное значение
     * @return $this
     */
    public function addExtraData(string $key, $value)
    {
        $this->extra_data[$key] = $value;
        return $this;
    }

    /**
     * Устанавливает Произвольные данные
     *
     * @param array $data Произвольные данные
     * @return $this
     */
    public function setExtraData(array $data)
    {
        $this->extra_data = $data;
        return $this;
    }

    /**
     * Возвращает установленные произвольные данные
     *
     * @return array
     */
    public function getExtraData()
    {
        return $this->extra_data;
    }

    /**
     * Возвращает текст, полученный от последней итерации
     *
     * @return string
     */
    public function getDeltaText()
    {
        return $this->delta_text;
    }

    /**
     * Устанавливает текст, полученный от последней итерации
     *
     * @param string $delta_text Часть текста
     * @return $this
     */
    public function setDeltaText(string $delta_text)
    {
        $this->delta_text = $delta_text;
        return $this;
    }

    /**
     * Возвращает полный текст, полученный с начала потока
     *
     * @return string
     */
    public function getFullText()
    {
        return $this->full_text;
    }

    /**
     * Устанавливает полный текст, полученный с начала потока
     *
     * @param string $full_text Полный текст
     * @return $this
     */
    public function setFullText(string $full_text)
    {
        $this->full_text = $full_text;

        return $this;
    }

    /**
     * Возвращает true, если это последний элемент потока
     *
     * @return bool
     */
    public function isFinish()
    {
        return $this->is_finish;
    }

    /**
     * Устанавливает, является ли это последним элементом потока
     *
     * @param bool $is_finish
     * @return $this
     */
    public function setIsFinish(bool $is_finish)
    {
        $this->is_finish = $is_finish;
        return $this;
    }
}