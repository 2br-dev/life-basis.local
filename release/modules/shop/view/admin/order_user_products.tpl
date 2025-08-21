{$order = $cell->getRow()}
{$cart = $order->getCart()}
{if $cart}
    {$items = $cart->getProductItems()}
    {if !$items}
        {t}Нет товаров{/t}
    {else}
        {$first_product = reset($items)}
        <span>{$first_product.cartitem.title}</span>
        <small class="c-gray">
            {$multioffers_values = unserialize($first_product.cartitem.multioffers|default:'')}
            {if !empty($multioffers_values)}
                <br>
                {$offer = array()}
                {foreach $multioffers_values as $mo_value}
                    {$offer[] = "{$mo_value.title}: {$mo_value.value}"}
                {/foreach}
                {implode(', &nbsp; ', $offer)}
            {elseif !empty($first_product.cartitem.model)}
                {t}Модель{/t}: {$first_product.cartitem.model}
            {/if}
        </small>

        {$count_items = count($items)}
        {if $count_items > 1}
            <span class="bg-softgray p-l-5 p-r-5 brd-2">{t n={$count_items-1}}+%n{/t}</span>
        {/if}
    {/if}
{/if}