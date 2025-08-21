<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Exchange\Model\Task;

use Exchange\Model\Log\LogExchange;

class TaskQueue{
    const SESSION_VAR = "exchange_state";
    const TASKS_KEY   = "tasks";
    const CUR_KEY     = "current_task";

    private $log;

    public function __construct()
    {
        $this->log = LogExchange::getInstance();
    }

    public function addTask($task_name, AbstractTask $task)
    {
        if(!isset($_SESSION[self::SESSION_VAR][self::TASKS_KEY][$task_name]))
        {
            $_SESSION[self::SESSION_VAR][self::TASKS_KEY][$task_name] = $task;
        }
        return $_SESSION[self::SESSION_VAR][self::TASKS_KEY][$task_name];
    }
    
    public function exec($max_exec_time = 0)
    {   
        $start_time = time();

        // Если текущая задача не установлена в сессии (вероятно это первый запуск метода exec())
        if(!isset($_SESSION[self::SESSION_VAR][self::CUR_KEY])){
            // Запоминаем первую задачу как текущую
            $tasks_names = array_keys($_SESSION[self::SESSION_VAR][self::TASKS_KEY]);
            $first_task_name = reset($tasks_names);
            $_SESSION[self::SESSION_VAR][self::CUR_KEY] = $first_task_name;
        }
        
        $residuary_max_exec_time = $max_exec_time;
        
        while(true) {
            // Если максимальное время выполнения больше нуля (если ноль - ограничения нет)
            if ($max_exec_time) {
                // Если оставшееся время выполнения меньше либо равно нулю
                if ($residuary_max_exec_time <= 0) {
                    $this->log->write(t('Превышено максимальное время выполнения'), LogExchange::LEVEL_TASK);
                    return;
                }
            }
            
            $current_task_name = $_SESSION[self::SESSION_VAR][self::CUR_KEY];

            $this->log->write(t('Запускаем Задачу %0', [$current_task_name]), LogExchange::LEVEL_TASK);

            $finished = $_SESSION[self::SESSION_VAR][self::TASKS_KEY][$current_task_name]->_exec($residuary_max_exec_time);        

            if (!$finished) {
                $this->log->write(t('Задача не завершена, выходим (эта же задача будет продолжена на следующий вызов exec)'), LogExchange::LEVEL_TASK);
                return;
            }
            
            // Если задача полностью завершена
            $has_next = $this->next();
            if (!$has_next) {
                $this->log->write(t('Все задачи выполнены'), LogExchange::LEVEL_TASK);
                unset($_SESSION[self::SESSION_VAR]);
                return;
            }

            // Если максимальное время выполнения больше нуля (если ноль - ограничения нет)
            if ($max_exec_time) {
                // Уменьшаем оставшееся максимальное время выполения, на число пройденных секунд
                $residuary_max_exec_time -= (time() - $start_time);
            }
        }
    }
    
    private function next()
    {
        $tasks_names = array_keys($_SESSION[self::SESSION_VAR][self::TASKS_KEY]);      // Имена всех задач
        $current_task_name = $_SESSION[self::SESSION_VAR][self::CUR_KEY];              // Имя текущей задачи
        $cur_index = array_search($current_task_name, $tasks_names);                   // Индекс текущей задачи
        // Если следующая задача существует
        if(isset($tasks_names[$cur_index + 1])){
            // Ставим следующую задачу текущей
            $_SESSION[self::SESSION_VAR][self::CUR_KEY] = $tasks_names[$cur_index + 1];
            return true;
        }
        return false;
    }
    
    public function isCompleted()
    {
        return !isset($_SESSION[self::SESSION_VAR]);
    }
    
    static public function clearAll()
    {
        unset($_SESSION[self::SESSION_VAR]);
    }
    
    static public function getSessionVar($var_name)
    {
        if(!isset($_SESSION[self::SESSION_VAR][$var_name])){
            return null;
        }
        return $_SESSION[self::SESSION_VAR][$var_name];
    }
    
    static public function setSessionVar($var_name, $value)
    {
        $_SESSION[self::SESSION_VAR][$var_name] = $value;
    }
}
