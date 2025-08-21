<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Shipment;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Utils;
use ExternalApi\Model\Validator\ValidateArray;
use Shop\Model\Orm\Order;
use Shop\Model\ShipmentApi;
use ExternalApi\Model\Exception as ApiException;

/**
 * Создает один документ "отгрузка" для заказа
 */
class Add extends AbstractAuthorizedMethod
{
    const RIGHT_CREATE = 1;

    protected $validator;

    /**
     * Возвращает комментарии к кодам прав доступа
     *
     * @return [
     *     КОД => КОММЕНТАРИЙ,
     *     КОД => КОММЕНТАРИЙ,
     *     ...
     * ]
     */
    public function getRightTitles()
    {
        return [
            self::RIGHT_CREATE => t('Создание отгрузки'),
        ];
    }

    /**
     * Возвращает валидатор для входящих данных для отгрузки
     *
     * @return array
     */
    private function getDataScheme()
    {
        return [
            'order_id' => [
                '@title' => t('ID Заказа'),
                '@type' => 'integer',
                '@require' => true,
            ],
            'add_delivery' => [
                '@title' => t('Добавить доставку в отгрузку'),
                '@type' => 'bool',
            ],
            'create_receipt' => [
                '@title' => t('Выбить чек отгрузки'),
                '@type' => 'bool',
            ],
        ];
    }

    /**
     * Возвращает объект валидатора
     *
     * @return ValidateArray
     */
    public function getDataValidator()
    {
        if ($this->validator === null) {
            $this->validator = new ValidateArray($this->getDataScheme());
        }

        return $this->validator;
    }

    /**
     * Форматирует комментарий, полученный из PHPDoc
     *
     * @param string $text - комментарий
     * @return string
     */
    protected function prepareDocComment($text, $lang)
    {
        $text = parent::prepareDocComment($text, $lang);

        $validator = $this->getDataValidator();
        $text = preg_replace_callback('/\#data-info/', function() use($validator) {
            return $validator->getParamInfoHtml();
        }, $text);

        return $text;
    }

    /**
     * Валидирует значения для обновления
     *
     * @param array $data
     * @return array
     */
    public function validateData($data)
    {
        return $this->getDataValidator()->validate('data', $data, $this->method_params);
    }

    /**
     * Создает документ "Отгрузка" для заказа.
     * ---
     * Перед созданием отгрузки обязательно должен быть вызван метод shipment.beforeAdd, который возвращает данные
     * для создания новой отгрузки и формирования значения поля data.shipment[]
     *
     * При сканировании маркировок, должен вызываться метод order.addMarking,
     * после чего опять должен быть вызван метод shipment.beforeAdd, который вернет новые данные для создания отгрузки.
     *
     * @param string $token Авторизационный токен
     * @param array $shipment_data Данные для создания одной отгрузки #data-info
     *
     * @example POST /api/methods/shipment.add?token=311211047ab5474dd67ef88345313a6e479bf616&shipment_data[order_id]=1521&shipment_data[add_delivery]=1
     *
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true,
     *         "shipment": {
     *             "id": 36,
     *             "order_id": "1521",
     *             "date": "2024-07-02 21:55:16",
     *             "info_order_num": "406897",
     *             "info_total_sum": 69450
     *         }
     *     }
     * }
     * </pre>
     *
     * @return array Возвращает документ созданной отгрузки
     */
    public function process($token, $shipment_data)
    {
        $data = $this->validateData($shipment_data);

        $order = new Order($data['order_id']);
        if (!$order['id']) {
            throw new ApiException(t('Заказ с таким ID не найден'));
        }

        $api = new ShipmentApi();

        $items_for_shipment = $api->buildShipmentItemsArray($order);
        $shipment = $api->createShipment($order,
            $items_for_shipment,
            $data['add_delivery'] ?? false,
            $data['create_receipt'] ?? false);

        if ($shipment) {
            return [
                'response' => [
                    'success' => true,
                    'shipment' => Utils::extractOrm($shipment)
                ]
            ];
        } else {
            throw new ApiException($api->getErrorsStr(), ApiException::ERROR_INSIDE);
        }
    }
}