{* Горизонтальное меню в шапке. Отображает 2 уровня вложенности *}
<ul>
{if $items->count()}
    {foreach $items as $node} {* Первый уровень *}
        {$menu = $node->getObject()}
        {if $menu->title!="Главная" && $menu->title<>"Смотреть видео"}
        <li {if $node@first}class="offcanvas__list-separator"{/if}>
            <a class="offcanvas__list-item" href="{$menu->getHref()|default:"#"}" {if $menu.target_blank}target="_blank"{/if} {$menu->getDebugAttributes()}>{$menu.title}</a>
            {* {if $node->getChilds()}
                {$submenu = $node->getChilds()}
                {if count($submenu) > 0}
                <a href="javascript:void(0)" class="folder-trigger"><i class="bx bx-chevron-down"></i></a>
                {/if}
                <ul>
                    {foreach $node->getChilds() as $subitem}
                        <li><a class="dropdown-item" href="{$subitem.fields->getHref()}">{$subitem.fields.title}</a></li>
                    {/foreach}
                </ul>
            {/if} *}
        </li>
        {/if}
    {/foreach}
{/if}
</ul>