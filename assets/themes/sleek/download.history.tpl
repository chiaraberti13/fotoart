<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div id="contentContainer" class="center">
			{include file='search.row.tpl'}
			<div id="contentLeftContainer">
				<div>
					{include file='memnav.tpl'}
				</div>
			</div>
			<div id="contentRightContainer">
				<div>
					<h1>{$lang.downloadHistory}</h1>
					{if $downloadsRows}
						<table class="dataTable">
							<tr>
								<th style="text-align: center">{$lang.media}</th>
								<th style="width: 100%">{$lang.dateDownloadUpper}</th>
								<!--<th style="text-align: center">VERSION</th>-->
								<th style="white-space: nowrap; text-align: center">{$lang.downloadTypeUpper}</th>
							</tr>
							{foreach $downloadsArray as $key => $download}
								<tr>
									<td style="text-align: center"><img src="{mediaImage mediaID=$download.media.encryptedID type=thumb folderID=$download.media.encryptedFID size=35}" class="genericImgBorder"><!--{$download.asset_id}--></td>
									<td>{$download.download_date_display}</td>
									<!--<td style="text-align: center">{$download.dsp_id}</td>-->
									<td style="text-align: center">{$download.download_type_display}{if $download.dl_type_id} {$download.dl_type_id}{/if}</td>
								</tr>
							{/foreach}
						</table>
					{else}
						<p class="notice">{$lang.noDownloads}</p>
					{/if}
				</div>		
			</div>
		</div>
		{include file='footer.tpl'}
	</div>
</body>
</html>