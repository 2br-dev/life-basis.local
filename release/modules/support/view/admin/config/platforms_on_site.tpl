{include file=$field->getOriginalTemplate()}
<script>
    $(function() {
        let onChange = function() {
            let checked = $(this).is(':checked');
            $("[name='platforms_on_site[]'][value!='all']")
                .prop('disabled', checked);
        };

        $("[name='platforms_on_site[]'][value='all']")
            .change(onChange)
            .change();
    });
</script>