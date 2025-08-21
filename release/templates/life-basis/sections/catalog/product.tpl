{addcss file="pages/product.css"}

<section id="product-page">
	<div class="container">
		<div class="row">
			<div class="col">
				<div class="back-link-wrapper">
					<a href="{$product->getMainDir()->getUrl()}" class="back-link">
						<span class="image">
							<img src="{$THEME_IMG}/chevron-left.svg" alt="Назад">
						</span>
						<span class="title">
							Назад
						</span>
					</a>
				</div>
			</div>
			<div class="col">
				<h1>{$product->title}</h1>
			</div>
			<div class="col l6 m12">
				{if $product->description != null}
				<div class="fogged">{$product->description}</div>
				{/if}
				<div class="image-wrapper hide-l-up">
					<div class="row flex vcenter">
						<div class="col s7 xs6 t6">
							{* Картинка текущей позиции *}
							<img data-src="{$product->getMainImage()->getOriginalUrl()}" alt="{$product->title}" title="{$product->title}" class="lazy responsive-img current">
						</div>
						{* Данные следующей позиции *}
						{moduleinsert name="\CatalogExt\Controller\Block\NextProduct" currentId="{$product->id}"}
					</div>
				</div>
				<h2>Характеристики воды</h2>
				<div class="row flex">
					{foreach $product->properties[1]['properties'] as $property}
					<div class="col s6 xs6 t12">
						<div class="prop-card">
							<div class="prop-name">
								{$property->title}
							</div>
							<div class="prop-data">
								{$property->val_str} {$property->unit}
							</div>
						</div>
					</div>
					{/foreach}
				</div>
			</div>
			<div class="col l6 hide-l-down">
				<div class="image-wrapper">
					<div class="row flex vcenter">
						<div class="col t7 xs7">
							{* Картинка текущей позиции *}
							<img data-src="{$product->getMainImage()->getOriginalUrl()}" alt="{$product->title}" title="{$product->title}" class="lazy responsive-img current">
						</div>
						{* Данные следующей позиции *}
						{moduleinsert name="\CatalogExt\Controller\Block\NextProduct" currentId="{$product->id}"}
					</div>
				</div>
			</div>
		</div>
	</div>
</section>