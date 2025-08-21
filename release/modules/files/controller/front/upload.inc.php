<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Controller\Front;

use ExternalApi\Model\Orm\AuthorizationToken;
use Files\Model\FileApi;
use Files\Model\FilesType\AbstractType;
use Files\Model\Orm\File;
use RS\Application\Auth;
use RS\Controller\AuthorizedFront;
use RS\Module\Manager;

/**
 * Контроллер AJAX-загрузки файлов от пользователей.
 * Предполагаем, что загрузка будет происходить через плагин dropzone.js
 */
class Upload extends AuthorizedFront
{
    /**
     * @var AbstractType
     */
    protected $link_type;
    /**
     * @var $file_api FileApi
     */
    protected $file_api;

    /**
     * Инициализирует контроллер
     */
    public function init()
    {
        $this->authorizeByMobileClient();
        $link_type_id = $this->url->get('linkType', TYPE_STRING);
        $this->file_api = new FileApi();
        $this->link_type = FileApi::getTypeClassInstance($link_type_id);
        $this->wrapOutput(false);
    }

    /**
     * Обрабатывает загрузку файла. Ожидается передача только одного файла в переменной file
     */
    public function actionUpload()
    {
        $this->addClientSiteAppHeaders();
        if ($this->link_type->canClientUploadFiles() || $this->user->isAdmin()) {
            $file = $this->url->files('file');
            $results = $this->file_api->uploadFromPost($file, $this->link_type, $this->link_type->getDefaultLinkId());

            if ($results) {
                $result = $results[0];
                if ($result['success']) {
                    /**
                     * @var $file File
                     */
                    $file = $result['file'];
                    return $this->result->setSuccess(true)->addSection([
                        'public_hash' => $file['uniq'],
                        'link' => $file->getHashedUrl()
                    ]);
                } else {
                    $error = $result['error'];
                }
            } else {
                $error = t('Файл не был загружен');
            }
        } else {
            $error = t('Клиенты не могут загружать файлы для данного объекта');
        }

        return $this->result->setSuccess(false)
            ->addSection([
                'error' => $error
            ]);
    }

    /**
     * Удаляет загруженный ранее файл.
     * Удалить файл может только тот, кто его загрузил
     */
    public function actionRemove()
    {
        $this->addClientSiteAppHeaders();
        $uniq = $this->url->get('public_hash', TYPE_STRING);
        $file = File::loadByWhere([
            'uniq' => $uniq,
            'link_type_class' => $this->link_type->getShortName(),
        ]);

        if ($file['id'] && $file->delete()) {
            return $this->result->setSuccess(true);
        } else {
            return $this->result->setSuccess(false)
                ->addSection([
                    'error' => $file->getErrorsStr()
                ]);
        }
    }

    /**
     * Авторизация пользователя по токену из приложения
     *
     * @throws \RS\Exception
     */
    protected function authorizeByMobileClient()
    {
        if (Manager::staticModuleExists('externalapi')) {
            $auth_token = $this->url->request('auth_token', TYPE_STRING);

            $token = AuthorizationToken::loadByWhere([
                'token' => $auth_token
            ]);

            if ($token['user_id']) {
                Auth::setCurrentUser($token->getUser());
                $this->user = clone $token->getUser();
            }
        }
    }

    /**
     * Добавляет заголовки при запросе от приложения
     *
     * @return void
     */
    protected function addClientSiteAppHeaders()
    {
        if (Manager::staticModuleExists('externalapi')) {
            $clientsiteapp = $this->url->request('clientsiteapp', TYPE_FLOAT);
            $client_version = $this->url->request('client_version', TYPE_STRING);
            $client_name = $this->url->request('client_name', TYPE_STRING);

            if ($clientsiteapp && $client_version && $client_name) {
                $origin = \ExternalApi\Model\ApiRouter::getOriginForRequest($client_name, $client_version);
                $this->app->headers
                    ->addHeader('Access-Control-Allow-Origin', $origin)
                    ->addHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
                    ->addHeader('Access-Control-Allow-Credentials', 'true')
                    ->addHeader('Access-Control-Allow-Headers', '*, x-client-name, x-client-version')
                    ->addHeader('Content-type', 'application/json; charset=utf-8');
            }
        }
    }
}