{if $paginator->total}
    <div class="updatable" data-update-block-id="quick-orders" data-update-replace>
        {$back_url=$url->getSavedUrl("Support\Controller\Admin\TopicsCtrlindex")}
        <p>{t}Здесь отображаются другие отфильрованные в <a href="{$back_url}" class="u-link">административной панели темы</a>{/t}</p>

        <div class="quick-orders">
            {foreach $topics as $topic}
                <div class="m-b-20">
                    <a href="{adminUrl do=false id=$topic.id}" class="f-18 va-m-c">
                        {$status = $topic->getStatus()}
                        <span class="ticket-number" style="background-color: {$status.background}; color:{$status.color}" title="{$status.title}">№ {$topic.number}</span>
                        <span>{$topic.title}</span>
                    </a>
                    <p class="m-t-5">{$topic.updated|dateformat:"@date"} {$topic->getUser()->getFio()}</p>
                </div>
            {/foreach}
        </div>

        {include file="%SYSTEM%/admin/widget/paginator.tpl" paginatorClass="with-top-line" noUpdateHash=true}
    </div>
{else}
    <div class="rs-side-panel__empty">
        {t}Нет тем{/t}
    </div>
{/if}