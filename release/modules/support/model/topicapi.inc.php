<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model;
use Main\Model\NoticeSystem\HasMeterInterface;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;
use Support\Config\File;
use Support\Model\Orm\Topic;
use Support\Model\Platform\Manager;
use Support\Model\Platform\PlatformSite;

/**
 * API для работы с тикетами
 */
class TopicApi extends EntityList implements HasMeterInterface
{
    const PLATFORMS_ALL = 'all';

    function __construct()
    {
        parent::__construct(new Topic, [
            'multisite' => true,
            'defaultOrder' => 'updated DESC',
            'nameField' => 'title',
            'aliasField' => 'number'
        ]);
    }

    /**
     * Возвращает API по работе со счетчиками
     *
     * @return \Main\Model\NoticeSystem\MeterApiInterface
     */
    function getMeterApi()
    {
        return new TopicMeterApi($this);
    }

    /**
     * Возвращает общее количество элементов, согласно условию.
     *
     * @return integer
     */
    function getListCount()
    {
        $q = clone $this->queryObj();
        $q->orderby(false);

        //Используем сложный запрос для подсчета количества элементов,
        // если в запросе используется having
        $q->select = $this->defAlias().'.id';
        $count = OrmRequest::make()
            ->from('('.$q->toSql().')', 'subquery')
            ->count();

        return $count;
    }

    /**
     * Возвращает тикет по уникальному номеру
     *
     * @param string $number
     * @return Topic|bool(false)
     */
    public function getByNumber($number)
    {
        $q = $this->getCleanQueryObject();
        return $q->where([$this->alias_field => $number])->orderby(null)->object();
    }

    /**
     * Устанавливает фильтрацию по платформам, отображаемым у пользователя
     * в личном кабинете
     * @return self
     */
    public function setUserAccountPlatformFilter()
    {
        $this->setFilter('platform', self::getUserAccountPlatForms(), 'in');
        return $this;
    }

    /**
     * Возвращает идентификаторы платформ, которые должны отображаться
     * у пользователя в личном кабинете
     *
     * @return array
     */
    public static function getUserAccountPlatForms()
    {
        $ids = [];
        $config = File::config();
        $all_platforms = array_keys(Manager::getAllowOnSitePlatfromTitles());

        if (in_array(self::PLATFORMS_ALL, (array)$config['platforms_on_site'])) {
            $ids = $all_platforms;
        } else {
            foreach ($config['platforms_on_site'] as $id) {
                if (in_array($id, $all_platforms)) {
                    $ids[] = $id;
                }
            }
        }

        $ids[] = PlatformSite::PLATFORM_ID;
        return $ids;
    }
}
