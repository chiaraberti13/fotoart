<script type="text/javascript">
	$(function(){ assignDownloadButtonActions(); });
</script>
<div class="divTable cartItemsList collectionDownloadList">
	{foreach $mediaArray as $key => $media}	
	<div class="divTableRow">
		<div class="divTableCell thumbRow">
			<a href="media.details.php?mediaID={$media.useMediaID}"><img src="{mediaImage mediaID=$media.encryptedID type=thumb folderID=$media.encryptedFID size=50 seo=$media.seoName}" alt="{$media.title}" class="thumb"></a>
		</div>
		<div class="divTableCell itemRow">{$media.title}</div>
		<div class="divTableCell actionsRow">
			<input 
				type="button" 
				value="{if $media.downloadableStatus == '4'}{$lang.request}{else}{$lang.downloadUpper}{/if}" 
				class="orderDownloadButton btn btn-xs btn-primary" 
				downloadableStatus="{$media.downloadableStatus}" 
				key="{$media.downloadKey}"  
			/><!--{$media.downloadableStatus}/{$media.downloadKey}-->
		</div>
	</div>
	{/foreach}
</div>

