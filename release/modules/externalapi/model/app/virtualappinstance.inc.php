<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\App;

use ExternalApi\Model\Orm\VirtualApp;

/**
 * Класс описывает виртуальное приложение, создаваемое
 * на основе данных ORM объекта VirtualApp
 */
class VirtualAppInstance extends AbstractAppType
{
    protected $virtual_app;

    function __construct(VirtualApp $virtual_app)
    {
        $this->virtual_app = $virtual_app;
    }

    /**
     * Возвращает строковый идентификатор приложения
     *
     * @return string
     */
    public function getId()
    {
        return $this->virtual_app->getClientId();
    }

    /**
     * Возвращает название приложения
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->virtual_app['title'];
    }

    /**
     * Возвращает true, если секрет подходит
     *
     * @param $client_secret
     * @return bool
     */
    public function checkSecret($client_secret)
    {
        return $this->virtual_app->generateSecretHash($client_secret) === $this->virtual_app['client_secret'];
    }

    /**
     * Возвращает права доступа к методам для данного приложения
     *
     * @return array
     */
    public function getAppRights()
    {
        $rights = [];

        if (is_array($this->virtual_app['rights'])) {
            foreach ($this->virtual_app['rights'] as $method => $method_rights) {
                if (in_array(self::FULL_RIGHTS, $method_rights)) {
                    $rights[$method] = self::FULL_RIGHTS;
                } else {
                    $rights[$method] = $method_rights;
                }
            }
        }

        return $rights;
    }

    /**
     * Возвращает группы пользователей, которым доступно данное приложение
     *
     * @return array
     */
    public function getAllowUserGroup()
    {
        return (array)$this->virtual_app['groups'];
    }
}