{* Шаблон для фильтра с типом - список *}
{$is_open = $filters[$prop.id] || $prop.is_expanded}
<div class="accordion-item rs-type-multiselect">
    <div class="accordion-header">
        <button class="accordion-button {if !$is_open}collapsed{/if}" type="button" data-bs-toggle="collapse" data-bs-target="#accordionFilter-{$prop.id}">
            <span class="me-2 d-flex align-items-center">{$prop.title} {if $prop.unit}({$prop.unit}){/if}
                {if $prop.description}
                    <a class="btn-popover align-middle dark-popover"
                       data-bs-toggle="popover"
                       data-bs-trigger="hover"
                       data-bs-placement="top"
                       data-bs-html="true"
                       tabindex="0"
                       data-bs-content="{$prop.description|escape}"> ? </a>
                {/if}
            </span>
        </button>
        <a class="filter-clear rs-clear-one-filter"><img src="{$THEME_IMG}/icons/close.svg" alt="" width="16"></a>
    </div>
    <div id="accordionFilter-{$prop.id}" class="accordion-collapse collapse {if $is_open}show{/if}">
        <div class="accordion-body">
            {$property_values = $prop->getAllowedValues()}
            {if $property_values && count($property_values) > $param.search_min_elements}
                <input type="text" class="form-control mb-4 rs-list-search" placeholder="{t}Поиск{/t}">
            {/if}
            <ul class="filter-list mb-4 rs-selected d-none"></ul>
            <ul class="filter-list rs-unselected">
                {foreach $property_values as $key => $value}
                    <li style="order: {$value@iteration};" {if isset($filters_allowed_sorted[$prop.id][$key]) && ($filters_allowed_sorted[$prop.id][$key] == false)}class="disabled-property"{/if}>
                        <div class="checkbox check">
                            <input id="cb-{$prop.id}-{$key}" type="checkbox" {if is_array($filters[$prop.id]) && in_array($key, $filters[$prop.id])}checked{/if} name="pf[{$prop.id}][]" value="{$key}">
                            <label for="cb-{$prop.id}-{$key}">
                                <span class="checkbox-attr">
                                    {include file="%THEME%/helper/svg/checkbox.tpl"}
                                </span>
                                <span>{$value}</span>
                            </label>
                        </div>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>