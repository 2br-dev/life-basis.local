{$values = $field->get()}
{if is_array($values)}
    {foreach $values as $value}
        <input type="hidden" name="{$field->getFormName()}" value="{$value}" {$field->getAttr()} />
    {/foreach}
{else}
    <input type="hidden" name="{$field->getFormName()}" value="{$values}" {$field->getAttr()} />
{/if}
