<div class="table-mobile-wrapper">
    <table class="table" id="rights-table">
        <thead>
            <tr>
                <td class="p-l-0">
                    <input type="checkbox" value="1" title="Включить/выключить все методы" class="toggle-cb">
                </td>
                <td>{t}Метод API{/t}</td>
                <td>{t}Уровень доступа{/t}</td>
            </tr>
        </thead>
        <tbody>
        {foreach $field->getList() as $method => $data}
            <tr>
                <td class="p-l-0">
                    <input type="checkbox" {if isset($elem.rights[$method])}checked{/if} data-method="{$method}" class="enable-cb">
                </td>
                <td>
                    <strong>{$method}</strong> <i><small>({$data.info_last_version.comment})</small></i>
                </td>
                <td>
                    {$is_authorized_method = $data.instance instanceof \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod}
                    <div>
                        <label><input type="checkbox" name="rights[{$method}][]" class="right-cb" value="all"
                                      {if !isset($elem.rights[$method]) || in_array('all', (array)$elem.rights[$method])}checked{/if}
                                      {if !isset($elem.rights[$method])}disabled{/if}> {if $is_authorized_method}{t}Полный доступ{/t}{else}{t}Доступ без авторизации{/t}{/if}</label>
                    </div>
                    {if $is_authorized_method}
                        {foreach $data.instance->getRightTitles() as $key => $title}
                            <div>
                                <label><input type="checkbox" name="rights[{$method}][]" class="right-cb" value="{$key}"
                                            {if in_array($key, (array)$elem.rights[$method])}checked{/if}
                                            {if !isset($elem.rights[$method]) || in_array('all', (array)$elem.rights[$method])}disabled{/if}> {$title}</label>
                            </div>
                        {/foreach}
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>

<script>
    $(function() {
        let table = $('#rights-table');
        let tbodyTr = $('tbody tr', table);
        let toggleCheckBox = $('.toggle-cb', table);
        let allChecked = true;

        $('.right-cb[value="all"]', tbodyTr).on('change', function() {
            let tr = $(this).closest('tr');
            let enable = $('.enable-cb', tr);
            $('.right-cb:not([value="all"])', tr).prop('disabled', this.checked || !enable[0].checked);
        });

        table.closest('tr').find('.otitle').css('vertical-align', 'top');
        tbodyTr.each(function() {
            let tr = this;
            let checkBox = $('.enable-cb',tr);
            checkBox.on('change', function(event, noUnselectToggleCb) {
                $('.right-cb', tr).prop('disabled', !this.checked).trigger('change');
                if (!noUnselectToggleCb) {
                    toggleCheckBox.prop('checked', false);
                }
            });

            allChecked = allChecked && checkBox.is(':checked');
        });

        $('.toggle-cb', table).on('change', function() {
            $('.enable-cb', tbodyTr)
                .prop('checked', this.checked)
                .trigger('change', [true]);
        });

        toggleCheckBox.prop('checked', allChecked);
    });
</script>