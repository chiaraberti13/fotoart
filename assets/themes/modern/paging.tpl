<div class="paging">
	{$lang.page}
	<select class="pagingPageNumber">
		{foreach $paging.pageNumbers as $page}
			<option value="{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$page@key}"}" {if $page@key == $paging.currentPage}selected="selected"{/if}>{$page}</option>
		{/foreach}
	</select> 
	{$lang.of} <strong>{$paging.totalPages}</strong> <span class="totalResults">({$paging.totalResults} {$lang.itemsTotal})</span> &nbsp;
	
	{if $paging.previousPage}<a href="{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$paging.previousPage}"}" class="btn btn-xs btn-primary">&laquo; {$lang.prevUpper}</a>{/if}

	{if $paging.nextPage}<a href="{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$paging.nextPage}"}" class="btn btn-xs btn-primary">{$lang.nextUpper} &raquo;</a>{/if}<!--{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$paging.nextPage}"}-->
</div>