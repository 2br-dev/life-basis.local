{* Шаблон фильтра по наличию товара *}
{if $param.show_is_num}
    {$is_open = $basefilters.isnum != '' || (is_array($param.expanded) && in_array('num', $param.expanded))}
    <div class="accordion-item rs-type-radio">
        <div class="accordion-header">
            <button class="accordion-button {if !$is_open}collapsed{/if}" type="button" data-bs-toggle="collapse"
                    data-bs-target="#accordionNum">
                <span class="me-2">{t}Наличие{/t}</span>
            </button>
        </div>
        <div id="accordionNum" class="accordion-collapse collapse {if $is_open}show{/if}">
            <div class="accordion-body">
                <ul class="filter-list">
                    <li>
                        <div class="radio check">
                            <input id="cb-isnum-empty" type="radio" {if !isset($basefilters.isnum)}checked{/if} name="bfilter[isnum]" value="" data-start-value class="radio rs-stock-by-num">
                            <label for="cb-isnum-empty">
                                <span class="radio-attr">
                                    {include file="%THEME%/helper/svg/radio.tpl"}
                                </span>
                                <span>{t}Неважно{/t}</span>
                            </label>
                        </div>
                    </li>
                    <li>
                        <div class="radio check">
                            <input id="cb-isnum-no" type="radio" {if $basefilters.isnum == '0'}checked{/if} name="bfilter[isnum]" value="0" class="radio rs-stock-by-num">
                            <label for="cb-isnum-no">
                                <span class="radio-attr">
                                    {include file="%THEME%/helper/svg/radio.tpl"}
                                </span>
                                <span>{t}Нет{/t}</span>
                            </label>
                        </div>
                    </li>
                    <li>
                        <div class="radio check">
                            <input id="cb-isnum-yes" type="radio" {if $basefilters.isnum == '1'}checked{/if} name="bfilter[isnum]" value="1" class="radio rs-stock-by-num">
                            <label for="cb-isnum-yes">
                                <span class="radio-attr">
                                    {include file="%THEME%/helper/svg/radio.tpl"}
                                </span>
                                <span>
                                    {if $filter_warehouses}
                                        {t}В наличии{/t}
                                    {else}
                                        {t}Есть{/t}
                                    {/if}
                                </span>
                            </label>
                        </div>
                    </li>

                    {if $filter_warehouses}
                        <li>
                            <div class="radio check">
                                <input id="cb-isnum-wh"
                                       type="radio" {if $basefilters.isnum == '2'}checked{/if} name="bfilter[isnum]" value="2" class="radio rs-stock-by-warehouse">
                                <label for="cb-isnum-wh">
                                    <span class="radio-attr">
                                        {include file="%THEME%/helper/svg/radio.tpl"}
                                    </span>
                                    <span>{t}В магазинах{/t}</span>
                                </label>
                            </div>
                        </li>
                        {foreach $filter_warehouses as $warehouse}
                            <li>
                                <div class="checkbox check">
                                    <input id="cb-isnum-wh-{$warehouse.id}" type="checkbox"
                                           {if is_array($basefilters.warehouses) && in_array($warehouse.id, $basefilters.warehouses)}checked{/if}
                                           name="bfilter[warehouses][]" value="{$warehouse.id}"
                                           class="rs-stock-by-warehouse-item">

                                    <label for="cb-isnum-wh-{$warehouse.id}">
                                        <span class="checkbox-attr">
                                            {include file="%THEME%/helper/svg/checkbox.tpl"}
                                        </span>
                                        <span>{$warehouse.title}</span>
                                    </label>
                                </div>
                            </li>
                        {/foreach}
                    {/if}
                </ul>
            </div>
        </div>
    </div>
{/if}