{$user = $data->topic->getUser()}

<h1>{t number=$data->topic.number}Новый тикет №%number в поддержку{/t}</h1>

<p>{t}Дата{/t}: {$data->topic.created|dateformat:"@date @time"}<br>
{t}Тема переписки{/t}: <strong>{$data->topic.title}</strong></p>

{include file="%support%/notice/_user.tpl" topic = $data->topic}
{include file="%support%/notice/_message.tpl" message = $data->support}