<div class="paging">
	{$lang.page}
	<select class="pagingPageNumber">
		{foreach $paging.pageNumbers as $page}
			<option value="{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$page@key}"}" {if $page@key == $paging.currentPage}selected="selected"{/if}>{$page}</option>
		{/foreach}
	</select> 
	{$lang.of} <strong>{$paging.totalPages}</strong> <span class="totalResults">({$paging.totalResults} {$lang.itemsTotal})</span>
	{if $paging.previousPage}<input type="button" value="&laquo;" href="{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$paging.previousPage}"}" class="previous">{/if}
	{if $paging.nextPage}<input type="button" value="&raquo;" href="{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$paging.nextPage}"}" class="next">{/if}<!--{linkto page="{$paging.pageName}&id={$id}&{$paging.pageVar}={$paging.nextPage}"}-->
</div>