{if $entry.type == 'system'}
    {include file="%crm%/form/chat/chat_entry_system.tpl"}
{else}
    {include file="%crm%/form/chat/chat_entry_message.tpl"}
{/if}
