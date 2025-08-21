<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Comments\Model\ExternalApi\Comment;

use Catalog\Model\Orm\Product;
use Comments\Model\Orm\Comment;
use ExternalApi\Model\AbstractMethods\AbstractAdd;
use ExternalApi\Model\Exception as ApiException;
use RS\Config\Loader;

/**
* Добавляет комментарий
*/
class Add extends AbstractAdd
{
    public $use_post_keys = ['user_name', 'message', 'aid', 'rate', 'type'];

    /**
     * Форматирует комментарий, полученный из PHPDoc
     *
     * @param string $text - комментарий
     * @return string
     */
    protected function prepareDocComment($text, $lang)
    {
        $text = parent::prepareDocComment($text, $lang);

        //Валидатор для пользователя
        $validator = $this->getCommentValidator();
        $text = preg_replace_callback('/\#data-comment/', function() use($validator) {
            return $validator->getParamInfoHtml();
        }, $text);


        return $text;
    }

    /**
     * Возвращает валидатор для комментария
     *
     * @return \ExternalApi\Model\Validator\ValidateArray
     */
    private function getCommentValidator()
    {
        return new \ExternalApi\Model\Validator\ValidateArray([
            'aid' => [
                '@title' => t('Идентификатор объекта.'),
                '@type' => 'string',
                '@require' => true,
            ],
            'user_name' => [
                '@title' => t('Имя пользователя.'),
                '@type' => 'string',
                '@require' => true,
            ],
            'rate' => [
                '@title' => t('Оценка.'),
                '@type' => 'integer',
                '@require' => true,
            ],
            'message' => [
                '@title' => t('Отзыв.'),
                '@type' => 'string',
                '@require' => true,
            ],
        ]);
    }

    /**
     * Добавляет комментарий
     *
     * @param array $data поля комментария для сохранения #data-comment
     * @param string $client_name имя клиентского приложения
     * @param string $client_id id клиентского приложения
     * @param string $token Авторизационный токен
     * @return array
     * @throws ApiException
     * @throws \RS\Exception
     * @example GET /api/methods/brand.getList?filter[aid]=21196
     *
     * Ответ:
     * <pre>
     *     "response": {
     *          "success": true,
     *          "comment": {
     *              "id": 1,
     *              "type": "\\Catalog\\Model\\CommentType\\Product",
     *              "aid": 1,
     *              "__url__": {
     *                  "name": null,
     *                  "description": null,
     *                  "formtype": "input"
     *              },
     *              "dateof": "2022-04-08 11:41:53",
     *              "user_id": null,
     *              "__url_user__": {
     *                  "name": null,
     *                  "description": null,
     *                  "formtype": "input"
     *              },
     *              "user_name": "Иванов Иван",
     *              "message": "Все супер!",
     *              "moderated": 0,
     *              "rate": 5,
     *              "help_yes": null,
     *              "help_no": null,
     *              "ip": "127.0.0.1",
     *              "useful": null
     *          }
     *  }
     * </pre>
     *
     */
    protected function process($data, $client_name = null, $client_id = null, $token = null)
    {
        $save_data = $this->prepareData($data);
        $config = \RS\Config\Loader::byModule($this);

        if ($token && $current_user = $this->token->getUser()) {
            $save_data['user_id'] = $current_user['id'];
        }
        $this->object = $this->getOrmObject();
        if ($this->object->save(null, $save_data)) {
            return [
                'response' => [
                    'success' => true,
                    'need_moderate' => $config['need_moderate'],
                    $this->getObjectSectionName() => \ExternalApi\Model\Utils::extractOrm($this->object)
                ]
            ];
        }

        throw new ApiException(t($this->object->getErrorsStr()), ApiException::ERROR_WRITE_ERROR);
    }

    /**
     * Подготавливает данные для запроса на добавление комментария
     *
     * @param $data - данные
     * @return array
     */
    function prepareData($data)
    {
        $post_data = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $this->use_post_keys)) {
                $post_data[$key] = $value;
            }
        }

        return $post_data;
    }

    /**
     * Возвращает объект комментария
     *
     * @return Comment
     */
    public function getOrmObject()
    {
        $orm_obj = new Comment();
        $orm_obj['replace_by_ip'] = !Loader::byModule($this)['allow_more_comments'];
        return $orm_obj;
    }
}
