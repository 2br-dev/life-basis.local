<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Antivirus\Config;

use RS\Orm\ConfigObject;
use RS\Orm\Type;
use RS\Router\Manager;

/**
* Конфигурационный файл модуля
*/

/**
 * Class File
 * @package Antivirus\Config
 *
 * @property int signverify_step_size
 * @property int signverify_step_size_intensive
 * @property int signverify_auto_recover
 * @property int antivirus_step_size
 * @property int antivirus_step_size_intensive
 * @property int antivirus_auto_recover
 * @property int antivirus_max_file_size_kb
 * @property int proactive_allowed_interval
 * @property int proactive_trigger_request_count
 * @property int proactive_block_duration
 * @property int proactive_trigger_malicious_request_count
 * @property int proactive_auto_block
 * @property string proactive_trusted_ips
 * @property string proactive_trusted_user_agents
 * @property string proactive_trusted_url
 */
class File extends ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'author' => new Type\Text([
                'description' => t('Автор модуля'),
                'readOnly' => true,
                'useToSave' => false,
            ]),
            t('Проверка целостности'),
            'signverify_step_size' => new Type\Integer([
                'description' => t('Количество проверяемых файлов за один шаг'),
            ]),
            'signverify_step_size_intensive' => new Type\Integer([
                'description' => t('Количество проверяемых файлов за один шаг при полной проверке'),
            ]),
            'signverify_auto_recover' => new Type\Integer([
                'description' => t('Автоматически восстанавливать поврежденные файлы'),
                'checkboxView' => [1,0],
            ]),
            t('Антивирус'),
            'antivirus_step_size' => new Type\Integer([
                'description' => t('Количество проверяемых файлов за один шаг'),
            ]),
            'antivirus_step_size_intensive' => new Type\Integer([
                'description' => t('Количество проверяемых файлов за один шаг при полной проверке'),
            ]),
            'antivirus_max_file_size_kb' => new Type\Integer([
                'description' => t('Максимальный размер проверяемого файла, Кб'),
            ]),
            'antivirus_auto_recover' => new Type\Integer([
                'description' => t('Автоматически лечить зараженные файлы'),
                'checkboxView' => [1,0],
            ]),
            t('Проактивная защита'),
            'proactive_allowed_interval' => new Type\Integer([
                'description' => t('Интервал между запросами, меньше которого начинается подсчет запросов для данного IP (мс)'),
            ]),
            'proactive_block_duration' => new Type\Integer([
                'description' => t('Период блокировки'),
                'List' => [[__CLASS__, 'selectBlockTimeList']],
            ]),
            'proactive_auto_block' => new Type\Integer([
                'description' => t('Автоматически блокировать вредоносные запросы'),
                'checkboxView' => [1,0],
            ]),
            'proactive_trigger_request_count' => new Type\Integer([
                'description' => t('Количество подряд запросов, приводящее к блокировке данного IP'),
            ]),
            'proactive_trigger_malicious_request_count' => new Type\Integer([
                'description' => t('Количество вредоносных запросов, приводящих к блокировке'),
            ]),
            'proactive_trusted_ips' => new Type\Text([
                'description' => t('Список доверенных IP-адресов (каждый адрес с новой строки).'),
            ]),
            'proactive_trusted_user_agents' => new Type\Text([
                'description' => t('Список доверенных User Agent заголовков (каждый заголовок с новой строки).'),
                'hint' => t('Поддерживается формат регулярных выражений'),
            ]),
            'proactive_trusted_url' => new type\Text([
                'description' => t('Список URL, у которых необходимо отключить защиту'),
                'hint' => t('Проверяется частичное вхождение указанной строки в URL (каждый с новой строки)')
            ])
        ]);
    }

    /**
    * Конфигурация общая для всех мультисайтов
    *
    * @return bool
    */
    public function isMultisiteConfig()
    {
        return false;
    }

    /**
     * Возвращает справочник длительностей блокировки IP
     *
     * @return array
     */
    public static function selectBlockTimeList()
    {
        return [
            1200 => t('20 минут'),
            3600 => t('1 час'),
            7200 => t('2 часа'),
            3600*24 => t('1 сутки'),
            0    => t('Бессрочно'),
        ];
    }

    /**
     * Возвращает значения свойств по-умолчанию
     *
     * @return array
     * @throws \RS\Module\Exception
     */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => Manager::obj()->getAdminUrl('runFull', [], 'antivirus-ctrl'),
                    'title' => t('Запустить полную проверку'),
                    'description' => t('Проверить на наличие поврежденных файлов и вирусов'),
                    'confirm' => t('Вы действительно запустить полную проверку системы? В процессе проверки возможна повышенная нагрузка на сервер.')
                ],
                [
                    'url' => Manager::obj()->getAdminUrl('ajaxShowChangedFiles', [], 'antivirus-ctrl'),
                    'title' => t('Список измененных файлов'),
                    'description' => t('Показать все файлы системы, которые содержат изменения'),
                    'class' => 'crud-add'
                ],
                [
                    'url' => Manager::obj()->getAdminUrl(false, [], 'antivirus-events'),
                    'title' => t('Обнаруженные угрозы'),
                    'description' => t('Показать угрозы, обнаруженные проактивной защитой и антивирусом'),
                    'class' => ' '
                ],
                [
                    'url' => Manager::obj()->getAdminUrl(false, [], 'antivirus-excludedfiles'),
                    'title' => t('Исключения'),
                    'description' => t('Здесь можно создать список файлов, которые не следует проверять'),
                    'class' => ' '
                ],
            ]
            ];
    }
}

