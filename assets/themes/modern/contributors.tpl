<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script>
		$(function()
		{	
			$('.featuredContributor img').load(function()
			{
				setEquals('.featuredContributor'); // After the images all load do a final height resize
			});
			
			setEquals('.featuredContributor'); // Backup for IE
		});
	</script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		{include file='header2.tpl'}		
		<div class="container" style="min-height: 600px;">					
			{if $featuredContributors}
			<h1>{$lang.contributors} <a href="{linkto page='create.account.php?jumpTo=members'}" class="btn btn-xs btn-success" style="float: right;">{$lang.signUpNow}</a></h1>
			<hr>
			<div id="featuredContributorsList">				
				{foreach $featuredContributors as $contributor}
					<div class="featuredContributor">
						<a href="{$contributor.profileLinkto}">{if $contributor.avatar}<img src="{memberAvatar memID=$contributor.mem_id size=100 crop=100}">{else}<img src="{$imgPath}/avatar.png" style="width: 80px;">{/if}</a><strong><a href="{$contributor.profileLinkto}">{$contributor.display_name}</a></strong>
						{if $contributor.bio_content}<p class="bio">{$contributor.bio_content|truncate:120}</p>{/if}
						<p><br><a href="{$contributor.profileLinkto}" class="btn btn-xs btn-primary">{$lang.profile}</a> <a href="{$contributor.allMediaLinkto}" class="btn btn-xs btn-primary">{$lang.media}</a></p>
					</div>
				{/foreach}
			</div>
		{/if}				
		<ul id="contributorsList">
		{foreach $contributorsList as $contributor}
			<li><a href="{$contributor.profileLinkto}"><img src="{memberAvatar memID=$contributor.mem_id size=30 crop=30}"></a> <a href="{$contributor.profileLinkto}">{$contributor.display_name}</a></li>
		{/foreach}
		</ul>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>
