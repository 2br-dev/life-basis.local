{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Загрузка корзины завершена{/t}{/block}
{block "class"}modal-lg{/block}
{block "attributes"}id="rs-import-cart"{/block}
{block "body"}
    {if $import_result}
        <div class="alert alert-success">
            {t}Все товары были успешно добавлены в корзину.{/t}
        </div>
    {else}
        <div class="alert alert-warning">
            {t}Во время обработки файла возникли ошибки.{/t}
        </div>

        <div class="mt-4">
            <h4>Отчет</h4>
            <ul>
                <li>{t}Добавлено позиций{/t}: {$report.added_count}</li>
                {if $report.missing_products}
                    <li>{t}Не найдены товары{/t}: <i>{$report.missing_products|join:", "|escape}</i></li>
                {/if}
                {if $report.internal_error}
                    <li>{t}Ошибка{/t}: {$report.internal_error}</li>
                {/if}
            </ul>
        </div>
    {/if}

    <div class="mt-6 d-flex flex-column flex-sm-row justify-content-center gap-3">
        <a href="JavaScript:location.reload();" class="btn btn-primary">{t}Перейти в корзину{/t}</a>
        <a href="{$router->getUrl('shop-front-cartpage', ['Act' => 'importCart'])}" class="btn btn-secondary rs-in-dialog">{t}Загрузить другой CSV-файл{/t}</a>
    </div>
{/block}