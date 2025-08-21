<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\Router;

use ExternalApi\Config\File;
use RS\Http\Request;
use RS\Router\Route;

/**
 * Маршрут для внешнего API
 */
class ApiRoute extends Route
{

    /**
     * Возвращает Uri с нужными параметрами.
     * В случае, если не передан параметр api_key, подставляет его автоматически
     *
     * @param array $params параметры для uri
     * @param bool $absolute если true, то вернет абсолютный путь
     * @param mixed $mask_key индекс маски по которой будет строиться url, если не задан, то будет определен автоматически
     */
    public function buildUrl($params = [], $absolute = false, $mask_key = null)
    {
        $params += [
            //Добавляем по умолчанию API-ключ, либо из настроек модуля, либо из виртуального приложения
            'api_key' => Request::commonInstance()->get('api_key', TYPE_STRING, File::config()->api_key)
        ];

        if ($params['api_key'] == '') {
            unset($params['api_key']);
        }
        return parent::buildUrl($params, $absolute, $mask_key);
    }
}