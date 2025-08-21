{extends file="%THEME%/default.tpl"}

{addcss file="pages/details.css"}

{block name="content"}

	{* Реквизиты *}
	{include file="%THEME%/sections/details/details.tpl"}

	{* Свяжитесь с нами *}
	{include file="%THEME%/sections/common/coop.tpl"}

{/block}