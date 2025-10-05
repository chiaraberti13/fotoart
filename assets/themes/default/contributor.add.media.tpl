<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/jqueryUI/jqueryUI.js"></script>
	
	{*if $member.uploader == 2}<!-- Moved to workbox -->
		<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
		<script type="text/javascript" src="{$baseURL}/assets/plupload/plupload.full.js"></script>
		<script type="text/javascript" src="{$baseURL}/assets/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
		<style type="text/css">
			@import url({$baseURL}/assets/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css);
			.plupload_button.plupload_start, .plupload_header{ display:none; } /* Hide start button */
		</style>
	{/if*}
	
	<script>
		$(function()
		{
			{if $incomingFileCount > 0}
				loadContrImportWindow();
			{else}
				$('.contrUploadMedia').click();
				$('#contrImportContainer').hide();	
			{/if}
		});
	</script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div class="divTable contentContainer">
			<div class="divTableRow">
				<div class="divTableCell contentLeftColumn">
					{include file='memnav.tpl'}
				</div>
				<div class="divTableCell contentRightColumn">
					<div class="content">
						<h1>{$lang.contUploadNewMedia}</h1>
						
						<input type="button" href="workbox.php?mode=contrUploadMedia" value="{$lang.uploadUpper}" class="colorButton contrUploadMedia" style="width: 100px; height: 30px;">{if $config.settings.contr_cd2} {$lang.OR} <a href="workbox.php?mode=contrMailinMedia" class="contrMailinMedia">{$lang.mailInMedia}</a>{/if}
						<br><br><br>
						
						<div id="contrImportContainer">
							<div class="contrIconsContainer">
								<a href="#" class="contrImportSelectAll"><img src="{$imgPath}/contr.select.all.png" title="{$lang.selectAll}" style="width: 13px; height: 13px;"></a>
								<a href="#" class="contrImportSelectNone"><img src="{$imgPath}/contr.select.none.png" title="{$lang.selectNone}" style="width: 13px; height: 13px;"></a>
								<a href="workbox.php?mode=contrDeleteImportMedia&contrAlbumID={$contrAlbumID}" class="contrDeleteImportMedia"><img src="{$imgPath}/contr.delete.png" title="{$lang.deleteMedia}"></a>
							</div>
							<h2>{$lang.waitingForImport}</h2>							
							<div id="contrImportListContainer" class="importWindowLoader"></div>
							<input type="button" value="{$lang.importSelectedUpper}" id="importSelectedButton" class="colorButton dropshadowdark">
						</div>
						
					</div>
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>