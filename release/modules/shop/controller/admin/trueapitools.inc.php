<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use RS\Controller\Admin\Front;
use RS\Controller\Result\Standard;
use RS\Exception;
use Shop\Model\Marking\TrueApi\CheckCodes;

/**
 * Вспомогательные инструменты для взаимодействия с честным знаком
 */
class TrueApiTools extends Front
{
    protected $api;

    function init()
    {
        $this->api = new CheckCodes();
    }

    /**
     * Проверяет авторизацию
     *
     * @return Standard
     */
    function actionCheckAuthorization()
    {
        $this->result->addSection('noUpdate', true);

        try {
            $this->api->checkAuthorization();
            return $this->result->addMessage(t('Авторизация прошла успешно'));
        } catch(Exception $e) {
            return $this->result->addEMessage(t('Не удалось авторизоваться.').$e->getMessage());
        }
    }

    /**
     * Обновляет хосты Честного знака
     *
     * @return Standard
     */
    function actionUpdateHosts()
    {
        $this->result->addSection('noUpdate', true);

        try {
            $this->api->cleanHosts();
            $hosts = $this->api->getHosts(true);
            if ($hosts) {
                return $this->result->addMessage(t('Хосты обновлены'));
            }

            throw new Exception('');
        } catch(Exception $e) {
            return $this->result->addEMessage(t('Не удалось загрузить хосты. ').$e->getMessage());
        }
    }
}