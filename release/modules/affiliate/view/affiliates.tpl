{* Диалог выбора города *}
{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "class"}modal-lg{/block}
{block "title"}
    {if $current_affiliate.id}{t affiliate=$current_affiliate.title}%affiliate. Выбрать другой город.{/t}{else}{t}Выбрать город{/t}{/if}
{/block}
{block "body"}
    <div class="mb-lg-4 mb-3 position-relative">
        <label for="input-city1" class="form-label">{t}Поиск города{/t}</label>
        <input type="text" class="form-control rs-city-search" id="input-city1" placeholder="{t}По названию{/t}" data-url-search="{$router->getUrl('affiliate-front-affiliates', ['Act' => 'ajaxSearch', 'referer' => $referer])}" autocomplete="off">
        <div class="head-search__dropdown w-100 rs-autocomplete-result"></div>
    </div>
    <div class="mb-lg-6 mb-4">
        <div class="row row-cols-lg-3 row-cols-2 g-3 fs-5 affiliate-name-list">
            {foreach $affiliates as $item}
                {if $item.fields.clickable}
                    <div class="item"><a class="{if $item.fields.is_highlight}fw-bold{/if}"
                            data-is-default="{$item.fields.is_default}"
                            data-redirect="{$item.fields->getChangeAffiliateUrl($referer)}">{$item.fields.title}</a></div>
                {else}
                    <div class="item {if $item.fields.is_highlight}fw-bold{/if}">{$item.fields.title}</div>
                {/if}

                {if $item.child}
                    {foreach $item.child as $subitem}
                        {if $subitem.fields.clickable}
                            <div class="item affiliate-sublevel">… <a class="{if $subitem.fields.is_highlight}fw-bold{/if}"
                               data-is-default="{$subitem.fields.is_default}"
                               data-redirect="{$subitem.fields->getChangeAffiliateUrl($referer)}">{$subitem.fields.title}</a></div>
                        {else}
                            <div class="item affiliate-sublevel">… <span class="{if $subitem.fields.is_highlight}fw-bold{/if}">{$subitem.fields.title}</span></div>
                        {/if}
                    {/foreach}
                {/if}
            {/foreach}
        </div>
    </div>
{/block}