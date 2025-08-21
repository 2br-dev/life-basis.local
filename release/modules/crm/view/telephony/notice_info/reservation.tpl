{if ModuleManager::staticModuleExists('shop')}
    <div class="tel-line">
        <div class="tel-row">
            {$count = $client->getLastReservation(false)}
            {if $count}
                {$filter = [
                "phone" => $client.phone
                ]}
            {else} {$filter = null} {/if}

            <a href="{adminUrl do=false mod_controller="shop-reservationctrl" f=$filter}">{t}Предзаказов{/t}: {if $count > 0}{$count}{else}{t}нет{/t}{/if}</a>
            {if $count}
                <div class="tel-dot"></div>
                <div>
                    <a class="btn btn-default btn-rect btn-inline zmdi zmdi-chevron-down" data-toggle-class="active-more" data-target-closest=".tel-line"></a>
                </div>
            {/if}
        </div>
        {if $count}
            <div class="tel-more-block">
                {foreach $client->getLastReservation() as $reservation}
                    <a href="{adminUrl do=edit id=$reservation.id mod_controller="shop-reservationctrl"}" class="crud-edit">№{$reservation.id} от {$reservation.dateof|dateformat:"@date"}</a>
                {/foreach}
            </div>
        {/if}
    </div>
{/if}