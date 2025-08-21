<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace MobileSiteApp\Model\ExternalApi\MobileSiteApp;

use Catalog\Model\Orm\Dir;
use ExternalApi\Model\Exception as ApiException;
use Mobilesiteapp\Model\OnBoardingApi;
use RS\Config\Loader;
use RS\Orm\Request;
use Shop\Config\File as ShopConfig;
use Shop\Model\Orm\Region;

/**
* Возвращает параметры для специального блока
*/
class GetSpecialBlock extends \ExternalApi\Model\AbstractMethods\AbstractMethod
{
    /**
     * Возвращает параметры для блока со спец.категориями на главном экране
     *
     * @param string $token Авторизационный токен
     *
     * @example GET /api/methods/mobilesiteapp.getSpecialBlock
     *
     * Ответ:
     * <pre>
     * </pre>
     *
     * @return array
     * @throws \RS\Exception
     */
    protected function process($token = null)
    {
        $config = Loader::byModule($this);
        $response['response'] = [];

        if ($config['promo_special_dirs']) {
            $q = Request::make()
                ->from(new Dir())
                ->whereIn('id', $config['promo_special_dirs']);

            $list = \ExternalApi\Model\Utils::extractOrmList($q->objects()); //Преобразуем список

            $response['response']['special_dirs'] = $list;
        }

                  
        return $response;
    }
}
