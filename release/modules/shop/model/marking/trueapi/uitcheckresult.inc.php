<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Marking\TrueApi;

use Shop\Model\Orm\OrderItemUIT;

/**
 * Объект результата проверки одной маркировки
 */
class UitCheckResult
{
    const STATUS_OK = 'ok'; //Ошибок нет
    const STATUS_WARNING = 'warning'; //Есть ошибки от Честного знака
    const STATUS_INTERNAL_ERROR = 'internal_error'; //Не удалось проверить маркировку
    const STATUS_UNKNOWN = 'unknown'; //Данная маркировка не проходила проверку

    private OrderItemUit $uit;
    private array $check_data;
    private array $check_data_code;
    private array $valid_data_code;
    private $internal_error;
    private $status;

    /**
     * @param OrderItemUit $uit ID кода маркировки в ReadyScript
     * @param array $check_data Данные, полученные от сервиса "Честный знак" после проверки кода маркировки
     * @param string $internal_error Код внутренней ошибки, из-за которой не произошла проверка
     */
    function __construct(OrderItemUit $uit, array $check_data = [], string $internal_error = '')
    {
        $this->uit = $uit;
        $this->check_data = $check_data;
        $this->check_data_code = $check_data['code'] ?? [];
        $this->valid_data_code = [
            'found' => true,
            'valid' => true,
            'verified' => true,
            'utilised' => true,
            'realizable' => true,
            'grayZone' => false,
            'sold' => false,
            'isBlocked' => false,
            'message' => ''
        ];
        $this->internal_error = $internal_error;
    }

    /**
     * Возвращает статус проверки кода маркироваки
     *
     * @return string
     */
    public function getCheckStatus()
    {
        if ($this->status === null) {
            if ($this->isUnknownStatus()) {
                $this->status = self::STATUS_UNKNOWN;
            } elseif ($this->isWarning()) {
                $this->status = self::STATUS_WARNING;
            } elseif ($this->isInternalError()) {
                $this->status = self::STATUS_INTERNAL_ERROR;
            } else {
                $this->status = self::STATUS_OK;
            }
        }

        return $this->status;
    }

    /**
     * Возвращает статус в текстовом виде
     *
     * @return string
     */
    public function getCheckStatusTitle()
    {
        return self::getCheckStatusTitles()[$this->getCheckStatus()];
    }

    /**
     * Возвращает цвет фона для статуса
     *
     * @return string
     */
    public function getCheckStatusColor()
    {
        $colors = [
            self::STATUS_UNKNOWN => '#999999',
            self::STATUS_WARNING => '#f0ad4e',
            self::STATUS_INTERNAL_ERROR => '#d9534f',
            self::STATUS_OK => '#5cb85c',
        ];

        return $colors[$this->getCheckStatus()];
    }

    /**
     * Возвращает идентификатор ZMDI изображения для данного статуса проверки.
     * Используется в административной панели ReadyScript
     *
     * @return string
     */
    public function getCheckStatusRsIcon()
    {
        $icons = [
            self::STATUS_UNKNOWN => 'cloud-off',
            self::STATUS_WARNING => 'alert-triangle',
            self::STATUS_INTERNAL_ERROR => 'alert-triangle',
            self::STATUS_OK => 'check',
        ];

        return $icons[$this->getCheckStatus()];
    }

    /**
     * Возвращает список всех возможных названий статусов
     *
     * @return array
     */
    public static function getCheckStatusTitles()
    {
        return [
            self::STATUS_UNKNOWN => t('Проверка не проводилась'),
            self::STATUS_WARNING => t('Есть ошибки от честного знака'),
            self::STATUS_INTERNAL_ERROR => t('Не удалось проверить маркировку'),
            self::STATUS_OK => t('Проверка успешно пройдена')
        ];
    }

    /**
     * Возвращает true, если нет ошибок
     *
     * @return bool
     */
    public function isOk()
    {
        return !$this->isWarning()
            && !$this->isInternalError();
    }

