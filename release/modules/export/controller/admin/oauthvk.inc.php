<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Controller\Admin;

use Export\Model\Orm\ExportProfile;
use RS\Router\Manager;

/**
 * Обрабатывает входящий запрос от ВК, для получения accessToken из code
 */
class OauthVK extends \RS\Controller\Admin\Front
{

    /**
     * Производим запись токена в профиль экспорта данных
     *
     * @throws \RS\Event\Exception
     */
    function actionSetApi()
    {
        $code = $this->url->get('code', TYPE_STRING);
        $profile_id = $this->url->get('profile_id', TYPE_INTEGER);
        $device_id = $this->url->get('device_id', TYPE_STRING);

        $profile = new ExportProfile($profile_id);
        $profile_type = $profile->getTypeObject();
        $data = $profile['data'];


        $router = Manager::obj();
        $url = $router->getAdminUrl('SetApi', ['profile_id' => $profile_id], 'export-oauthvk', true);

        if ($profile['id']) {
            $code_verifier = sha1(\Setup::$SECRET_SALT . 'VK-CODE-VERIFIER');
            $postdata = http_build_query([
                'grant_type' => 'authorization_code',
                'code_verifier' => $code_verifier,
                'redirect_uri' => $url,
                'code' => $code,
                'client_id' => $profile_type->getAppClientId(),
                'device_id' => $device_id,
                'state' => md5(sha1(\Setup::$SECRET_SALT . 'VK-STATE'))
            ]);

            $opts = ['http' =>
                [
                    'method'  => 'POST',
                    'header'  => 'Content-Type: application/x-www-form-urlencoded'."\r\n"
                                 ."Content-Length: " . strlen($postdata),
                    'content' => $postdata,
                ]
            ];
            $context  = stream_context_create($opts);

            $response = file_get_contents('https://id.vk.com/oauth2/auth', false, $context);
            if ($response) {
                $auth_response = @json_decode($response, true);
                if ($auth_response) {
                    if (isset($auth_response['access_token'])) {
                        $access_token = $auth_response['access_token'];

                        $data['access_token'] = $access_token;
                        $profile['data'] = $data;
                        $profile->update();

                        $this->app->redirect($this->router->getAdminUrl(false, [], 'export-ctrl'));
                    } elseif (isset($auth_response['error'])) {
                        return $auth_response['error'].':'.$auth_response['error_description'];
                    }
                }
            }
        }
    }
}