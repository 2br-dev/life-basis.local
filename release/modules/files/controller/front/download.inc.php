<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Controller\Front;

use ExternalApi\Model\Orm\AuthorizationToken;
use Files\Model\Orm\File;
use RS\Application\Auth;
use RS\Module\Manager;

/**
* Контроллер, отвечающий за отдачу файлов для закачки браузером
*/
class Download extends \RS\Controller\Front
{
    function actionIndex()
    {
        if (Manager::staticModuleExists('externalapi')) {
            $this->authorizeByMobileClient();
        }
        $uniq_hash = $this->url->get('uniq', TYPE_STRING);
        $uniq_name = $this->url->get('uniq_name', TYPE_STRING);

        $file = File::loadByWhere([
            ($uniq_hash ? 'uniq' : 'uniq_name') => $uniq_hash ?: $uniq_name
        ]);
        if (!$file['id']) {
            $this->e404(t('Файл с таким идентификатором не найден'));
        }
        
        //Администратор может скачивать файлы без проверки прав
        $linktype = $file->getLinkType();
        $group = $linktype->getNeedGroupForDownload($file);
        if ($group && ($this->user['id'] <= 0 || !$this->user->inGroup($group))) {
            return $this->authPage(t('Для загрузки файла недостаточно прав'), $this->url->selfUri());
        }
        
        if ($error = $file->getLinkType()->checkDownloadRightErrors($file)) {
            $this->app->showException(403, $error);
        }
        
        $mime = $file['mime'] ?: 'application/octet-stream';
        \RS\File\Tools::sendToDownload($file->getServerPath(), $file['name'], $mime);
        exit;
    }

    /**
     * Авторизация пользователя по токену из приложения
     *
     * @throws \RS\Exception
     */
    protected function authorizeByMobileClient()
    {
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
