<div class="col t4 xs4">
	<a href="{$nextProduct->getUrl()}">
		<img data-src="{$nextProduct->getMainImage()->getOriginalUrl()}" alt="{$nextProduct->title}" class="lazy responsive-img next" title="{$nextProduct->title}">
	</a>
</div>
<div class="col s1 t2 xs2">
	<a href="{$nextProduct->getUrl()}" title="{$nextProduct->title}" class="next-link">
		<img src="{$THEME_IMG}/chevron-right.svg" alt="{$nextImage->title}">
	</a>
</div>