{addjs file="%catalog%/rscomponent/sidefilters.js"}
{$catalog_config = ConfigLoader::byModule('catalog')}

<div class="rs-filter-wrapper">
    <div class="rs-filter-section {if $basefilters || $filters}rs-filter-active{/if}" data-query-value="{$url->get('query', $smarty.const.TYPE_STRING)}">
        <div class="catalog-filter">
            <div class="catalog-filter__head">
                <div class="h3">
                    <img width="24" height="24" src="{$THEME_IMG}/icons/filter.svg" alt="">
                    <span class="ms-2">{t}Фильтры{/t}</span>
                </div>
                <div>
                    <div class="offcanvas-close">
                        <img src="{$THEME_IMG}/icons/close.svg" width="24" height="24" alt="">
                    </div>
                </div>
            </div>
            <form method="GET" class="rs-filters" action="{urlmake filters=null pf=null bfilter=null p=null}" autocomplete="off">
                <div class="accordion filter-accordion mt-4">

                    {include file="%catalog%/blocks/sidefilters/type/cost.tpl"}

                    {include file="%catalog%/blocks/sidefilters/type/stock.tpl"}

                    {include file="%catalog%/blocks/sidefilters/type/brand.tpl"}

                    {foreach $prop_list as $item}
                        {foreach $item.properties as $prop}
                            {include file="%catalog%/blocks/sidefilters/type/{$prop.type}.tpl"}
                        {/foreach}
                    {/foreach}
                </div>
            </form>
        </div>
        <div class="catalog-offcanvas-buttons">
            <button class="btn btn-primary offcanvas-close w-100 mt-3 catalog-filter__apply">{t}Применить фильтр{/t}</button>
            <button type="button" class="btn btn-outline-primary offcanvas-close col-12 mt-3 catalog-filter__clean rs-clean-filter">{t}Сбросить фильтры{/t}</button>
        </div>
    </div>
</div>