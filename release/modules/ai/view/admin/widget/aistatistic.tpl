{* Виджет: Статистика использования запросов к ИИ *}
{addcss file="{$mod_css}admin/ai-statistic.css?v=3" basepath="root"}
{addjs file="flot/jquery.flot.min.js" basepath="common"}
{addjs file="flot/jquery.flot.time.js" basepath="common" waitbefore=true}
{addjs file="flot/jquery.flot.resize.min.js" basepath="common"}
{addjs file="{$mod_js}admin/jquery.ai-statistic.js" basepath="root"}

<div class="ai-statistic-widget" id="aiStatistic">
    <div class="widget-filters">
        {* Фильтр по периоду *}
        <div class="dropdown">
            <a id="last-order-switcher" data-toggle="dropdown" class="widget-dropdown-handle">{if $range=='lastyear'}{t}Последний год{/t}{else}{t}Последний месяц{/t}{/if} <i class="zmdi zmdi-chevron-down"></i></a>
            <ul class="dropdown-menu" aria-labelledby="last-order-switcher">
                <li {if $range=='lastyear'}class="act"{/if}><a data-update-url="{adminUrl mod_controller="ai-widget-aistatistic" aistatistic_range="lastyear" aistatistic_filter=$filter}" class="call-update">{t}Последний год{/t}</a></li>
                <li {if $range=='lastmonth'}class="act"{/if}><a data-update-url="{adminUrl mod_controller="ai-widget-aistatistic" aistatistic_range="lastmonth" aistatistic_filter=$filter}" class="call-update">{t}Последний месяц{/t}</a></li>
            </ul>
        </div>

        {if $can_show_all_statistic}
            {* Фильтр по графикам *}
            <div class="dropdown">
                <a id="last-order-filter" data-toggle="dropdown" class="widget-dropdown-handle">{if $filter=='all'}{t}Все запросы{/t}{else}{t}Мои запросы{/t}{/if} <i class="zmdi zmdi-chevron-down"></i></a>
                <ul class="dropdown-menu year-filter" aria-labelledby="last-order-filter">
                    <li {if $filter=='all'}class="act"{/if}><a data-update-url="{adminUrl mod_controller="ai-widget-aistatistic" aistatistic_range=$range aistatistic_filter="all"}" class="call-update">{t}Все запросы{/t}</a></li>
                    <li {if $filter=='my'}class="act"{/if}><a data-update-url="{adminUrl mod_controller="ai-widget-aistatistic" aistatistic_range=$range aistatistic_filter="my"}" class="call-update">{t}Мои запросы{/t}</a></li>
                </ul>
            </div>
        {/if}

        {* Фильтр по графикам *}
        <div class="dropdown">
            <a id="last-order-filter" data-toggle="dropdown" class="widget-dropdown-handle">{t}График{/t} <i class="zmdi zmdi-chevron-down"></i></a>
            <ul class="dropdown-menu chart-filter" aria-labelledby="last-order-filter">
                <li><label><input type="checkbox" value="total_requests" checked> {t}Кол-во запросов{/t}</label></li>
                <li><label><input type="checkbox" value="input_tokens_sum" checked> {t}Токенов в запросе{/t}</label></li>
                <li><label><input type="checkbox" value="completion_tokens_sum" checked> {t}Токенов в ответе{/t}</label></li>
                <li><label><input type="checkbox" value="total_tokens_sum" checked> {t}Всего токенов{/t}</label></li>
            </ul>
        </div>
    </div>

    {if $dynamics_arr}
        <div class="placeholder" style="height:300px;" data-inline-data='{$chart_data|escape}'></div>

        <script>
            $.allReady(function() {
                $('#aiStatistic').aiStatistic();
            });
        </script>
    {else}
        <div class="empty-widget">
            {t}Пока не было ни одного запроса{/t}
        </div>
    {/if}
</div>