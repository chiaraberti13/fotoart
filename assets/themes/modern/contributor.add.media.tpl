<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/jqueryUI/jqueryUI.js"></script>	
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
		{include file='header2.tpl'}		
		
		<div class="container">
			<div class="row">
				{include file='memnav.tpl'}		
				<div class="col-md-9">
					
					<h1>{$lang.contUploadNewMedia}</h1>
					<hr>
					<input type="button" href="workbox.php?mode=contrUploadMedia" value="{$lang.uploadUpper}" class="contrUploadMedia btn btn-md btn-primary" style="width: 100px; height: 30px;">{if $config.settings.contr_cd2} {$lang.OR} <a href="workbox.php?mode=contrMailinMedia" class="contrMailinMedia">{$lang.mailInMedia}</a>{/if}
					<br><br><br>
					
					<div id="contrImportContainer">
						<div class="contrIconsContainer">
							<a href="#" class="contrImportSelectAll"><img src="{$imgPath}/contr.select.all.png" title="{$lang.selectAll}" style="width: 13px; height: 13px;"></a>
							<a href="#" class="contrImportSelectNone"><img src="{$imgPath}/contr.select.none.png" title="{$lang.selectNone}" style="width: 13px; height: 13px;"></a>
							<a href="workbox.php?mode=contrDeleteImportMedia&contrAlbumID={$contrAlbumID}" class="contrDeleteImportMedia"><img src="{$imgPath}/contr.delete.png" title="{$lang.deleteMedia}"></a>
						</div>
						<h2>{$lang.waitingForImport}</h2>							
						<div id="contrImportListContainer" class="importWindowLoader"></div>
						<input type="button" value="{$lang.importSelectedUpper}" id="importSelectedButton" class="btn btn-xs btn-primary">
					</div>
					
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>