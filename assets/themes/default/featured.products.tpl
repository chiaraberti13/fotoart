<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/featured.page.js"></script>
	<script type="text/javascript">
		$(function()
		{
			$('#featuredSubnavProducts').addClass('selectedNav');
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
						<h1>{$lang.featuredProducts}</h1>
						{if $featuredProductsRows}
							{foreach $featuredProducts as $product}
								<div class="featuredPageItem workboxLinkAttach">
									<h2><a href="{$product.linkto}" class="workboxLink">{$product.name}</a></h2>
									<p class="description">{if $product.photo}<img src="{productShot itemID=$product.prod_id itemType=prod photoID=$product.photo.id size=70}">{/if}{$product.description|truncate:360}</p>
									<p class="moreInfo"><!--<input type="button" value="{$lang.more}">-->{if $product.price}<span class="price">{$product.price.display}</span>{if $product.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_prod} {$lang.priceCreditSep} {/if}{if $product.credits}<span class="price">{$product.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
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