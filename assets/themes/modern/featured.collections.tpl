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
					
					<h1>{$lang.featuredCollections}</h1>
					<hr>
					{if $featuredCollectionsRows}
						{foreach $featuredCollections as $collection}
							<div class="featuredPageItem workboxLinkAttach">
								<h2><a href="{$collection.linkto}" class="workboxLink">{$collection.name}</a></h2>
								<p class="description">{if $collection.photo}<img src="{productShot itemID=$collection.coll_id itemType=coll photoID=$collection.photo.id size=125}"><br><br>{/if}{$collection.description|truncate:360}</p>
								<p class="moreInfo">{if $collection.price}<span class="price">{$collection.price.display}</span>{if $collection.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_coll} {$lang.priceCreditSep} {/if}{if $collection.credits}<span class="price">{$collection.credits} {$lang.mediaLabelCredits}</span>{/if}</p>
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