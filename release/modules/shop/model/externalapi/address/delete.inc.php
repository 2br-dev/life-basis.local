<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Address;

use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\Exception as ApiException;
use Shop\Model\Orm\Address;

/**
 * Метод API, удаляющий адрес
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
            self::RIGHT_DELETE => t('Право скрытия/удаления адреса')
        ];
    }

    /**
     * Удаляет или скрывает адрес
     *
     * @param string $token
     * @param integer $address_id
     * @param bool $real_delete
     *
     * @example GET /api/methods/address.delete?token=311211047ab5474dd67ef88345313a6e479bf616&address_id=139
     *
     * <pre>
     * {
     *     "response": {
     *         "success": true
     *     }
     * }
     * </pre>
     *
     * @return array
     */
    public function process($token, $address_id, $real_delete = false)
    {
        $address = new Address($address_id);
        if (!$address['id']) {
            throw new ApiException(t('Адрес не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        if ($real_delete) {
            $result = $address->delete();
        } else {
            $address['deleted'] = 1;
            $result = $address->update();
        }

        if (!$result) {
            throw new ApiException(t('Не удалось удалить адрес: %0', [$address->getErrorsStr()]), ApiException::ERROR_WRITE_ERROR);
        }

        return [
            'response' => [
                'success' => true
            ]
        ];
    }
}