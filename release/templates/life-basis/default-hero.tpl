{include file="%THEME%/helpers/header.tpl" class="hero"}

<main class="hero">
{block name="content"}
	{$app->blocks->getMainContent()}
{/block}
</main>

{include file="%THEME%/helpers/footer.tpl"}
