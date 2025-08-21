{* Шаблон для фильтра с типом - строка *}
{$is_open = $filters[$prop.id] || $prop.is_expanded}
<div class="accordion-item">
    <div class="accordion-header">
        <button class="accordion-button {if !$is_open}collapsed{/if}" type="button" data-bs-toggle="collapse"
                data-bs-target="#accordionFilter-{$prop.id}">
            <span class="me-2 d-flex align-items-center">{$prop.title}
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
    </div>
    <div id="accordionFilter-{$prop.id}" class="accordion-collapse collapse {if $is_open}show{/if}">
        <div class="accordion-body">
            <input type="text" class="form-control" name="pf[{$prop.id}]" value="{$filters[$prop.id]}" data-start-value="">
        </div>
    </div>
</div>