{$transformer = $elem->getTransformer()}
{if $transformer}
    <select name="field">
        {html_options options=$elem.__field->getList() selected=$elem.field}
    </select>
    <div class="notice notice-warning m-t-20">
        <p>{t}Вы можете использовать следующие переменные в тексте запроса:{/t}</p>
        {foreach $transformer->getVariables() as $replace_var_object}
            <b>{ldelim}${$replace_var_object->name}{rdelim}</b> - {$replace_var_object->title}<br>
        {/foreach}
    </div>
{else}
    {t}Выберите объект трансформера{/t}
{/if}