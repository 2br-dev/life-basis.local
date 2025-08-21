{include file=$field->getOriginalTemplate()}

<script>
    $(function() {
        $('[name="ban_type"]').change(function() {
            let hasBan = $(this).val() > 0;
            let isTemporaryBan = $(this).val() == 2;
            $('[name="ban_expire"]').closest('tr').toggle(isTemporaryBan);
            $('[name="ban_reason"]').closest('tr').toggle(hasBan);
        }).change();
    });
</script>