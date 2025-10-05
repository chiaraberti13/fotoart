<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/featured.page.js"></script>
	<script type="text/javascript">
		$(function()
		{
			$('#featuredSubnavCredits').addClass('selectedNav');
		});
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
					<h1>{$lang.featuredCredits}</h1>
					{if $featuredCreditsRows}
						{foreach $featuredCredits as $credit}
							<div class="featuredPageItem workboxLinkAttach">
								<h2><a href="{$credit.linkto}" class="workboxLink">{$credit.name}</a></h2>
								<p class="description">{if $credit.photo}<img src="{productShot itemID=$credit.credit_id itemType=credit photoID=$credit.photo.id size=120}">{/if}{$credit.description|truncate:360}</p>
								{if $cartStatus}<p class="moreInfo">{if $credit.price}<span class="price">{$credit.price.display}</span>{if $credit.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}</p>{/if}
							</div>
						{/foreach}
					{else}
						<p class="notice">{$lang.noFeatured}</p>
					{/if}
				</div>		
			</div>
		</div>
		{include file='footer.tpl'}
	</div>
</body>
</html>