{$row=$cell->getRow()}
{$progress = $row->getChecklistProgress()}
{if $progress}
    {$progress.done}/{$progress.total} ({$progress.percent}%)
{else}
    {t}Нет{/t}
{/if}