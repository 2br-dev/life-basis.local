<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Type;

/**
 * ORM Объект автозадачи
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $title Название
 * @property integer $enable Включено
 * --\--
 */
class AutoTask extends OrmObject
{
    protected static
        $table = 'crm_autotask';

    function _init()
    {
        parent::_init()->append([
            'title' => new Type\Varchar([
                'description' => t('Название'),
                'hint' => t('Придумайте названия правила'),
                'checker' => ['chkEmpty', t('Укажите название правила')],
            ]),
            'identificator' => new Type\Varchar([
                'description' => t('Идентификатор автозадачи'),
                'hint' => t('Это может быть любое слово или несколько слов, описывающих создаваемую задачу. Данный идентификатор может использоваться при построении связей между автозадачами'),
                'checker' => ['chkEmpty', t('Укажите идентификатор автозадачи')]
            ]),
            'category_id' => new Type\Integer([
                'description' => t('Категория'),
                'hint' => t('Если у вас много правил автоматизации, то можно распределить их по категориям для удобства навигации по ним. Категории нужно предварительно создать.'),
                'list' => [['\Crm\Model\AutotaskCategoryApi', 'staticSelectList'], [0 => t('Без связи с категорией')]],
            ]),
            'enable' => new Type\Integer([
                'description' => t('Включено'),
                'hint' => t('Только включенные правила будут срабатывать'),
                'checkboxView' => [1,0]
            ]),
            'if_type' => new Type\Varchar([
                'description' => t('Условие'),
                'hint' => t('Составьте условие, при котором правило будет срабатывать'),
                'template' => '%crm%/form/autotask/autotask_if_type.tpl',
                'list' => [['\Crm\Model\AutoTaskApi', 'getAllIfRules'], ['' => t('Выберите объект условия')]],
                'checker' => ['ChkEmpty', t('Укажите объект условия')]
            ]),
            'if_action' => new Type\Varchar([
                'description' => t('Действие'),
                'visible' => false,
            ]),
            'if_params' => new Type\Text([
                'description' => t('Дополнительные параметры условия'),
                'visible' => false,
            ]),
            'if_params_arr' => new Type\ArrayList([
                'description' => t('Дополнительные параметры условия'),
                'visible' => false,
            ]),
            'then_type' => new Type\Varchar([
                'description' => t('Действие'),
                'hint' => t('Сформируйте действие, которое будет выполняться при срабатывании условия'),
                'template' => '%crm%/form/autotask/autotask_then_type.tpl',
                'list' => [['\Crm\Model\AutoTaskApi', 'getAllThenRules'], ['' => t('Выберите объект действия')]],
                'checker' => ['ChkEmpty', t('Укажите объект действия')]
            ]),
            'then_action' => new Type\Varchar([
                'description' => t('Действие'),
                'checker' => ['ChkEmpty', t('Укажите действие')],
                'visible' => false,
            ]),
            'then_params' => new Type\Text([
                'description' => t('Дополнительные параметры действия'),
                'visible' => false,
            ]),
            'then_params_arr' => new Type\ArrayList([
                'description' => t('Дополнительные параметры действия'),
                'visible' => false,
            ]),
        ]);
    }

    /**
     * Действия при загрузке
     */
    function afterObjectLoad()
    {
        $this['if_params_arr'] = json_decode(base64_decode($this['if_params']), true) ?: [];
        $this['then_params_arr'] = json_decode(base64_decode($this['then_params']), true) ?: [];
    }

    /**
     * Действия перед записью типа
     *
     * @param string $flag - insert или update
     * @return false|null|void
     */
    function beforeWrite($flag)
    {
        $this['if_params'] = base64_encode(json_encode($this['if_params_arr'], JSON_UNESCAPED_UNICODE));
        $this['then_params'] = base64_encode(json_encode($this['then_params_arr'], JSON_UNESCAPED_UNICODE));
    }
}