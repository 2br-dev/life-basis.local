{addcss file="{$mod_css}tree.css" basepath="root"}
{addcss file="{$mod_css}groupedit.css" basepath="root"}
{addjs file="%users%/admin/jquery.groupedit.js"}

{if $elem.alias == 'supervisor'}
    <div style="margin-top:10px;" class="notice-box no-padd">
        <div class="notice-bg">
            {t}Супервизор имеет полные права ко всем модулям, сайтам и пунктам меню{/t}
        </div>
    </div>
{else}

<div class="switch-site-access">
    <input id="site_admin" type="checkbox" name="site_access" value="1" {if !empty($site_access)}checked{/if}>&nbsp;<label for="site_admin">{t}Включить доступ к администрированию текущего сайта{/t}</label>
</div>

<h3>{t}Доступ к пунктам меню{/t}</h3>
<br>

<div class="treeblock">
    <div class="left">
        <div class="full-access l-p-space">
            <input type="checkbox" name="menu_access[]" value="{$smarty.const.FULL_USER_ACCESS}" {if isset($menu_access[$smarty.const.FULL_USER_ACCESS])}checked{/if} id="full_user">
            <label for="full_user">{t}Полный доступ к меню пользователя{/t}</label>
        </div>
        <div class="lefttree localform" style="position:relative">
            <div class="overlay" id="user_overlay">&nbsp;</div>
            <div class="wrap">
                {$user_tree->getView(['render_all_nodes' => true])}
            </div>
        </div>
    </div>

    <div class="right">
        <div class="full-access l-p-space">
            <input type="checkbox" name="menu_admin_access[]" value="{$smarty.const.FULL_ADMIN_ACCESS}" {if isset($menu_access[$smarty.const.FULL_ADMIN_ACCESS])}checked{/if} id="full_admin">
            <label for="full_admin">{t}Полный доступ к меню администратора{/t}</label>
        </div>

        <div class="righttree localform" style="position:relative">
            <div class="overlay" id="admin_overlay">&nbsp;</div>
            <div class="wrap">
                {$admin_tree->getView(['render_all_nodes' => true])}
            </div>
        </div>
    </div>
</div> <!--Treeblock -->

<h3>{t}Права к модулям{/t}</h3>
<br>
<div class="modrights-search">
    <div class="inputs">
        <div>
            <label>Название модуля</label><br>
            <input type="text" placeholder="{t}Наименование или идентификатор{/t}" id="module-name">
        </div>
        <div>
            <label>Название права</label><br>
            <input type="text" placeholder="{t}Например: Создание{/t}" id="module-right-name">
        </div>
    </div>
</div>

<div class="modrights access-scope">
    <div class="modrights-head">
        <div class="column-module">
            <div class="icon-item"></div>
            <div>{t}Модуль{/t}</div>
        </div>
        <div class="column-rights">
            <div class="access-item">
                <span class="f-18 zmdi zmdi-check visible-xs" title="{t}Разрешено{/t}"></span>
                <span class="hidden-xs">{t}Разрешено{/t}</span>
            </div>
            <div class="access-item">
                <span class="f-18 zmdi zmdi-close visible-xs" title="{t}Запрещено{/t}"></span>
                <span class="hidden-xs">{t}Запрещено{/t}</span>
            </div>
            <div class="access-item">
                <span class="f-18 zmdi zmdi-triangle-up visible-xs" title="{t}По умолчанию{/t}"></span>
                <span class="hidden-xs">{t}По умолчанию{/t}</span>
            </div>
        </div>
    </div>

    <div class="modrights-list access-scope">
        <div class="list-item access-group all">
            <div class="item-head">
                <div class="column-module">
                    <a class="icon-item">
                        <i class="zmdi zmdi-plus expand-all" title="{t}Раскрыть список прав всех модулей{/t}"></i>
                        <i class="zmdi zmdi-minus collapse-all" title="{t}Закрыть список прав всех модулей{/t}" style="display:none"></i>
                    </a>
                    <div>
                        <div class="title">Все модули</div>
                    </div>
                </div>
                <div class="column-rights">
                    <label class="access-item">
                        <input type="radio" name="full_access" value="allow" title="{t}Разрешено для всех модулей{/t}">
                    </label>
                    <label class="access-item">
                        <input type="radio" name="full_access" value="disallow" title="{t}Запрещено для всех модулей{/t}">
                    </label>
                    <label class="access-item">
                        <input type="radio" name="full_access" value="" title="{t}По умолчанию для всех модулей{/t}">
                    </label>
                </div>
            </div>
        </div>

        {foreach $module_list as $row}
            <div class="list-item access-scope">
                <div class="item-head access-group">
                    <div class="column-module">
                        <a href="#module-{$row.class}" role="button" class="icon-item handler collapsed"
                           data-toggle="collapse" title="{t}Показать список прав модуля{/t}">
                            <i class="zmdi zmdi-chevron-down"></i>
                        </a>
                        <div>
                            <div class="title">{$row.name}</div>
                            <div class="class">
                                <i class="zmdi zmdi-puzzle-piece"></i>
                                {$row.class}
                            </div>
                        </div>
                    </div>
                    <div class="column-rights">
                        <label class="access-item">
                            <input type="radio" name="full_access[{$row.class}]" value="allow" title="{t}Разрешено для модуля{/t}">
                        </label>
                        <label class="access-item">
                            <input type="radio" name="full_access[{$row.class}]" value="disallow" title="{t}Запрещено для модуля{/t}">
                        </label>
                        <label class="access-item">
                            <input type="radio" name="full_access[{$row.class}]" value="" title="{t}По умолчанию для модуля{/t}">
                        </label>
                    </div>
                </div>
                <div class="item-rights collapse" id="module-{$row.class}">
                    {include file="%users%/form/group/recursive_rights_table_branch.tpl" list=$row.right_object->getRightsTree() level=1}
                </div>
            </div>
        {/foreach}
    </div>
</div>
{/if}
