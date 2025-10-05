<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/bill.mailin.js"></script>
	<script type="text/javascript">
		<!--
			$(function()
			{
				// Hide top nav so we can replace it with steps bar
				$('#searchBar').hide();
				$('#topNav').hide();
			});
		-->
	</script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div class="contentContainer">

			<div class="orderDetailsHeader">{$lang.payment}</div>
			
			<div class="content" style="padding-left: 0; padding-right: 0; padding-bottom: 0;">
				<!--<h1>Cart > Shipping > Review Your Order</h1>-->
				<div class="divTable cartContainer" style="width: 100%">
					<div class="divTableRow">
						<div class="divTableCell">
							
							<div class="cartReviewAddresses" style="float: right;">
								<div style="margin-left: 10px;">
									<h1>{$lang.mailTo}:</h1>
									<p>
										<strong>{$config.settings.business_name}</strong><br>
										{$config.settings.business_address}<br>
										{if $config.settings.business_address2}{$config.settings.business_address2}<br>{/if}
										{$config.settings.business_city}, {$config.settings.business_state} {$config.settings.business_zip}<br>
										{$config.settings.business_country}<br>
										<!--
										{if $shippingAddress.phone}{$shippingAddress.phone}<br>{/if}
										{if $shippingAddress.email}{$shippingAddress.email}<br>{/if}
										-->
									</p>
								</div>
							</div>							
							<p>
								{$lang.billMailinThanks}<br><br>
								{$lang.total}: <span class="price">{$billTotals.billGrandTotalLocal.display}</span><br><br>
								*{$lang.mailinRef}: <strong>{$billInfo.orderNumber}</strong><br><br>
							</p>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell" style="padding-top: 10px;"><input type="button" value="{$lang.returnToSiteUpper}" style="float: right" id="cartReturnButton" class="colorButton"></div>
					</div>
				</div>
				
			</div>
			
			{* Debug Area *}
			{if $debugMode}
				<div class="debug">
					<h1>Debug: Shipping Address</h1>
					<ul>
						{foreach $shippingAddress as $shippingAddressKey => $shippingAddressValue}
							<li><strong>{$shippingAddressKey}:</strong> {$shippingAddressValue}</li>
						{/foreach}
					</ul>
				</div>
				<div class="debug">
					<h1>Debug: Billing Address</h1>
					<ul>
						{foreach $billingAddress as $billingAddressKey => $billingAddressValue}
							<li><strong>{$billingAddressKey}:</strong> {$billingAddressValue}</li>
						{/foreach}
					</ul>
				</div>
				<div class="debug">
					<h1>Debug: Cart Info</h1>
					<ul>
						{foreach $cartInfo as $cartInfoKey => $cartInfoValue}
							<li>
								<strong>{$cartInfoKey}:</strong> 
								{if is_array($cartInfoValue)}
									<ul>
										{foreach $cartInfoValue as $subcartInfoKey => $subcartInfoValue}
											<li><strong>{$subcartInfoKey}:</strong> {$subcartInfoValue}</li>
										{/foreach}
									</ul>
								{else}
									{$cartInfoValue}
								{/if}
							</li>
						{/foreach}
					</ul>
				</div>
				<div class="debug">
					<h1>Debug: Cart Totals</h1>
					<ul>
						{foreach $cartTotals as $cartTotalsKey => $cartTotalsValue}
							<li>
								<strong>{$cartTotalsKey}:</strong> 
								{if is_array($cartTotalsValue)}
									<ul>
										{foreach $cartTotalsValue as $subcartTotalsKey => $subcartTotalsValue}
											<li><strong>{$subcartTotalsKey}:</strong> {$subcartTotalsValue}</li>
										{/foreach}
									</ul>
								{else}
									{$cartTotalsValue}
								{/if}
							</li>
						{/foreach}
					</ul>
				</div>
			{/if}
			
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>