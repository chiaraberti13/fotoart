<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/featured.page.js"></script>
	<script type="text/javascript">
		<!--
			$(function()
			{
				{if $promoID}workbox({ page : 'promo.php?id={$promoID}' });{/if}
			});
		-->
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
					<h1>{$lang.promotions}</h1>
					{if $promotions}
						{foreach $promotions as $promo}
							<div class="featuredPageItem workboxLinkAttach">
								<h2><a href="{$promo.linkto}" class="workboxLink">{$promo.name}</a></h2>
								<p class="description">
									{if $promo.photo}<img src="{productShot itemID=$promo.promo_id itemType=promo photoID=$promo.photo.id size=120}">{/if}
									{$promo.description}
									{if $promo.autoapply}<br><br><span class="promoUse">*{$lang.autoApply}</span>{elseif $promo.promo_code}<br><br><span class="promoUse">*{$lang.useCoupon}<strong>: {$promo.promo_code}</strong></span>{/if}
									{if $promo.oneuse and !$loggedIn}<br><br><span class="promoUse">*{$lang.couponLoginWarn}</span>{/if}
								</p>
							</div>
						{/foreach}							
						{* {if !$promo.autoapply}<p class="moreInfo"><input type="button" value="{$lang.apply}" onclick="goto('{linkto page="cart.php?coupon={$promo.promo_code}"}');"></p>{/if} *}
					{else}
						<p class="notice">{$lang.noPromotions}</p>
					{/if}
				</div>		
			</div>
		</div>
		{include file='footer.tpl'}
	</div>
</body>
</html>