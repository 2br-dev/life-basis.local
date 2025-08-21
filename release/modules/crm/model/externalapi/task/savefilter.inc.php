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
use ExternalApi\Model\Utils;
use ExternalApi\Model\Validator\ValidateArray;

/**
 * Класс, реализует метод сохранения фильтра
 */
class SaveFilter extends AbstractAuthorizedMethod
{
    const RIGHT_SAVE = 1;
    private $validator_filter;

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
            self::RIGHT_SAVE => t('Сохранение фильтра')
        ];
    }

    /**
     * Возвращает допустимую структуру значений в переменной data.products,
     * в которой будут содержаться сведения для обновления
     *
     * @return ValidateArray
     */
    public function getDataValidator()
    {
        if ($this->validator_filter === null) {
            $this->validator_filter = new ValidateArray([
                'id' => [
                    '@title' => t('ID фильтра для обновления. Если не задан, то создается новый фильтр'),
                    '@type' => 'integer',
                ],
                'title' => [
                    '@title' => t('Название фильтра'),
                    '@type' => 'integer',
                    '@require' => true
                ],
                'filters_arr' => [
                    '@title' => t('Фильтры, ключ => значение'),
                    '@type' => 'array',
                    '@require' => true
                ]
            ]);
        }

        return $this->validator_filter;
    }


    /**
     * Проверяет входящие данные
     *
     * @param array $data
     * @return void
     */
    private function validateData(array $data)
    {
        $this->getDataValidator()->validate('data', $data, $this->method_params);

        if (empty($data['filters_arr'])) {
            throw new ApiException(t('Не выбран ни один фильтр'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
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
        $validator_data = $this->getDataValidator();

        $text = preg_replace_callback('/\#data-info/', function() use($validator_data) {
            return $validator_data->getParamInfoHtml();
        }, $text);

        return $text;
    }

    /**
     * Создает или обновляет пресет для фильтров по задачам
     *
     * @param string $token Авторизационный токен
     * @param array $data Данные одного пресета
     * #data-info
     *
     * @example POST /api/methods/task.saveFilter?token=311211047ab5474dd67ef88345313a6e479bf616
     *
     * Тело запроса:
     * <pre>{
     *       "data": {
     *          "id": 27,
     *          "title": "Исполнитель - Я",
     *           "filters_arr": {
     *               "implementer_user_id": 2
     *           }
     *       }
     *}
     * </pre>
     * Ответ:
     *
     * <pre>{
     *       "response": {
     *           "success": true,
     *           "filter": {
     *               "id": "27",
     *               "title": "Исполнитель - Я",
     *               "filters_arr": {
     *                   "implementer_user_id": 2
     *               }
     *           }
     *       }
     *}
     * </pre>
     * @return array
     */
    public function process($token, $data)
    {
        $this->validateData($data);
        $task_filter = new TaskFilter();

        if (!empty($data['id'])) {
            if ($task_filter->load((int)$data['id'])) {
                if ($task_filter['user_id'] != $this->token['user_id']) {
                    throw new ApiException(t('У вас нет прав на сохранение фильтра с id `%0`', [(int)$data['id']]), ApiException::ERROR_WRONG_PARAM_VALUE);
                }
            } else {
                throw new ApiException(t('Фильтр с id `%0` не найден', [(int)$data['id']]), ApiException::ERROR_OBJECT_NOT_FOUND);
            }
        }

        $task_filter['user_id'] = $this->token['user_id'];
        $task_filter['title'] = $data['title'];
        $task_filter['filters_arr'] = $data['filters_arr'];

        if (!empty($data['id'])) {
            $result = $task_filter->update();
        } else {
            $result = $task_filter->insert();
        }

        if ($result) {
            return [
                'response' => [
                    'success' => true,
                    'filter' => Utils::extractOrm($task_filter)
                ]
            ];
        }

        throw new ApiException($task_filter->getErrorsStr(), ApiException::ERROR_WRITE_ERROR);
    }
}