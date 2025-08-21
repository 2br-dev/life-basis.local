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
use ExternalApi\Model\Validator\ValidateArray;
use Files\Model\Orm\File;

/**
 * Метод API - Обновляет сведения по прикрепленному к объекту файлу
 */
class Update extends AbstractAuthorizedMethod
{
    const RIGHT_UPDATE = 1;
    private $validator;

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
            self::RIGHT_UPDATE => t('Редактирование файлов')
        ];
    }

    /**
     * Возвращает валидатор данных для файла
     *
     * @return ValidateArray
     */
    protected function getFileDataValidator()
    {
        if ($this->validator === null) {
            $this->validator = new ValidateArray([
                'access' => [
                    '@title' => t('Уровень доступа'),
                    '@type' => 'string'
                ],
                'description' => [
                    '@title' => t('Описание'),
                    '@type' => 'string'
                ]
            ]);
        }

        return $this->validator;
    }

    /**
     * Форматирует комментарий, полученный из PHPDoc
     *
     * @param string $text - комментарий
     * @return string
     */
    protected function prepareDocComment($text, $lang)
    {
        $text = parent::prepareDocComment($text, $lang);

        $validator = $this->getFileDataValidator();
        $text = preg_replace_callback('/\#data-file-info/', function() use($validator) {
            return $validator->getParamInfoHtml();
        }, $text);

        return $text;
    }

    /**
     * Обновляет сведения по прикрепленному к объекту файлу
     *
     * @param string $token Авторизационный токен
     * @param string $uniq Уникальный строковый идентификатор файла
     * @param array $data Данные для обновления:
     * #data-file-info
     * @return array
     */
    function process($token, $uniq, $data)
    {
        $file = File::loadByUniq($uniq);
        if (!$file['id']) {
            throw new ApiException(t('Файл не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        $validator = $this->getFileDataValidator();
        $validator->validate('data', $data, $this->method_params);
        $allowed_access = array_keys($file->getLinkType()->getAccessTypes());

        if (isset($data['access']) && !in_array($data['access'], $allowed_access)) {
            throw new ApiException(t('Недопустимый тип доступа'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $update_data = array_intersect_key($data, array_flip(array_keys($validator->getSchema())));

        $file->getFromArray($update_data);
        if ($file->update()) {
            return [
                'response' => [
                    'success' => true
                ]
            ];
        } else {
            throw new ApiException(t('Не удалось обновить данные. %0', [$file->getErrorsStr()]), ApiException::ERROR_WRITE_ERROR);
        }
    }
}