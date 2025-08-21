{addcss file="%crm%/chat.css"}
{addjs file="%crm%/jquery.rs.chat.js?v=3"}

{$can_update=$elem->canChatUpdate()}
{$can_read_files=$elem->canChatReadFiles()}
{$can_add_files=$elem->canChatAddFiles()}

{if $chat_history}
    <div
            class="task-log__wrapper"
            data-task-id="{$elem.id}"
            data-root-id="{if $elem.autotask_root_id}{$elem.autotask_root_id}{else}{$elem.id}{/if}"
            data-first-id="{$elem.chat.first_id}"
            data-last-id="{$elem.chat.last_id}"
            data-unread-chat-messages-count="{$elem->getUnreadChatMessagesCount()}"
            data-can-update="{if $can_update}true{else}false{/if}"
            data-send-message-url='{$router->getAdminUrl('', ['do' => 'AjaxSendMessage'], 'crm-taskctrl')}'
            data-update-url="{$router->getAdminUrl('', ['do' => 'AjaxGetNewMessages'], 'crm-taskctrl')}"
    >
        <div class="task-log__entries-wrapper">
            <div class="task-log__entries">
                {foreach $elem.chat.messages as $date => $entries}
                    <div class="log-date__separator" data-date="{$date}">{$date|date_format:"d.m.Y"}</div>

                    {foreach $entries as $entry}
                        {include file="%crm%/form/chat/chat_entry.tpl" can_update=$can_update}
                    {/foreach}
                {/foreach}
            </div>

            <div class="scroll-to-bottom-btn hide" id="scrollToBottomBtn">
                <div class="message-count hide" id="newMessageCount"></div>
                <div class="arrow">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
            </div>
        </div>

        {if $can_update}
            <div class="task-log__form">
                <div class="reply-preview hide">
                    <div class="reply-content">
                        <div class="reply-author">Имя отправителя</div>
                        <div class="reply-text">Текст сообщения...</div>
                    </div>
                    <span class="cancel-reply" title="Отменить">
                <i class="zmdi zmdi-close"></i>
            </span>
                </div>
                <div class="input-wrapper m-b-10">
                    <textarea id="messageInput" rows="1" placeholder="Напишите сообщение..." maxlength="3000"></textarea>

                    <button id="sendBtn" class="send-btn" disabled title="Отправить сообщение (Ctrl+Enter)">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="white">
                            <path d="M2 21l21-9L2 3v7l15 2-15 2v7z" />
                        </svg>
                    </button>
                </div>

                {if $can_read_files && $can_add_files}
                    <div class="w-100 attachment-zone">
                        {$chat_history.__attachments->formView(['force_client_view' => true])}
                    </div>
                {/if}
            </div>
        {/if}
    </div>

    <script>
        $.allReady(function () {
            $('.task-log__wrapper').chatHistory();
        });
    </script>
{else}
    <p>Для доступа к чату необходимо сохранить задачу</p>
{/if}

