<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/cart.js"></script>
	<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
	
	<script type="text/javascript">
		// This identifies your website in the createToken call below
		Stripe.setPublishableKey('{$stripe.value}');
	</script>
	
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		{include file='header2.tpl'}		
		
		<div class="container">
			
			<div class="row">
				<div class="col-md-12">
					<ul class="cartStepsBar {if $stepNumber.b}cartStepsBar25{else}cartStepsBar33{/if}">
						<li class="off cart"><p>{$stepNumber.a}</p><div>{$lang.cart}</div></li>
						{if $stepNumber.b}<li class="off shipping"><p>{$stepNumber.b}</p><div>{$lang.shipping}</div></li>{/if}
						<li class="off"><p>{$stepNumber.c}</p><div>{$lang.reviewOrder}</div></li>
						<li class="on"><p>{$stepNumber.d}</p><div>{$lang.payment}</div></li>
					</ul>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-8">		
						{if $cartTotals.shippingRequired}
							{*
							<div style="clear: both; overflow: auto;">
								<div class="cartReviewAddresses">
									<div>
										<h2>{$lang.shipTo}:</h2>
										<p>
											<strong>{$shippingAddress.name}</strong><br>
											{$shippingAddress.address}<br>
											{if $shippingAddress.address2}{$shippingAddress.address2}<br>{/if}
											{$shippingAddress.city}, {$shippingAddress.state} {$shippingAddress.postalCode}<br>
											{$shippingAddress.country}<br>
											<!--
											{if $shippingAddress.phone}{$shippingAddress.phone}<br>{/if}
											{if $shippingAddress.email}{$shippingAddress.email}<br>{/if}
											-->
										</p>
									</div>
								</div>
								
								<div class="cartReviewAddresses">
									<div style="margin-left: 10px;">
										<h2>{$lang.billTo}:</h2>
										<p>
											<strong>{$billingAddress.name}</strong><br>
											{$billingAddress.address}<br>
											{if $billingAddress.address2}{$billingAddress.address2}<br>{/if}
											{$billingAddress.city}, {$billingAddress.state} {$billingAddress.postalCode}<br>
											{$billingAddress.country}<br>
											<!--
											{if $shippingAddress.phone}{$shippingAddress.phone}<br>{/if}
											{if $shippingAddress.email}{$shippingAddress.email}<br>{/if}
											-->
										</p>
									</div>
								</div>
							</div>
							*}
						{/if}
						
						<div>
							
							<p style="margin-top: 20px; margin-bottom: 20px;">{$lang.ccMessage}</p>
							
							<form action="{$baseURL}/assets/gateways/stripe/ipn.php" method="POST" id="payment-form" class="cleanForm form-group">
							
							{$gatewayForm}
								
							<div class="payment-errors messageBar alert alert-danger" style="display: none;" id="creditCardWarning"></div>
														
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
						
					</div>
						
					<div class="col-md-4">
					
						<div class="cartTotalList">
							{if $priCurrency.currency_id != $selectedCurrency}<div class="cartTotalListWarning"><img src="{$imgPath}/notice.icon.png"/>{$lang.cartTotalListWarning}</div>{/if}
							
							{if $cartTotals.priceSubTotal}
								<div class="divTable">
									<div class="divTableRow">
										<div class="divTableCell">{$lang.subtotal}:</div>
										<div class="divTableCell"><span class="price">{$cartTotals.subTotalLocal.display}</span></div>
									</div>
									{if $cartTotals.shippingRequired}
										<div class="divTableRow">
											<div class="divTableCell">{$lang.shipping}:</div>
											<div class="divTableCell"><span class="{if $cartTotals.clearShipping}strike{/if}">{$cartTotals.shippingTotalLocal.display}</span></div>
										</div>
									{/if}
									{if $cartTotals.taxA}
										<div class="divTableRow">
											<div class="divTableCell">{$lang.taxAName}<!-- ({$tax.tax_a_default}%)-->:</div>
											<div class="divTableCell"><span class="{if $cartTotals.clearTax}strike{/if}">{$cartTotals.taxALocal.display}</span></div>
										</div>
									{/if}
									{if $cartTotals.taxB}
										<div class="divTableRow">
											<div class="divTableCell">{$lang.taxBName}<!-- ({$tax.tax_b_default}%)-->:</div>
											<div class="divTableCell"><span class="{if $cartTotals.clearTax}strike{/if}">{$cartTotals.taxBLocal.display}</span></div>
										</div>
									{/if}
									{if $cartTotals.taxC}
										<div class="divTableRow">
											<div class="divTableCell">{$lang.taxCName}<!-- ({$tax.tax_c_default}%)-->:</div>
											<div class="divTableCell"><span class="{if $cartTotals.clearTax}strike{/if}">{$cartTotals.taxCLocal.display}</span></div>
										</div>
									{/if}
									{if $cartTotals.totalDiscounts}
										<div class="divTableRow">
											<div class="divTableCell">{$lang.discounts}:</div>
											<div class="divTableCell"><span class="cartTotalDiscounts">-{$cartTotals.totalDiscountsLocal.display}</span></div>
										</div>
									{/if}
									<div class="divTableRow">
										<div class="divTableCell"><span class="price">{$lang.total}:</span></div>
										<div class="divTableCell"><span class="price">{$cartTotals.cartGrandTotalLocal.display}</span></div>
									</div>
								</div>
							{/if}
							
							{if $cartTotals.creditsSubTotal}	
								<div class="divTable">
									<div class="divTableRow">
										<div class="divTableCell">{$lang.creditsSubtotal}:</div>
										<div class="divTableCell"><span class="price">{$cartTotals.creditsSubTotal}</span></div>
									</div>
									{if $cartTotals.totalCreditsDiscounts}
										<div class="divTableRow">
											<div class="divTableCell">{$lang.creditsDiscounts}:</div>
											<div class="divTableCell"><span class="cartTotalDiscounts">-{$cartTotals.totalCreditsDiscounts}</span></div>
										</div>
									{/if}
									<div class="divTableRow">
										<div class="divTableCell"><span class="price">{$lang.credits}:</span></div>
										<div class="divTableCell"><span class="price">{$cartTotals.creditsTotal}</span></div>
									</div>
								</div>
							{/if}
						</div>
						
						<p><input type="submit" value="{$lang.pay}" style="float: right" id="cartReviewButton" class="btn btn-xs btn-success"></p>
						</form>
					</div>						
				</div>
			</div>
				
			</div>
			
			{* Debug Area *}
			{if $debugMode}
				{debugOutput value=$shippingAddress title='Shipping Address'}
				{debugOutput value=$billingAddress title='Billing Address'}
				{debugOutput value=$cartInfo title='Cart Info'}
				{debugOutput value=$cartTotals title='Cart Totals'}
			{/if}
			
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>