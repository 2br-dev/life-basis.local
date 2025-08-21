<section id="products">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1>{$category}</h1>
			</div>
		</div>
		<div class="row flex">
			{foreach $list as $product}
				<div class="col xl3 l4 m6 s6 xs12 margin-bottom">
					<div class="product-card">
						<div class="image-wrapper">
							<img src="{$product->getMainImage()->getOriginalUrl()}" alt="{$product->name}">
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
</section>