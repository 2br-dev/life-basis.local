<section>
	<div class="container">
		<div class="row flex vcenter">
			<div class="col xs8">
				<hgroup>
					<{$tag}>{$header}</{$tag}>
					{if $subheader!=null}
						<p class="fogged">{$subheader}</p>
					{/if}
				</hgroup>
			</div>
			<div class="col xs4">
				<div class="slider-navi">
					<a href="#!" class="production-prev slider-nav-button"><img src="{$THEME_IMG}/chevron-left.svg" alt="Назад"></a>
					<a href="#!" class="production-next slider-nav-button"><img src="{$THEME_IMG}/chevron-right.svg" alt="Вперёд"></a>
				</div>
			</div>
		</div>
		<div class="row">
			{moduleinsert name="\CatalogExt\Controller\Block\ProductSlider"}
		</div>
	</div>
</section>