{extends file="%THEME%/default.tpl"}


{block name="content"}

	{* Каталог *}
	{moduleinsert name="\CatalogExt\Controller\Block\ProductList"}

	{* Свяжитесь с нами *}
	{include file="%THEME%/sections/common/coop.tpl"}

{/block}

