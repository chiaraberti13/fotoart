$(function()
{
	
	goToButton(); // Initialize goToButton function in the workbox
	
	$('#workboxAddToCart').click(function(event)
	{
		$('.formErrorMessage').remove(); // Remove the error messages from previous submits
		$('*').removeClass('formError'); // Remove the formError class from any errors previously
		
		var error = false;
		error = checkRequired();

		if(!error)
		{
			if(miniCart == '1')
			{
				$('#miniCartContainer').html('');
				
				// Submit form to cart using ajax				
				$.ajax({
					type: 'POST',
					url: baseURL+'/cart.php?miniCart=1',
					data: $("#workboxItemForm").serialize(),
					error: function(jqXHR,errorType,errorThrown)
					{
						alert(errorType+' - '+jqXHR.responseText);
					},
					dataType: 'json',
					success: function(data,textStatus)
					{
						closeWorkbox();
						addToMiniCart(data);
					}
				});				
			}
			else
				$('#workboxItemForm').submit(); // No errors - submit the form
		}
		else
			return false;
	});
	
	
	$('.addPackagePhoto').click(function(event)
	{
		if($('#assignMediaID').val())
		{
			var newSrc = baseURL+'/image.php?mediaID=' + $('#assignMediaID').val() + '&folderID='+ $('#assignFolderID').val() +'&type=thumb&size=50';
			$(this).attr('src',newSrc).addClass('removePackagePhoto');
			
			var itemID = $(this).attr('itemID');

			$('#packagePhotoClose-'+itemID).show();
			$('#itemMedia-'+itemID).val($('#unencryptedMediaID').val());
		}
		else
		{
			//alert('warn');
		}
	});
	
	$('.packagePhotoClose').click(function(event)
	{
		if($('#assignMediaID').val())
		{
			
		}
		else
		{
			
		}
		
		var itemID = $(this).attr('itemID');
		var newSrc = imgPath + '/blank.add.png';
		$('#itemPhoto-'+itemID).attr('src',newSrc).removeClass('removePackagePhoto').addClass('addPackagePhoto');
		$(this).hide();
		$('#itemMedia-'+itemID).val('');
	});
	
	/*
	* Choose a package from the assign to package list
	*/
	$('.assignToPackageList .divTableRow').click(function(event)
	{	
		//alert($(this).attr('cartEditID'));
		workbox({'page' : baseURL+'/package.php?id='+$(this).attr('usePackageID')+'&mediaID='+$('#useMediaID').val()+'&edit='+$(this).attr('cartEditID'), 'skipOverlay' : true});
	});
	
	/*
	* Used to be used to add packages to the cart pre 4.4	
	$('#addToCartButton').click(function(event)
	{
		$('#workboxItemForm').submit();
	});
	*/
	
	/*
	$('#startNewPackage').click(function(event)
	{
		var packID = $('#packID').val();
		var mediaID = $('#mediaID').val();
		
		$.ajax({
			type: 'GET',
			url: 'package.php',
			data: {"id" : packID,"mediaID" : mediaID, "configMode" : "new"},
			success: function(data)
			{					
				$('#workbox').html(data);	
			}
		});
	});
	
	$('#editExistingPackage').click(function(event)
	{
		var packID = $('#packID').val();
		var mediaID = $('#mediaID').val();
		
		$.ajax({
			type: 'GET',
			url: 'package.php',
			data: {"id" : packID,"mediaID" : mediaID, "configMode" : "edit"},
			success: function(data)
			{					
				$('#workbox').html(data);	
			}
		});
	});
	*/
	
	$('#closeWorkbox,.closeWorkbox').click(function(event)
	{
		event.preventDefault();
		closeWorkbox();
	});
	
	/*
	* Do lightbox delete
	*/
	$('#deleteLightboxYes').click(function(event)
	{
		event.preventDefault();
		var lightboxID = $(this).attr('href');
		goto(baseURL+'/lightboxes.php?delete='+lightboxID);
	});
	
	/*
	* Lightbox selection dropdown
	*/
	$('#lightboxDropdown').change(function()
	{
		if($(this).val() == 0)
			$('.newLightboxRow').show();	
		else
			$('.newLightboxRow').hide();
	});
	
	/*
	* Lightbox add notes toggle
	*/
	$('#addNotesLink').click(function(event)
	{
		event.preventDefault();
		$('#mediaNotesRow').clicktoggle(); //toggle
	});
		
	/*
	* Remove item from lightbox
	*/
	$('#removeItemFromLightbox').click(function(event)
	{
		event.preventDefault();
		var mediaID = $(this).attr('mediaID'); 
		
		$.ajax({
			type: 'POST',
			url: baseURL+'/actions.php',
			data: 
			{
				'action' : 'removeItemFromLightbox',
				'lightboxItemID' : $('#lightboxItemID').val(),
				'umem_id' : $('#umem_id').val()
			},
			dataType: 'json',
			success: function(data)
			{	
				if(pageMode == 'lightbox')
				{
					//$('#mediaContainer'+mediaID).fadeOut(300);
					location.reload();
				}
				else
				{
					$('#addToLightboxButton'+mediaID).fadeOut(300,function()
					{
						$('#addToLightboxButton'+mediaID).attr('src',imgPath+'/lightbox.icon.0.png');
						$('#addToLightboxButton'+mediaID).fadeIn(700);
					});
					
					$('#addToLightboxButton'+mediaID).attr('inLightbox','0'); // Set inLightbox to 0
				}
			}
		});
		closeWorkbox();
	});
	
	/*
	* Delete Avatar
	
	$('#deleteAvatar').click(function(event)
	{
		event.preventDefault();
		
		$.ajax({
				type: 'GET',
				url: 'actions.php',
				data: $("#accountInfoForm").serialize(),
				dataType: 'json',
				success: function(data)
				{	
					switch(data.errorCode)
					{
						case 'deleted':
							//goto('account.php?notice=accountUpdated');
							$('.memberAvatar').attr('src','avatar.php?memID=none');
						break;
						case 'noDelete':
							//
						break;
						case 'noAvatar':
							//
						break;
					}
				}
			});
	});
	*/
	
	/*
	* Allow enter to perform a workbox submit/form check
	*/
	$('#workboxForm').submit(function(event)
	{		
		event.preventDefault();
		submitWorkboxForm();
	});
	
	/*
	* Show the paypal email entry box
	*/
	$('#commissionTypePayPal').click(function(event)
	{		
		$('#commissionPayPalEmail').show();
	});
	
	$('.membershipRadios').click(function(event)
	{		
		//var saveButtonLabel = $(this).attr('saveButtonLabel');
		//$('#saveAccountInfo').val(saveButtonLabel);
	});
	
	/*
	* Check and submit the form
	*/
	$('#saveWorkboxForm').click(function()
	{	
		submitWorkboxForm();
	});
	
	/*
	* Contributors
	*/
	//$('.ablumTypeTable .divTableRow:first').removeClass('opac40');				
	$('.albumType').click(function()
	{
		$('#newAlbumSettings').hide();
		
		$('.ablumTypeTable > .divTableRow').addClass('opac40');
		
		if($(this).val() == 'new')
			$('#newAlbumSettings').show();
		
		$(this).parent().parent().removeClass('opac40');
	});
	
	$('.albumTypeInput').focus(function()
	{
		$(this).parent().parent().find('.albumType').click();
	});
	
	$('.contrItem').click(function(elem)
	{
		var parentRow = $(this).parent().parent();
		
		if($(this).is(':checked'))
		{
			$(parentRow).removeClass('opac40');
			$(parentRow).find('.pricingInfo').show();
		}
		else
		{
			$(parentRow).addClass('opac40');
			$(parentRow).find('.pricingInfo').hide();
		}
	});
	
	$('#deleteContrMediaYes').click(function(event)
	{
		event.preventDefault();
		var mediaID = $(this).attr('href');		
		$.ajax({
			type: 'POST',
			url: baseURL+'/actions.php?action=deleteContrMedia&mediaID='+mediaID,
			data: '',
			dataType: 'json',
			success: function(data)
			{	
				if(data.errorCode == 0)
				{
					//alert(data.mediaID);			
					$('#mediaContainer'+data.mediaID).fadeOut(400,function(){ $(this).remove(); });
					closeWorkbox();
				}
			}
		});			
		//goto(baseURL+'/lightboxes.php?delete='+lightboxID);
	});
	
	$('#thumbButton').click(function(event)
	{
		$('#thumbUploaderContainer').show();
	});
	
	$('#videoButton').click(function(event)
	{
		$('#videoUploaderContainer').show();
	});
		
	$('.closeSampleUploaderBox').click(function(event)
	{
		$('.sampleUploaderBox').hide();
	});
	
	$('.deleteRelease').click(function(event)
	{
		var mediaID = $('#mediaID').val();		
		var rType = $(this).attr('rType');
		event.preventDefault();

		$.ajax({
			type: 'POST',
			url: baseURL+'/actions.php?action=deleteRelease&mediaID='+mediaID+'&rType='+rType,
			data: '',
			dataType: 'json',
			success: function(data)
			{	
				$('#'+rType+'ReleaseFileDiv').hide();
			}
		});
		
	});
	
});

