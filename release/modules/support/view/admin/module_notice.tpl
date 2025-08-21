{if $unexists_modules}
<div class="notice notice-danger m-b-10">
    {t modules="{$unexists_modules|join:", "}"}Отсутствуют следующие модули, необходимые для работы сборщика почты: <b>%modules</b>. Включите данные модули в настройках вашего хостинга.{/t}<br>
</div>
{/if}