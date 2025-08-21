{* Шаблон фильтра по цене *}
{if $param.show_cost_filter}
    {$is_open = $basefilters.cost || (is_array($param.expanded) && in_array('cost', $param.expanded))}
    <div class="accordion-item rs-type-interval">
        <div class="accordion-header">
            <button class="accordion-button {if !$is_open}collapsed{/if}" type="button" data-bs-toggle="collapse"
                    data-bs-target="#accordionCostFilter">
                <span class="me-2">{t}Цена{/t}</span>
            </button>
        </div>
        <div id="accordionCostFilter" class="accordion-collapse collapse {if $is_open}show{/if}">
            <div class="accordion-body">
                <div class="row row-cols-2 g-3">
                    <div>
                        <label class="form-label">{t}От{/t}</label>
                        <input type="number" class="form-control rs-filter-from"
                               min="{$moneyArray.interval_from}" max="{$moneyArray.interval_to}" name="bfilter[cost][from]"
                               value="{if !$catalog_config.price_like_slider}{$basefilters.cost.from}{else}{$basefilters.cost.from|default:$moneyArray.interval_from}{/if}"
                               data-start-value="{if $catalog_config.price_like_slider}{$moneyArray.interval_from|floatval}{/if}">
                    </div>
                    <div>
                        <label class="form-label">{t}До{/t}</label>
                        <input type="number" min="{$moneyArray.interval_from}" max="{$moneyArray.interval_to}"
                               class="form-control rs-filter-to" name="bfilter[cost][to]"
                               value="{if !$catalog_config.price_like_slider}{$basefilters.cost.to}{else}{$basefilters.cost.to|default:$moneyArray.interval_to}{/if}"
                               data-start-value="{if $catalog_config.price_like_slider}{$moneyArray.interval_to|floatval}{/if}">
                    </div>
                    {if $catalog_config.price_like_slider && ($moneyArray.interval_to > $moneyArray.interval_from)}
                        <div class="col-12">
                            <div class="px-3">
                                <input type="hidden" data-slider='{ "from":{$moneyArray.interval_from}, "to":{$moneyArray.interval_to},
                                                        "step": "{$moneyArray.step}", "round": {$moneyArray.round}, "dimension": " {$moneyArray.unit}",
                                                        "heterogeneity": [{$moneyArray.heterogeneity}]  }'
                                       value="{$basefilters.cost.from|default:$moneyArray.interval_from};{$basefilters.cost.to|default:$moneyArray.interval_to}"
                                       class="rs-plugin-input"/>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/if}