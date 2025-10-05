$(function()
{ 
	// Hide top nav so we can replace it with steps bar
	$('#searchBar').hide();
	$('#topNav').hide();
	
	loadShippingMethods(); // Run load shipping methods function
	
	$('.cartStepsBar li:first div').click(function()
	{
		goto('cart.php');
	});
	
	$('#shippingCountry').change(function()
	{
		loadShippingMethods();
		getStateList($('#shippingCountry').val(),'shippingState');
	});
	
	$('#billingCountry').change(function()
	{
		getStateList($('#billingCountry').val(),'billingState');
	});
	
	$('#shippingState').change(function()
	{
		loadShippingMethods();
	});
	
	$('#shippingPostalCode').change(function()
	{
		loadShippingMethods();
	});
	
	$('.duplicateInfo').click(function()
	{
		
	});
	
	$('#duplicateInfo1').click(function()
	{
		$('#billingInfoForm').hide();
	});
	
	$('#duplicateInfo0').click(function()
	{
		$('#billingInfoForm').show();
	});
	
	$('#cartContinueButton').click(function()
	{
		$('.formErrorMessage').remove(); // Remove the error messages from previous submits
		$('*').removeClass('formError'); // Remove the formError class from any errors previously
		
		var error = false;
		error = checkRequired();
		
		//alert($("#shippingAddressesForm").serialize());
		
		if(!error) // No other errors so far so continue checking
		{
			$('#shippingAddressesForm').submit();
		}
	});
	
});

function loadShippingMethods()
{
	//showLoader('#shippingMethods','loader1.gif');
	
	$('#shippingMethods').html('');
	$('#shippingMethods').addClass('loader1');
	
	$.ajax({
		type: 'POST',
		url: 'shipping.methods.php',
		data: $("#shippingAddressesForm").serialize(),
		success: function(data)
		{	
			$('#shippingMethods').removeClass('loader1');
			
			$('#shippingMethods').html(data);
			
			$('#shippingMethods li:first').addClass('paymentGatewaySelected'); // Select the first payment method
	
			$('#shippingMethods input[type="radio"]').click(function()
			{
				$('#shippingMethods li').removeClass('paymentGatewaySelected');
				$(this).closest('li').addClass('paymentGatewaySelected');
			});
			
			$('#shippingMethods li').click(function()
			{
				$(this).children('input[type="radio"]').attr('checked','checked');
				$('#shippingMethods li').removeClass('paymentGatewaySelected');
				$(this).addClass('paymentGatewaySelected');
			});
		}
	});
}