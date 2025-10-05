<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/cart.js"></script>
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	<script type="text/javascript">
		// This identifies your website in the createToken call below
		Stripe.setPublishableKey('{$gatewaySetting.pkey}');
	</script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div class="contentContainer">
		
			<ul class="cartStepsBar">
				<li class="on" style="width: 100%;"><div>{$lang.payment}</div></li>
			</ul>
			
			<div class="content" style="padding-left: 0; padding-right: 0; padding-bottom: 0;">
				<!--<h1>Cart > Shipping > Review Your Order</h1>-->
				<div class="divTable cartContainer" style="width: 100%">
					<div class="divTableRow">
						<div class="divTableCell">
							
							
							<div>
							
							<p style="margin-top: 20px; margin-bottom: 20px;">{$lang.ccMessage}</p>
							
							<form action="{$baseURL}/assets/gateways/stripe/ipn.php" method="POST" id="payment-form" class="cleanForm form-group">
							
							{$gatewayForm}
							
							<div class="payment-errors messageBar alert alert-danger" style="display: none; margin-bottom: 15px;" id="creditCardWarning"></div>
														
							<div class="divTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										{$lang.ccNumber}: 
									</div>
									<div class="divTableCell">
										<input type="text" size="20" data-stripe="number" class="form-control">
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										{$lang.ccCVC}: 
									</div>
									<div class="divTableCell">
										<input type="text" size="4" data-stripe="cvc" class="form-control" style="width: 50px;">
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										{$lang.ccExpiration}: 
									</div>
									<div class="divTableCell">																				
										<input type="text" size="2" data-stripe="exp-month" class="form-control col-md-2" style="width: 50px;"><input type="text" size="4" data-stripe="exp-year" class="form-control col-md-4" style="width: 100px;">
									</div>
								</div>
								
							</div>
							
						</div>
					
						<div class="cartTotalList">
							<div class="divTable">									
								<div class="divTableRow" style="font-size: 14px;">
									<div class="divTableCell"><span class="price">Total: {$formData.amount}</span></div>
								</div>
							</div>
						</div>
						
						<p><input type="submit" value="{$lang.pay}" style="float: right" id="cartReviewButton" class="btn btn-xs btn-success"></p>
						</form>
							
						</div>
					</div>
				</div>
				
			</div>
			
			
			
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>