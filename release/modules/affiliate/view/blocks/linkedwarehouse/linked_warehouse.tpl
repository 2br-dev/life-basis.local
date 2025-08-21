{if $warehouses}
<section class="section pt-0">
    <div class="container">
        <h2 class="m-0">{t}Магазины в вашем городе{/t}</h2>
        <div class="city-warehouses">
            <div class="city-warehouses__head">
                <div class="col">{t}Адрес магазина{/t}</div>
                <div class="city-warehouses__phone">{t}Телефон{/t}</div>
                <div class="col d-flex justify-content-end">
                    <div class="city-warehouses__mode">{t}Режим работы{/t}</div>
                </div>
            </div>
            {foreach $warehouses as $warehouse}
                <div class="city-warehouses__item">
                    <div class="col">{$warehouse.adress}</div>
                    <div class="city-warehouses__phone">
                        <a class="text-nowrap text-inherit" href="tel:{$warehouse.phone|format_phone}">{$warehouse.phone}</a>
                    </div>
                    <div class="col d-flex justify-content-end">
                        <div class="city-warehouses__mode">
                            <div class="me-3">{$warehouse.work_time}</div>
                            <div><a href="{$router->getUrl('catalog-front-warehouse', ["id" => {$warehouse.alias|default:$warehouse.id}])}">{t}Подробнее{/t}</a></div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</section>
{/if}