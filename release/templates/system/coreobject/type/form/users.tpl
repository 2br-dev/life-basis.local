{addjs file="jquery.rs.objectselect.js" basepath="common"}
{addjs file="jquery.rs.userslinks.js" basepath="common"}

{function userInput}
    <div class="user-line">
        <span class="form-inline">
            <div class="input-group">
                <input type="text" data-name="{$field->getFormName()}" class="object-select" {if $user_id > 0} value="{$user_fullname}"{/if} {$field->getAttr()} data-request-url="{$field->getRequestUrl()}">
                {if $user_id > 0}<input type="hidden" name="{$field->getFormName()}" value="{$user_id}">{/if}
                <span class="input-group-addon"><i class="zmdi zmdi-{$field->getIconClass()}"></i></span>
            </div>
        </span>
        <a title="{t}Удалить{/t}" class="btn f-18 c-red users-remove {if !$index}hidden{/if}"><i class="zmdi zmdi-close"></i></a>
        {if !$index}<a title="{t}Добавить пользователя{/t}" class="btn f-18 users-add"><i class="zmdi zmdi-plus"></i></a>{/if}
    </div>
{/function}

<div class="users-links">
    {$users = $field->getSelectedUsers()}
    <div class="users-main">
        {if isset($users.0)}
            {userInput field=$field user_fullname=$users.0->getFio() user_id=$users.0.id}
        {else}
            {userInput field=$field user_fullname=$field->getSearchPlaceholder()}
        {/if}
    </div>
    <div class="users-other">
        {foreach $field->getSelectedUsers() as $n => $user}
            {if $user@first}{continue}{/if}
            {userInput field=$field index=$n user_fullname=$user->getFio() user_id=$user.id}
        {/foreach}
    </div>
</div>

<script>
    $.allReady(function() {
        $('.users-links').usersLinks();
    });
</script>