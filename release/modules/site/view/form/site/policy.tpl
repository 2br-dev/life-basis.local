<p><a id="load-{$field->getName()}">{t}Загрузить типовой документ{/t}</a></p>
{include file=$field->getOriginalTemplate() field=$field}

<script>
    $('#load-{$field->getName()}').click(function() {
        if (confirm(lang.t('Вы действительно желаете загрузить типовой документ (текущий текст в редакторе будет заменен)?'))) {
            $.ajaxQuery({
                url: '{$field->loadDefaultUrl}',
                success: function(response) {
                    if (response.success) {
                        $('[name="{$field->getName()}"]').val(response.html);
                    }
                }
            });
        }
    });
</script>