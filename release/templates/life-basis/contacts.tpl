{addcss file="/pages/contacts.css"}

{$app->title->addSection('Контакты — Основа жизни | Оптовая продажа питьевой воды', 0, 'replace')|devnull}

{extends file="%THEME%/default.tpl"}

{block name="content"}

	{include file="%THEME%/sections/contacts/contacts.tpl"}

{/block}