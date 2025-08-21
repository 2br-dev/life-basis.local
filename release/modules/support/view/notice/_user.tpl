{if $support}
    {$user = $support->getUser()}
    {$username = $user->getFio()}
{elseif $topic}
    {$user = $topic->getUser()}
    {$username = $topic->getUserName()}
{/if}

<h3>{t}Пользователь{/t}</h3>
{t}Контакт{/t}: <strong>{$username}</strong><br>
{if $user.phone}
    {t}Телефон{/t}: <strong>{$user.phone}</strong><br>
{/if}
{if $user.e_mail}
    E-mail: <strong>{$user.e_mail}</strong><br>
{/if}