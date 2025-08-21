<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Cargo;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Validator\ValidateArray;
use Shop\Model\OrderCargoApi;
use Shop\Model\Orm\Order;

/**
 * Сохраняет сведения о распределении заказа по грузовым местам
 */
class Save extends AbstractAuthorizedMethod
{
    const RIGHT_SAVE = 'save';
    private $validator_cargo;
    private $validator_cargo_item;

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
            self::RIGHT_SAVE => t('Сохранение грузовых мест')
        ];
    }

    /**
     * Возвращает допустимую структуру значений в переменной data.products,
     * в которой будут содержаться сведения для обновления
     *
     * @return ValidateArray
     */
    public function getCargoDataValidator()
    {
        if ($this->validator_cargo === null) {
            $this->validator_cargo = new ValidateArray([
                'id' => [
                    '@title' => t('ID грузового места. Отрицательное число для новых грузовых мест'),
                    '@type' => 'integer',
                    '@require' => true
                ],
                'title' => [
                    '@title' => t('Название грузового места'),
                    '@type' => 'integer',
                    '@require' => true
                ],
                'width' => [
                    '@title' => t('Ширина, мм'),
                    '@type' => 'integer',
                    '@require' => true
                ],
                'height' => [
                    '@title' => t('Высота, мм'),
                    '@type' => 'integer',
                    '@require' => true
                ],
                'dept' => [
                    '@title' => t('Глубина, мм'),
                    '@type' => 'integer',
                    '@require' => true
                ],
                'weight' => [
                    '@title' => t('Вес упаковки, грамм'),
                    '@type' => 'integer',
                ],
                'products' => [
                    '@title' => t('Товары'),
                    '@type' => 'array',
                ]
            ]);
        }

        return $this->validator_cargo;
    }


    /**
     * Возвращает допустимую структуру значений в переменной data.products,
     * в которой будут содержаться сведения для обновления
     *
     * @return ValidateArray
     */
    public function getCargoItemsDataValidator()
    {
        if ($this->validator_cargo_item === null) {
            $this->validator_cargo_item = new ValidateArray([
                'uniq' => [
                    '@title' => t('Уникальный код позиции в заказе'),
                    '@type' => 'string',
                    '@require' => true
                ],
                'uit_id' => [
                    '@title' => t('ID Маркировки или 0, если товар без маркировки'),
                    '@type' => 'integer',
                    '@require' => true,
                ],
                'amount' => [
                    '@title' => t('Количество'),
                    '@type' => 'float',
                    '@require' => true,
                ]
            ]);
        }

        return $this->validator_cargo_item;
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

        $validator_cargo = $this->getCargoDataValidator();
        $validator_cargo_item = $this->getCargoItemsDataValidator();

        $text = preg_replace_callback('/\#data-cargo-info/', function() use($validator_cargo) {
            return $validator_cargo->getParamInfoHtml();
        }, $text);

        $text = preg_replace_callback('/\#data-cargo-item-info/', function() use($validator_cargo_item) {
            return $validator_cargo_item->getParamInfoHtml();
        }, $text);

        return $text;
    }

    /**
     * Проверяет входящие данные
     *
     * @param array $data
     * @param Order $order
     * @return void
     */
    private function validateData(array $data, Order $order)
    {
        foreach($data as $j => $cargo) {
            $this->getCargoDataValidator()->validate('data.' . $j, $cargo, $this->method_params);

            if (!empty($cargo['products'])) {
                foreach ($cargo['products'] as $n => $item) {
                    $this->getCargoItemsDataValidator()->validate('data.'.$j.'.products.' . $n, $item, $this->method_params);
                }
            }
        }
    }

    /**
     * Трансформирует входящий массив данных в формат, необходимый для метода saveCargos
     *
     * @param array $raw_data
     * @return array
     */
    public function prepareCargosData($raw_data)
    {
        $result = [];
        foreach($raw_data as $cargo_item) {
            $products = [];
            foreach($cargo_item['products'] as $cargo_product_item) {
                $products[$cargo_product_item['uniq']][$cargo_product_item['uit_id']]['amount'] = $cargo_product_item['amount'];
            }

            $result[$cargo_item['id']] = [
                'title' => $cargo_item['title'],
                'width' => $cargo_item['width'],
                'height' => $cargo_item['height'],
                'dept' => $cargo_item['dept'],
                'weight' => $cargo_item['weight'],
                'products' => $products
            ];
        }

        return $result;
    }

    /**
     * Сохраняет сведения о распределении заказа по грузовым местам
     *
     * @param string $token Авторизационный токен
     * @param string $order_id ID заказа
     * @param array $data Данные о грузовых местах
     * #data-cargo-info
     *
     * Структура данных поля <b>data.products</b>:
     * #data-cargo-item-info
     *
     * @example POST /api/methods/cargo.save?token=311211047ab5474dd67ef88345313a6e479bf616&order_id=1563
     *
     * Тело запроса. Content-type:application/json
     * <pre>
     * {
        "data": [
            {
                "id": 60,
                "title": "Коробка 1",
                "width": "100",
                "height": "200",
                "dept": "300",
                "weight": "150",
                "products": [
                    {
                        "uniq":"0504db4321",
                        "uit_id":"56",
                        "amount": 1

                    },
                    {
                        "uniq":"0504db4321",
                        "uit_id":"57",
                        "amount":1
                    }
                ]
            }
        ]
     * }
     * </pre>
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "success": true
            }
        }
     * </pre>
     *
     * @return array
     */
    public function process($token, $order_id, $data)
    {
        $order = new Order($order_id);
        if (!$order['id']) {
            throw new ApiException(t('Заказ с ID %0 не найден', [$order_id]), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $this->validateData($data, $order);
        $cargos_data = $this->prepareCargosData($data);

        $cargo_api = new OrderCargoApi();
        if ($cargo_api->saveCargos($order['id'], $cargos_data)) {
            return [
                'response' => [
                    'success' => true
                ]
            ];
        } else {
            throw new ApiException($cargo_api->getErrorsStr());
        }
    }
}