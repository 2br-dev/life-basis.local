<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;

use RS\Config\Loader;
use RS\Exception as RSException;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request;
use Shop\Model\BonusCardType\AbstractBonusCard;
use Shop\Model\Orm\BonusCards;

class BonusCardsApi extends EntityList
{
    private static $bonus_cards_providers;

    function __construct()
    {
        parent::__construct(new BonusCards(), ['multisite' => true]);
    }

    /**
     * Возвращает список провайдеров бонусных карт
     *
     * @return array
     * @throws \Exception
     */
    public function getBonusCardsProviders()
    {
        if (self::$bonus_cards_providers === null) {
            $event_result = \RS\Event\Manager::fire('bonuscards.getproviders', []);
            $list = $event_result->getResult();
            self::$bonus_cards_providers = [];
            foreach($list as $sender_object) {
                if (!($sender_object instanceof AbstractBonusCard)) {
                    throw new \Exception(t('Тип отправки SMS должен быть наследником \Users\Model\BonusCardType\AbstractBonusCard'));
                }
                self::$bonus_cards_providers[$sender_object->getShortName()] = $sender_object;
            }
        }

        return self::$bonus_cards_providers;
    }

    /**
     * Возвращает объект провайдера по его короткому строковому идентификатору
     *
     * @param string $name
     * @return AbstractBonusCard
     */
    public static function getSenderByShortName($name)
    {
        $_this = new self();
        $list = $_this->getBonusCardsProviders();
        return isset($list[$name]) ? $list[$name] : null;
    }

    /**
     * @param $user_id
     * @param null $site_id
     * @return bool|BonusCards
     * @throws \Exception
     */
    public function getUserBonusCard($user_id, $site_id = null)
    {
        if ($site_id === null) {
            $site_id = \RS\Site\Manager::getSiteId();
        }

        if (!$site_id) {
            throw new \Exception(t('Не определен текущий сайт'));
        }

        return Request::make()
            ->from(new BonusCards())
            ->where([
                'site_id' => $site_id,
                'user_id' => $user_id
            ])->object();
    }

    /**
     * Возвращает список провайдеров бонусных карт
     *
     * @return array
     * @throws \Exception
     */
    public static function selectBonusCardList()
    {
        $_this = new self();
        $result = [];
        foreach($_this->getBonusCardsProviders() as $key => $object) {
            $result[$key] = $object->getTitle();
        }
        return $result;
    }

    /**
     * Добавляет бонусную карту в систему
     *
     * @param $user_id - id пользователя
     * @param $number - номер бонусной карты
     * @param $data - дополнительные данные
     * @return BonusCards|bool
     * @throws RSException
     */
    public function addBonusCard($user_id, $number, $data)
    {
        $config = Loader::byModule('shop');
        $bonus_card_class = $config['bonus_card_class'];

        if ($bonus_card_class){
            /** @var AbstractBonusCard */
            if ($sender = self::getSenderByShortName($bonus_card_class)) {
                return $sender->addBonusCard($user_id, $number, $data);
            }
        }

        return false;
    }

    /**
     * Возвращает массив дополнительных полей для бонусной карты
     *
     * @return array|mixed
     */
    public function getAdditionalFields()
    {
        $config = Loader::byModule('shop');
        $bonus_card_class = $config['bonus_card_class'];

        if ($bonus_card_class){
            /** @var AbstractBonusCard */
            if ($sender = self::getSenderByShortName($bonus_card_class)) {
                return $sender->getAdditionalFields();
            }
        }

        return [];
    }
}