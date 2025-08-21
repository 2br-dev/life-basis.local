{if is_array($cell->getValue())}
    {foreach $cell->getValue() as $key => $value}{$value}{if !$value@last}, {/if}{/foreach}
{/if}