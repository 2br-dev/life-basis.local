<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\Orm;

use MobileManagerApp\Model\AppTypes\StoreManagement;
use MobileManagerApp\Model\Push\ScanCode;
use PushSender\Model\PushTokenApi;
use RS\Application\Auth;
use RS\Exception;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use Shop\Model\ApiUtils;

/**
 * ORM-объект, содержащий информацию о запросе на сканирование
 * --/--
 * @property string $id Уникальный идентификатор (ID)
 * @property integer $user_id Пользователь, создатель запроса
 * @property string $date_of_create Дата создания
 * @property string $date_of_update Дата получения результата
 * @property string $formats Форматы сканирования
 * @property string $filter Тип обработки результата
 * @property string $status Статус
 * @property string $raw_result Результат сканирования
 * @property string $fail_reason Причина ошибки
 * --\--
 */
class ScanRequest extends OrmObject
{
    const REQUEST_LIFETIME = 172800; //48 часов, время жизни запроса

    const STATUS_WAITING = 'waiting';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    const FILTER_MARKING = 'marking';

    const FORMAT_AZTEC = 'AZTEC';
    const FORMAT_CODABAR = 'CODABAR';
    const FORMAT_CODE_39 = 'CODE_39';
    const FORMAT_CODE_93 = 'CODE_93';
    const FORMAT_CODE_128 = 'CODE_128';
    const FORMAT_DATA_MATRIX = 'DATA_MATRIX';
    const FORMAT_EAN8 = 'EAN_8';
    const FORMAT_EAN13 = 'EAN_13';
    const FORMAT_ITF = 'ITF';
    const FORMAT_PDF417 = 'PDF_417';
    const FORMAT_QR_CODE = 'QR_CODE';
    const FORMAT_UPC_A = 'UPC_A';
    const FORMAT_UPC_E = 'UPC_E';

    protected static $table = 'managerapp_scan_request';

    protected function _init()
    {
        $this->getPropertyIterator()->append([
            'id' => (new Type\Varchar())
                ->setDescription(t('Уникальный идентификатор (ID)'))
                ->setMaxLength(50)
                ->setAllowEmpty(false)
                ->setPrimaryKey(true),
            'user_id' => (new Type\User())
                ->setDescription(t('Пользователь, создатель запроса')),
            'date_of_create' => (new Type\Datetime())
                ->setDescription(t('Дата создания'))
                ->setIndex(true),
            'date_of_update' => (new Type\Datetime())
                ->setDescription(t('Дата получения результата')),
            'formats' => (new Type\Varchar())
                ->setMaxLength(30)
                ->setDescription(t('Форматы сканирования')),
            'filter' => (new Type\Varchar())
                ->setDescription(t('Тип обработки результата'))
                ->setMaxLength(50),
            'status' => (new Type\Enum(array_keys(self::getStatusTitles())))
                ->setDescription(t('Статус'))
                ->setIndex(true)
                ->setList([__CLASS__, 'getStatusTitles']),
            'raw_result' => (new Type\Text())
                ->setDescription(t('Результат сканирования')),
            'fail_reason' => (new Type\Varchar())
                ->setDescription(t('Причина ошибки'))
        ]);
    }

    /**
     * Обрабатывает действия перед сохранением
     *
     * @param $save_flag
     * @return void
     */
    public function beforeWrite($save_flag)
    {
        $this['date_of_update'] = date('Y-m-d H:i:s');
    }

    /**
     * Возвращает список возможных статусов
     *
     * @return array
     */
    public static function getStatusTitles()
    {
        return [
            self::STATUS_WAITING => t('Ожидание'),
            self::STATUS_SUCCESS => t('Успешно завершено'),
            self::STATUS_FAIL => t('Произошла ошибка')
        ];
    }

