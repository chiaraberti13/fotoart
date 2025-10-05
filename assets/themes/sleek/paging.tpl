<div class="paging">
	{$lang.page}
	<select class="pagingPageNumber">
		{foreach $paging.pageNumbers as $page}
			<option value="{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$page@key}"}" {if $page@key == $paging.currentPage}selected="selected"{/if}>{$page}</option>
		{/foreach}
	</select> 
	{$lang.of} <strong>{$paging.totalPages}</strong> <span class="totalResults">({$paging.totalResults} {$lang.itemsTotal})</span> &nbsp;
	{if $paging.previousPage}<img src="{$imgPath}/prev.icon.png" href="{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$paging.previousPage}"}" class="previous" title="{$lang.prevUpper}" alt="{$lang.prevUpper}">{/if}
	{if $paging.nextPage}<img src="{$imgPath}/next.icon.png" href="{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$paging.nextPage}"}" class="next" title="{$lang.nextUpper}" alt="{$lang.nextUpper}">{/if}<!--{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$paging.nextPage}"}-->
</div>