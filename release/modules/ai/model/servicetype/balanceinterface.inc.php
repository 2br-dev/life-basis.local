<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\ServiceType;

/**
 * Интерфейс GPT-сервиса, который может возвращать баланс
 */
interface BalanceInterface
{
    /**
     * Возвращает Баланс пользователя на GPT - сервисе
     *
     * @param bool $cache
     * @return mixed
     */
    public function getBalance($cache = true);

    /**
     * Возвращает URL страницы, где можно пополнить баланс
     * @return string
     */
    public function getBalanceRefillUrl();
}