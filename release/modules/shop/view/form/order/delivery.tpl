{$type_object = $delivery->getTypeObject()}
{$rights_delivery_changing = $rights[Shop\Config\ModuleRights::RIGHT_DELIVERY_CHANGING]}

<h3>
    {t}Доставка{/t}
    {if $elem.delivery>0 && $rights_delivery_changing}
        <a href="{adminUrl do=deliveryDialog order_id=$elem.id delivery=$elem.delivery user_id=$user_id}" class="crud-add m-l-10" id="editDelivery" title="{t}редактировать{/t}">
            <i class="zmdi zmdi-edit"></i>
        </a>
    {/if}
</h3>

{if $elem.delivery>0}
    <input type="hidden" name="delivery" value="{$elem.delivery}"/>
    {* Блок о доставке *}

    <table class="otable delivery-params">
        <tr>
            <td class="otitle">
                {t}Тип{/t}
            </td>
            <td class="d_title">{$delivery.title}</td>
        </tr>
        {if !empty($warehouse_list) && $rights[Shop\Config\ModuleRights::RIGHT_WAREHOUSE_READING]}
            <tr>
                <td class="otitle">{t}Склад{/t}</td>
                <td class="d_warehouse">
                    {if $rights_delivery_changing}
                        <select name="warehouse">
                            <option value="0">{t}не выбран{/t}</option>
                            {foreach $warehouse_list as $warehouse}
                                <option value="{$warehouse.id}" {if $elem.warehouse == $warehouse.id}selected="selected"{/if}>{$warehouse.title}</option>
                            {/foreach}
                        </select>
                    {else}
                        {foreach $warehouse_list as $warehouse}
                            {if $elem.warehouse == $warehouse.id}
                                <select readonly name="warehouse">
                                    <option value="{$warehouse.id}" selected="selected">{$warehouse.title}</option>
                                </select>
                            {/if}
                        {/foreach}
                    {/if}
                </td>
            </tr>
        {/if}

        {if $courier_list && $rights[Shop\Config\ModuleRights::RIGHT_COURIER_READING]}
            <tr>
                <td class="otitle">{t}Курьер{/t}</td>
                <td>
                    {if $rights_delivery_changing}
                        <select name="courier_id">
                            <option value="0">{t}не выбран{/t}</option>
                            {foreach $courier_list as $courier_id => $courier}
                                <option value="{$courier_id}" {if $elem.courier_id == $courier_id}selected="selected"{/if}>{$courier}</option>
                            {/foreach}
                        </select>
                    {else}
                        {foreach $courier_list as $courier_id => $courier}
                            {if $elem.courier_id == $courier_id}
                                <select readonly name="courier_id">
                                    <option value="{$courier_id}" selected="selected">{$courier}</option>
                                </select>
                            {/if}
                        {/foreach}
                    {/if}
                </td>
            </tr>
        {/if}

        {$order_delivery_fields}
    </table>

    {$type_object->getAdminDeliveryParamsHtml($elem)}

    {* @deprecated (21.01) для совместимости с устаревшими классами доставки *}
    {if $elem.delivery_new_query && method_exists($type_object, 'getAdminAddittionalHtml')}
        {$type_object->getAdminAddittionalHtml($elem)}
    {/if}
    {if $elem.id > 0 && $show_delivery_buttons && method_exists($type_object, 'getAdminHTML')}
        {$type_object->getAdminHTML($elem)}
    {/if}

{else}
    <p class="emptyOrderBlock">{t}Тип доставки не указан.{/t} <a href="{adminUrl do=deliveryDialog order_id=$elem.id user_id=$user_id}" class="crud-add u-link">{t}Указать доставку{/t}</a>.</p>
{/if}