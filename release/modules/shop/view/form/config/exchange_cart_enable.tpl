{include file=$field->getOriginalTemplate()}

<script>
    $(function() {
        $('[name="exchange_cart_enable"]').change(function() {
            let enable = $(this).val();
            $('[data-show-when="exchange_cart_enable"]').closest('tr').toggle(enable);
        }).change();
    });
</script>