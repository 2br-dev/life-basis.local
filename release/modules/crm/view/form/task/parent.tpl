{addcss file="%crm%/linked-objects.css?v=1.1"}

{$list = $elem->getRelatedTasks()}
{$statuses = Crm\Model\Orm\Status::getStatusesByObjectType('crm-task')}

{if $list}
    {foreach $list as $task}
        {$files = $task->getFiles()}
        {$task_status = $statuses[$task.status_id]}
        {if $task.id != $elem['id']}
            <li class="m-b-20 list-unstyled linked-objects">
                <a
                        href="{$router->getAdminUrl('', ['id'=>{$task.id}, 'do' => 'edit'], 'crm-taskctrl')}"
                        class="crud-edit linked-object"
                        data-crud-dialog-width="90%"
                        data-crud-dialog-height="90%"
                >
                    <div class="f-12 c-gray linked-object__num-date">№{$task.task_num} от {$task.date_of_create|dateformat:"@date @time"}</div>
                    {if $task.description}
                        <div class="f-12 linked-object__num-description">{$task.description}</div>
                    {/if}
                    <div class="m-t-5 m-b-5 linked-object__status-title">
                        <span class="status c-white m-r-5 m-b-5" style="background: {$task_status.color}">
                            {$task_status.title}
                        </span>
                        <span>
                            {$task.title}
                        </span>
                    </div>
                </a>

                {if $files}
                    <div class="m-t-10 linked-object__files">
                        {foreach $files as $file}
                            <div class="m-b-5 linked-object__file">
                                <i class="zmdi zmdi-attachment-alt"></i>

                                <a href="{$file->getAdminDownloadUrl()}">{$file.name} {$file.size|format_filesize}</a>
                            </div>
                        {/foreach}
                    </div>
                {/if}
            </li>
        {/if}
    {/foreach}
{else}
    -
{/if}

<input type="hidden" name="parent" value="{$elem.__parent->get()}">
