<span class="ai-models">
    {include file="%ai%/admin/service/model_form.tpl"}
</span>

<a data-href="{adminUrl do="loadModels"}" class="ai-models-refresh btn btn-default">Обновить список</a>
<script>
    $(function () {
        $('.ai-models-refresh').on('click', function (e) {
            const form = $(this).closest('form');

            $.ajaxQuery({
                method:'POST',
                url: $(this).data('href'),
                data: form.serializeArray(),
                success: function (response) {
                    if (response.success) {
                        $('.ai-models').html(response.html);
                    }
                }
            });
        });
    })
</script>