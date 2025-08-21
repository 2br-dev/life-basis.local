<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Comments\Model\ExternalApi\Comment;

use Comments\Model\Api;
use ExternalApi\Model\AbstractMethods\AbstractMethod;
use ExternalApi\Model\Utils;
use RS\Config\Loader;

/**
* Проверяет может ли пользователь оставить комментарий
*/
class Check extends AbstractMethod
{
    /**
     * Проверяет возможность оставить комментарий
     *
     * @param string $id Идентификатор товара
     * @param string $token Авторизационный токен
     * @return array
     * @example GET /api/methods/comment.check?id=1
     *
     * Ответ:
     *
     * <pre>{
     * "response": {
     *      "success": true,
     *      "need_auth": false
     * }
     * </pre>
     *
     */
    protected function process($id, $token = null)
    {
        $config = Loader::byModule('comments');
        $ip = $_SERVER['REMOTE_ADDR'];

        $result['response'] = [
            'success' => true,
            'need_auth' => false,
        ];

        if (!$config['allow_more_comments'] && $comments_count = $this->getCommentItem($ip, $id)) {
            $result['response']['success'] = false;
            $result['response']['comments_count'] = $comments_count;
        }
        
        if (!$token && $config['need_authorize'] == 'Y') {
            $result['response']['success'] = false;
            $result['response']['need_auth'] = true;
        }

        return $result;
    }
    
    private function getCommentItem($ip, $aid)
    {
        $api = new Api();
        $api->setFilter(['ip' => $ip, 'aid' => $aid]);
        
        $comments_count = $api->getListCount();
        
        return $api->getListCount();
    }
}
