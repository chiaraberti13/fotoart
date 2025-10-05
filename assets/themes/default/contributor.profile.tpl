<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript">
		<!--
			$(function()
			{
				$('#navContributors').addClass('selectedNav');
				$('.contrViewAllMedia').click(function(event)
				{
					event.preventDefault();
					goto('{$contributor.allMediaLinkto}'); 
				});
			});
		-->
	</script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div class="contentContainer">
			<div class="content" style="padding-left: 0; padding-right: 0; overflow: auto;" id="contributorProfile">
				<h1>{$contributor.display_name}</h1>
				
				<div class="contributorInfo">
					{if $contributor.avatar}<img src="{memberAvatar memID=$contributor.mem_id size=200}"><br>{/if}
					<!-- Profile Views: {$contributor.profile_views} -->
					<a href="#" class="contrViewAllMedia">{$lang.mediaNav}: {$contrMediaCount}</a><br>
					{*if $contributor.f_name}{$lang.name}: <span class="info">{$contributor.f_name} {$contributor.l_name}</span><br>{/if*}
					{if $contributor.comp_name}{$lang.companyName}: <span class="info">{$contributor.comp_name}</span><br>{/if}
					{if $contributor.website}{$lang.website}: <a href="{$contributor.website}" class="color" target="_blank">{$contributor.website}</a><br>{/if}
					{if $contributor.state or $contributor.country}{$lang.location}: <span class="info">{$contributor.state}, {$contributor.country}</span><br>{/if}
					{if $contributor.memberSince}{$lang.memberSince}: <span class="info">{$contributor.memberSince}</span>{/if}
					{if $contrAlbums}
						<h2 style="clear: both; margin-top: 20px;">{$lang.albums}</h2>
						<ul>
							{foreach $contrAlbums as $album}
								<li><a href="{$album.linkto}">{$album.name}</a></li>
							{/foreach}
						</ul>
					{/if}
					<br><br><input type="button" value="{$lang.viewAllMedia}" style="float: right;" class="colorButton contrViewAllMedia">
				</div>
				
				<p>{if $contributor.bio_content && $contributor.bio_status == 1}{$contributor.bio_content}{else}{$lang.noBioMessage}{/if}</p>
				
				<p style="text-align: right"></p>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>