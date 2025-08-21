<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\User;
use ExternalApi\Model\AbstractMethods\AbstractMethod;
use Main\Model\DaDataApi;

/**
 * Возвращает подсказки для поля "адрес"
 */
class AutocompleteAddress extends AbstractMethod
{
    /**
     * Возвращает подсказки к адресу
     *
     * @param string $term Строка адреса
     * @param string $token Авторизационный token
     *
     * @example GET /api/methods/user.autocompleteAddress?term=красная
     * Ответ:
     * <pre>
     *[
     *  {
     *      "label": "ул Красная",
     *      "data": {
     *      "street": "ул Красная"
     *      }
     *  },
     *  {
     *      "label": "ул Красная",
     *      "data": {
     *      "street": "ул Красная",
     *      "zipcode": "111111"
     *      }
     *  },
     *  {
     *      "label": "ул Красная, д 2",
     *      "data": {
     *      "street": "ул Красная",
     *      "house": "2"
     *      }
     *  },
     *]
     *</pre>
     *
     * @return array Возвращает список адресов
     */
    protected function process($term, $token = null)
    {
        $order = \Shop\Model\Orm\Order::currentOrder();
        $address = $order->getAddress();

        $response = [];
        if ($term) {
            $query = $address->getCountry()['title'] . ', ' . $address->getRegion()['title'] . ', ' . $address->getCity()['title'] . ', ' . $term;

            foreach (DaDataApi::getInstance()->getAddressSuggestion($query) as $item) {
                if (!empty($item['data']['street_with_type'])) {
                    $label_parts = [];
                    $data_parts = [];
                    if (!empty($item['data']['city_district_with_type'])) {
                        $label_parts[] = $item['data']['city_district_with_type'];
                    }
                    $label_parts[] = $item['data']['street_with_type'];
                    $data_parts['street'] = $item['data']['street_with_type'];
                    if (!empty($item['data']['house'])) {
                        $label_parts[] = "{$item['data']['house_type']} {$item['data']['house']}";
                        $data_parts['house'] = $item['data']['house'];
                    }
                    if (!empty($item['data']['block'])) {
                        $label_parts[] = "{$item['data']['block_type']} {$item['data']['block']}";
                        $data_parts['block'] = $item['data']['block'];
                    }
                    if (!empty($item['data']['flat'])) {
                        $label_parts[] = "{$item['data']['flat_type']} {$item['data']['flat']}";
                        $data_parts['flat'] = $item['data']['flat'];
                    }
                    if (!empty($item['data']['postal_code'])) {
                        $data_parts['zipcode'] = $item['data']['postal_code'];
                    }

                    $response[] = [
                        'label' => implode(', ', $label_parts),
                        'data' => $data_parts,
                    ];
                }
            }
        }
        return $response;
    }
}
