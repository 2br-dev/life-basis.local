{addjs file="%crm%/jquery.rs.autotaskifparams.js?v=1.3"}
{addcss file="%crm%/autotask.css?v=1.3"}
<script
    rel="current-values-if"
    type="application/json"
    data-current-type="{$elem.__if_type->get()}"
    data-current-action="{$elem.__if_action->get()}"

>
    {$elem.__if_params_arr->get()|json_encode:320}
</script>

<div
        class="autotask-if-wrapper"
        data-get-types-url="{adminUrl do="AjaxGetIfActionsFromType"}"
        data-get-params-url="{adminUrl do="AjaxGetIfParamsFromType"}"
        {if $main_if_params}data-params-count="{count($main_if_params)}"{/if}
>
    Объект

    <select name="if_type" data-previous-value="{$if_type}">
        {html_options options=$field->getList() selected=$if_type}
    </select>

    <span class="autotask_if_action" data-previous-value="{$if_action}">
        {if $if_action}
            <select name="if_action">
                {html_options options=$if_type_class->getActions() selected=$if_action}
            </select>
        {/if}
    </span>

    <div class="m-t-20 {if !$if_type || empty($if_type_class->getParams())}hidden{/if} autotask_if_action-wrapper">
        <p>{t}И имеет дополнительные параметры{/t}</p>
        <a id="addParamIf" class="button {if $if_params && $main_if_params && count($if_params) == count($main_if_params)}hidden{/if}">{t}Добавить{/t}</a>
        <div class="autotask_if_params m-t-20">
            {if $if_params}
                {foreach $if_params as $param_id => $param}
                    {capture assign="delete_button"}
                        <a class="remove zmdi zmdi-delete"></a>
                    {/capture}

                    <div class="m-b-10 autotask_if_param">
                        <select id="main-select" class="m-r-10" name="if_params_arr[{$param_id}][key]">
                            <option value="">Выберите параметр</option>
                            {foreach $main_if_params as $key => $value}
                                <option value="{$key}" {if $param_id == $key}selected{/if}>{$value}</option>
                            {/foreach}
                        </select>

                        {if $param.type == 'input'}
                            {if $param.multiple}
                                {$delete_button}
                                <div class="multi-input-wrapper m-t-10">
                                    <div class="multi-input-values">
                                        {foreach $param.selected as $value}
                                            <div class="multi-input-line form-inline m-b-5">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="if_params_arr[{$param_id}][value][]" value="{$value}">
                                                </div>
                                                <a
                                                        class="btn f-18 multi-input-{if $value@iteration == 1}add{else}remove c-red{/if}"
                                                        title="{if $value@iteration == 1}Добавить значение{else}Удалить{/if}"
                                                >
                                                    <i class="zmdi zmdi-{if $value@iteration == 1}plus{else}close{/if}"></i>
                                                </a>

                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                            {else}
                                <input class="m-r-10" name="if_params_arr[{$param_id}][value]" type="text" value="{$param.selected}">
                                {$delete_button}
                            {/if}
                        {elseif $param.type == 'checkbox'}
                            <input class="m-r-10" name="if_params_arr[{$param_id}][value]" type="hidden" value="off">
                            <input class="m-r-10" name="if_params_arr[{$param_id}][value]" type="checkbox" {if $param.selected == 'on'}checked{/if}>
                            {$delete_button}
                        {elseif $param.type == 'select'}
                            <select class="m-r-10" name="if_params_arr[{$param_id}][value]">
                                <option value="">Выберите значение</option>
                                {foreach $param.values as $key => $value}
                                    <option value="{$key}" {if $param.selected == $key}selected{/if}>{$value}</option>
                                {/foreach}
                            </select>
                            {$delete_button}
                        {elseif $param.type == 'time'}
                            <input class="m-r-10" name="if_params_arr[{$param_id}][value]" type="time" value="{$param.selected}">
                            {$delete_button}
                        {elseif $param.type == 'days'}
                            {$delete_button}
                            <div class="days-selector days m-r-10" name="if_params_arr[{$param_id}][value]">
                                {foreach $param.values as $key => $value}
                                    <input
                                        type="checkbox"
                                        id="day_{$param_id}_{$key}"
                                        name="if_params_arr[{$param_id}][value][]"
                                        value="{$key}"
                                        class="day-checkbox"
                                        {if in_array($key, $param.selected)}checked{/if}
                                    >
                                    <label for="day_{$param_id}_{$key}">{$value}</label>
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                {/foreach}
            {/if}
        </div>
    </div>

</div>

<script>
    $.contentReady(function() {
        $('.autotask-if-wrapper').autoTaskIfParams();
    });
</script>
