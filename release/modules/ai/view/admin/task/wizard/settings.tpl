{addcss file="%ai%/admin/task.css"}

<div data-dialog-options='{ "width": "800", "height":700 }'>
    {if $fields}
        <form action="{urlMake do="wizardSettings"}" method="POST" class="crud-form task-form-settings">
            <p class="m-t-20">{t}Выберите поля, которые необходимо заполнить с помощью ИИ:{/t}</p>
            <div class="notice m-b-20">По умолчанию, запрос на генерацию будет отправлен только, если поле в объекте не заполнено,
                чтобы не повредить существующие значения. Если вам необходимо в любом случае сгенерировать новые данные для полей, то включите для поля "Режим перезаписи"</div>

            <div class="fields">
                <div class="panel panel-default ai-panel-head">
                    <div class="panel-heading" role="tab">
                        <label class="va-m-c m-0">
                            <input type="checkbox" class="m-0 m-r-5 check-all">
                            <span>{t}Выбрать все{/t}</span>
                        </label>
                        <a role="button" data-toggle="collapse" class="pull-right toggle-all-button" href="#{$tab_id}" aria-expanded="false" aria-controls="{$tab_id}">
                            <i class="zmdi zmdi-settings"></i>
                        </a>
                    </div>
                </div>

                {foreach $fields as $n => $field}
                    {$tab_id="field-{$n}"}
                    <div class="panel panel-default ai-panel">
                        <div class="panel-heading" role="tab">
                            <label class="va-m-c m-0">
                                <input type="checkbox" name="settings[fields][{$field->getFieldName()}][enable]" value="1" class="m-0 m-r-5 check-one">
                                <span>{$field->getTitle()}</span>
                            </label>
                            <a role="button" data-toggle="collapse" class="pull-right collapsed show toggle-button" href="#{$tab_id}" aria-expanded="false" aria-controls="{$tab_id}">
                                <i class="zmdi zmdi-settings"></i>
                            </a>
                        </div>
                        <div id="{$tab_id}" class="panel-collapse collapse" role="tabpanel">
                            <div class="panel-body">
                                <table class="otable table-bordered-hor">
                                    {$field->getSettingFormObject()->getForm(null, null, false, null, '%system%/coreobject/tr_form.tpl')}
                                </table>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        </form>
    {else}
        <div class="notice notice-danger">{t}У вас не настроено ни одного промпта (запроса к ИИ) для заполнения полей. Перейдите в раздел <a class="u-link" href="{adminUrl do=false mod_controller="ai-promptctrl"}">Шаблоны запросов к ИИ</a>.{/t}</div>
    {/if}
</div>

<script>
    $.allReady(() => {
        let context = $('.task-form-settings');
        if (context.length) {

            let checkAll = $('.check-all', context);
            let checkBoxes = $('.ai-panel .check-one', context);

            checkAll.change(function () {
                checkBoxes.prop('checked', $(this).prop('checked'));
            });
            checkBoxes.change(function () {
                checkAll.prop('checked', false);
            });

            let toggleButtons = $('.toggle-button', context);
            $('.toggle-all-button', context).click(function() {
                let action = toggleButtons[0].classList.contains('collapsed') ? 'show' : 'hide';
                $('.panel-collapse', context).collapse(action)
            })
        }
    });
</script>