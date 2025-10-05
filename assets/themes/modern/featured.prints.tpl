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
					
					<h1>{$lang.featuredPrints}</h1>
					<hr>
					{if $featuredPrintsRows}
						{foreach $featuredPrints as $print}
							
							<div class="featuredPageItem workboxLinkAttach">
								<h2><a href="{$print.linkto}" class="workboxLink">{$print.name}</a></h2>
								<p class="description">{if $print.photo}<img src="{productShot itemID=$print.print_id itemType=print photoID=$print.photo.id size=125}"><br><br>{/if}{$print.description|truncate:360}</p>
								<p class="moreInfo">{if $print.price}<span class="price">{$print.price.display}</span>{if $print.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3} {$lang.priceCreditSep} {/if}{if $print.credits and $config.settings.credits_print}<span class="price">{$print.credits} {$lang.mediaLabelCredits}</span>{/if}</p>
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