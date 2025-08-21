{include file=$field->getOriginalTemplate()}
<div class="notice-box notice-info m-t-10" style="max-width:500px">
    {t}Адрес для доступа к API для данного приложения будет выглядеть так: <strong>/api-[ваш api-ключ]/</strong>{/t}
</div>

<script>
    $(function() {
        $('input[name="use_vapp_endpoint"]').change(function() {
            $('[name="vapp_endpoint_api_key"], [name="vapp_endpoint_enable_api_help"]')
                .closest('tr')
                .toggle($(this).is(':checked'));
        }).change();
    });
</script>