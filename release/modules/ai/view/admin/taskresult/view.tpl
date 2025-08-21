{$task = $task_result->getTask()}
{$form_object = $task_result->getResultFormObject()}
{addcss file="%ai%/admin/task-result.css"}
<div class="updatable" data-dialog-options='{ "width":"1200", "height":"700"}'
     data-no-update-hash="true" data-update-replace="true" data-update-parse="true">

    <form method="POST" class="crud-form" action="{urlMake}">
        <div class="task-result-box">
            <div class="task-col-number">{t}Объект{/t}</div>
            <div class="task-col-number-value">{$task_result.number} из {$task.total_count}</div>
            <div class="task-col-status">{t}Статус{/t}</div>
            <div class="task-col-status-value">
                <span class="status-field" style="background-color:{$task_result->getStatusColor()}">{$task_result->getStatusTitle()}</span>
            </div>
            <div class="task-col-object">{t}ID Объекта{/t}</div>
            <div class="task-col-object-value">{$task_result.entity_id}</div>
            <div class="task-col-entity">{t}Наименование{/t}</div>
            <div class="task-col-entity-value">
                <a href="{$task_result->getEntityAdminUrl()}" class="crud-edit">{$task_result->getEntityTitle()}</a>
            </div>
            {if $task_result.error}
                <div class="task-col-errors">{t}Ошибки генерации{/t}</div>
                <div class="task-col-errors-value">
                    {$task_result.error}
                </div>
            {/if}
        </div>

        {if $task_result.status == 'new'}
            <div class="task-result-new">
                <i class="icon zmdi zmdi-refresh-sync-alert"></i>
                <p class="f-16">{t}Для данного объекта еще не сгенерированы данные.<br>Пожалуйста, подождите, когда статус данного результата изменится.{/t}</p>
                <p>
                    <a href="{adminUrl do="view" number=$task_result.number task_id=$task_result.task_id}" class="btn btn-default call-update">{t}Обновить{/t}</a>
                </p>
            </div>
        {else}
            <table class="otable">
                {foreach $form_object as $name => $property}
                    {if $property->isVisible()}
                    <tr>
                        <td class="otitle">{$property->getDescription()}</td>
                        <td>
                            {if $task_result.status != 'approved'}
                                <div class="task-result-before">
                                    <div>
                                        <a class="collapsed task-result-before-toggle" data-toggle="collapse" href="#before-value-{$name}">
                                            <span>{t}Показать предыдущее значение{/t}</span>
                                            <i class="zmdi zmdi-chevron-down task-collapse-chevron"></i></a>
                                    </div>
                                    <div id="before-value-{$name}" class="collapse">
                                        <div class="task-result-before-value">
                                            {$form_object.source_object[$name]|default:"{t}пусто{/t}"}
                                        </div>
                                    </div>
                                </div>
                            {/if}
                            {$property->formView()}
                        </td>
                    </tr>
                    {/if}
                {/foreach}
            </table>
        {/if}
    </form>
</div>