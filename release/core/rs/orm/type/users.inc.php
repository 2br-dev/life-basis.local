<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

use Users\Model\Orm\User;

/**
 * Тип поля - выбор пользователей
 */
class Users extends ArrayList
{
    protected $form_template = '%system%/coreobject/type/form/users.tpl';
    private $request_url;

    function __construct(array $options = null)
    {
        $this->view_attr = [
            'size' => 40,
            'placeholder' => $this->getSearchPlaceholder()
        ];
        parent::__construct($options);
    }

    /**
     * Устанавливает URL, который будет возвращать результат поиска
     *
     * @return void
     */
    function setRequestUrl($url)
    {
        $this->request_url = $url;
    }

    /**
     * Возвращает список выбранных пользователей
     *
     * @return User[]
     */
    public function getSelectedUsers()
    {
        $result = [];
        foreach($this->get() ?? [] as $user_id) {
            $result[] = new User($user_id);
        }
        return $result;
    }

    /**
     * Возвращает текст, который будет отображаться в поле поиска
     *
     * @return string
     */
    public function getSearchPlaceholder()
    {
        return t('e-mail, фамилия, организация');
    }

    /**
     * Возвращает URL, который будет возвращать результат поиска
     *
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->request_url ?: \RS\Router\Manager::obj()->getAdminUrl('ajaxEmail', [
            'groups' => $this->getUserGroups()
        ], 'users-ajaxlist');
    }

    /**
     * Возвращает класс иконки zmdi
     *
     * @return string
     */
    function getIconClass()
    {
        return 'account';
    }
}