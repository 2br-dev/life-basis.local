{$name = $fitem->getName()}
{$value = $fitem->getValue()}
{foreach $fitem->costs as $cost}
    <div class="m-b-15">
        <label>{t}Цена - {/t}{$cost.title}</label><br>
        <input type="text" name="{$name}[{$cost.id}][from]" value="{$value[$cost.id].from}" class="pr-int" placeholder="{t}от{/t}"> -
        <input type="text" name="{$name}[{$cost.id}][to]" value="{$value[$cost.id].to}" class="pr-int" placeholder="{t}до{/t}">
    </div>
{/foreach}
