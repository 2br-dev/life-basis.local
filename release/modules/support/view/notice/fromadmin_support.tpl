{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    <h1>{t}Уважаемый, пользователь!{/t}</h1>
    <p>{t domain = $url->getDomainStr()}Из службы поддержки поступило сообщение (отправленное с сайта %domain).{/t}</p>
    {$topic = $data->support->getTopic()}
    {$user = $data->user}
    <p>{t}Дата{/t}: {$data->support.dateof|dateformat:"@date @time:@sec"}<br>
    {t}Тема переписки{/t}: <strong>{$topic.title}</strong></p>

    {include file="%support%/notice/_message.tpl" message=$data->support}

    <p><a href="{$topic->getUrl(true)}">{t}Ответить{/t}</a></p>
{/block}