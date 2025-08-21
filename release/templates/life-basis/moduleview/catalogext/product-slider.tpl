<div class="row">
	<div class="col">
		<div class="swiper" id="product-slider">
			<div class="swiper-wrapper">
				{foreach $products as $product}
					<div class="swiper-slide">
						<div class="product-card">
							<div class="image-wrapper">
								{if $product->image != ""}
								<img data-src="{$product->getMainImage()->getOriginalUrl()}" alt="{$product->title}" class="responsive-img lazy">
								{/if}
							</div>
							<div class="product-info">
								<div class="title-wrapper">
									<strong>{$product->title}</strong>
									<small>{$product->longtitle}</small>
								</div>
								<div class="arrow-wrapper">
									<img src="{$THEME_IMG}/chevron-right.svg" alt="Подробнее">
								</div>
							</div>
							<a href="{$product->getUrl()}" class="card-link">Подробнее</a>
						</div>
					</div>
				{/foreach}
			</div>
		</div>
		<div class="swiper-pagination" id="product-pagination"></div>
	</div>
</div>