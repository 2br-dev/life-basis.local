<div class="task-log__entry system">
    {$entry.message nofilter}
    {if $entry.changes}
        <ul>
            {foreach $entry.changes as $change}
                <li>
                    <strong>{$change.title|escape}</strong>:
                    {if $change.field == 'checklist' && $change.summary}
                        {$change.summary|escape}
                    {else}
                        {if $change.before != null}
                            {$change.before|escape}
                        {else}
                            <em>пусто</em>
                        {/if}
                        →
                        <strong>
                            {if $change.after != null}
                                {$change.after|escape}
                            {else}
                                <em>пусто</em>
                            {/if}
                        </strong>
                    {/if}
                </li>
            {/foreach}
        </ul>
    {/if}
</div>
