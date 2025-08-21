{addcss file="%crm%/show_call.css"}
<div class="updatable" data-url="{adminUrl do="edit" id=$call_history.id mod_controller="crm-callhistoryctrl" refresh=1}">
    <div class="call-page text-center">
        <div class="call-circle">
            <i class="zmdi zmdi-{if $call_history.call_flow == 'in'}phone{else}phone-forwarded{/if}"></i>
        </div>

        <h4 class="flow">
            {if $call_history.call_flow == 'in'}
                {t}Входящий вызов{/t}
            {else}
                {t}Исходящий вызов{/t}
            {/if}
        </h4>
        <h4 class="phone-number m-b-20"><strong>{$call_history->getOtherUser()->phone|phone}</strong></h4>

        <table class="table table-va-center">
            <tbody>
            {if $call_history.record_id}
                <tr>
                    <td align="right" style="vertical-align: middle;">{t}Запись{/t}</td>
                    <td align="left">
                        {if $url=$call_history->getRecordUrl()}
                            <audio src="{$url}" controls class="audio"></audio>
                        {else}
                            {t}Нет{/t}
                        {/if}
                    </td>
                </tr>
            {/if}
            {if $call_history.event_time}
                <tr>
                    <td align="right">{t}Время звонка{/t}</td>
                    <td align="left">
                        {$call_history.event_time|dateformat:"@date @time:@sec"}
                    </td>
                </tr>
            {/if}
            {if $call_history.duration}
                <tr>
                    <td align="right">{t}Время разговора{/t}</td>
                    <td align="left">
                        {$call_history->getDurationString()}
                    </td>
                </tr>
            {/if}

            {foreach $call_history as $key => $property}
                {if $property->isVisible()}
                    {if $property->get()}
                        <tr>
                            <td align="right" width="50%">{$property->getDescription()}</td>
                            <td align="left" width="50%">
                                {if $property->getCheckboxParam()}
                                    {t}Да{/t}
                                {else}
                                    {$property->textView()}
                                {/if}
                            </td>
                        </tr>
                    {/if}
                {/if}
            {/foreach}
                {$client = $call_history->getOtherUser()}
                <tr>
                    <td align="right" width="50%">Адресат:</td>
                    <td align="left" width="50%">
                        {if $client.id > 0}
                            <a href="{adminUrl do=edit id=$client.id mod_controller="users-ctrl"}" class="btn btn-default crud-edit">{$client->getFio()}</a>
                        {else}
                            <a href="{adminUrl do="add" from_call=$call_history.id mod_controller="users-ctrl"}" class="btn btn-warning zmdi zmdi-plus crud-add" title="{t}Создать пользователя{/t}"></a>
                            {t}Неизвестный пользователь{/t}
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td align="right" width="50%">Связанные взаимодействия:</td>
                    <td align="left" width="50%">
                        <a href="{adminUrl do="add" from_call=$call_history.id mod_controller="crm-interactionctrl"}" class="btn btn-warning zmdi zmdi-plus crud-add" title="{t}Создать{/t}"></a>
                        {foreach $call_history->getInteractions() as $interaction}
                            <a href="{adminUrl do=edit id=$interaction.id mod_controller="crm-interactionctrl"}" class="btn btn-default crud-edit">{$interaction.date_of_create|dateformat:"@date @time"}</a>
                        {/foreach}
                    </td>
                </tr>
                {if $client.id > 0}
                    {* Если есть связанный пользователь, то отображаем связанные объекты *}

                    {$order_count = $client->getLastOrders(false)}
                    <tr>
                        <td align="right" width="50%">{t}Заказы{/t} ({t all=$order_count}Всего: %all{/t}):</td>
                        <td align="left" width="50%">
                            <a href="{adminUrl do="add" from_call=$call_history.id mod_controller="shop-orderctrl"}" class="btn btn-warning zmdi zmdi-plus" title="{t}Создать заказ{/t}"></a>
                            {foreach $client->getLastOrders() as $order}
                                <a href="{adminUrl do=edit id=$order.id mod_controller="shop-orderctrl"}" class="btn btn-default crud-edit">{$order.order_num}</a>
                            {/foreach}
                        </td>
                    </tr>

                    {$deal_count = $client->getLastDeals(false)}
                    <tr>
                        <td align="right" width="50%">{t}Сделки{/t} ({t all=$deal_count}Всего: %all{/t}):</td>
                        <td align="left" width="50%">
                            <a href="{adminUrl do="add" from_call=$call_history.id mod_controller="crm-dealctrl"}" class="btn btn-warning zmdi zmdi-plus crud-add" title="{t}Создать сделку{/t}"></a>
                            {foreach $client->getLastDeals() as $deal}
                                <a href="{adminUrl do=edit id=$deal.id mod_controller="crm-dealctrl" from_call=$call_history.id}" class="btn btn-default crud-edit">{$deal.deal_num}</a>
                            {/foreach}
                        </td>
                    </tr>

                    {$task_count = $client->getLastTasks(false)}
                    <tr>
                        <td align="right" width="50%">{t}Задачи{/t} ({t all=$task_count}Всего: %all{/t}):</td>
                        <td align="left" width="50%">
                            <a href="{adminUrl do="add" from_call=$call_history.id mod_controller="crm-taskctrl"}" class="btn btn-warning zmdi zmdi-plus crud-add" title="{t}Создать задачу{/t}"></a>
                            {foreach $client->getLastTasks() as $task}
                                <a href="{adminUrl do=edit id=$task.id mod_controller="crm-taskctrl"}" class="btn btn-default crud-edit">{$task.task_num}</a>
                            {/foreach}
                        </td>
                    </tr>

                    {$oneclick_count = $client->getLastOneClick(false)}
                    <tr>
                        <td align="right" width="50%">{t}Покупки в 1 клик{/t} ({t all=$oneclick_count}Всего: %all{/t}):</td>
                        <td align="left" width="50%">
                            {foreach $client->getLastOneClick() as $oneclick}
                                <a href="{adminUrl do=edit id=$oneclick.id mod_controller="catalog-oneclickctrl"}" class="btn btn-default crud-edit">{$oneclick.id} от {$oneclick.dateof|dateformat:"@date"}</a>
                            {/foreach}
                        </td>
                    </tr>

                    {$reservation_count = $client->getLastReservation(false)}
                    <tr>
                        <td align="right" width="50%">{t}Предзаказы{/t} ({t all=$reservation_count}Всего: %all{/t}):</td>
                        <td align="left" width="50%">
                            {foreach $client->getLastReservation() as $reservation}
                                <a href="{adminUrl do=edit id=$reservation.id mod_controller="shop-reservationctrl"}" class="btn btn-default crud-edit">{$reservation.id} от {$reservation.dateof|dateformat:"@date"}</a>
                            {/foreach}
                        </td>
                    </tr>

                {/if}
            </tbody>
        </table>

    </div>
</div>