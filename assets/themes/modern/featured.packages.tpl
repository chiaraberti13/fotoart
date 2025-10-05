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
					
					<h1>{$lang.featuredPackages}</h1>
					<hr>
					{if $featuredPackagesRows}
						{foreach $featuredPackages as $package}
							<div class="featuredPageItem workboxLinkAttach">
								<h2><a href="{$package.linkto}" class="workboxLink">{$package.name}</a></h2>
								<p class="description">{if $package.photo}<img src="{productShot itemID=$package.pack_id itemType=pack photoID=$package.photo.id size=125}"><br><br>{/if}{$package.description|truncate:360}</p>
								<p class="moreInfo">{if $package.price}<span class="price">{$package.price.display}</span>{if $package.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_pack} {$lang.priceCreditSep} {/if}{if $package.credits}<span class="price">{$package.credits} {$lang.mediaLabelCredits}</span>{/if}</p>
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