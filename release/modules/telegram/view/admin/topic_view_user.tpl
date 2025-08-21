{$tg_user_id = $topic->getPlatformData('telegram_user_id')}
{$tg_user = $topic->getPlatform()->getTelegramUser()}
{$user = $topic->getUser()}
{if $user.id > 0}
    <div class="m-b-5">
        <a href="{adminUrl do=false mod_controller="users-ctrl" f=['id' => $user.id]}" class="ticket-badge">ID: {$user.id}</a>
    </div>
    <a class="info-user crud-edit" href="{adminUrl do="edit" id=$user.id mod_controller="users-ctrl"}">{$user.surname} {$user.name}</a>
    <span class="m-r-10">({t balance=$user->getBalance(false, true)}Баланс: %balance{/t})</span>
    <a href="{adminUrl do=false mod_controller="support-topicsctrl" f=['user_id' => $user.id]}" class="btn btn-xs btn-alt btn-primary">{t count=$topic->getUserTotalTicketCount()}Тикетов: %count{/t}</a>
    <div>
        <span>{t}В Telegram:{/t}</span>
        <a href="{adminUrl do="edit" id=$tg_user_id mod_controller="telegram-userctrl"}" class="crud-edit">
            {$tg_user.first_name}({$tg_user.username})</a>
    </div>
{else}
    <a class="info-user m-r-10 crud-edit" href="{adminUrl do="edit" id=$tg_user_id mod_controller="telegram-userctrl"}">{$topic.user_name}</a>
    <a href="{adminUrl do=false mod_controller="support-topicsctrl" f=['user_name' => $topic.user_name]}" class="btn btn-xs btn-alt btn-primary">{t}Все тикеты{/t}</a>
{/if}