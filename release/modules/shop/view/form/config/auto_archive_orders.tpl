{include file=$field->getOriginalTemplate()}
<script>
    $(function() {
        $('[name="auto_archive_orders"]').change(function() {
            $('[name="auto_archive_orders_after_days"]').closest('tr').toggle( $(this).is(':checked') );
        }).change();
    });
</script>