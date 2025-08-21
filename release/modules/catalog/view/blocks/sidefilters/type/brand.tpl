{* Шаблон фильтра по бренду *}
{if $param.show_brand_filter && $brands && count($brands) > 1}
    {$is_open = $basefilters.brand || (is_array($param.expanded) && in_array('brand', $param.expanded))}
    <div class="accordion-item rs-type-multiselect">
        <div class="accordion-header">
            <button class="accordion-button {if !$is_open}collapsed{/if}" type="button" data-bs-toggle="collapse"
                    data-bs-target="#accordionBrand">
                <span class="me-4">{t}Бренд{/t}</span>
            </button>
            <a class="filter-clear rs-clear-one-filter"><img src="{$THEME_IMG}/icons/close.svg" alt="" width="16"></a>
        </div>
        <div id="accordionBrand" class="accordion-collapse collapse {if $is_open}show{/if}">
            <div class="accordion-body">
                {if $brands && count($brands) > $param.search_min_elements}
                <input type="text" class="form-control mb-4 rs-list-search" placeholder="{t}Поиск{/t}">
                {/if}
                <ul class="filter-list mb-4 rs-selected d-none"></ul>
                <ul class="filter-list rs-unselected">
                    {foreach $brands as $brand}
                        <li style="order: {$brand@iteration};" {if isset($filters_allowed_sorted['brand'][$brand.id]) && ($filters_allowed_sorted['brand'][$brand.id] == false)}class="disabled-property"{/if}>

                            <div class="checkbox check">
                                <input id="cb-brand-{$brand.id}" type="checkbox" {if is_array($basefilters.brand) && in_array($brand.id, $basefilters.brand)}checked{/if} name="bfilter[brand][]" value="{$brand.id}">
                                <label for="cb-brand-{$brand.id}">
                                                        <span class="checkbox-attr">
                                                            {include file="%THEME%/helper/svg/checkbox.tpl"}
                                                        </span>
                                    <span>{$brand.title}</span>
                                </label>
                            </div>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    </div>
{/if}