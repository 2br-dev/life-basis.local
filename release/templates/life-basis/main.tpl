{addcss file="/pages/main.css"}

{$app->title->addSection('Главная — Основа жизни | Оптовая продажа питьевой воды', 0, 'replace')|devnull}

{extends file="%THEME%/default.tpl"}

{block name="content"}

	{* Герой *}
	{include file="%THEME%/sections/main/hero.tpl"}

	{* Надёжный производитель *}
	{include file="%THEME%/sections/main/production.tpl"}

	{* Прозрачное производство *}
	{include file="%THEME%/sections/main/crystal.tpl"}

	{* Наши продукты *}
	{include file="%THEME%/sections/common/products.tpl" header="Наши продукты" tag="h2"}

	{* Знакомтесь с нашим производством *}
	{include file="%THEME%/sections/main/meet.tpl"}
	
	{* Свяжитесь с нами *}
	{include file="%THEME%/sections/common/coop.tpl"}
	

{/block}