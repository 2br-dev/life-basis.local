{if $topics}
    <table class="wtable mrg overable">
        <thead>
            <tr>
                <th width="40"></th>
                <th>{t}Номер{/t}</th>
                <th>{t}Тема{/t}</th>
                <th class="w-date">{t}Дата{/t}</th>
            </tr>
        </thead>
        <tbody>
        {foreach $topics as $topic}
            <tr onclick="location.href='{adminUrl do=false mod_controller="support-supportctrl" id=$topic.id}'" class="clickable">
                <td>
                    <span title="{if $topic.newadmcount>0}{t}есть новые сообщения{/t}{else}{t}прочитано{/t}{/if}"
                          class="w-point {if $topic.newadmcount>0} bg-red {else} bg-gray{/if}"></span>&nbsp;
                </td>
                <td>{$topic.number}</td>
                <td>{$topic.title}</td>
                <td class="w-date">
                    {$topic.updated|dateformat:"j %v %!Y"}<br>
                    {$topic.updated|dateformat:"@time"}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{else}
    <div class="empty-widget">
        {t}Нет сообщений{/t}
    </div>
{/if}    

{include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line"}