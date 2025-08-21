{include file=$field->getOriginalTemplate()}
<script>
    $(function() {
        $('[name="type_code_provider"]').change(function() {
            $('[name^="telegram_gw"]').closest('tr').toggle($(this).val() == 'telegram-gateway');
        }).change();
    });
</script>