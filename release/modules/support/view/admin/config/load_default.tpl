<div class="m-b-10">
    <a data-href="{adminUrl do="GetDefaultTemplate" mod_controller="support-crawlerctrl" type={$field->getName()}}"
       id="load-default-{$field->getName()}" data-name="{$field->getName()}">
        <i class="zmdi zmdi-refresh f-16"></i>
        {t}Загрузить типовой шаблон{/t}</a>
</div>
{include file=$field->getOriginalTemplate()}
<script>
    $(function() {
        $('#load-default-{$field->getName()}').click(function() {
            if (confirm(lang.t('Вы действительно желаете загрузить шаблон по-умолчанию'))) {
                let name = $(this).data('name');

                $.ajaxQuery({
                   url: $(this).data('href'),
                   success: function(response) {
                       if (response.success) {
                           $('textarea[name="' + name + '"]').val(response.html);
                           console.log( $('textarea[name="' + name + '"]') );
                       }
                   }
                });
            }
        });
    });
</script>