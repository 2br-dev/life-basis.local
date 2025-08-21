{$task = $data->task}
{$current_user = $data->current_user}
{$message = $data->message}

<h1>
    {t num=$task->task_num}Получено новое сообщение в чате у задачи №%num{/t}
    {if $current_user} {t}от{/t} {$current_user->getFio()}{/if}
</h1>

<p>{t}Номер задачи{/t}: <a href="{$router->getAdminUrl('edit', ['id' => $task->id], 'crm-taskctrl', true)}">{$task->task_num}</a></p>

<p>{t}Сообщение{/t}: {$message.message}</p>