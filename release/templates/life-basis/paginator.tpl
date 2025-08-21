{if $paginator->total_pages>1}
    {$pagestr = t('Страница %page', ['page' => $paginator->page])}
    {if $paginator->page > 1 && !substr_count($app->title->get(), $pagestr)}
        {$app->title->addSection($pagestr, 0, 'after')|devnull}
    {/if}

    {if !$paginator_len}
        {$paginator_len = 5}
    {/if}
    {$paginator->setPaginatorLen($paginator_len)|devnull}
    <div class="{$class|default:"mt-5"}">
		<ul class="pagination">
			{if $paginator->page>1}
				<li class="pagination-page">
					<a href="{$paginator->getPageHref($paginator->page-1)}" data-page="{$paginator->page-1}" title="{t}предыдущая страница{/t}">
						<img src="{$THEME_IMG}/chevron-left.svg" alt="Назад">
					</a>
				</li>
			{/if}
			{if $paginator->showFirst()}
				<li class="pagination-page">
					<a href="{$paginator->getPageHref(1)}" data-page="1" title="{t}первая страница{/t}"s>1</a>
				</li>
				<li class="pagination-dots">
					<span>...</span>
				</li>
			{/if}

			{foreach $paginator->getPageList() as $page}
				<li class="pagination-page {if $page.act}active{/if}">
					<a href="{$page.href}" data-page="{$page.n}">{$page.n}</a>
				</li>
			{/foreach}

			{if $paginator->showLast()}
				<li class="pagination-dots">
					<span>...</span>
				</li>
				<li class="pagination-page">
					<a href="{$paginator->getPageHref($paginator->total_pages)}" data-page="{$paginator->total_pages}" title="{t}последняя страница{/t}">{$paginator->total_pages}</a>
				</li>
			{/if}

			{if $paginator->page < $paginator->total_pages}
				<li class="pagination-page">
					<a href="{$paginator->getPageHref($paginator->page+1)}" data-page="{$paginator->page+1}" title="{t}следующая страница{/t}">
						<img src="{$THEME_IMG}/chevron-right.svg" alt="Вперёд">
					</a>
				</li>
			{/if}
		</ul>
    </div>
{/if}