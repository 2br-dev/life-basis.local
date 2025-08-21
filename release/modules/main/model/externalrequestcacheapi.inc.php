<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Model;

use Main\Model\Orm\ExternalRequestCache;
use Main\Model\Requester\ExternalRequest;
use Main\Model\Requester\ExternalResponse;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;

/**
 * API для кэша внешних запросов
 */
class ExternalRequestCacheApi extends EntityList
{
    const MAX_CACHABLE_RESPONSE_SIZE = 524288;

    function __construct()
    {
        parent::__construct(new ExternalRequestCache());
    }

    /**
     * Возвращает запись из кэша, или false
     *
     * @param ExternalRequest $request - объект внешнего запроса
     * @return bool|ExternalResponse
     */
    public static function loadResponseByRequest(ExternalRequest $request)
    {
        $cache = (new OrmRequest())
            ->from(new ExternalRequestCache())
            ->where([
                'source_id' => $request->getSourceId(),
                'request_url' => $request->getUrl(),
                'request_hash' => $request->getRequestHash(),
                'idempotence_key' => $request->getIdempotenceKey(),
            ])
            ->where('date > "#old"', [
                'old' => date('Y-m-d H:i:s', time() - $request->getCacheLifeTime())
            ])
            ->object();

        if (!empty($cache['date'])) {
            return new ExternalResponse((int)$cache['response_status'], unserialize((string)$cache['response_headers']), $cache['response_body']);
        } else {
            return false;
        }
    }

    /**
     * Создаёт запись в кэше
     *
     * @param ExternalRequest $request - объект внешнего запроса
     * @param ExternalResponse $response - объект ответа
     */
    public static function saveResponse(ExternalRequest $request, ExternalResponse $response)
    {
        self::clearOldCache($request);

        if (strlen($response->getRawResponse()) <= self::MAX_CACHABLE_RESPONSE_SIZE) {
            $params = $request->getParams() instanceof \SimpleXMLElement
                ? $request->getParams()->asXML() : $request->getParams();

            $cache = new ExternalRequestCache();
            $cache['date'] = date('Y-m-d H:i:s');
            $cache['source_id'] = $request->getSourceId();
            $cache['request_url'] = $request->getUrl();
            $cache['request_headers'] = serialize($request->getPreparedHeaders());
            $cache['request_params'] = serialize($params);
            $cache['request_hash'] = $request->getRequestHash();
            $cache['idempotence_key'] = $request->getIdempotenceKey();
            $cache['response_status'] = $response->getStatus();
            $cache['response_headers'] = serialize($response->getHeaders());
            $cache['response_body'] = $response->getRawResponse();
            $cache->insert();
        }
    }

    /**
     * Удаляет старые записи в кэше
     *
     * @param ExternalRequest $request объект внешнего запроса
     */
    protected static function clearOldCache(ExternalRequest $request)
    {
        if (rand(1, 10) == 1) {
            (new OrmRequest())
                ->delete()
                ->from(ExternalRequestCache::_getTable())
                ->where([
                    'source_id' => $request->getSourceId()
                ])
                ->where('date < "#old"', [
                    'old' => date('Y-m-d H:i:s', time() - $request->getCacheLifeTime())
                ])
                ->exec();
        }
    }

    /**
     * Удаляет все записи в кэше
     */
    public function clearCache()
    {
        (new OrmRequest())
            ->delete()
            ->from(ExternalRequestCache::_getTable())
            ->exec();
    }
}