    /**
     * Возвращает true, если имеются ошибки со стороны
     * результата проверки честного знака
     *
     * @return bool
     */
    public function isWarning()
    {
        if (!$this->check_data_code) return false;

        //Добавляем недостающие ключи
        $check_data = $this->check_data_code + $this->valid_data_code;

        return $check_data['found'] === false
            || $check_data['valid'] === false
            || $check_data['verified'] === false
            || $check_data['utilised'] === false
            || ($check_data['realizable'] === false && $check_data['grayZone'] === false)
            || $check_data['sold'] === true
            || $check_data['isBlocked'] === true
            || $check_data['message'] !== ''
            || (isset($check_data['expireDate']) && strtotime($check_data['expireDate']) <= time());
    }

    /**
     * Возвращает true, если статус маркировки неизвестен. Она не проходила проверку.
     * Нет сведений о результатах проверки
     *
     * @return bool
     */
    public function isUnknownStatus()
    {
        return !$this->check_data
               && $this->internal_error === '';
    }


    /**
     * Возвращает true, если имеются внутренние ошибки при проверке маркировки
     *
     * @return bool
     */
    public function isInternalError()
    {
        return $this->internal_error !== '';
    }

    /**
     * Возвращает результат проверки в виде строки
     *
     * @return string
     */
    public function getCheckText()
    {
        if ($this->isUnknownStatus()) {
            return t('Проверка не проводилась');
        }
        elseif ($this->isWarning()) {
            //Добавляем недостающие ключи
            $check_data = $this->check_data_code + $this->valid_data_code;

            $error = '';
            if ($check_data['found'] === false) {
                $error = t('Маркировка не найдена в базе Честного знака');
            }
            elseif ($check_data['valid'] === false) {
                $error = t('Структура кода маркировки не прошла проверку');
            }
            elseif ($check_data['verified'] === false) {
                $error = t('Проверка крипто-подписи завершилась с ошибкой');
            }
            elseif ($check_data['utilised'] === false) {
                $error = t('Некорректный статус маркировки. Маркировка не нанесена.');
            }
            elseif ($check_data['realizable'] === false && $check_data['grayZone'] === false) {
                $error = t('Маркировка в статусе, отличном от «В обороте»');
            }
            elseif ($check_data['sold'] === true) {
                $error = t('Маркировка выведена из оборота');
            }
            elseif ($check_data['isBlocked'] === true) {
                $error = t('Маркировка заблокирована');
            }
            elseif ($check_data['message'] === true) {
                $error = t('Ошибка: #0', [$check_data['message']]);
            }
            elseif ((isset($check_data['expireDate']) && strtotime($check_data['expireDate']) <= time())) {
                $error = t('Срок годности товара по данной маркировке истек');
            }

            if (!$error) {
                $error = t('Ошибка не определена. Обратитесь в поддержку ReadyScript.');
            }

            return t('Результат проверки в Честном знаке:<br> %0', [$error]);
        }
        elseif ($this->isInternalError()) {
            return $this->internal_error;
        }
        else {
            return t('Проверка успешно пройдена');
        }
    }

    /**
     * Возвращает данные, полученные от сервиса "Честный знак" после проверки
     *
     * @return array
     */
    public function getCheckData()
    {
        return $this->check_data;
    }

    /**
     * Возвращает код внутренней ошибки
     *
     * @return string
     */
    public function getInternalError()
    {
        return $this->internal_error;
    }

    /**
     * Возвращает объект кода маркировки
     *
     * @return OrderItemUIT
     */
    public function getUit()
    {
        return $this->uit;
    }

    /**
     * Возвращает JSON со сведениями о проверке маркировки
     *
     * @return string
     */
    public function exportJSON()
    {
        return json_encode([
            'check_data' => $this->getCheckData(),
            'internal_error' => $this->getInternalError()
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Возвращает объект текущего класса, созданный из JSON
     *
     * @param OrderItemUIT $uit Объект маркировки
     * @param string $json_string Строка в формате JSON
     *
     * @return static
     */
    public static function makeFromJSON(OrderItemUit $uit, $json_string)
    {
        $json = json_decode((string)$json_string, true) ?: [];
        $check_data = $json['check_data'] ?? [];
        $internal_error = $json['internal_error'] ?? '';

        return new static($uit, $check_data, $internal_error);
    }
}