{$rights_pay_changing = $rights[Shop\Config\ModuleRights::RIGHT_PAY_CHANGING]}

<h3>{t}Оплата{/t}
    {if $elem.payment>0 && $rights_pay_changing}
        <a href="{adminUrl do=paymentDialog order_id=$elem.id}" class="crud-add m-l-10" title="{t}редактировать{/t}">
            <i class="zmdi zmdi-edit"></i>
        </a>
    {/if}
</h3>

{if $elem.payment>0}
    {if isset($payment_id)}
       <input type="hidden" name="payment" value="{$payment_id}"/>
    {/if}
    <table class="otable">
        <tr>
            <td class="otitle">
                {t}Тип{/t}
            </td>
            <td>{$pay.title}</td>
        </tr>
        {if $elem.id>0}
            {$type_object=$pay->getTypeObject()}

            {if $rights[Shop\Config\ModuleRights::RIGHT_IS_PAY_READING]}
                <tr>
                    <td class="otitle">
                        {t}Заказ оплачен?{/t}
                    </td>
                    <td>
                        <div class="toggle-switch">
                            <input {if !$rights_pay_changing}readonly{/if} id="is_payed" name="is_payed" type="checkbox" hidden="hidden" {if $elem.is_payed}checked{/if} value="1">
                            <label for="{if $rights_pay_changing}is_payed{/if}" class="ts-helper"></label>
                        </div>
                    </td>
                </tr>
            {/if}
            {if $rights[Shop\Config\ModuleRights::RIGHT_PAY_DOCS_READING]}
                <tr>
                    <td class="otitle">
                        {t}Документы покупателя{/t}
                    </td>
                    <td>
                        {foreach $type_object->getDocsName() as $key => $doc}
                            <a href="{$type_object->getDocUrl($key)}" class="underline" target="_blank">{$doc.title}</a>{if !$doc@last},{/if}
                        {foreachelse}
                            {t}Не предусмотрены{/t}
                        {/foreach}
                    </td>
                </tr>
            {/if}
            {if $type_object->canOnlinePay() && $type_object->getShortName() != 'personalaccount'}
                <tr>
                    <td class="otitle">
                        {t}Ссылка на оплату{/t}
                    </td>
                    <td>
                        <a href="{$elem->getOnlinePayUrl()}">{t}Оплатить{/t}</a>
                    </td>
                </tr>
            {/if}
        {/if}

        {$order_payment_fields}
    </table>

    {if $type_object && $rights_pay_changing}
        {$type_object->getAdminPaymentHtml($elem)}
    {/if}

    {include file="%shop%/form/order/order_transactions.tpl"}
{else}
    <p class="emptyOrderBlock">{t}Тип оплаты не указан.{/t} <a href="{adminUrl do=paymentDialog order_id=$elem.id}" class="u-link crud-add">{t}Указать оплату{/t}</a>.</p>
{/if}