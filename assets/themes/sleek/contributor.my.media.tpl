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
					<div class="contrIconsContainer">
						<a href="contributor.add.media.php"><img src="{$imgPath}/contr.new.media.png" title="{$lang.contUploadNewMedia}"></a>
						{if $contrMediaMode == 'album'}
							<a href="workbox.php?mode=contrEditAlbum&contrAlbumID={$contrAlbumID}" class="contrEditAlbum"><img src="{$imgPath}/contr.edit.png" title="{$lang.editAlbum}"></a>
							{if $member.membershipDetails.deleting}<a href="workbox.php?mode=contrDeleteAlbum&contrAlbumID={$contrAlbumID}" class="contrDeleteAlbum"><img src="{$imgPath}/contr.delete.png" title="{$lang.deleteAlbum}"></a><!-- actions.php?action=contrDeleteAlbum&albumID={$contrAlbumID} -->{/if}
						{/if}
						{if $member.membershipDetails.personal_galleries}<a href="workbox.php?mode=contrNewAlbum" class="contrNewAlbum"><img src="{$imgPath}/contr.new.album.png" title="{$lang.newAlbum}"></a>{/if}
					</div>
					<h1><a href="contributor.my.media.php?mode=all">{$lang.contMedia}</a> {if $currentAblum.name}&raquo; {$currentAblum.name}{/if}</h1>
					{if $mediaRows}
						{include file="paging.tpl" paging=$mediaPaging}
						<div id="mediaListContainer">
							{foreach $mediaArray as $media}
								{include file='contributor.media.container.tpl'}
							{/foreach}
						</div>
						{include file="paging.tpl" paging=$mediaPaging}
					{else}
						<p class="notice">{$lang.noMediaAlbum}</p>
					{/if}
				</div>		
			</div>
		</div>
		{include file='footer.tpl'}
	</div>
</body>
</html>