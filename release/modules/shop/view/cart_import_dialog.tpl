{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Загрузка корзины из CSV{/t}{/block}
{block "class"}modal-lg{/block}
{block "attributes"}id="rs-import-cart"{/block}
{block "body"}
    <p>{t}Вы можете добавить товары в корзину из CSV файла (Excel).
        Файл должен содержать колонки строго в указанной последовательности.
        Первая строка в файле будет игнорироваться, так как в ней ожидаются названия колонок.{/t}</p>
    <ol>
        <li>{t}Артикул{/t}</li>
        <li>{t}Наименование товара (может быть пустое){/t}</li>
        <li>{t}Количество{/t}</li>
    </ol>

    <p>{t charset=$charset}Ожидается следующая кодировка данных в CSV-файле: %charset.
            Во время импорта товары в первую очередь будут сопоставляться по артикулу, во вторую - по названию.{/t}</p>

    <form method="POST" action="{urlMake}" enctype="multipart/form-data">
        <div class="mb-4">
            <input type="file" class="form-control" name="file">
            {if $error}
                <div class="invalid-feedback d-block">{$error}</div>
            {/if}
        </div>
        <div>
            <input type="submit" value="{t}Отправить{/t}" class="btn btn-primary">
        </div>
    </form>
{/block}