<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Exchange\Model\Task;

use Catalog\Model\Orm\Product;
use Exchange\Model\Log\LogExchange;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

abstract class AbstractTask
{
    protected $startTime;
    protected $maxExecTime;
    /** @var LogExchange */
    protected $log;

    public function __construct()
    {
        $this->log = LogExchange::getInstance();
    }

    final public function _exec($max_exec_time = 0)
    {
        if ($this->log === null) {
            $this->log = LogExchange::getInstance();
        }

        $this->startTime = time();
        $this->maxExecTime = $max_exec_time;

        $this->log->write(t('Задача %0 начата', [$this]), LogExchange::LEVEL_TASK);

        $ret = $this->exec($max_exec_time);

        if ($ret === true) {
            $this->log->write(t('Задача %0 завершена', [$this]), LogExchange::LEVEL_TASK);
        } else {
            $this->log->write(t('Задача %0 прервана', [$this]), LogExchange::LEVEL_TASK);
        }

        return $ret;
    }

    abstract public function exec($max_exec_time = 0);

    protected function isExceed()
    {
        if ($this->maxExecTime == 0) return false;
        return time() >= $this->startTime + $this->maxExecTime;
    }

    public function __toString()
    {
        $vars = get_object_vars($this);
        $str = get_class($this) . ' [' . http_build_query($vars, "", ', ') . ']';

        return $str;
    }

    /**
     * Возвращает id товаров, обработанных во время импорта
     *
     * @return int[]
     */
    protected function getProcessedProductsIds()
    {
        $ids = (new OrmRequest())
            ->select('id')
            ->from(Product::_getTable())
            ->where([
                'site_id' => SiteManager::getSiteId(),
                'processed' => 1
            ])
            ->exec()->fetchSelected(null, 'id');

        return $ids;
    }
}
