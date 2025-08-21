{include file=$field->getOriginalTemplate()}
<div class="m-t-10">
    <a data-url="{adminUrl do="checkAuthorization" mod_controller="shop-trueapitools"}" data-confirm-text="{t}Изменения должны быть сохранены перед проверкой авторизации. Продолжить?{/t}" class="crud-get btn btn-default">Проверить авторизацию</a>
    <a data-url="{adminUrl do="updateHosts" mod_controller="shop-trueapitools"}" data-confirm-text="{t}Изменения должны быть сохранены перед обновлением хостов. Продолжить?{/t}" class="crud-get btn btn-default">Обновить список хостов ЧЗ</a>
</div>