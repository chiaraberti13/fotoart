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
					
					<h1>{$lang.featuredSubscriptions}</h1>
					<hr>
					{if $featuredSubscriptionsRows}
						{foreach $featuredSubscriptions as $subscription}
							<div class="featuredPageItem workboxLinkAttach">
								<h2><a href="{$subscription.linkto}" class="workboxLink">{$subscription.name}</a></h2>
								<p class="description">{if $subscription.photo}<img src="{productShot itemID=$subscription.sub_id itemType=sub photoID=$subscription.photo.id size=125}"><br><br>{/if}{$subscription.description|truncate:360}</p>
								<p class="moreInfo">{if $subscription.price}<span class="price">{$subscription.price.display}</span>{if $subscription.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_sub} {$lang.priceCreditSep} {/if}{if $subscription.credits}<span class="price">{$subscription.credits} {$lang.mediaLabelCredits}</span>{/if}</p>
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