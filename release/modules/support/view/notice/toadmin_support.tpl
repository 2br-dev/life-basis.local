{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {$topic = $data->support->getTopic()}
    {$user = $data->support->getUser()}

    <h1>{t}Уважаемый, администратор!{/t}</h1>
    <p>{t url = $url->getDomainStr()}В поддержку поступило сообщение (отправленное на сайте %url).{/t}</p>
    <p>{t}Дата{/t}: {$data->support.dateof|dateformat:"@date @time:@sec"}<br>
    {t}Тема переписки{/t}: <strong>{$topic.title}</strong></p>

    {include file="%support%/notice/_user.tpl" support = $data->support}
    {include file="%support%/notice/_message.tpl" message = $data->support}

    <p><a href="{$router->getAdminUrl(false, ['id' => $topic.id], 'support-supportctrl', true)}">{t}Ответить{/t}</a></p>
{/block}