    /**
     * Возвращает список названий форматов
     *
     * @return array
     */
    public static function getFormatTitles()
    {
        return [
            self::FORMAT_AZTEC => 'AZTEC',
            self::FORMAT_CODABAR => 'CODABAR',
            self::FORMAT_CODE_39 => 'CODE 39',
            self::FORMAT_CODE_93 => 'CODE 93',
            self::FORMAT_CODE_128 => 'CODE 128',
            self::FORMAT_DATA_MATRIX => 'DATA MATRIX',
            self::FORMAT_EAN8 => 'EAN-8',
            self::FORMAT_EAN13 => 'EAN-13',
            self::FORMAT_ITF => 'ITF',
            self::FORMAT_PDF417 => 'PDF-417',
            self::FORMAT_QR_CODE => 'QR-CODE',
            self::FORMAT_UPC_A => 'UPC-A',
            self::FORMAT_UPC_E => 'UPC-E',
        ];
    }

    /**
     * Возвращает URL для установки результата сканирования
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        return RouterManager::obj()->getUrl('externalapi-front-apigate', [
            'method' => 'managerApp.setScanResult',
            'request_id' => $this['id']
        ], true);
    }

    /**
     * Создает новый объект с запросом на сканирование
     *
     * @param string|array $formats Форматы сканирования. Можно передать несколько форматов через запятую в виде строки
     * или в виде массива, где каждый элемент массива - один формат
     * @param integer $user_id ID пользователя, которому в приложение ReadyScript будет отправлено уведомление
     *
     * @return self
     * @throws Exception
     */
    public static function create($formats, $filter = null, $user_id = null)
    {
        $formats = array_map(function($format) {
            return mb_strtoupper(trim($format));
        }, explode(',', $formats));

        if ($bad_format = array_diff($formats, array_keys(self::getFormatTitles()))) {
            throw new Exception(t('Передан неизвестный формат для сканирования: %0', [implode(', ', $bad_format)]));
        }

        if ($filter && !in_array($filter, [self::FILTER_MARKING])) {
            throw new Exception(t('Неизвестный тип фильтра для сканирования: %0', [$filter]));
        }

        $user_id = $user_id ?? Auth::getCurrentUser()->id;
        if (!PushTokenApi::getPushTokensByUserIds([$user_id], StoreManagement::ID)) {
            throw new Exception(t('Не найден Push-токен для вашего пользователя. Возможно вы не авторизованы в приложении ReadyScript. Проверьте разрешение на отправку Push-уведомлений, перезапустите приложение.'));
        }

        self::clearOldRequests();

        $self = new static();
        $self['id'] = sha1(uniqid(mt_rand(), true));
        $self['date_of_create'] = date('Y-m-d H:i:s');
        $self['status'] = self::STATUS_WAITING;
        $self['formats'] = implode(',', $formats);
        $self['user_id'] = $user_id;
        $self['filter'] = $filter;

        return $self;
    }

    /**
     * Возвращает результат сканирования с учетом обработки данных
     *
     * @return string
     */
    public function getResult()
    {
        $result = htmlspecialchars_decode((string)$this['raw_result']);

        if ($this['filter'] && $this['filter'] == self::FILTER_MARKING) {
            $result = ApiUtils::prepareMobileDataMatrix($result);
        }
        return $result;
    }

    /**
     * Очищает старые запросы на сканирование из базы данных
     *
     * @return integer Возвращает количество удаленных записей
     */
    public static function clearOldRequests()
    {
        return Request::make()
            ->from(new static())
            ->where("date_of_update < '#date'", [
                'date' => date('Y-m-d H:i:s', time() - self::REQUEST_LIFETIME)
            ])
            ->exec()
            ->affectedRows();
    }

    /**
     * Отправляет Push-уведомление для начала сканирования в приложение
     *
     * @return bool
     * @throws \PushSender\Model\Exception
     */
    public function sendPush()
    {
        $push = new ScanCode();
        $push->init($this);
        if (!$push->send()) {
            throw new Exception($push->getProvider()->getError());
        }

        return true;
    }
}