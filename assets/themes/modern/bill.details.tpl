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
		
		<div class="container">
			<div class="row">	
				<div class="col-md-12">
					<p class="notice">
						<div class="orderDetailsHeader">
							<h1>{$lang.yourBill}</h1>
							<p style="float: right;"><a href="invoice.php?billID={$billInfo.ubill_id}" class="btn btn-xs btn-primary" target="_blank">{$lang.viewInvoice}</a></p>
						</div>
						{$lang.paymentThanks}
						{if $billInfo.membership}		
							{$lang.msActive}
						{/if}
						<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
					</p>
				</div>
			</div>
		</div>
		
		{include file='footer.tpl'}
    </div>
</body>
</html>