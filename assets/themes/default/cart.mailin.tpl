<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/cart.mailin.js"></script>
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
		
			<ul class="cartStepsBar {if $stepNumber.b}cartStepsBar25{else}cartStepsBar33{/if}">
				<li class="off"><p>{$stepNumber.a}</p><div>{$lang.cart}</div></li>
				{if $stepNumber.b}<li class="off shipping"><p>{$stepNumber.b}</p><div>{$lang.shipping}</div></li>{/if}
				<li class="off"><p>{$stepNumber.c}</p><div>{$lang.reviewOrder}</div></li>
				<li class="on"><p>{$stepNumber.d}</p><div>{$lang.payment}</div></li>
			</ul>
			
			<div class="content" style="padding-left: 0; padding-right: 0; padding-bottom: 0;">
				<!--<h1>Cart > Shipping > Review Your Order</h1>-->
				<div class="divTable cartContainer" style="width: 100%">
					<div class="divTableRow">
						<div class="divTableCell">
							{$content.body}
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