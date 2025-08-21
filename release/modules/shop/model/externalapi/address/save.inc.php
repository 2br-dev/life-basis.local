<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Address;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Utils;
use ExternalApi\Model\Validator\ValidateArray;
use ExternalApi\Model\Exception as ApiException;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Region;
use RS\Orm\Type;

/**
 * Метод API, создающий адрес для заказа или пользователя
 */
class Save extends AbstractAuthorizedMethod
{
    const RIGHT_SAVE = 1;

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
            self::RIGHT_SAVE => t('Создание/обновление адреса')
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
            'city_id' => [
                '@title' => t('ID города'),
                '@type' => 'string',
                '@require' => true,
            ],
            'user_id' => [
                '@title' => t('ID Пользователя'),
                '@type' => 'integer',
            ],
            'order_id' => [
                '@title' => t('Добавить доставку в отгрузку'),
                '@type' => 'integer',
            ],
            'zipcode' => [
                '@title' => t('Индекс'),
                '@type' => 'string',
            ],
            'address' => [
                '@title' => t('Адрес одной строкой'),
                '@type' => 'string',
            ],
            'street' => [
                '@title' => t('Улица'),
                '@type' => 'string',
            ],
            'house' => [
                '@title' => t('Дом'),
                '@type' => 'string',
            ],
            'block' => [
                '@title' => t('Корпус'),
                '@type' => 'string',
            ],
            'apartment' => [
                '@title' => t('Квартира'),
                '@type' => 'string',
            ],
            'entrance' => [
                '@title' => t('Подъезд'),
                '@type' => 'string',
            ],
            'entryphone' => [
                '@title' => t('Домофон'),
                '@type' => 'string',
            ],
            'floor' => [
                '@title' => t('Этаж'),
                '@type' => 'string',
            ],
            'subway' => [
                '@title' => t('Станция метро'),
                '@type' => 'string',
            ],
            'extra' => [
                '@title' => t('Дополнительные данные'),
                '@type' => 'array',
            ]
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
     * Создает или обновляет объект адреса
     * ---
     * Адрес сразу может быть связан с заказом или пользователем
     *
     * @param string $token Авторизационный токен
     * @param array $address_data Данные для создания адреса #data-info
     * @param int $address_id ID адреса, если нужно обновить уже существующий адрес
     *
     * @example POST /api/methods/address.save?token=311211047ab5474dd67ef88345313a6e479bf616&address_data[city_id]=9&address_data[address]=Ленина,25
     *
     * <pre>
     * {
     *       "response": {
     *           "success": true,
     *           "address": {
     *               "id": 319,
     *               "user_id": 0,
     *               "order_id": 0,
     *               "zipcode": null,
     *               "country": "Россия",
     *               "region": "Адыгея республика",
     *               "city": "Адыгейск",
     *               "address": "Ленина,25",
     *               "street": null,
     *               "house": null,
     *               "block": null,
     *               "apartment": null,
     *               "entrance": null,
     *               "entryphone": null,
     *               "floor": null,
     *               "subway": null,
     *               "city_id": "9",
     *               "region_id": 2,
     *               "country_id": 1,
     *               "deleted": 0,
     *               "extra": [],
     *               "_extra": "[]",
     *               "coords": ""
     *           }
     *       }
     *   }
     * </pre>
     *
     * @return array Возвращает объект созданного адреса
     */
    public function process($token, $address_data, $address_id = null)
    {

        if (!$address_data['city_id'] && $address_data['city'] && $address_data['region_id'] && $address_data['country_id']) {
            $city_by_name = Region::loadByWhere([
                'is_city' => 1,
                'parent_id' => $address_data['region_id'],
                'title' => $address_data['city']
            ]);
            if ($city_by_name['id']) {
                $address_data['city_id'] = $city_by_name['id'];
            }
        }

        if ($address_data['city_id']) {
            $city = new Region($address_data['city_id']);

            $region = $city->getParent();
            $country = $region->getParent();
        }else {
            $region = new Region($address_data['region_id'] ?? null);
            $country = new Region($address_data['country_id'] ?? null);
        }

        if (!$country['id']) {
            throw new ApiException(t('Должен быть выбран именно город, а не область или страна'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $address_data['region_id'] = $region['id'];
        $address_data['country_id'] = $country['id'];
        $address_data['id'] = $address_id;

        $address = new Address();
        if ($address->save($address_id, [], $address_data)) {
            (new Address())
                ->getPropertyIterator()->append([
                    'short_view' => (new Type\Varchar())
                        ->setVisible(true),
                    'full_view' => (new Type\Varchar())
                        ->setVisible(true),
                ]);
            $address['short_view'] = $address->getLineView(false);
            $address['full_view'] = $address->getLineView(true);
            return [
                'response' => [
                    'success' => true,
                    'address' => Utils::extractOrm($address)
                ]
            ];
        }

        throw new ApiException(t('Не удалось сохранить адрес. %0', [$address->getErrorsStr()]), ApiException::ERROR_INSIDE);
    }
}