{addjs file="%crm%/jquery.rs.autotaskthenparams.js?v=1.3"}
{addjs file="jquery.rs.objectselect.js" basepath="common"}
{addjs file="jquery.rs.userslinks.js" basepath="common"}
<script
        rel="current-values-then"
        type="application/json"
        data-current-type="{$elem.__then_type->get()}"
        data-current-action="{$elem.__then_action->get()}"

>
    {$elem.__then_params_arr->get()|json_encode:320}
</script>

<div
    class="autotask-then-wrapper p-t-20"
    data-get-types-url="{adminUrl do="AjaxGetThenActionsFromType"}"
    data-get-params-url="{adminUrl do="AjaxGetThenParamsFromType"}"
    data-get-condition-url="{adminUrl do="AjaxGetThenConditionParamsFromType"}"
    data-get-param-data-url="{adminUrl do="AjaxGetThenParamDataFromType"}"
    {if $main_then_params}data-params-count="{count($main_then_params)}"{/if}
    {if $main_then_conditions}data-conditions-count="{count($main_then_conditions)}"{/if}
    style="border-top: 1px solid rgb(237, 237, 237);"
>
    Что сделать

    <select name="then_type" data-previous-value="{$then_type}">
        {html_options options=$field->getList() selected=$then_type}
    </select>

    <span class="autotask_then_action">
        {if $then_action}
            <select name="then_action" data-previous-value="{$then_action}">
                {html_options options=$then_type_class->getActions($if_type_class) selected=$then_action}
            </select>
        {/if}
    </span>
    {if $if_type_class}
        {$replace_vars = $if_type_class->getReplaceVarTitles()}
    {/if}
    <div class="then_vars {if !$replace_vars}hidden{/if} notice notice-warning m-t-20" id="variables-block">
        <p>{t}Переменные, которые можно использовать в названии и описании задач:{/t}</p>
        <ul id="variables-list">
            {if $replace_vars}
                {foreach $replace_vars as $var => $title}
                    <li>{ldelim}{$var}{rdelim} - {$title}</li>
                {/foreach}
            {/if}
        </ul>
    </div>

    <div class="m-t-20 {if !$then_type || ($then_action && $then_action == 'create')}hidden{/if} autotask_then_condition-wrapper">
        <p>{t}По каким параметрам искать объект{/t}</p>
        <a id="addThenCondition" class="button {if $then_conditions && $main_then_conditions && count($then_conditions) == count($main_then_conditions)}hidden{/if}">{t}Добавить{/t}</a>
        <div class="autotask_then_conditions m-t-20">
            {if $then_conditions && ($then_action && $then_action != 'create')}
                {foreach $then_conditions as $param_id => $param}
                    <div class="m-b-10 autotask_then_condition">
                        <select id="main-select" class="m-r-10" name="then_params_arr[conditions][{$param_id}][key]">
                            <option value="">Выберите параметр</option>
                            {foreach $main_then_conditions as $key => $value}
                                <option value="{$key}" {if $param_id == $key}selected{/if}>{$value}</option>
                            {/foreach}
                        </select>
                        <a class="remove zmdi zmdi-delete"></a>
                        <div class="m-b-25 p-b-25 m-t-10 autotask_then_conditions_data" style="border-bottom: 1px solid rgb(237, 237, 237);">
                            {$param.field->formView()}
                        </div>
                    </div>
                {/foreach}
            {/if}
        </div>
    </div>

    <div class="m-t-20 {if !$then_type}hidden{/if} autotask_then_params-wrapper">
        <p>{t}Какие параметры изменить у объекта{/t}</p>
        <a id="addThenParam" class="button {if $then_params && $main_then_params && count($then_params) == count($main_then_params)}hidden{/if}">{t}Добавить{/t}</a>
        <div class="autotask_then_params m-t-20">
            {if $then_params}
                {foreach $then_params as $param_id => $param}
                    <div class="m-b-10 autotask_then_param">
                        <select id="main-select" class="m-r-10" name="{$param.name}">
                            <option value="">Выберите параметр</option>
                            {foreach $main_then_params as $key => $value}
                                <option value="{$key}" {if $param_id == $key}selected{/if}>{$value}</option>
                            {/foreach}
                        </select>
                        <a class="remove zmdi zmdi-delete"></a>
                        <div class="m-b-25 p-b-25 m-t-10 autotask_then_params_data" style="border-bottom: 1px solid rgb(237, 237, 237);">
                            {$param.field->formView()}
                        </div>
                    </div>
                {/foreach}
            {/if}
        </div>
    </div>
</div>

<script>
    $.contentReady(function() {
        $('.autotask-then-wrapper').autoTaskThenParams();
    });
</script>
