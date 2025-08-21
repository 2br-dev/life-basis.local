{extends file="%THEME%/default.tpl"}

{addcss file="pages/about.css"}

{block name="content"}

	{* Слайдер продукции *}
	{include file="%THEME%/sections/common/products.tpl" 
		header="Дистрибьюторам" 
		tag="h1"
		subheader="Гибко управляйте ассортиментом, расширяя продуктовую матрицу известными брендами с идеальной репутацией"
	}

	{* Узнайте с кем предстоит работать *}
	{include file="%THEME%/sections/about/about.tpl"}

	{* Описание технологического процесса *}
	{include file="%THEME%/sections/about/process.tpl"}

	{* Свяжитесь с нами *}
	{include file="%THEME%/sections/common/coop.tpl"}

{/block}