/*
* Load the list of galleries
*/

var selectedGalleries = Array();
function loadContrGalleries()
{	
	$.ajax({
		type: 'POST',
		url: baseURL+'/tree.data.php?mode=contr',
		data: '',
		dataType: 'json',
		success: function(data)
		{			
			$.each(data, function(num,value)
			{				 
				buildGalleryList(value);
			});
		}
	});	
}

/*
* Build a list of galleries for the multi select
*/
function buildGalleryList(value)
{
	//addGalContent += '<li><input type="checkbox" id="'+value.attr.id+'"> <label for="'+value.attr.id+'">'+value.data.title+'</label>';
	//addGalContent += '<li>'+value.data.title;
	
	var spaces = '';
	for(x=0;x<value.data.level;x++)
		spaces = spaces + '&nbsp;&nbsp;&nbsp;';

	if($.inArray(value.attr.id,selectedGalleries) > -1)
		var selectedAttr = 'selected="selected"';

	$('#gallerySelector').append('<option value="'+value.attr.id+'" '+selectedAttr+'>'+spaces+value.data.title+'</option>');
	
	if(value.children.length)
	{		
		//$('#gallerySelector').html($('#gallerySelector').html() + '<optgroup>');		
		$.each(value.children, function(cnum,cvalue)
		{
			buildGalleryList(cvalue);
		});		
		//$('#gallerySelector').html($('#gallerySelector').html() + '</optgroup>');
	}	
}



