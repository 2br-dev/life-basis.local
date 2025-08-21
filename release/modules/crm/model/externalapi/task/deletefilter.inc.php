<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\ExternalApi\Task;

use Crm\Model\Orm\TaskFilter;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;

/**
 * Удаляет заранее сохраненный фильтр
 */
class DeleteFilter extends AbstractAuthorizedMethod
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
            self::RIGHT_DELETE => t('Удаление фильтров')
        ];
    }

    /**
     * Удаляет сохраненный фильтр
     *
     * @param string $token Авторизационный токен
     * @param integer $filter_id Идентификатор фильтра
     *
     * @return array
     */
    public function process($token, $filter_id)
    {
        $task_filter = TaskFilter::loadByWhere([
            'user_id' => $this->token['user_id'],
            'id' => $filter_id
        ]);

        if (!$task_filter['id']) {
            throw new ApiException(t('Фильтр не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        if (!$task_filter->delete()) {
            throw new ApiException($task_filter->getErrorsStr(), ApiException::ERROR_WRITE_ERROR);
        }

        return [
            'response' => [
                'success' => true,
            ]
        ];
    }
}