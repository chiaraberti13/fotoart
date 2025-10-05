<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/featured.page.js"></script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		{include file='header2.tpl'}		
		
		<div class="container">
			<div class="row">
				{include file='subnav.tpl'}		
				<div class="col-md-9">
					
					<h1>{$lang.featuredProducts}</h1>
					<hr>
					{if $featuredProductsRows}
						{foreach $featuredProducts as $product}
							<div class="featuredPageItem workboxLinkAttach">
								<h2><a href="{$product.linkto}" class="workboxLink">{$product.name}</a></h2>
								<p class="description">{if $product.photo}<img src="{productShot itemID=$product.prod_id itemType=prod photoID=$product.photo.id size=125}"><br><br>{/if}{$product.description|truncate:360}</p>
								<p class="moreInfo"><!--<input type="button" value="{$lang.more}">-->{if $product.price}<span class="price">{$product.price.display}</span>{if $product.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_prod} {$lang.priceCreditSep} {/if}{if $product.credits}<span class="price">{$product.credits} {$lang.mediaLabelCredits}</span>{/if}</p>
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