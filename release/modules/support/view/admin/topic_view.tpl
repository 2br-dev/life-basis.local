{$app->autoloadScripsAjaxBefore()}
{addjs file="%support%/admin.ticketview.js"}
{addjs file="%catalog%/selectproduct.js"}
{addcss file="%catalog%/selectproduct.css"}

{if !$url->isAjax()}
<div class="crud-ajax-group">
    <div class="updatable" data-url="{urlmake}">
{/if}

{addcss file="%support%/support_admin.css"}
<div class="viewport">
    <div class="top-toolbar">
        <p><a href="{$cancel_url}"><i class="zmdi zmdi-chevron-left m-r-5"></i> {t}Назад к списку тем{/t}</a></p>
        <div class="c-head">
            {$mainMenuIndex = $elements->getMainMenuIndex()}
            <h2 class="title titlebox">
                <a class="va-m-c" data-side-panel="{adminUrl do="ajaxQuickShowTopics" exclude_id=$elem.id}" title="{t}Показать другие тикеты{/t}"><i class="zmdi zmdi-tag-more c-black"></i></a>
                <span class="go-to-menu" {if $mainMenuIndex !== false}data-main-menu-index="{$mainMenuIndex}"{/if}>{$elements.formTitle}</span>
                {if isset($elements.topHelp)}<a class="help-icon" data-toggle-class="open" data-target-closest=".top-toolbar">?</a>{/if}</h2>

            {if $elements.topToolbar}
                <div class="buttons xs-dropdown place-left">
                    <a class="btn btn-default toggle visible-xs-inline-block" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false" id="clientHeadButtons" >
                        <i class="zmdi zmdi-more-vert"><!----></i>
                    </a>
                    <div class="xs-dropdown-menu" aria-labelledby="clientHeadButtons">
                        {$elements.topToolbar->getView()}
                    </div>
                </div>
            {/if}
        </div>
    </div>

    <div class="ticket-view">
        <span class="ticket-number">{$topic.number}</span>
        <a href="{adminUrl do="edit" id=$topic.id mod_controller="support-topicsctrl"}" class="crud-edit btn btn-link">{t}Редактировать{/t}</a>
        <a href="{adminUrl do="del" id=$topic.id redirect_to_topic=1 mod_controller="support-topicsctrl"}" data-confirm-text="{t}Вы действительно желаете удалить данную тему и всю переписку по данной теме?{/t}" class="crud-get btn c-red btn-link">{t}Удалить{/t}</a>
    </div>
    <div class="ticket-info">
        <div class="ticket-info__main">
            <div class="info-title">
                <h4>{t}Основные сведения{/t}</h4>
            </div>
            <div class="info-body">
                <div class="info-line">
                    <div class="key">
                        <span class="key-title">{t}Статус{/t}</span>
                        {$status = $topic->getStatus()}
                        <span class="ticket-number" style="background:{$status.background}; color:{$status.color}">{$status.title}</span>
                    </div>
                    <div class="value">
                        <div class="ticket-dropdown btn-group">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="f-18 zmdi zmdi-more-vert"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">
                                    {t}Изменить статус{/t}
                                </li>
                                {foreach $topic->getStatusesTitles() as $status => $title}
                                    <li>
                                        <a class="crud-get"
                                           href="{adminUrl do="AjaxChangeStatus" id=$topic.id status=$status mod_controller="support-topicsctrl"}">{$title}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="info-line">
                    <div class="key">
                        <span class="key-title">{t}Менеджер{/t}</span>
                        <div>
                            {if $topic.manager_id}
                                {$topic->getManagerUser()->getFio()}
                            {else}
                                {t}Не назначен{/t}
                            {/if}
                        </div>
                    </div>
                    <div class="value">
                        <div class="ticket-dropdown btn-group">
                            <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="f-18 zmdi zmdi-more-vert"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">
                                    {t}Изменить менеджера{/t}
                                </li>
                                <li>
                                    <a class="crud-get"
                                       href="{adminUrl do="AjaxChangeManager" id=$topic.id manager_id=0 mod_controller="support-topicsctrl"}">{t}Без менеджера{/t}</a>
                                </li>
                                {foreach $managers as $user}
                                    <li>
                                        <a class="crud-get"
                                           href="{adminUrl do="AjaxChangeManager" id=$topic.id manager_id=$user.id mod_controller="support-topicsctrl"}">{$user->getFio()}</a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="info-line">
                    <div class="key">
                        <span class="key-title">{t}Комментарий администратора{/t}</span>
                        <div>
                            {if $topic.comment !== ''}
                            <strong>{$topic.comment}</strong>
                            {else}
                                {t}нет{/t}
                            {/if}
                        </div>
                    </div>
                    <div class="value">
                        <a href="{adminUrl do="editComment" id=$topic.id mod_controller="support-topicsctrl"}" class="crud-edit crud-sm-dialog"><i class="f-18 zmdi zmdi-edit"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="ticket-info__extra">
            <div class="info-title">
                <h4>{t}Дополнительные сведения{/t}</h4>
            </div>
            <div class="info-body">
                <table class="info-table">
                    <tr>
                        <td>{t}Пользователь{/t}</td>
                        <td>
                            <div>
                                {$topic->getPlatform()->getUserInfoHtml($topic)}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>{t}Дата создания{/t}</td>
                        <td>{$topic.created|dateformat:"@date @time"}</td>
                    </tr>
                    <tr>
                        <td>{t}Дата обновления{/t}</td>
                        <td>{$topic.updated|dateformat:"@date @time"}</td>
                    </tr>
                    <tr>
                        <td>{t}Создано на платформе{/t}</td>
                        <td>{$topic->getPlatform()->getTitle()}</td>
                    </tr>
                    {foreach $topic->getPlatform()->getPublicData() as $data}
                        <tr>
                            <td>{$data.title}</td>
                            <td>{$data.value}</td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        </div>
    </div>

    <div class="ticket-messages"
         data-last-message-id="{$topic->getLastMessageId()}"
         data-topic-id="{$topic.id}"
         {if $config.enable_new_message_sound}
            data-new-message-mp3-url="{$mp3_folder_url}/new-message.mp3"
         {/if}
         data-enable-autoupdate="{$is_enable_autoupdate}"
         data-refresh-url="{adminUrl do="AjaxGetNewMessages" mod_controller="support-supportctrl"}">

        {include file="admin/ticket_view_messages.tpl" messages=$topic->getMessagesForAdmin()}

    </div>

    <form method="post" action="{urlmake}#answer" class="ticket-form" id="answer">
        <input type="hidden" name="topic_id" value="{$topic.id}">
        <div class="ticket-form__head">
            <h4>{t}Ваш ответ{/t}</h4>
        </div>
        <div class="ticket-form__body">
            {$formErrors = $new_message->getDisplayErrors()}
            <div class="crud-form-error">
                {if count($formErrors)}
                    <ul class="error-list">
                        {foreach $formErrors as $data}
                            <li>
                                <div class="{$data.class|default:"field"}">{$data.fieldname}<i class="cor"></i></div>
                                <div class="text">
                                    {foreach $data.errors as $error}
                                        {$error}
                                    {/foreach}
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                {/if}
            </div>

            <div class="m-b-20 field-message">
                {$new_message.__message->formView()}
            </div>
            <div class="m-b-20 field-files">
                {$new_message.__attachments->formView(['force_client_view' => true])}
            </div>
            <div>
                <button type="submit" class="btn btn-success btn-lg m-r-10">{t}Отправить{/t}</button>
                <a href="{$cancel_url}" class="btn btn-default btn-lg">{t}Отменить{/t}</a>
            </div>
        </div>
    </form>
</div>

{if !$url->isAjax()}
    </div>
</div>
{/if}
{$app->autoloadScripsAjaxAfter()}