/*
var addGalContent;

function buildGalleryList(value)
{
	addGalContent += '<li><input type="checkbox" id="'+value.attr.id+'"> <label for="'+value.attr.id+'">'+value.data.title+'</label>';
	//addGalContent += '<li>'+value.data.title;
	
	if(value.children.length)
	{
		//alert(value.children);
		//alert(value);
		
		addGalContent += '<ul>';
		//addContent += '<li>(children)</li>';
		//buildGalleryList(value.children);
		
		$.each(value.children, function(cnum,cvalue)
		{
			buildGalleryList(cvalue);
		});
		
		addGalContent += '</ul>';
	}
	
	addGalContent += '</li>';	
	//$('#galleriesTreeContainer ul').html($('#galleriesTreeContainer ul').html() + addContent);
	
}
*/

function submitWorkboxForm()
{
	var error = false;
	var mode = $('#mode').val();
	
	$('.formErrorMessage').remove(); // Remove the error messages from previous submits
	
	$('*').removeClass('formError'); // Remove the formError class from any errors previously
	
	error = checkRequired();
	
	switch(mode)
	{
		default:
		break;
		case "editContrMedia":

		break;
		case "forgotPassword":
		
		break;
		case "emailToFriend":
			
		break;
		case "password":
			if($('#newPass').val() != $('#vNewPass').val()) // Make sure the new pass and verify pass match
			{
				$('#newPass,#vNewPass').addClass('formError');
				//var errorMessage = $('#newPass').attr('errorMessage2');
				//$('<p class="formErrorMessage">'+errorMessage+'</p>').insertAfter('#newPass');
				displayFormError('#newPass',2);
				displayFormError('#vNewPass',0);
				error = true;
				return false;
			}
			
			if(!error) // No other errors so far so continue checking
			{
				if($('#newPass').val().length < 6) // Make sure the length of the password is at least 6 characters
				{
					//$('#newPass').addClass('formError');
					//var errorMessage = $('#newPass').attr('errorMessage3');
					//$('<p class="formErrorMessage">'+errorMessage+'</p>').insertAfter('#newPass');
					displayFormError('#newPass',3);
					error = true;
					return false;	
				}
			}
		break;
		case "addToLightbox":
			if($('#lightboxDropdown').val() == 0) // New lightbox
			{
				if($('#lightboxName').val() == '') // Make sure a name has been entered for this lightbox
				{
					displayFormError('#lightboxName',1);
					error = true;
					return false;
				}
			}
		break;
		case "contrNewAlbum":
			if(!$('#newAlbumName').val()) // New contr album
			{
				$('#newAlbumName').addClass('formError');
				displayFormError('#newAlbumName',0);
				error = true;
				return false;
			}
		break;
		case "contrEditAlbum":
			if(!$('#albumName').val()) // Edit contr album
			{
				$('#albumName').addClass('formError');
				displayFormError('#albumName',0);
				error = true;
				return false;
			}
		break;
		case "cartAddNotes":
			//
		break;
	}
	
	if(!error)
	{		
		$.ajax({
			type: 'POST',
			url: baseURL+'/actions.php',
			data: $("#workboxForm").serialize(),
			dataType: 'json',
			error: function(jqXHR,errorType,errorThrown)
			{
				alert(errorType+' - '+jqXHR.responseText);
			},
			success: function(data,textStatus)
			{
				//alert('done');
				switch(data.errorCode)
				{
					case '0':
						goto(baseURL+'/account.php?notice=accountUpdated&mode='+mode);
					break;
					case 'emailExists':
						$('#email').addClass('formError');
						$('<p class="formErrorMessage">'+data.errorMessage+'</p>').insertAfter('#email');
					break;
					case 'incorrectPassword':
						$('#currentPass').addClass('formError');
						$('<p class="formErrorMessage">'+data.errorMessage+'</p>').insertAfter('#currentPass');
					break;
					case 'newPasswordDiff':
						$('#newPass').addClass('formError');
						$('<p class="formErrorMessage">'+data.errorMessage+'</p>').insertAfter('#newPass');
					break;
					case 'shortPass':
						$('#newPass').addClass('formError');
						$('<p class="formErrorMessage">'+data.errorMessage+'</p>').insertAfter('#newPass');
					break;
					case 'shortPass':
						$('#newPass').addClass('formError');
						$('<p class="formErrorMessage">'+data.errorMessage+'</p>').insertAfter('#newPass');
					break;
					case 'createBill':
						goto(baseURL+'/bills.php?notice=newBill'); // Go to the bill page
					break;
					case 'billExists':
						goto(baseURL+'/bills.php?notice=billExists'); // Go to the bill page
					break;
					case 'lightboxCreated':
						goto(baseURL+'/lightboxes.php?notice=lightboxCreated'); // Go to the lightbox page
					break;
					case 'lightboxSaved':
						goto(baseURL+'/lightboxes.php?notice=savedChangesMessage'); // Go to the lightbox page
					break;
					case 'addedToLightbox':
						closeWorkbox();
						$('#addToLightboxButton'+data.mediaID).fadeOut(300,function()
						{
							$('#addToLightboxButton'+data.mediaID).attr('src',imgPath+'/lightbox.icon.1.png');
							$('#addToLightboxButton'+data.mediaID).fadeIn(700);
						});
						
						$('#addToLightboxButton'+data.mediaID).attr('inLightbox','1'); // Set inLightbox to 1
						$('#addToLightboxButton'+data.mediaID).attr('lightboxItemID',data.lightboxItemID); // Set lightbox item ID
					break;
					case 'editLightboxItem':
						closeWorkbox();
					break;
					case 'sentEmailToFriend':
						//alert('test');
						$('#emailSentNotice').show();
						$('#toEmail').val('');
						$('#emailMessage').val('');
					break;
					case 'sentPasswordToEmail':
						$('#emailPasswordFailed').hide();
						$('#emailPasswordSent').show();
					break;
					case 'passwordToEmailFailed':
						$('#emailPasswordSent').hide();
						$('#emailPasswordFailed').show();
					break;
					case 'newAlbumCreated':
						closeWorkbox();
						goto('contributor.my.media.php?mode=album&albumID='+data.uAlbumID);						
						//location.reload();
					break;
					case 'editAlbumCompleted':
						closeWorkbox();
						goto('contributor.my.media.php?mode=album&albumID='+data.uAlbumID);						
						//location.reload();
					break;
					case 'contrEditMediaCompleted':
						closeWorkbox();
						//goto('contributor.my.media.php?mode=album&albumID='+data.uAlbumID);						
						location.reload();
					break;
					case 'cartAddNotesSaved':
						closeWorkbox();
					break;
				}	
			}
		});
	}
	return false; 	
}

function update_image_win()
{
	//alert('test');
	//$('.memberAvatar').attr('src','avatar.php?memID=');
}