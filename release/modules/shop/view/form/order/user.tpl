{* Если права не переданы в шаблон, не учитывать их *}
<div id="userBlockWrapperContent">
    {if isset($user.id) && $user.id>0}
        <input type="hidden" name="user_id" value="{$user.id}"/>
        <table class="otable">
            {if $rights[Shop\Config\ModuleRights::RIGHT_CUSTOMER_FULLNAME_READING]}
                <tr>
                    <td class="otitle">
                        {t}Фамилия Имя Отчество:{/t}
                    </td>
                    <td>
                        <a href="{adminUrl mod_controller="users-ctrl" do="edit" id=$user.id}" target="_blank">{$user.surname} {$user.name} {$user.midname} ({$user.id})</a>&nbsp;
                        <a class="all-user-orders u-link" href="{adminUrl mod_controller="shop-orderctrl" f=["user_id" => $user.id] do=false}">{t}все заказы{/t} ({$user_num_of_order|default:0})</a>
                        {if $user.is_company}<div class="company_info">{$user.company}, {t}ИНН{/t}: {$user.company_inn}</div>{/if}
                        {if $rights[Shop\Config\ModuleRights::RIGHT_CUSTOMER_CHANGING]} {* Если установлены права для изменения *}
                            <a class="btn btn-default bnt-sm crud-add all-user-orders" href="{adminUrl do=userDialog}">{t}Указать другого{/t}</a>
                        {/if}
                    </td>
                </tr>
            {/if}
            {if $rights[Shop\Config\ModuleRights::RIGHT_CUSTOMER_PHONE_READING]}
                <tr>
                    <td class="otitle">{t}Телефон:{/t}</td>
                    <td>{$user.phone|phone}</td>
                </tr>
            {/if}
            {if $rights[Shop\Config\ModuleRights::RIGHT_CUSTOMER_EMAIL_READING]}
                <tr>
                    <td class="otitle">{t}E-mail:{/t}</td>
                    <td>{$user.e_mail}</td>
                </tr>
            {/if}
            {if $rights[Shop\Config\ModuleRights::RIGHT_CUSTOMER_USERFIELD_READING]}
                <tr>
                    {$user_type_cost = $user->getUserTypeCost()}
                    <td class="otitle">{t}Тип цен:{/t}</td>
                    <td class="user-cost" data-user-cost-id="{$user_type_cost.id}">
                        {if $user->getUserTypeCostId()}
                            {$user_type_cost.title}
                        {else}
                            {t}- По умолчанию -{/t} ({$user_type_cost.title})
                        {/if}
                    </td>
                </tr>
                {foreach from=$user->getUserFields() item=item name=uf}
                    <tr>
                        <td class="otitle">{$item.title}</td>
                        <td>{$item.current_val}</td>
                    </tr>
                {/foreach}
            {/if}
        </table>
    {else}
        <p class="emptyOrderBlock">{t}Пользователь не указан.{/t} <a class="crud-add u-link" href="{adminUrl do=userDialog}">{t}Указать пользователя{/t}</a>.</p>
        <table class="otable user-create-options">
            {if $rights[Shop\Config\ModuleRights::RIGHT_CUSTOMER_FULLNAME_READING]}
                <tr>
                    <td class="otitle">
                        {t}Фамилия Имя Отчество:{/t}
                        <div class="fieldhelp">({t}без регистрации{/t})</div>
                    </td>
                    <td>{$order.__user_fio->formView()}</td>
                </tr>
            {/if}
            {if $rights[Shop\Config\ModuleRights::RIGHT_CUSTOMER_EMAIL_READING]}
                <tr>
                    <td class="otitle">
                        {t}E-mail:{/t}
                        <div class="fieldhelp">({t}без регистрации{/t})</div>
                    </td>
                    <td>{$order.__user_email->formView()}</td>
                </tr>
            {/if}
            {if $rights[Shop\Config\ModuleRights::RIGHT_CUSTOMER_PHONE_READING]}
                <tr>
                    <td>{t}Телефон:{/t}
                        <div class="fieldhelp">({t}без регистрации{/t})</div>
                    </td>
                    <td>{$order.__user_phone->formView()}
                        {if $phone = $order->getUser()->getFormattedPhoneNumber()}
                            <a href="tel:{$phone}" class="btn btn-default"><i class="zmdi zmdi-phone"></i></a>
                        {/if}
                    </td>
                </tr>
            {/if}
        </table>
        {$order_id = null}
        {if $order->id > 0}
            {$order_id = $order->id}
        {/if}
        <button id="createUserFromNoRegister" data-href="{$router->getAdminUrl('createUserFromNoRegister', ['order' => $order_id],'shop-orderctrl')}" class="btn btn-default">{t}Создать пользователя из текущего{/t}</button>
    {/if}
</div>