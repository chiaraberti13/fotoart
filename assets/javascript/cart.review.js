$(function()
{
	// Hide top nav so we can replace it with steps bar
	$('#searchBar').hide();
	$('#topNav').hide();
	
	// Go back to the cart page
	$('.cartStepsBar li.cart, .editButton').click(function(){ goto('cart.php'); });
	
	// Go back to the cart shipping page
	$('.cartStepsBar li.shipping').click(function(){ goto('cart.shipping.php');	});
	
	// Submit cart checkout form
	$('#cartReviewButton').click(function()
	{	
		var error = false;
		error = checkRequired();
		
		if(!error) // No other errors so far so continue checking
		{
			// Set the action to go directly to the stripe checkout page if that gateway is chosen
			if($('[name=paymentType]:checked').val() == 'stripe')
				$('#cartPaymentForm').attr('action','stripe.php');
			
			$('#cartPaymentForm').submit();
		}
	}); 
	
	$('#cartPaymentForm li:first').addClass('paymentGatewaySelected'); // Select the first payment method
	
	// Highlight the background on the selected payment gateway
	$('#cartPaymentForm input[type="radio"]').click(function()
	{
		$('#cartPaymentForm li').removeClass('paymentGatewaySelected');
		$(this).closest('li').addClass('paymentGatewaySelected');
	});
	
	// Highlight the background on the selected payment gateway
	$('#cartPaymentForm li').click(function()
	{
		$(this).children('input[type="radio"]').attr('checked','checked');
		$('#cartPaymentForm li').removeClass('paymentGatewaySelected');
		$(this).addClass('paymentGatewaySelected');
	});
	
	
	// Check if the purchase agreement is checked
	$('#purchaseAgreement').click(function()
	{
		//alert($(this).is(':checked'));		
		if($(this).is(':checked'))
			$('#cartReviewButton').removeAttr('disabled');
		else
			$('#cartReviewButton').attr('disabled','disabled');
	});
	
	if(settingsPurchaseAgreement != 0)
	{
		// Disable the cart checkout button to start
		$('#cartReviewButton').attr('disabled','disabled');
	}
});