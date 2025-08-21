<section id="news">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1>{$dir->title}</h1>
			</div>
		</div>
		<div class="row flex">
			{if $list}
				{foreach $list as $item}
					<div class="col xl3 l4 m6 s12 margin-bottom">
						<div class="news-card">
							<div class="image-wrapper">
							{if $item.image}
								<img data-src="{$item->__image->getLink()}" alt="{$item->title}" class="lazy responsive-img">
							{else}
								<img data-src="{$THEME_IMG}/no-photo.svg" alt="{$item->title}" class="lazy responsive-img">
							{/if}
							</div>
							<div class="news-title">
								<span>{$item->title}</span>
							</div>
							<a href="{$item->getUrl()}">Подробнее</a>
						</div>
					</div>
				{/foreach}
			{else}

			{/if}
		</div>
		<div class="row">
			<div class="col">
				{include file="%THEME%/paginator.tpl"}
			</div>
		</div>
	</div>
</section>

{* Свяжитесь с нами *}
{include file="%THEME%/sections/common/coop.tpl"}