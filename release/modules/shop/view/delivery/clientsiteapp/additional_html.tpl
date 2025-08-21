{if $pvz_list}
    {$delivery = $delivery_type->getDelivery()}
    {$city = $order->getAddress()->getCity()}
    {$delivery_extra = $order->getExtraKeyPair('delivery_extra')}
    {$delivery_extra_value = json_decode(htmlspecialchars_decode("{$delivery_extra.pvz_data|default:""}"), true)}

    <div class="margin-16-top">
        <label class="checkout-label" for="pvz_delivery">Пункт выдачи</label>
        <select class="form-select clientsiteapp_extra" id="pvz_delivery" name="pvz_data" (change)="setDeliveryExtra($event)">
            {foreach $pvz_list as $pvz}
                <option value='{$pvz->getDeliveryExtraJson()}' {if !empty($delivery_extra_value.code) && ($delivery_extra_value.code == $pvz->getCode())}selected{/if}>
                    {$pvz->getAddress()}
                </option>
            {/foreach}
        </select>

        <div class="margin-16-top margin-16-bottom">
            <button class="button additional-button w-100" (click)="selectPvz()">Выбрать на карте</button>
        </div>
    </div>
{/if}
