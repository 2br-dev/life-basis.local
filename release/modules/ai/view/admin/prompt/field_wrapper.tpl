<div class="prompt-field-wrapper">
    {include file="%ai%/admin/prompt/field_form.tpl"}
</div>
<script>
    $(function() {
        $('select[name="transformer_id"]').change(function() {
            $.ajaxQuery({
                url: '{adminUrl do="changeTransformer"}',
                data: {
                    "transformer_id": $(this).val()
                },
                success: function(response) {
                    $('.prompt-field-wrapper').html(response.html);
                }
            });
        });
    });
</script>