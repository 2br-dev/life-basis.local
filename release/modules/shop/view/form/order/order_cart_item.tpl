{* Если права не переданы в шаблон, не учитывать их *}
{$rights_products_changing = $rights[Shop\Config\ModuleRights::RIGHT_PRODUCTS_CHANGING]}
<tr data-n="{$n}" class="item">
    <td class="l-w-space"></td>
    <td class="chk">
        <input type="checkbox" name="chk[]" value="{$n}" {if !$order->canEdit()
        || ($item.cartitem.type == 'product' && !$rights[Shop\Config\ModuleRights::RIGHT_PRODUCTS_DELETE])}disabled{/if}>
        <input type="hidden" name="items[{$n}][uniq]" value="{$n}">
        <input type="hidden" name="items[{$n}][title]" value="{$item.cartitem.title}">
        <input type="hidden" name="items[{$n}][entity_id]" value="{$item.cartitem.entity_id}">
        <input type="hidden" name="items[{$n}][type]" value="{$item.cartitem.type}">
        <input type="hidden" name="items[{$n}][single_weight]" value="{$item.cartitem.single_weight}">
        <input type="hidden" name="items[{$n}][discount_from_old_cost]" class="discount_from_old_cost" value="{$item.cartitem->getOldCostDiscount()}">
    </td>
    <td>
        {if $product->hasImage()}
            <a href="{$product->getMainImage(800, 600, 'xy')}" rel="lightbox-products" data-title="{$item.cartitem.title}"><img src="{$product->getMainImage(36,36, 'xy')}"></a>
        {else}
            <img src="{$product->getMainImage(36,36, 'xy')}">
        {/if}
    </td>
    <td>
        {if $rights[Shop\Config\ModuleRights::RIGHT_CARGO_READING]}
            <span class="zmdi {$products_in_cargo_status[$n].zmdi_class} m-r-5 product-in-cargo-status {$products_in_cargo_status[$n].status}" title="{$products_in_cargo_status[$n].status_text}"></span>
        {/if}
    </td>
    <td>
        {hook name="shop-orderview:cart-body-product-title" title=t('Редактирование заказа(админ. панель):Название товара в корзине заказа') item=$item product=$product}
            {if $product.id}
                <a href="{$product->getUrl()}" target="_blank" class="title">{$item.cartitem.title}</a>
            {else}
                {$item.cartitem.title}
            {/if}
                <br>
            {if !empty($item.cartitem.model)}{t}Модель{/t}: {$item.cartitem.model}{/if}
            {if $product.multioffers.use && $order->canEdit()}
                {$multioffers_values = unserialize($item.cartitem.multioffers)}
                <div>
                    {foreach $product.multioffers.levels as $level}
                        {foreach $level.values as $value}
                            {if $value.val_str == $multioffers_values[$level.prop_id].value}
                                <div class="offer_subinfo">
                                    {if $level.title}{$level.title}{else}{$level.prop_title}{/if} : {$value.val_str}
                                </div>
                            {/if}
                        {/foreach}
                    {/foreach}
                </div>
                {if $rights_products_changing}
                    <a class="show-change-offer btn btn-default">{t}изменить{/t}</a>
                {/if}

                <div class="change-offer-block unvisible">
                    <div class="multiOffers unvisible">
                        {foreach $product.multioffers.levels as $level}
                            {if !empty($level.values)}
                                <div class="title">{if $level.title}{$level.title}{else}{$level.prop_title}{/if}</div>
                                <select name="items[{$n}][multioffers][{$level.prop_id}]" class="product-multioffer " data-url="{adminUrl do="getOfferPrice" product_id=$product.id}" data-prop-title="{if $level.title}{$level.title}{else}{$level.prop_title}{/if}">
                                    {foreach $level.values as $value}
                                        <option value="{$value.val_str}" {if $value.val_str == $multioffers_values[$level.prop_id].value}selected="selected"{/if}>{$value.val_str}</option>
                                    {/foreach}
                                </select>
                            {/if}

                        {/foreach}

                        {if $product->isOffersUse()}
                            {* Комплектации к многомерным комлектациям *}

                            <select name="items[{$n}][offer]" class="product-offers unvisible">
                                {$exist = false}
                                {foreach from=$product.offers.items item=offer key=key}
                                    <option value="{$key}" id="offer_{$n}_{$key}" class="hidden_offers" {if $key == $item.cartitem.offer}selected="selected"{$exist = true}{/if} {if $catalog_config.use_offer_unit}data-unit="{$product.offers.items[$key]->getUnit()->stitle}"{/if} data-info='{$offer->getPropertiesJson()}' data-num="{$offer.num}">{$offer.title}</option>
                                {/foreach}
                                {if !$exist}
                                    <option value="{$item.cartitem.offer}" id="offer_{$n}_{$item.cartitem.offer}" class="hidden_offers" selected="selected" {if $catalog_config.use_offer_unit}data-unit=""{/if} data-info='' data-num="0">{$item.cartitem.model}{t}(удалено){/t}</option>
                                {/if}
                            </select>

                            {* Комплектации к многомерным комлектациям *}

                            <select class="product-offer-cost unvisible">{*Сюда будут вставлены цены комплектации*}</select>
                            <input type="button" value="OK" class="apply-cost-btn unvisible"/>
                        {/if}
                    </div>
                </div>
            {elseif $product->isOffersUse() && $order->canEdit()}
                {if $rights_products_changing}
                    <a class="show-change-offer btn btn-default">{t}изменить{/t}</a>
                {/if}

                <div class="change-offer-block unvisible">
                    <select name="items[{$n}][offer]" class="product-offer unvisible" data-url="{adminUrl do="getOfferPrice" product_id=$product.id}">
                        {$exist = false}
                        {foreach $product.offers.items as $key => $offer}
                            <option value="{$key}" {if $key == $item.cartitem.offer}selected="selected"{$exist = true}{/if} {if $catalog_config.use_offer_unit}data-unit="{$product.offers.items[$key]->getUnit()->stitle}"{/if}>{$offer.title}</option>
                        {/foreach}
                        {if !$exist}
                            <option value="{$item.cartitem.offer}" id="offer_{$n}_{$item.cartitem.offer}" class="hidden_offers" selected="selected" {if $catalog_config.use_offer_unit}data-unit=""{/if} data-info='' data-num="0">{$item.cartitem.model}{t}(удалено){/t}</option>
                        {/if}
                    </select>
                    <select class="product-offer-cost unvisible">{*Сюда будут вставлены цены комплектации*}</select>
                    <input type="button" value="OK" class="btn btn-default apply-cost-btn unvisible"/>
                </div>
            {/if}

            {* Кнопка показа рекомендуемых или сопутствующих *}
            {if $order->canEdit() && $product.id && ($product->isHaveRecommended() || $product->isHaveConcomitant())}
                <a id="showRecommended{$n}" class="show-recommended" data-url="{adminUrl do="getRecommendedAndConcomitantBlock" order_id=$order.id ids=[$product.id]}" data-product-id="{$product.id}" data-id="{$n}"><span>{t}Показать доп. товары{/t}</span></a>
            {/if}
        {/hook}
        {if !in_array($product.amount_step, ['', '0', '1'])}<span class="amount_step">Рекомендуемый шаг изменения количества товара:{$product.amount_step} {$product->getUnit()->stitle}</span>{/if}

    </td>
    <td>{$item.cartitem.barcode}</td>
    <td>{$item.cartitem.single_weight}</td>
    <td>
        <input type="text" {if !$rights_products_changing}disabled{/if} name="items[{$n}][single_cost]" class="invalidate single_cost" value="{$item.single_cost_noformat}" size="10" {if !$order->canEdit()}disabled{/if}>
    </td>
    <td>
        <div class="amount-block {if $shop_config.check_quantity && $item.cartitem.amount > $order_available_stocks[$item.cartitem.offer]}amount-error{/if}" {if $shop_config.check_quantity}data-max-amount="{$order_available_stocks[$item.cartitem.offer]}"{/if}>
            <input type="text" {if !$rights_products_changing}disabled{/if} name="items[{$n}][amount]" class="invalidate num" value="{$item.cartitem.amount}" size="4" data-product-id="{$product.id}" {if !$order->canEdit()}disabled{/if}>
            {if $catalog_config.use_offer_unit}
                <span class="unit">
                {$item.cartitem.data.unit}
            </span>
            {/if}
            {if $shop_config.check_quantity}
                <i title="{t}Недостаточно товара на складе.<br>Максимально доступное количество:{/t} {$order_available_stocks[$item.cartitem.offer]}" class="icon-amount-error zmdi zmdi-alert-triangle"></i>
            {/if}
        </div>
    </td>
    <td>
        <span class="cost">{$item.total}</span>
        {if $item.discount_unformated > 0}<div class="discount">{t discount=$item.discount}скидка %discount{/t}</div>{/if}
    </td>
    <td class="r-w-space"></td>
</tr>