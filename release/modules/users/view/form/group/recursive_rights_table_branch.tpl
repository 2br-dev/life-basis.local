{if $list}
    {foreach $list as $item}
        {if $item->isGroup()}
            {$input_name = 'group_access'}
        {else}
            {$input_name = 'module_access'}
        {/if}
        <div class="item-right-group access-scope">
            <div class="item-right {if $item->isGroup()}access-group access-group-hl{else}access-right{/if}">
                <div class="column-module">
                    {for $i=1 to $level}
                        {if $level > 1 && !$item->isGroup() && $i == $level}{continue}{/if}
                        <div class="icon-item">
                            {if $i == $level && $item->isGroup()}
                                <i class="zmdi zmdi-folder-outline f-18"></i>
                            {else}
                                &middot;
                            {/if}
                        </div>
                    {/for}
                    <div class="title">{$item->getTitle()}</div>
                </div>
                <div class="column-rights">
                    <label class="access-item">
                        <input type="radio" name="{$input_name}[{$row.class}][{$item->getAlias()}]" value="allow" title="{t}Разрешено{/t}{if $item->isGroup()} {t}для группы{/t}{/if}" {if isset($row.access[$item->getAlias()]['allow'])}checked{/if}>
                    </label>
                    <label class="access-item">
                        <input type="radio" name="{$input_name}[{$row.class}][{$item->getAlias()}]" value="disallow" title="{t}Запрещено{/t}{if $item->isGroup()} {t}для группы{/t}{/if}" {if isset($row.access[$item->getAlias()]['disallow'])}checked{/if}>
                    </label>
                    <label class="access-item">
                        <input type="radio" name="{$input_name}[{$row.class}][{$item->getAlias()}]" value="" title="{t}По умолчанию{/t}{if $item->isGroup()} {t}для группы{/t}{/if}" {if !isset($row.access[$item->getAlias()]) && !$item->isGroup()}checked{/if}>
                    </label>
                </div>
            </div>

            {if $item->isGroup()}
                {include file="%users%/form/group/recursive_rights_table_branch.tpl" list=$item->getChilds() level=$level+1}
            {/if}
        </div>
    {/foreach}
{/if}
