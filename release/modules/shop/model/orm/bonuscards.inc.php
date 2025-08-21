<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm;
use RS\Helper\QrCode\QrCodeGenerator;
use RS\Orm\OrmObject;
use RS\Orm\Type as OrmType;
use Users\Model\Orm\User;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $number Номер карты
 * @property integer $user_id ID пользователя
 * @property string $save_date Дата создания
 * @property array $data Дополнительные сведения
 * @property string $_serialized Дополнительные сведения
 * --\--
 */
class BonusCards extends OrmObject
{
    protected static $table = 'user_bonus_cards';

    protected function _init()
    {
        parent::_init()->append([
            'site_id' => new OrmType\CurrentSite(),
            'number' => (new OrmType\Varchar())
                ->setDescription(t('Номер карты'))
                ->setMaxLength(13)
                ->setAllowEmpty(false),
            'user_id' => (new OrmType\Integer())
                ->setDescription(t('ID пользователя'))
                ->setAllowEmpty(false),
            'save_date' => (new OrmType\Datetime())
                ->setDescription('Дата создания')
                ->setIndex(true),
            'data' => (new OrmType\ArrayList())
                ->setDescription('Дополнительные сведения')
                ->setVisible(false),
            '_serialized' => (new OrmType\Text())
                ->setDescription('Дополнительные сведения')
                ->setVisible(false),
        ]);

        $this->addIndex(['user_id','number'], self::INDEX_UNIQUE);
    }

    /**
     * Возвращает ссылку на штрихкод
     *
     * @return string
     */
    public function getBonusCardBarcode($absolute = true)
    {
        return QrCodeGenerator::buildUrl($this->number, [
            'w' => 700,
            'h' => 350,
            'pv' => 20,
            'ts' => 5,
            'th' => 40,
            'pb' => 50,
            's' => 'ean-13-nopad',
        ], null, $absolute);
    }

    /**
     * Возвращает пользователя, к которому привязана бонусная карта
     *
     * @return User
     */
    public function getUser(): User
    {
        static $users = [];
        if (!isset($users[$this['user_id']])) {
            $users[$this['user_id']] = new User($this['user_id']);
        }
        return $users[$this['user_id']];
    }

    /**
     * Вызывается перед сохранением объекта
     *
     * @param string $flag - строковое представление текущей операции (insert или update)
     * @return false|void
     */
    function beforeWrite($flag)
    {
        $this['_serialized'] = serialize($this['data']);

        if ($flag == self::INSERT_FLAG) {
            $this['save_date'] = date('Y-m-d H:i:s');
        }
    }

    /**
     * Вызывается после загрузки объекта
     *
     * @return void
     */
    function afterObjectLoad()
    {
        $this['data'] = @unserialize((string)$this['_serialized']);
    }
}
