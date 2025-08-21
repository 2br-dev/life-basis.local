<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Affiliate\Model\ExternalApi\Affiliate;

use Affiliate\Model\AffiliateApi;
use Affiliate\Model\Orm\Affiliate;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;

/**
 * Устанавливает текущий филиал, который потом будет использоваться в Вашем приложении
 */
class Set extends AbstractAuthorizedMethod
{
    protected $token_require = false;
    const RIGHT_LOAD = 1;

    /**
     * Возвращает комментарии к кодам прав доступа
     *
     * @return string[] - [
     *     КОД => КОММЕНТАРИЙ,
     *     КОД => КОММЕНТАРИЙ,
     *     ...
     * ]
     */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Установка текущего филиала')
        ];
    }


    /**
     * Устанавливает текущий филиал, который потом будет использоваться в Вашем приложении
     *
     * @param int $affiliate_id - id филиала
     * @param string $token - Авторизационный токен
     * @example GET /api/methods/affiliate.set?affiliate_id=1
     *
     * Ответ:
     * <pre>
     * {
     *  "response": {
     *     "success" : true
     *  }
     *}
     * </pre>
     *
     * @return array Возвращает true, если удалось установить текщуй филиал
     */
    protected function process($affiliate_id, $token = null)
    {
        $response = [];
        $api = new AffiliateApi();
        $api->setFilter('public', 1);
        /** @var Affiliate $affiliate */
        $affiliate = $api->getById($affiliate_id);
        if ($affiliate && $affiliate['id']) {
            AffiliateApi::setCurrentAffiliate($affiliate, true);
            $response['response']['success'] = true;
        } else {
            $response['response']['success'] = false;
        }

        return $response;
    }
}
