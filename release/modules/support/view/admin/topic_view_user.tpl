{$user = $topic->getUser()}
{if $user.id > 0}
    <div class="m-b-5">
        <a href="{adminUrl do=false mod_controller="users-ctrl" f=['id' => $user.id]}" class="ticket-badge">ID: {$user.id}</a>
    </div>
    <a class="info-user crud-edit" href="{adminUrl do="edit" id=$user.id mod_controller="users-ctrl"}">{$user.surname} {$user.name}</a>
    <span class="m-r-10">({t balance=$user->getBalance(false, true)}Баланс: %balance{/t})</span>
    <a href="{adminUrl do=false mod_controller="support-topicsctrl" f=['user_id' => $user.id]}" class="btn btn-xs btn-alt btn-primary">{t count=$topic->getUserTotalTicketCount()}Тикетов: %count{/t}</a>
{elseif $topic.user_name}
    <span class="info-user m-r-10">{$topic.user_name}</span>
    <a href="{adminUrl do=false mod_controller="support-topicsctrl" f=['user_name' => $topic.user_name]}" class="btn btn-xs btn-alt btn-primary">{t}Все тикеты{/t}</a>
{elseif $topic.user_email}
    <span class="info-user m-r-10">{$topic.user_email}</span>
    <a href="{adminUrl do=false mod_controller="support-topicsctrl" f=['user_email' => $topic.user_email]}" class="btn btn-xs btn-alt btn-primary">{t}Все тикеты{/t}</a>
{else}
    <span class="info-user">{t}Неизвестно{/t}</span>
{/if}