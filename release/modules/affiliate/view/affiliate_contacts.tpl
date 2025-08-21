{$main_config = ConfigLoader::byModule('main')}
{$main_config->initMapJs()}
{addjs file="%affiliate%/rscomponent/affiliate.js"}
{addjs file="%main%/rscomponent/mapblock.js"}

<section>
    <h1>{t}Контакты{/t}</h1>
    <div class="mt-5">
        <div class="fs-3 mb-3">{t}Ваш город{/t}</div>
        <div class="city-select">
            <span class="city-select__name">{$affiliate.title}</span>
            <a href="#affiliate-list" data-bs-toggle="collapse" role="button">{t}Изменить{/t}</a>
        </div>

        <div class="affiliate-list collapse" id="affiliate-list">
            <div class="mb-lg-4 mb-3 position-relative col-md-4">
                <label for="input-city1" class="form-label">{t}Поиск города{/t}</label>
                <input type="text" class="form-control rs-city-search" id="input-city1" placeholder="{t}По названию{/t}" data-url-search="{$router->getUrl('affiliate-front-affiliates', ['Act' => 'ajaxSearch', 'contact_page' => 1])}">
                <div class="head-search__dropdown w-100 rs-autocomplete-result"></div>
            </div>
            <div class="affiliate-list__columns row row-cols-2 row-cols-sm-3 row-cols-lg-4 row-cols-xxl-5 g-3 fs-5">
            {foreach $affiliates as $item}
                {if $item.fields.clickable}
                    <div><a class="{if $item.fields.is_highlight}fw-bold{/if}"
                            data-is-default="{$item.fields.is_default}"
                            href="{$item.fields->getContactPageUrl()}">{$item.fields.title}</a></div>
                {else}
                    <div class="{if $item.fields.is_highlight}fw-bold{/if}">{$item.fields.title}</div>
                {/if}

                {if $item.child}
                    {foreach $item.child as $subitem}
                        {if $subitem.fields.clickable}
                            <div class="affiliate-sublevel">… <a class="{if $subitem.fields.is_highlight}fw-bold{/if}"
                                      data-is-default="{$subitem.fields.is_default}"
                                      href="{$subitem.fields->getContactPageUrl()}">{$subitem.fields.title}</a></div>
                        {else}
                            <div class="affiliate-sublevel">… <span class="{if $subitem.fields.is_highlight}fw-bold{/if}">{$subitem.fields.title}</span></div>
                        {/if}
                    {/foreach}
                {/if}

            {/foreach}
            </div>
        </div>

    </div>
    <div class="mt-6">
        {$affiliate.contacts}
    </div>

    {if !empty($affiliate.coord_lat) && !empty($affiliate.coord_lng)}
        <div class="mt-5">
            <h3>{t}Схема проезда{/t}</h3>
            <div class="mt-5 mb-5">
                {moduleinsert name='Main\Controller\Block\Map' width=0 height=450 points=[[ 'lat' => $affiliate.coord_lat, 'lon' => $affiliate.coord_lng, 'balloonContent' => '' ]] zoom=11}
            </div>
        </div>
    {/if}
</section>

{moduleinsert name="Affiliate\Controller\Block\LinkedWarehouse"}