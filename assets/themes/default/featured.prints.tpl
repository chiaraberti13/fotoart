<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/featured.page.js"></script>
	<script type="text/javascript">
		$(function()
		{
			$('#featuredSubnavPrints').addClass('selectedNav');
		});
	</script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div class="divTable contentContainer">
			<div class="divTableRow">
				<div class="divTableCell contentLeftColumn">
					{include file='subnav.tpl'}
				</div>
				<div class="divTableCell contentRightColumn">
					<div class="content">
						<h1>{$lang.featuredPrints}</h1>
						{if $featuredPrintsRows}
							{foreach $featuredPrints as $print}
								<div class="featuredPageItem workboxLinkAttach">
									<h2><a href="{$print.linkto}" class="workboxLink">{$print.name}</a></h2>
									<p class="description">{if $print.photo}<img src="{productShot itemID=$print.print_id itemType=print photoID=$print.photo.id size=70}">{/if}{$print.description|truncate:360}</p>
									<p class="moreInfo">{if $print.price}<span class="price">{$print.price.display}</span>{if $print.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3} {$lang.priceCreditSep} {/if}{if $print.credits and $config.settings.credits_print}<span class="price">{$print.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
								</div>
							{/foreach}
						{else}
							<p class="notice">{$lang.noFeatured}</p>
						{/if}
					</div>
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>