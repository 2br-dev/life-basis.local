<section id="news">
	<div class="container">
		<div class="row flex">
			<div class="col">
				<hgroup>
					<h1>{$article->title}</h1>
					<div class="fogged">
						{$article->short_content}
					</div>
				</hgroup>
			</div>
			<div class="col l6 m12">
				{$article->content}
			</div>
			<div class="col l6 m12">
				<div class="pin">
				{if $article.image}
					<img data-src="{$article->__image->getLink()}" alt="{$article->title}" class="news-image lazy responsive-img">
				{else}
					<img data-src="{$THEME_IMG}/no-photo.svg" alt="{$article->title}" class="lazy responsive-img news-image">
				{/if}
				</div>
			</div>
		</div>
	</div>
</section>

{* Свяжитесь с нами *}
{include file="%THEME%/sections/common/coop.tpl"}