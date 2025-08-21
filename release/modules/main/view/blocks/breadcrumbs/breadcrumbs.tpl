{$bc = $app->breadcrumbs->getBreadCrumbs()}
<nav class="breadcrumb" aria-label="breadcrumb">
    {if !empty($bc)}
    <ul class="breadcrumb__list">
        {foreach from=$bc item=item name="path"}
            {hook name="main-breadcrumbs:item" title="{t}Элемент хлебных крошек{/t}" item = $item}
                {if empty($item.href)}
                    <li class="breadcrumb__item"><span>{$item.title}</span></li>
                {else}
                    <li class="breadcrumb__item"><a href="{$item.href}" {if $smarty.foreach.path.first}class="first"{/if}>{$item.title}</a></li>
                {/if}
            {/hook}
        {/foreach}
    </ul>
    {/if}
</nav>
