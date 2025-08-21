<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Model\ExternalApi\File;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use Files\Model\Orm\File;

/**
 * Метод API - Удаляет один привязанный к объекту файл
 */
class Delete extends AbstractAuthorizedMethod
{
    const RIGHT_DELETE = 1;

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
            self::RIGHT_DELETE => t('Удаление файлов')
        ];
    }

    /**
     * Удаляет один привязанный к объекту файл
     *
     * @param string $token Авторизационный токен
     * @param string $uniq Уникальный строковый идентификатор файла
     * @return array
     */
    function process($token, $uniq)
    {
        $file = File::loadByUniq($uniq);
        if (!$file['id']) {
            throw new ApiException(t('Файл не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        if ($file->delete()) {
           return [
               'response' => [
                   'success' => true
               ]
           ];
        } else {
           throw new ApiException(t('Не удалось удалить файл. %0', [$file->getErrorsStr()]), ApiException::ERROR_WRITE_ERROR);
        }

    }
}