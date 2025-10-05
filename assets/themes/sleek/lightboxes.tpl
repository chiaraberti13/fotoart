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
					{if $loggedIn}
						{include file='memnav.tpl'}
					{else}
						{include file='subnav.tpl'}
					{/if}
				</div>
			</div>
			<div id="contentRightContainer">
				<div>
					<input type="button" value="{$lang.newLightbox}" style="float: right; margin-bottom: 10px;" id="newLightbox">
					<h1>{$lang.lightboxes}</h1>					
					
					{if $notice}
						<p class="notice" style="margin-bottom: 14px;">{$lang.{$notice}}</p>
					{/if}
					
					{if $lightboxRows}
						<table class="dataTable">
							<tr>
								<th>{$lang.lightboxUpper}</th>
								<th style="text-align: center">{$lang.itemsUpper}</th>
								<th>{$lang.createdUpper}</th>
								<th></th>
							</tr>
							{foreach $lightboxArray as $key => $lightbox}
								<tr>
									<td><a href="{$lightbox.linkto}" class="colorLink">{$lightbox.name}</a></td>
									<td style="text-align: center">{$lightbox.items}</td>
									<td>{$lightbox.create_date_display}</td>
									<td style="text-align: right"><a href="{$lightbox.linkto}" class="buttonLink">{$lang.view}</a> <a href="{$lightbox.ulightbox_id}" class="buttonLink lightboxEdit">{$lang.edit}</a> <a href="{$lightbox.ulightbox_id}" class="buttonLink lightboxDelete">{$lang.delete}</a></td>
								</tr>
							{/foreach}
						</table>
					{else}
						<p class="notice">{$lang.noLightboxes}</p>
					{/if}
				</div>		
			</div>
		</div>
		{include file='footer.tpl'}
	</div>
</body>
</html>