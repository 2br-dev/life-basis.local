<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photo\Controller\front;

use Photo\Model\PhotoType\Exception;
use RS\Application\Auth;
use RS\Controller\AuthorizedFront;
use RS\File\Tools;
use RS\Module\Manager;
use ExternalApi\Model\Orm\AuthorizationToken;
use Photo\Model\PhotoApi;
use Photo\Model\PhotoType\AbstractType;
use Photo\Model\Orm\Image;

/**
 * Контроллер AJAX-загрузки файлов от пользователей.
 * Предполагаем, что загрузка будет происходить через плагин dropzone.js
 */
class Upload extends AuthorizedFront
{
    protected AbstractType $link_type;
    protected PhotoApi $photo_api;

    /**
     * Инициализирует контроллер
     * @throws Exception|\RS\Exception
     */
    public function init()
    {
        $link_type = $this->url->get('linkType', TYPE_STRING);
        $this->photo_api = new PhotoApi();
        $this->link_type = PhotoApi::getTypeClassInstance($link_type);
        $this->wrapOutput(false);
    }

    /**
     * Обрабатывает загрузку файла. Ожидается передача только одного файла в переменной file
     * @throws \RS\Exception
     */
    public function actionUpload()
    {
        if (!Auth::isAuthorize()) {
            $error = t('Запрещена загрузка изображений для неавторизованных пользователей');
        } elseif ($this->link_type->canClientUploadPhotos() || $this->user->isAdmin()) {
            $files = $this->url->files('file');
            $files_normalized = Tools::normalizeFilePost($files);

            if ($files_normalized) {
                $error = $this->link_type->checkUploadRightErrors($files_normalized);
                if ($error === false) {
                    $image = $this->photo_api->uploadImage($files_normalized[0], $this->link_type->getLinkType(), $this->link_type->getDefaultLinkId());
                    if ($image) {
                        return $this->result->setSuccess(true)->addSection([
                            'public_hash' => $image['uniq'],
                        ]);
                    } else {
                        $error = implode(',', $this->photo_api->getUploadError());
                    }
                }
            } else {
                $error = t('Фото не загружено');
            }
        } else {
            $error = t('Загрузка фото запрещена');
        }

        return $this->result
            ->setSuccess(false)
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
        $uniq = $this->url->get('public_hash', TYPE_STRING);
        $image = Image::loadByWhere([
            'uniq' => $uniq,
            'type' => $this->link_type->getLinkType(),
        ]);

        if ($image['id'] && $image->delete()) {
            return $this->result->setSuccess(true);
        } else {
            return $this->result->setSuccess(false)
                ->addSection([
                    'error' => $image->getErrorsStr()
                ]);
        }
    }
}