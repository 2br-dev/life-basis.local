<div class="task-log__entry message {if $entry.user.is_current_user}right{else}left{/if}" data-id="{$entry.message_id}">
    <div class="bubble">
        {if $entry.reply_to}
            <div class="quoted-message" data-id="{$entry.reply_to.message_id}">
                <div class="quoted-author">{$entry.reply_to.user.name|escape}</div>
                <div class="quoted-text">{$entry.reply_to.message|escape}</div>
            </div>
        {/if}

        <div class="meta">
            {$entry.user.name|escape} • {$entry.time}
        </div>
        <div class="text">{$entry.message|escape}</div>
        <div class="attachments">
            {if $entry.attachments}
                <div class="ticket-attachments m-t-10">
                    {foreach $entry.attachments as $attachment}
                        <div class="ticket-file m-b-5">
                            <a href="{$attachment.url}" target="_blank">
                                <i class="zmdi zmdi-attachment-alt"></i>
                                <span>{$attachment.name}</span>
                            </a>, {$attachment.size|format_filesize}
                        </div>
                    {/foreach}
                </div>
            {/if}
        </div>
        {if !$elem || $can_update}
            <span class="reply-icon" title="Ответить">
                <i class="zmdi zmdi-hc-lg zmdi-mail-reply"></i>
            </span>
        {/if}
    </div>
</div>
