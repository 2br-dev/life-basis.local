<div class="crm-column-item-title">
    <span><a href="{adminUrl do="edit" id=$item.id mod_controller="crm-taskctrl"}" class="crud-edit u-link">{$item.task_num}</a></span>
    <strong>{$item.title}</strong>
</div>

<span>{t}Создано{/t}: {$item.date_of_create|dateformat:"@date @time"}</span>
{if $item.date_of_planned_end}
    <br><span>{t}Выполнить до{/t}:
    <span class="c-{$item->getPlannedEndStatus()}" title="{$item->getPlannedEndStatusTitle()}">{$item.date_of_planned_end|dateformat:"@date @time"}</span>
    </span>
{/if}
{if $item.implementer_user_id}
    {$implementer=$item->getImplementerUser()}
    <br><span>{t}Исполнитель{/t}: {$implementer->getFio()} ({$implementer.id})</span>
{/if}
<br>

{$messages_count = $item->getUnreadChatMessagesCount()}
{$roles = $item->getRoleIcons()}
{$files = $item->getFiles()}
{$parent_status_icon = $item->getParentStatusIcon()}
{if $roles || $files || $parent_status_icon || $messages_count}
    <div class="m-t-10">
        {if $roles}
            {foreach $roles as $role}
                <i title="{$role.title}" class='m-r-5 {$role.class}' style="color: {$role.color}"></i>
            {/foreach}
        {/if}
        {if $files}
            <i title="{t}Есть вложение{/t}" class='m-r-5 zmdi zmdi-hc-lg zmdi-attachment-alt' style="color: #a7a7a7"></i>
        {/if}
        {if $parent_status_icon}
            <i title="{$parent_status_icon.title}" class='m-r-5 {$parent_status_icon.class}' style="color: {$parent_status_icon.color}"></i>
        {/if}
        {if $messages_count}
        <i
            title="{t count=$messages_count}Есть новые сообщения в чате (%count){/t}"
            data-root-id="{if $item.autotask_root_id}{$item.autotask_root_id}{else}{$item.id}{/if}"
            class="m-r-5 board-new-message-element"
        >
                <span class="c-red zmdi zmdi-hc-lg zmdi-comment-alt-text"></span>
        </i>
        {/if}
    </div>
{/if}

{if $progress = $item->getChecklistProgress()}
    <div class="m-t-10 m-r-20 board__checklist-progress">
        <div
            class="board__progress-label"
            title="{t done=$progress.done total=$progress.total}Чек-лист %done из %total{/t}"
        >{$progress.done}/{$progress.total} ({$progress.percent}%)</div>
        <div class="board__progress-bar-background">
            <div
                class="board__progress-bar-fill"
                style="width: {$progress.percent}%; background: {if $progress.done == $progress.total}#6ad183{else}#c3c3c3{/if}"
            ></div>
        </div>
    </div>
{/if}

<div class="view-element {if $item->isNew()} is-new{/if}"></div>


