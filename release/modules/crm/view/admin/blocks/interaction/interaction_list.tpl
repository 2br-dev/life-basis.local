{addjs file="%crm%/jquery.rs.blockcrm.js"}

{* Если права не переданы в шаблон, не учитывать их *}
{if $rights === null}
    {$rights['interaction_update'] = true}
    {$rights['interaction_read'] = true}
    {$rights['interaction_create'] = true}
    {$rights['interaction_delete'] = true}
{/if}

{if $link_type == 'crm-linktypeuser'}
    {$link_suffix='-user'}
{/if}
<div class="crm-block-interaction{$link_suffix}" data-refresh-url="{$this_controller->makeUrl()}"
                                   data-remove-url="{adminUrl do=false intdo="remove" link_type=$link_type link_id=$link_id mod_controller="crm-block-interactionblock"}" >
    <div class="notice notice-yellow">
        {t}Создавайте документ `взаимодействие` после каждого звонка или встречи с клиентом, фиксируйте результат.
        Так у вас сохранится вся история взаимодействия с клиентом.{/t}
    </div>

    <div class="tools-top">
        {if $rights['interaction_create']}
            <a class="btn btn-success add-interaction va-m-c" data-url="{adminUrl do=add link_type=$link_type link_id=$link_id from_call=$from_call mod_controller="crm-interactionctrl"}">
                <i class="zmdi zmdi-plus m-r-5 f-18"></i>
                <span>{t}Добавить взаимодействие{/t}</span>
            </a>
        {/if}
    </div>

    <div class="table-mobile-wrapper">
        <table class="rs-table values-list localform">
            <thead>
                <tr>
                    <th class="chk" style="width:26px">
                        <div class="chkhead-block">
                            <input type="checkbox" data-name="interaction[]" class="chk_head select-page" title="{t}Отметить элементы на этой странице{/t}">
                            <div class="onover">
                                <input type="checkbox" class="select-all" value="on" name="selectAll" title="{t}Отметить элементы на всех страницах{/t}">
                            </div>
                        </div>
                    </th>

                    {$columns=[
                    'title'               => [ 'sort' => true, 'name' => "{t}Краткое содержание{/t}" ],
                    'date_of_create'      => [ 'sort' => true, 'name' => "{t}Создано{/t}" ],
                    'creator_user_id'     => [ 'sort' => true, 'name' => "{t}Создатель{/t}"]
                    ]}

                    {foreach $columns as $key => $column}
                        <th>
                            {if $column.sort}
                                <a data-url="{$this_controller->makeUrl(['sort' => {$key}, 'nsort' => $default_n_sort[$key]])}" class="refresh sortable {if $cur_sort == $key}{$cur_n_sort}{/if}">{$column.name}</a>
                            {else}
                                {$column.name}
                            {/if}
                        </th>
                    {/foreach}

                    <th class="actions"></th>
                </tr>
            </thead>
            <tbody {if !$rights['interaction_read']}hidden{/if}>
                {foreach $interactions as $interaction}
                    <tr data-id="{$interaction.id}">
                        <td class="chk"><input type="checkbox" name="interaction[]" value="{$interaction.id}"></td>
                        <td><a class="interaction-edit" data-url="{adminUrl do=edit id={$interaction.id} mod_controller="crm-interactionctrl"}">{$interaction.title}</a></td>
                        <td>{$interaction.date_of_create|dateformat:"@date @time:@sec"}</td>
                        <td>
                            {$user=$interaction->getCreatorUser()}
                            {if $user->id > 0}
                                {if $current_user.id == $user->id}{t}Вы, {/t}{/if}
                                {$user->getFio()} ({$user->id})
                            {else}
                                {t}Не назначен{/t}
                            {/if}
                        </td>
                        <td class="actions">
                            <div class="inline-tools">
                                {if $rights['interaction_update']}
                                    <a data-url="{adminUrl do=edit id={$interaction.id} mod_controller="crm-interactionctrl"}" class="tool interaction-edit" title="{t}Редактировать{/t}"><i class="zmdi zmdi-edit"></i></a>
                                {/if}
                                {if $rights['interaction_delete']}
                                    <a class="tool interaction-del" title="{t}удалить{/t}"><i class="zmdi zmdi-delete c-red"></i></a>
                                {/if}
                            </div>
                        </td>
                    </tr>
                {foreachelse}
                    <tr>
                        <td colspan="5">{t}Пока нет ни одного взаимодействия{/t}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>

    <div class="tools-bottom">
        <div class="paginator virtual-form" data-action="{$this_controller->makeUrl(['int_page' => null, 'int_page_size' => null])}">
            {$paginator->getView(['is_virtual' => true])}
        </div>
    </div>

    {if $rights['interaction_delete']}
        <div class="group-toolbar">
            <span class="checked-offers">{t}Отмеченные<br> значения{/t}:</span>
            <a class="btn btn-danger delete">{t}Удалить{/t}</a>
        </div>
    {/if}
</div>

<script>
    $.allReady(function() {
        $('.crm-block-interaction{$link_suffix}').blockCrm({
            {if $link_suffix}
            counterElement: '.counter.crm-interaction{$link_suffix}'
            {/if}
        });
    });
</script>