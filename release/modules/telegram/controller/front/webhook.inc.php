<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Telegram\Controller\Front;

use RS\Controller\Front;
use Telegram\Model\Log\TelegramLog;
use Telegram\Model\Orm\Profile;

/**
 * Шлюз, принимающий события от Telegram
 */
class WebHook extends Front
{
    function actionIndex()
    {
        $this->wrapOutput(false);
        $secret_key = $this->url->get('secret_key', TYPE_STRING);
        $profile = Profile::loadByWhere([
            'secret_key' => $secret_key
        ]);

        if (!$profile['id']) {
            $this->e404(t('Профиль Телеграм бота не найден'));
        }

        $log = TelegramLog::getInstance();
        try {
            $log->write(t('Начало обработки webhook'), TelegramLog::LEVEL_INFO);
            $profile->getTelegramBot()->handle();
            $log->write(t('Конец обработки webhook'), TelegramLog::LEVEL_INFO);
            $response = 'OK';
        } catch(\Exception $e) {
            $response = t('Не удалось обработать WebHook от Telegram. Причина: %0', [$e->getMessage()]);
            $log->write($response, TelegramLog::LEVEL_INFO);
        }

        return $response;
    }
}