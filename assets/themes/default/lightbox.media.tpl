<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript">
		<!--
			$(function(){ $('#featuredSubnavMedia').addClass('selectedNav'); });
		-->		
	</script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div class="contentContainer">
			<div class="content" style="padding-left: 0; padding-right: 0;">
				<h1><a href="lightboxes.php">{$lang.lightboxes}</a> &raquo; {$lightbox.name}</h1>
				{if $lightbox.notes}<p>{$lightbox.notes}</p>{/if}
				{if $mediaRows}
					{include file="paging.tpl" paging=$mediaPaging}
					<div id="mediaListContainer">
						{foreach $mediaArray as $media}
							{include file='media.container.tpl'}
						{/foreach}
					</div>
					{include file="paging.tpl" paging=$mediaPaging}
				{else}
					<p class="notice">{$lang.noMedia}</p>
				{/if}
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>