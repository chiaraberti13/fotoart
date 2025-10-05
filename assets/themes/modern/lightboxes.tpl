<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		{include file='header2.tpl'}		
		
		<div class="container">
			<div class="row">
				{if $loggedIn}
					{include file='memnav.tpl'}
				{else}
					{include file='subnav.tpl'}
				{/if}	
				<div class="col-md-9">
					
					<h1>{$lang.lightboxes}</h1>
					<hr>
					<input type="button" value="{$lang.newLightbox}" style="float: right; margin-bottom: 10px;" id="newLightbox" class="btn btn-xs btn-success">
					
					{if $notice}
						<p class="notice" style="margin-bottom: 14px;">{$lang.{$notice}}</p>
					{/if}
					
					{if $lightboxRows}
						<table class="dataTable">
							<tr>
								<th>{$lang.lightboxUpper}</th>
								<th class="hidden-xs" style="text-align: center">{$lang.itemsUpper}</th>
								<th class="hidden-xs">{$lang.createdUpper}</th>
								<th></th>
							</tr>
							{foreach $lightboxArray as $key => $lightbox}
								<tr>
									<td><a href="{$lightbox.linkto}" class="colorLink">{$lightbox.name}</a></td>
									<td class="hidden-xs" style="text-align: center">{$lightbox.items}</td>
									<td class="hidden-xs">{$lightbox.create_date_display}</td>
									<td style="text-align: right; white-space: nowrap"><a href="{$lightbox.linkto}" class="btn btn-xs btn-primary">{$lang.view}</a> <a href="{$lightbox.ulightbox_id}" class="btn btn-xs btn-primary lightboxEdit">{$lang.edit}</a> <a href="{$lightbox.ulightbox_id}" class="btn btn-xs btn-danger lightboxDelete">{$lang.delete}</a></td>
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