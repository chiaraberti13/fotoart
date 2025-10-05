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
		{include file='header2.tpl'}		
		
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1>{$contributor.display_name}</h1>
					<hr>
				</div>
			</div>			
			<div class="row">
				<div class="col-md-3">
					<div class="contributorInfo">
						{if $contributor.avatar}<img src="{memberAvatar memID=$contributor.mem_id size=200}"><br>{/if}
						<!-- Profile Views: {$contributor.profile_views} -->
						<a href="#" class="contrViewAllMedia">{$lang.mediaNav} ({$contrMediaCount})</a><br><br>
						{*if $contributor.f_name}{$lang.name}: {$contributor.f_name} {$contributor.l_name}<br>{/if*}
						{if $contributor.comp_name}{$lang.companyName}: <span class="info">{$contributor.comp_name}</span><br><br>{/if}
						{if $contributor.website}{$lang.website}: <a href="{$contributor.website}" class="color" target="_blank">{$contributor.website}</a><br><br>{/if}
						{if $contributor.state or $contributor.country}{$lang.location}: <span class="info">{$contributor.state}, {$contributor.country}</span><br><br>{/if}
						{if $contributor.memberSince}{$lang.memberSince}: <span class="info">{$contributor.memberSince}</span><br>{/if}
						{if $contrAlbums}
							<h2 style="clear: both; margin-top: 20px;">{$lang.albums}</h2>
							<ul>
								{foreach $contrAlbums as $album}
									<li><a href="{$album.linkto}">{$album.name}</a></li>
								{/foreach}
							</ul>
						{/if}
						<br><br><input type="button" value="{$lang.viewAllMedia}" style="float: right;" class="btn btn-xs btn-primary contrViewAllMedia">
					</div>
				</div>
				<div class="col-md-9">				
					<p>{if $contributor.bio_content && $contributor.bio_status == 1}{$contributor.bio_content}{else}{$lang.noBioMessage}{/if}</p>
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>
