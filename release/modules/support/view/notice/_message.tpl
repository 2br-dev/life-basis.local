{if $message}
    <h3>{t}Сообщение{/t}</h3>
    {$message->getMessage()}

    {$attachments = $message->getAttachments()}
    {if $attachments}
        <h3>{t}Вложения{/t}</h3>
        {foreach $attachments as $file}
            <a href="{$file->getHashedUrl(true)}">{$file.name}</a>, {$file->getSizeStr()}<br>
        {/foreach}
    {/if}
{/if}