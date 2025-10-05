<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/cart.js"></script>
	<script type="text/javascript" src="{$baseURL}/assets/javascript/order.details.js"></script>
	<script type="text/javascript">
		$(function()
		{ 
			// Hide top nav so we can replace it with steps bar
			$('#searchBar').hide();
			$('#topNav').hide();
		});
	</script>
</head>
<body>
	<input type="hidden" id="loggedIn" name="loggedIn" value="{$loggedIn}">
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		
		<div class="orderDetailsHeader">{$lang.yourBill} <a href="invoice.php?billID={$billInfo.ubill_id}" class="buttonLink" target="_blank" style="float: right">{$lang.viewInvoice}</a></p></div>
		
		<div style="margin: 50px 0 100px 0">
			<p class="notice">
				{$lang.paymentThanks}
				{if $billInfo.membership}		
					{$lang.msActive}
				{/if}
			</p>
		</div>
		
		{include file='footer.tpl'}
    </div>
</body>
</html>