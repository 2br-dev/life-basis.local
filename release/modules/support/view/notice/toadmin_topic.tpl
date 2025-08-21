{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {$topic = $data->topic}
    {$user = $data->topic->getUser()}
    <h1>{t}Уважаемый, администратор!{/t}</h1>
    <p>{t url = $url->getDomainStr() number=$topic.number}В поддержку поступил новый тикет №%number(отправленное на сайте %url).{/t}</p>
    <p>{t}Дата{/t}: {$topic.dateof|dateformat:"@date @time:@sec"}<br>
        {t}Тема переписки{/t}: <strong>{$topic.title}</strong></p>

    {include file="%support%/notice/_user.tpl" topic = $topic}
    {include file="%support%/notice/_message.tpl" message = $data->support}

    <p><a href="{$router->getAdminUrl(false, ['id' => $topic.id], 'support-supportctrl', true)}">{t}Ответить{/t}</a></p>
{/block}