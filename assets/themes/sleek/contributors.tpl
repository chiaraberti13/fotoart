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
			<div id="contentLeftContainer">
				<div>
					{include file='subnav.tpl'}
				</div>
			</div>
			<div id="contentRightContainer">
				<div>
					{if $featuredContributors}
						<h1>{$lang.contributors} <a href="{linkto page='create.account.php?jumpTo=members'}" class="buttonLink" style="float: right;">{$lang.signUpNow}</a></h1>
						<div class="divTable" id="featuredContributorsList">
							<div class="divTableRow">
							{foreach $featuredContributors as $contributor}
								<div class="divTableCell"><a href="{$contributor.profileLinkto}">
									<p>{if $contributor.avatar}<img src="{memberAvatar memID=$contributor.mem_id size=100 crop=100}">{else}<img src="{$imgPath}/avatar.png" style="width: 100px;">{/if}</a></p>
									<h2><a href="{$contributor.profileLinkto}">{$contributor.display_name}</a></h2>
									<p><a href="{$contributor.profileLinkto}" class="colorLink">{$lang.profile}</a> | <a href="{$contributor.allMediaLinkto}" class="colorLink">{$lang.media}</a></p>
									{if $contributor.bio_status == 1}
									<p class="bio">{$contributor.bio_content|truncate:120}</p>
									{/if}
								</div>
								{if $contributor@iteration is div by 2}</div><div class="divTableRow">{/if}
							{/foreach}
							</div>
						</div>
					{/if}				
					<ul id="contributorsList">
					{foreach $contributorsList as $contributor}
						<li><a href="{$contributor.profileLinkto}"><img src="{memberAvatar memID=$contributor.mem_id size=30 crop=30}"></a> <a href="{$contributor.profileLinkto}">{$contributor.display_name}</a></li>
					{/foreach}
					</ul>
				</div>		
			</div>
		</div>
		{include file='footer.tpl'}
	</div>
</body>
</html>