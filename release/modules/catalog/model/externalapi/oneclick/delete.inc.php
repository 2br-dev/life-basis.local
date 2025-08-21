<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\ExternalApi\OneClick;

use Catalog\Model\Orm\OneClickItem;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;

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
            self::RIGHT_DELETE => t('Удаление покупки в 1 клик')
        ];
    }

    /**
     * Удаляет одну покупку в 1 клик
     *
     * @param string $token Авторизационный токен
     * @param integer $one_click_id ID покупки в 1 клик
     *
     * @return array
     * @example POST api/methods/oneclick.delete?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&one_click_id=1
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "success": true
            }
        }
     * </pre>
     */
    public function process($token, $one_click_id)
    {
        $one_click = new OneClickItem($one_click_id);
        if (!$one_click['id']) {
            throw new ApiException(t('Покупка в 1 клик не найдена'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        if (!$one_click->delete()) {
            throw new ApiException(t('Ошибка удаления: %0', [$one_click->getErrorsStr()]), ApiException::ERROR_WRITE_ERROR);
        }

        return [
            'response' => [
                'success' => true
            ]
        ];
    }
}