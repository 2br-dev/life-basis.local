{$topic = $data->support->getTopic()}
{$user = $data->support->getUser()}

<h1>{t}Сообщение в поддержку{/t}</h1>

<p>{t}Дата{/t}: {$data->support.dateof|dateformat:"@date @time"}<br>
{t}Тема переписки{/t}: <strong>{$topic.title}</strong></p>

{include file="%support%/notice/_user.tpl" support = $data->support}
{include file="%support%/notice/_message.tpl" message = $data->support}