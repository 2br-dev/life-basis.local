<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\BonusCardType\BonusCard;

use Main\Model\Requester\ExternalRequest;
use RS\Config\Loader;
use RS\Event\Manager;
use RS\Exception;
use Shop\Model\BonusCardsApi;
use Shop\Model\BonusCardType\AbstractBonusCard;
use Shop\Model\Orm\BonusCards;
use Users\Model\Orm\User;

/**
 * Бонусная карта, выпускаемая удаленной системой.
 * В удаленную систему, на указанный в настройках URL, передается JSON с параметрами пользователя.
 * В качестве авторизации - используется Basic авторизация.
 * В ответ ожидается JSON вида:
 * {"success": true, "barcode":"1234567890123"}
 * или
 * {"success": false, "error": "текст ошибки"}
 */
class RemoteBonusCard extends AbstractBonusCard
{
    const TIMEOUT_SEC = 5;

    protected $api,
        $config,
        $additional_fields = [];

    public function __construct()
    {
        $this->api = new BonusCardsApi();
        $this->config = Loader::byModule($this);

        $this->setAdditionalFields([
            'alias' => 'phone',
            'title' => 'Телефон',
            'type' => 'text',
            'options' => null
        ]);
        $this->setAdditionalFields([
            'alias' => 'birthday',
            'title' => 'Дата рождения',
            'type' => 'date',
            'options' => null
        ]);
    }

    /**
     * Возвращает сокращенное название провайдера (только латинские буквы)
     * @return string
     */
    public function getShortName()
    {
        return 'remote_bonus_card';
    }

    /**
     * Возвращает отображаемое название провайдера
     * @return string
     */
    public function getTitle()
    {
        return t('Сторонние бонусные карты');
    }

    /**
     * Добавляет бонусную карту в систему
     *
     * @param $user_id - id пользователя
     * @param $number - номер бонусной карты
     * @param $data - дополнительные данные
     * @return BonusCards
     * @throws Exception
     */
    public function addBonusCard($user_id, $number = null, $data  = [])
    {
        if ($this->additional_fields) {
            $err_fields = [];
            foreach ($this->additional_fields as $key => $field) {
                if (!array_key_exists($field['alias'], $data)) {
                    $err_fields[] = $field['title'];
                }
            }
            if (!empty($err_fields)) {
                throw new Exception(t('Отсутствуют обязательные поля: ' . implode(', ', $err_fields)));
            }
        }

        //Выполняем запрос на получение бонусной карты
        $number = $this->remoteRequest(
            $this->prepareRequestData($user_id, $data)
        );

        $card = new BonusCards();
        $card->user_id = $user_id;
        $card->number = $number;
        $card->data = $data;
        $card->insert();
        return $card;
    }

    /**
     * Возвращает данные, которые будут отправлены в удаленный сервис
     *
     * @param integer $user_id ID пользователя
     * @param array $data Произвольные данные
     * @return array
     * @throws Exception
     */
    protected function prepareRequestData($user_id, $data)
    {
        $user = new User($user_id);
        if (!$user['id']) {
            throw new Exception(t('Не удалось загрузить пользователя'));
        }

        $result = [
            'user' => $user->getValues(),
            'extra_data' => $data
        ];

        $event_result = Manager::fire('remoteBonusCard.prepareData', [
            'result' => $result,
            'user' => $user
        ]);

        return $event_result->getResult()['result'];
    }

    /**
     * Выполняет запрос на удаленный сервер для получения штрихкода бонусной карты
     *
     * @param array $request_data
     * @return mixed
     */
    protected function remoteRequest($request_data)
    {
        $request = new ExternalRequest('remoteBonusCard', $this->config['remote_bonus_api_url']);
        if ($this->config['remote_bonus_basic_username']) {
            $request->setBasicAuth($this->config['remote_bonus_basic_username'], $this->config['remote_bonus_basic_password']);
        }
        $request->setEnableCache(false);
        $request->setTimeout(self::TIMEOUT_SEC);
        $request->setContentType(ExternalRequest::CONTENT_TYPE_JSON);
        $request->setParams($request_data);
        $result = $request->executeRequest();
        if ($result->getStatus() != '200') {
            throw new Exception(t('Удаленный сервер недоступен'));
        }

        $json = $result->getResponseJson();
        if ($json) {
            if (!empty($json['error'])) {
                throw new Exception(t('Ошибка: %0', [$json['error']]));
            }

            if (!isset($json['barcode'])) {
                throw new Exception(t('Не получен штрихкод бонусной карты'));
            }

            if (strlen($json['barcode']) != 13) {
                throw new Exception(t('Некорректный формат. Штрихкод должен быть в формате EAN-13'));
            }
        } else {
            throw new Exception(t('Не удалось распарсить JSON в ответе на запрос'));
        }

        return $json['barcode'];
    }
}