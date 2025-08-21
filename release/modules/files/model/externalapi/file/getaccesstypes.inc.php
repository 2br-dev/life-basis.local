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
use Files\Model\FileApi;
use RS\Exception;

class GetAccessTypes extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;

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
            self::RIGHT_LOAD => t('Загрузка списка уровней доступа к файлам')
        ];
    }

    /**
     * Возвращает список уровней доступа к файлам
     *
     * @param string $token Авторизационный токен
     * @param string $file_type Тип файлов
     *
     * @return array
     * @example GET /api/methods/file.getAccessTypes?token=311211047ab5474dd67ef88345313a6e479bf616&file_type=files-shoporder
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "types": [
                    {
                        "id": "hidden",
                        "title": "скрытый",
                        "hint": ""
                    },
                    {
                        "id": "visible",
                        "title": "публичный",
                        "hint": ""
                    },
                    {
                        "id": "afterpay",
                        "title": "доступен после оплаты",
                        "hint": "Ссылка на скачивание данного файла будет доступна в разделе личного кабинета - Мои заказы"
                    }
                ]
            }
        }
     * </pre>
     */
    function process($token, $file_type)
    {
        try {
            $type = FileApi::getTypeClassInstance($file_type);

            $types = [];
            foreach($type::getAccessTypes() as $id => $data) {
                $types[] = [
                    'id' => $id,
                    'title' => is_array($data) ? $data['title'] : $data,
                    'hint' => is_array($data) ? $data['hint'] : ''
                ];
            }

            return [
                'response' => [
                    'types' => $types
                ]
            ];
        } catch (Exception $e) {
            throw new APIException($e->getMessage(), ApiException::ERROR_OBJECT_NOT_FOUND);
        }
    }
}