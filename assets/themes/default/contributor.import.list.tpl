<form id="importFilesForm">
<input type="hidden" name="action" value="deleteImportMedia">
{if $incomingFiles}
	{foreach $incomingFiles as $file}
		<div class="contrImportListItems">
			<input type="checkbox" name="files[]" value="{$file.path}" class="importFiles" checked="checked">
			<p class="importListImgContainer"><img src="contributor.image.php?contrID={$contrFID}&src={$file.icon}"></p>
			<p>{$file.name}</p>
		</div>
	{/foreach}
{else}
	<p class="notice">{$lang.noImportFilesMessage}</p>
{/if}
</form>
