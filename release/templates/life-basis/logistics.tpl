{extends file="%THEME%/default.tpl"}

{addcss file="pages/logistics.css"}

{block name="content"}

	{* Логистика *}
	{include file="%THEME%/sections/logistics/logistics.tpl"}

	{* Свяжитесь с нами *}
	{include file="%THEME%/sections/common/coop.tpl"}

{/block}