{include file=$field->getOriginalTemplate()}

<script>
    $(function() {
        $('[name="bonus_card_class"]').change(function() {
            let bonusClass = $(this).val();
            $('[data-bonus-class]').closest('tr').hide();
            $('[data-bonus-class="' + bonusClass + '"]').closest('tr').show();
        }).change();
    });
</script>