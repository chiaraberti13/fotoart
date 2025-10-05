<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript">
		<!--
			$(function(){ $('#navContributors').addClass('selectedNav'); });
		-->		
	</script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div id="contentContainer" class="center">
			{include file='search.row.tpl'}
			<div class="content">
				<h1>{if $contributor.avatar}<a href="{$contributor.profileLinkto}"><img src="{memberAvatar memID=$contributor.mem_id size=40 crop=40 hcrop=40}" class="h1PhotoHeader"></a>{/if} <a href="{$contributor.profileLinkto}">{$contributor.display_name}</a></h1>
				{if $contributor.bio_content && $contributor.bio_status == 1}<p class="contributorGalleryBio">{$contributor.bio_content|truncate:400} <a href="{$contributor.profileLinkto}" class="colorLink">{$lang.more}</a></p>{/if}
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