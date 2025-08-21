<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Cargo;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Utils;
use Shop\Model\CargoPresetApi;

/**
 * Возвращает список предустановленных коробок для грузовых мест
 */
class GetPresets extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;

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
            self::RIGHT_LOAD => t('Загрузка списка коробок')
        ];
    }

    /**
     * Возвращает список предустановленных коробок для грузовых мест.
     *
     * @param string $token Авторизационный токен
     * @example GET /api/methods/cargo.getPresets?token=311211047ab5474dd67ef88345313a6e479bf616
     * <pre>
     * {
            "response": {
                "presets": [
                    {
                        "id": "1",
                        "title": "Коробка 30x30",
                        "width": "300",
                        "height": "300",
                        "dept": "300",
                        "weight": "200"
                    },
                    {
                        "id": "2",
                        "title": "Коробка 20x20",
                        "width": "200",
                        "height": "200",
                        "dept": "200",
                        "weight": "100"
                    }
                ]
            }
        }
     * </pre>
     *
     * @return array
     */
    public function process($token)
    {
        $preset_api = new CargoPresetApi();
        $presets = $preset_api->getList();

        return [
            'response' => [
                'presets' => Utils::extractOrmList($presets)
            ]
        ];
    }
}