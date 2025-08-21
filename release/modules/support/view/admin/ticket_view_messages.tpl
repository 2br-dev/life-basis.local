{foreach $messages as $message}
    <div class="ticket-card {if $message.is_admin}ticket-admin{/if} {if $message->isSystemMessage()}ticket-system{/if}">
        <div class="ticket-bar"></div>
        <div class="ticket-body">
            <div class="ticket-head">
                <div class="ticket-dates">
                    <div class="ticket-date">
                        {$message.dateof|dateformat:"@date @time"}
                        {if $message.is_delivered}
                            <i title="{t}Доставлено{/t}" class="zmdi zmdi-check"></i>
                        {/if}
                    </div>
                    {if $message.updated}
                        <div class="ticket-updated">{t date="{$message.updated|dateformat:"@date @time"}"}Изменено %date{/t}</div>
                    {/if}
                </div>
                <div class="ticket-tools">
                    {$username = $message->getUserName($topic)}
                    <div class="ticket-user">
                        {if !$message.is_admin && $topic['user_id'] > 0}
                            <a href="{adminUrl do="edit" mod_controller="users-ctrl" id=$topic['user_id']}" class="crud-edit">{$username}</a>
                        {else}
                            {$username}
                        {/if}
                    </div>
                    <div class="ticket-dropdown btn-group">
                        <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="zmdi zmdi-more-vert"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a class="crud-edit" href="{$router->getAdminUrl('edit', ['id' => $message.id])}">{t}редактировать{/t}</a></li>
                            <li class="first">
                                <a class="crud-get c-red" data-confirm-text="{t}Вы действительно хотите удалить данное сообщение?{/t}"
                                   href="{$router->getAdminUrl('del', ['id' => $message.id])}">{t}удалить{/t}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="ticket-message">
                {$message->getMessage()}
            </div>
            {$attachments = $message->getAttachments()}
            {if $attachments}
                <div class="ticket-attachments">
                    {foreach $attachments as $attachment}
                        <div class="ticket-file">
                            <a href="{$attachment->getHashedUrl()}">
                                <img src="{$mod_img}/icons/attachment.svg" width="16" alt="">
                                <span>{$attachment.name}</span>
                            </a>, {$attachment.size|format_filesize}
                        </div>
                    {/foreach}
                </div>
            {/if}
        </div>
    </div>
{/foreach}