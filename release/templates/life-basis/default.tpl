{include file="%THEME%/helpers/header.tpl"}

<main>
{block name="content"}
	{$app->blocks->getMainContent()}
{/block}
</main>

{include file="%THEME%/helpers/footer.tpl"}
