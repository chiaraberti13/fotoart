<script type="text/javascript" src="{$baseURL}/assets/javascript/similar.media.js"></script>
{if $mediaRows}
	<div id="mediaListContainer">
		{foreach $mediaArray as $media}
			<p class="mediaThumbContainer similarMedia" id="thumb{$media.media_id}"><a href="{$media.linkto}"><img src="{mediaImage mediaID=$media.encryptedID type=thumb folderID=$media.encryptedFID size=100 crop=60}" class="mediaThumb {if $config.settings.rollover_status}showHoverWindow{/if}" mediaID="{$media.encryptedID}"></a></p>
		{/foreach}
	</div>
{else}
	<p class="notice">{$lang.noSimilarMediaMessage}</p>
{/if}