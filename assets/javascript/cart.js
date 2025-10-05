$(function()
{
	/*
	* Cart notes button
	*/
	$('.cartAddNotes').click(function(event)
	{
		var cartItemID = $(this).attr('cartItemID');		
		workbox({ page: 'workbox.php?mode=cartAddNotes&cartItemID='+cartItemID, mini: true });
		// db field cart_item_notes
	});
	
	$('#cartNotes').blur(function(event)
	{	
		$.ajax({
			type: 'POST',
			url: 'actions.php',
			data: {"action" : 'updateCartNotes', "cartNotes" : $('#cartNotes').val()},
			success: function(data)
			{					
				//
			}
		});
		
	});
		
	/*
	* Cart checkout button
	*/
	$('#cartCheckoutButton').click(function(event)
	{
		event.preventDefault();
		
		var creditsNeededToCheckout = $('#creditsNeededToCheckout').val()*1; // Force number
		var creditsAvailableAtCheckout = $('#creditsAvailableAtCheckout').val()*1; // Force number
	
		var unfinishedPackage = false;
		
		$('.checkPackageFill').each(
			function(elem)
			{
				if($(this).val() > 0)
				{
					unfinishedPackage = $(this).attr('packageID');
					
					/*
					var blinkItem = $(this).parent().children('.packageFilledContainer');
					
					var blinker = function(){
						$(blinkItem).hide().fadeIn();
					};					
					setInterval(blinker, 1000);
					*/
					
					$(this).parent().children('.packageFilledContainer').hide().fadeIn('slow');
				}	
			}
		);
		
		// Make sure all pacakges in the cart are finished
		if(!unfinishedPackage)
		{
			if(creditsAvailableAtCheckout < creditsNeededToCheckout)
			{
				//var pageX = event.pageX;
				//var pageY = event.pageY;
				//alert(creditsAvailableAtCheckout + ' < ' + creditsNeededToCheckout);
				workbox({ page: 'workbox.php?mode=creditsWarning', mini: true });
			}
			else
			{
				if($('#lowSubtotalWarning').val() == 1)
					workbox({ page: 'workbox.php?mode=subtotalWarning', mini: true });
				else
				{
					if($('#accountWorkbox').val() == 1)
						workbox({ page: 'workbox.php?mode=createOrLogin', mini: true });
					else
						$('#cartForm').submit();
				}
			}
		}
		else
		{
			workbox({ page: 'workbox.php?mode=unfinishedPackage&packageID='+unfinishedPackage, mini: true }); // Open workbox to notify of unfinished package
		}
		
	});
	
	/*
	* Update the payType of a cart item
	*/
	$(".payType li").click(function(event)
	{
		var cartItemID = $(this).attr('cartItemID');
		var payType = $(this).attr('payType');
		//alert(cartItemID + '-' + payType);
		goto('cart.php?cartMode=updatePayType&cid='+cartItemID+'&payType='+payType);
	});
	
	/*
	* Apply coupon code
	*/
	$("#applyCouponButton").click(function(event)
	{	
		var couponCode = $('#couponCode').val();
		//alert(cartItemID + '-' + payType);
		goto('cart.php?cartMode=applyCouponCode&couponCode='+couponCode);
	});
	
	/*
	* Set the promos to the same height
	*/
	$('.featuredPromos img').load(function()
	{
		setEquals('.featuredPromos'); // After the images all load do a final height resize
	});				
	setEquals('.featuredPromos'); // Backup for IE
	
	/*
	* Open the edit window for a cart item
	*/
	registerCartItemEditLinks();
	
	/*
	* Delete cart item
	*/
	$('.cartItemRemoveLink').click(function(event)
	{
		event.preventDefault();
		goto($(this).attr('href'));
	});
	
	/*
	* Remove coupon/discount
	*/
	$('.removeCouponButton').click(function(event)
	{
		event.preventDefault();
		goto($(this).attr('href'));
	});
	
	/*
	* Go to credits page
	*/
	$('.buyCreditsButton').click(function(event)
	{
		event.preventDefault();
		goto($(this).attr('href'));
	});
	
	/*
	* Update quantities in cart
	*/
	$('.updateQuantitiesButton').click(function()
	{
		$('#cartForm').attr('action','cart.php?cartMode=updateQuantities').submit();
	});
	
	/*
	* Open details container
	*/
	$('.cartItemDetailsButton').clicktoggle(function(event)
		{
			event.preventDefault();
			var itemID = $(this).attr('href');
			//var cartID = $(this).attr('cartID');
			
			$('#optionsBox'+itemID).show();
			$(this).html('&ndash;');
			
			$('#optionsBox'+itemID).addClass('optionsBoxLoader');

			// Ajax load
			$.ajax({
				type: 'GET',
				url: 'item.options.php',
				data: {"itemID" : itemID, "itemType" : $(this).attr('itemType'), "downloadOrderID" : $(this).attr('downloadOrderID')},
				success: function(data)
				{					
					$('#optionsBox'+itemID).removeClass('optionsBoxLoader').html(data);
				}
			});
		},
		function()
		{
			event.preventDefault();
			var itemID = $(this).attr('href');
			$('#optionsBox'+itemID).hide().html('');
			$(this).html('+');
		}
	);
	
	/*
	$('.cartItemsList h2 span').click(function(event)
	{
		var cartItemID = $(this).attr('cartItemID');
		var parentH2 = $(this).parent();
		
		parentH2.html('<input type="text" class="editInput" id="editInput'+cartItemID+'" maxlength="100" />');
		
		var editInput = parentH2.find('.editInput');
		editInput.focus();
		
		editInput.blur(function(event)
		{
			//alert('test');
			parentH2.html('<span cartItemID='+cartItemID+'>'+$(this).val()+'</span>');
		});
	});
	*/
	
	/*
	* Stripe
	*/
	$(function()
	{
		$('#payment-form').submit(function(event) {
			var $form = $(this);
			
			// Disable the submit button to prevent repeated clicks
			$form.find('button').prop('disabled', true);
			
			Stripe.card.createToken($form, stripeResponseHandler);
			
			// Prevent the form from submitting with the default action
			return false;
		});
	});
	
	function stripeResponseHandler(status, response) {
		var $form = $('#payment-form');
		
		$('#creditCardWarning').hide();
		
		if (response.error) {
			
			$('#creditCardWarning').show();
			
			// Show the errors on the form
			$form.find('.payment-errors').text(response.error.message);
			$form.find('button').prop('disabled', false);
		} else {
			// response contains id and card, which contains additional card details
			var token = response.id;
			// Insert the token into the form so it gets submitted to the server
			$form.append($('<input type="hidden" name="stripeToken" />').val(token));
			// and submit
			$form.get(0).submit();
		}
	};
	
});