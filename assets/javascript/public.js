//if(!themeJS) alert('No theme.js loaded'); // Check to make sure that the theme JS has been loaded

// place this before all of your code, outside of document ready.
$.fn.clicktoggle = function(a,b){
    return this.each(function(){
        var clicked = false;
        $(this).bind("click",function(){
            if (clicked) {
                clicked = false;
                return b.apply(this,arguments);
            }
            clicked = true;
            return a.apply(this,arguments);
        });
    });// fixed typo here, was missing )
};

var currentMousePos = { x: -1, y: -1 }; // Set initial mouse position

/*
* Run when page is first loaded
*/
$(function()
{	
	/*
	* viewCartLink
	*/
	$('.viewCartLink').click(function(event){
		if(miniCart == '1')
		{
			event.preventDefault();	
			openMiniCart();	
		}
	});
	
	$('#cartPreviewContainer').mouseleave(function(event){
		if(miniCart == '1')
		{		
			closeMiniCart();	
		}
	});
	
	
	/*
	* Mini Cart link
	*/
	$('.miniCartLink').click(function(event){
		event.preventDefault();	
		
		$('#miniCartContainer').html('');
		
		var addToCartLink = $(this).attr('href') + '&miniCart=1&onlyLastAdded=1';
		
		// Submit link to cart using ajax				
		$.ajax({
			type: 'GET',
			url: addToCartLink,
			error: function(jqXHR,errorType,errorThrown)
			{
				alert(errorType+' - '+jqXHR.responseText);
			},
			dataType: 'json',
			success: function(data,textStatus)
			{
				addToMiniCart(data);
			}
		});
	});
	
	/*
	* Shortcut keys to clear a session and debug mode
	*/   
	$(document).keydown(function(e){
		if(e.ctrlKey || e.metaKey)
		{
			//var pathname = window.location.pathname;					
			// Debug Mode On
			if (String.fromCharCode(e.charCode||e.keyCode)=="D")
			{
				goto(baseURL+'/index.php?debugMode=on');
				return false;
			}
			// Debug Mode Off
			if (String.fromCharCode(e.charCode||e.keyCode)=="O")
			{
				goto(baseURL+'/index.php?debugMode=off');
				return false;
			}
			// Destroy Session
			if (String.fromCharCode(e.charCode||e.keyCode)=="R")
			{
				goto(baseURL+'/actions.php?action=sessionDestroy');
				return false;
			}
		}
	});
	
	/*
	* Display and load the gallery list dropdown
	*/
	$('#galleryListToggle').clicktoggle(function()
		{
			$('#galleryList').show();			
			loadGalleriesTree(); // Moved to it's own function so it can be called differently in different themes
		},
		function()
		{
			$('#galleryList').hide();
		}
	);
	
	/*
	* Eye glass search button
	*/
	$('.eyeGlass').click(function()
	{
		$(this).closest('form').submit();
	});
	
	/*
	* Back to previous page link
	*/
	$('.backLink').click(function()
	{
		goto($(this).attr('href'));
	});
	
	/*
	* Close message bar
	*/
	$('.messageBar').click(function()
	{
		//alert('test');
		$(this).hide();
	});
	
	/*
	* Assign to a package button
	*/
	$('.assignToPackageButton').click(function(event)
	{		
		var mediaID = $(this).attr('mediaID');
		workbox({'page' : baseURL+'/workbox.php?mode=assignPackage&mediaID='+mediaID});
	});
	
	/*
	* Close Download Media Mini Window
	*/
	$('.downloadMediaButton').parent('div').mouseleave(function(event)
	{		
		$('.thumbDownloadContainer').hide();		
	});
	
	/*
	* Download Media Mini Window
	*/
	$('.downloadMediaButton').click(function(event)
	{	
		var mediaID = $(this).attr('mediaID');	
		$('.thumbDownloadContainer').hide();
		
		// download mini window
		$.ajax({
			type: 'GET',
			url: baseURL+'/download.mini.window.php',
			data: {"mediaID" : mediaID,"incMode" : 'digital'},
			success: function(data)
			{
				$('#thumbDownloadContainer'+mediaID).css('background-image','none');
				$('#thumbDownloadContainer'+mediaID).show();
				$('#thumbDownloadContainer'+mediaID).html(data);	
			}
		});
		
	});
	
	/*
	* Email to friend button
	*/
	$('.emailToFriend').click(function(event)
	{
		var mediaID = $(this).attr('mediaID');
		workbox({'page' : baseURL+'/workbox.php?mode=emailToFriend&mediaID='+mediaID, mini : true});
	});
	
	/*
	$('#treeMenu').jstree({ 
		"json_data" : {
			"data" : [
				{ 
					"attr" : { "id" : "main1" }, 
					"data" : { 
						"title" : "Main 1", 
						"attr" : { "href" : "#" } 
					},
					"children" : [ "Child 1", "A Child 2" ]
				},
				{ 
					"attr" : { "id" : "main2" }, 
					"data" : { 
						"title" : "Main 2", 
						"attr" : { "href" : "#" } 
					},
					"children" : [ "Child 1", "A Child 2" ]
				}
			]
		},
		"plugins" : [ "themes", "json_data", "ui" ]
	});
	*/
	
	//setEquals('.mediaContainer'); // Make all the mediaContainers the same height
	
	$(document).mouseover(function(){ $('#hoverWindow').hide(); }); // Make sure the hover window gets hidden
	
	/*
	* Fade the thumbs in and set the correct heights on the containers
	*/
	if(fadeInThumbnails.status){ $('.mediaThumb').css('opacity',0); }
	$('.mediaThumb').load(function()
	{
		if(fadeInThumbnails.status)
		{ 
			$(this).stop().animate({ opacity: 1.0 },fadeInThumbnails.speed,function()
			{	
				$(this).parents('p').removeClass('loader1Center'); // Remove any background loader image	
				//$(this).parents('p').css('background-image','none');
			}); // Fade in
		}
		else
		{
			$(this).show();
			$(this).parents('p').removeClass('loader1Center'); // Remove any background loader image
		} 
		//$(elem).parents('p').css('background-image','none')
		
		setEquals('.mediaContainer'); // Make all the mediaContainers the same height
		setEquals('.mediaThumbContainer'); // Make all the mediaThumb containers the same height
	});
	
	/*
	* Lightbox delete buttons
	*/
	$('.lightboxDelete').click(function(event)
	{
		event.preventDefault();
		
		var pageX = event.pageX;
		var pageY = event.pageY;
		
		var lightboxID = $(this).attr('href');
		//noticeBox({ width: '200px', height: '100px' });
		workbox({ page: baseURL+'/workbox.php?mode=deleteLightbox&lightboxID='+lightboxID, mini: true });
	});
	
	/*
	* Lightbox edit buttons
	*/
	$('.lightboxEdit').click(function(event)
	{
		event.preventDefault();
		
		var pageX = event.pageX;
		var pageY = event.pageY;
		
		var lightboxID = $(this).attr('href');
		//noticeBox({ width: '200px', height: '100px' });
		workbox({ page: baseURL+'/workbox.php?mode=editLightbox&lightboxID='+lightboxID, mini: true, width: '500' });
	});
	
	/*
	* Lightbox new button
	*/
	$('#newLightbox').click(function(event)
	{
		event.preventDefault();
		workbox({ page: baseURL+'/workbox.php?mode=newLightbox', mini: true, width: '500' });
	});
	
	/*
	* Add to lightbox
	*/
	$('.addToLightboxButton').click(function(event)
	{
		event.preventDefault();
			
		var mediaID = $(this).attr('mediaID');
		
		if($('#addToLightboxButton'+mediaID).attr('inLightbox') == 1)
		{	
			var lightboxItemID = $(this).attr('lightboxItemID');
			
			workbox({ page: baseURL+'/workbox.php?mode=editLightboxItem&lightboxItemID='+lightboxItemID+'&mediaID='+mediaID, mini: true, width: '500' });	
		}
		else
		{
			workbox({ page: baseURL+'/workbox.php?mode=addToLightbox&mediaID='+mediaID, mini: true, width: '500' });
		}
	});
	
	
	/*
	* If the hover window should follow the cursor set that up below
	*/
	registerFollowCursor();
	
	/*
	* Country selector
	*/
	$('#country').change(function()
	{
		var countryID = $(this).val();
		getStateList(countryID);
	});
	
	/*
	* Membership Workbox
	*/
	$('.membershipWorkbox').click(function(event)
	{
		event.preventDefault();
		var workboxPage = $(this).attr('href');
		workbox({ page : workboxPage });
		//scroll(0,0);
	});
		
	/*
	* Start hover window
	*/
	registerHoverFunction();
	
	/*
	* Setup star ratings
	*/
	
	var starCount = ($(".starRating:first").find('.ratingStar').size());
	
	$(".starRating").each(function(key,elem)
	{
		$(elem).hover('',function(){ returnStarStatus(elem); });
		
		$(elem).find('.ratingStar').each(function(key2,elem2)
		{
			var starValue = key2 + 1;			
			if(starCount == 5)
				starValue = starValue * 2; // Double the value if there are only 5 stars instead of 10
			
			var mediaID = $(elem).attr('mediaID'); // Get the media ID these stars are for
			
			$(elem2)
				.css('cursor','pointer') // Assign a pointer cursor to those stars that are active				
				.mouseover(function(){ highlightStars(elem,key2); }) // Assign a mouse over on the active stars				
				.click( function()
				{	
					$(elem) // Unbind stars to prevent double clicking
						.find('.ratingStar')
						.unbind()
						.css('cursor','default');
						
					$(elem).unbind().fadeOut(200,function()
					{
						$.get(baseURL+'/actions.php',{ action: 'rateMedia', starValue: starValue, mediaID: mediaID },function(data)
						{
							$(elem).html(data).fadeIn(200);
						});
					});
						
				}); // Do the rating when the star is clicked on
		});
	});
	
	/*
	* Gallery paging & buttons
	*/
	$('.pagingPageNumber').change(function()
	{
		goto($(this).val());
	});
	
	$('.previous, .next').click(function(event) // Previous and next buttons
	{
		event.preventDefault(); // Prevent the default if this is a link
		goto($(this).attr('href')); // Use the href attribute to switch pages
	});
	
	$('.searchInputBox').click(function(){ $(this).val(''); } ); // Clear search box when it is clicked on
	
	/*
	* Setup language selector if it exists
	*/
	$('#languageSelector li').each(function()
	{
		var aElement = $(this).find('a'); // Find all links
		var newLanguage = $(aElement).attr('href').split('setLanguage='); // Find the language in the URL string		
		$(aElement).click(function(event){ event.preventDefault(); }); // Prevent links from doing anything		
		$(this).click(function(){ changeLanguage(newLanguage[1]); }); // Change to the selected language
	});
	$('select#languageSelector').change(function() // Select dropdown instead
	{
		changeLanguage($(this).val());
	});
	
	/*
	* Setup language selector if it exists
	*/
	$('#currencySelector li').each(function()
	{
		var aElement = $(this).find('a'); // Find all links
		var newCurrency = $(aElement).attr('href').split('setCurrency='); // Find the selected currency	
		$(aElement).click(function(event){ event.preventDefault(); }); // Prevent links from doing anything	
		$(this).click(function(){ changeCurrency(newCurrency[1]); }); // Change to the selected currency
	});
	$('select#currencySelector').change(function() // Select dropdown instead
	{
		changeCurrency($(this).val());
	});
	
	registerWorkboxLinks();
	goToButton(); // Initialize goToButton function
});


/*
* Register cart edit links
*/
function registerCartItemEditLinks()
{
	$('.cartItemEditLink').unbind('click');
	$('.cartItemEditLink').click(function(event)
	{
		event.preventDefault();
		workbox({ 'page' : $(this).attr('href') });
	});
}

/*
* Register view cart button
*/
function registerViewCartButton()
{
	$('.viewCartButton').unbind('click');
	$('.viewCartButton').click(function(event)
	{
		goto(baseURL+'/cart.php');
	});
}

/*
* Minicart window
*/
function addToMiniCart(data)
{
	scroll(0,0);

	$('#cartItemsCount').html(data.cartDetails.items);
	$('#cartPreviewPrice').html(data.cartDetails.price);
	$('#cartPreviewCredits').html(data.cartDetails.credits);
	
	//$('#cartPreview').html(data.cartDetails.cartPreviewString);	
	
	$('#cartPreview').show();											
	$('#cartPreviewContainer').addClass('cartPreviewContainerOn');
	$('#miniCartContainer').slideDown(300);
	
	// Load mini cart
	$.ajax({
		type: 'GET',
		url: baseURL+'/cart.php',
		data: {'miniCart' : '1','onlyLastAdded' : '1'},
		success: function(data)
		{					
			$('#miniCartContainer').html(data);
			registerCartItemEditLinks();
			registerViewCartButton();	
		}
	});
	
	setTimeout(function(){ closeMiniCart(); },4000);
}

function openMiniCart()
{
	// Load Minicart				
	$.ajax({
		type: 'POST',
		url: baseURL+'/cart.php',
		data: 'miniCart=1',
		error: function(jqXHR,errorType,errorThrown)
		{
			alert(errorType+' - '+jqXHR.responseText);
		},
		success: function(data,textStatus)
		{
			//alert('worked');
			$('#miniCartContainer').html(data);
			$('#cartPreviewContainer').addClass('cartPreviewContainerOn');
			$('#miniCartContainer').slideDown(300);
			registerCartItemEditLinks();
			registerViewCartButton();
		}
	});
}

function closeMiniCart()
{
	$('#miniCartContainer').slideUp(250,function(){ $('#cartPreviewContainer').removeClass('cartPreviewContainerOn'); });
}

/*
* Turn each .workboxLink into a workbox object
*/
function registerWorkboxLinks()
{
	$('.workboxLink').unbind('click');
	
	$('.workboxLink').each(function()
	{
		$(this).click(function(event){ event.preventDefault(); });
		var workboxPage = $(this).attr('href');
		$(this).closest('.workboxLinkAttach').click(function()
		{
			workbox({ page : workboxPage });
		});
	});
}

/*
* Register a data group
*/ 
function registerDG(dataGroup)
{
	$('#'+dataGroup.id+' .tabs li:first').addClass('selectedTab'); // Set the first tab as selected
	$('#'+dataGroup.id+' .dataGroupContainer:first').show(); // Set the first purchase container visible
	
	$('#'+dataGroup.id+' .tabs li').click(function(event)
	{	
		$('#'+dataGroup.id+' .tabs li').removeClass('selectedTab');
		$(this).addClass('selectedTab');				
		$('#'+dataGroup.id+' .dataGroupContainer').hide();
		$('#'+$(this).attr('container')).show();
	});
}

/*
* Loader
*/
function showLoader(elem,img)
{
	$(elem).html('<img src="'+imgPath+'/'+img+'" class="loaderGraphic" />');
}
function hideLoader(elem)
{
	$(elem).html('');
}

/*
* Resize the video window to the video size
*/
function vidWindowResize(player)
{
	var size = jwplayer(player).getMeta();			
	$('#hoverMediaContainer').width(size.width);
	$('#hoverMediaContainer').height(size.height);
}

/*
* Generic go to button - must have href - put this in a function so I can initialize it
*/
function goToButton()
{
	$('.goToButton').unbind('click'); // Unbind first to prevent duplicates
	$('.goToButton').click(function(event)
	{
		event.preventDefault();
		goto($(this).attr('href'));
	});
}

/*
* Fix radio buttons
*/
function correctRadios()
{
	$('.radioGroup').each(function()
	{
		if(!$(this).children("input:radio:checked").val())
			$(this).children("input:radio:first").attr("checked","checked");
	});
}

/*
* Select all checkboxes
*/
function selectAllCheckboxes(items)
{
	$(items).each(function()
	{
		$(this).attr("checked","checked");
	});
}

/*
* Deselect all checkboxes
*/
function deselectAllCheckboxes(items)
{
	$(items).each(function()
	{
		$(this).removeAttr("checked");
	});
}

/*
* Check required function
*/
function checkRequired()
{
	var error = false;
	$('[require="require"]').each(function()
	{
		if($(this).attr('type') == 'checkbox') // See if the required field is a checkbox
		{
			if(!$(this).is(':checked'))
			{
				// old $(this).attr('checked')
				$(this).closest('.optionsTable').show();
				
				var element = $(this).attr('id');
				displayFormError('#'+element,'');			
				error = true;
				return false;
			}
		}
		else if($(this).attr('type') == 'radio') // See if the required field is a radio type
		{
			var name = $(this).attr('name');
			var radioValue = $("input:radio[name='"+name+"']:checked").val()
			
			//alert('test-'+radioValue);

			if(radioValue == '' || radioValue == 0)
			{
				$(this).closest('.optionsTable').show();
				
				var element = $(this).attr('id');
				displayFormError('#'+element,'');			
				error = true;
				return false;
			}
		}
		else
		{
			//alert('test-'+$(this).val());
			if($(this).val() == '' || $(this).val() == 0)
			{
				$(this).closest('.optionsTable').show();
				
				var element = $(this).attr('id');
				displayFormError('#'+element,'');			
				error = true;
				return false;
			}
		}
	});
	
	if(error == true)
		return true;
	else
		return false;
}

/*
* Galleries tree loader
*/
function loadGalleriesTree()
{
	$("#treeMenu").jstree({ 
		"json_data" : {
			"ajax" : {
				"url" : baseURL + '/tree.data.php',
				"data" : function (n) { 
					return { id : n.attr ? n.attr("id") : 0 }; 
				},
				"success" : function(){},
				"error": function(jqXHR,errorType,errorThrown)
				{
					//alert(errorType+' - '+jqXHR.responseText);
					$('#treeMenu').html(jqXHR.responseText);
				}
			}
		},
		"core" : {
			"animation" : 500,
			"initially_open" : [],
			"html_titles" : true
		},
		"plugins" : [ "themes", "json_data" ],
		"themes" : {
			"url" : baseURL + "/assets/themes/" + theme + "/tree.css",
			"dots" : false,
			"icons" : false
		}
	}).bind("loaded.jstree",function(e, data){  if(autoExpandGalleryTree){ $(this).jstree("open_all"); } }); //.set_theme("custom",baseURL + "/assets/themes/" + theme + "/style.css")
}

/*
* Highlight the rating stars as they are hovered over
*/
function highlightStars(elem,current)
{
	$(elem).find('.ratingStar').each(function(key,elem2)
  	{
		if(current >= key)
			$(elem2).attr('src',imgPath+'/star.1.png');
		else
			$(elem2).attr('src',imgPath+'/star.0.png');
  	});
}

/*
* Return the rating stars back to their original status
*/
function returnStarStatus(elem)
{
	$(elem).find('.ratingStar').each(function(key,elem2)
	{
		$(this).attr('src',imgPath+'/star.'+$(this).attr('originalStatus')+'.png');
	});
}

/*
* Position the hover window in the correct place
*/
function positionHoverWindow(elem)
{
	var hoverWindowWidth = $('#hoverWindow').outerWidth();
	var hoverWindowHeight = $('#hoverWindow').outerHeight();	
	var thumbContainerWidth = $(elem).outerWidth();
	var thumbContainerHeight = $(elem).outerHeight();	
	var offset = $(elem).offset();	
	
	if(hoverWindow.followCursor == true)
	{
		var offsetRightCheck = currentMousePos.x + hoverWindowWidth + 80 + hoverWindow.xOffset;
		var offsetBottomCheck = currentMousePos.y + hoverWindowHeight - $(window).scrollTop();
		
		if(offsetRightCheck > $(window).width())
			var offsetLeft = currentMousePos.x - hoverWindowWidth - hoverWindow.xOffset;
		else
			var offsetLeft = currentMousePos.x + 50 + hoverWindow.xOffset;
	
		if(offsetBottomCheck > $(window).height())
			var offsetTop = ($(window).height() + $(window).scrollTop()) - hoverWindowHeight;
		else
			var offsetTop = currentMousePos.y + hoverWindow.yOffset; //Math.round(hoverWindowHeight/8)
	}
	else
	{
		var offsetRightCheck = offset.left + hoverWindowWidth + thumbContainerWidth;
		var offsetBottomCheck = offset.top + hoverWindowHeight - $(window).scrollTop();
		
		if(offsetRightCheck > $(window).width())
			var offsetLeft = offset.left - hoverWindowWidth - hoverWindow.xOffset;
		else
			var offsetLeft = offset.left + thumbContainerWidth + hoverWindow.xOffset;
		
		if(offsetBottomCheck > $(window).height())
			var offsetTop = ($(window).height() + $(window).scrollTop()) - hoverWindowHeight;
		else
			var offsetTop = offset.top + hoverWindow.yOffset; //Math.round(hoverWindowHeight/8)
	}

	$('#hoverWindow').css("top",offsetTop).css("left",offsetLeft); // Position the hover window
}

$(window).load(function()
{
	setTimeout(function(){ $('.mediaThumb').stop().animate({ opacity: 1.0 },fadeInThumbnails.speed); },fadeInThumbnails.speed); // Fix for thumbs sometimes not displaying
	//$('.mediaThumb').show();
}); 

/*
* Hover window
*/		
var hoverWindowTimeout;

function createHoverWindow(elem)
{
	clearTimeout(hoverWindowTimeout);
	hoverWindowTimeout = setTimeout(function(){ fadeInHoverWindow(elem); },hoverWindow.delay);
}

var hoverWindowRequest;
function fadeInHoverWindow(elem)
{
	$('#hoverWindowInner').html(''); // Clear anything put in the hover window first
	
	//$('#hoverWindow').show();	
	//var mediaID = $(elem).attr('mediaID');
	
	hoverWindowRequest = $.get(baseURL+'/hover.php',{ mediaID: $(elem).attr('mediaID') },function(data)
	{
		//if(currentHover == $(elem).attr('mediaID'))
		//{
			
			$('#hoverWindowInner').html(data);
			
			setTimeout(function(){ $(elem).parents('p').removeClass('loader1Center'); },300); // Remove the background loader graphic // Put a slight delay to prevent "flicker" // $(elem).parents('p').css('background-image','none');
			positionHoverWindow(elem); // Reposition the hover window now that it has it has content loaded
			if(hoverWindow.fade)
				$('#hoverWindow').fadeIn(hoverWindow.fadeSpeed);
			else
				$('#hoverWindow').show();
			
		//}
	});
	
	hoverWindowRequest.complete(function()
	{
		setTimeout(function()
		{
			/*
			jwplayer("vidContainer").setup(
				{
					'file': "./assets/library/2011-11-30/samples/video_cpx-jd3.mp4",
					'autostart': true,
					'type': 'video',
					'repeat': 'always',
					'controlbar.position': 'none',
					'stretching': 'uniform',
					'width': '100%',
					'height': '100%',
					'screencolor': '#FF0000',
					'volume': 100,
					'modes': [
						{ 'type': 'flash', src: './assets/jwplayer/player.swf' },
						{ 'type': 'html5' },
						{ 'type': 'download' }
					]
				});
			*/
			//alert(mediaID);
		},100);
	});
	
}

/*
* Show overlay
*/
function overlay()
{
	$('#overlay').css("opacity","0.0"); // set the opactity to 0 initially
	$('#overlay').show(0,function()
	{
		$('#overlay').stop().animate({opacity: 0.7},700);
	});
}

/*
* Load workbox
*/
function workbox(workboxObj)
{
	//scroll(0,0);
	if(!workboxObj.skipOverlay)
		overlay();
	
	/*
	if(workboxObj.width) // Change the width of the workbox
	{
		$('#workbox').css({'width': workboxObj.width + 'px' });
		
		var leftMargin = Math.round(workboxObj.width/2)*-1;		
		$('#workbox').css({'margin-left': leftMargin + 'px' });
	}
	
	if(workboxObj.height) // Change the height of the workbox
	{
		$('#workbox').css({'min-height': workboxObj.height + 'px' });
	}
	*/
	
	$('#workbox').html('').addClass('workboxLoader').fadeIn(); // show();
	
	if(workboxObj.mini)
	{
		$('#workbox').removeClass('largeWorkbox'); // Remove large workbox if it exists
		$('#workbox').addClass('miniWorkbox'); // A mini workbox
		$('#workbox').css({ top: "25%" });
		$(window).unbind('scroll');
	}
	else
	{
		$('#workbox').removeClass('miniWorkbox'); // Remove mini workbox if it exists
		$('#workbox').addClass('largeWorkbox'); // Regular size workbox
		
		//var pageX = event.pageX;
		//var pageY = event.pageY;
		
		var winScroll = $(window).scrollTop(); // Find the scroll position
		$('#workbox').css({ top: (winScroll+30) + "px" });
		
		var originalScroll = winScroll;
		$(window).scroll(function(event)
		{
			var winScroll2 = $(window).scrollTop(); // Find the scroll position
			if(winScroll2 < originalScroll)
			{
				originalScroll = winScroll2;
				$('#workbox').css({ top: (winScroll2+30) + "px" });
			}
		});
	}
	
	if(workboxObj.page)
	{	
		$.get(workboxObj.page,'',function(data)
		{
			$('#workbox').removeClass('workboxLoader').html(data);
		});
	}
}

/*
* Load Notice Box
*/
function miniWorkbox(workboxObj)
{
	overlay();
	
	/*
	if(workboxObj.mini)
	{
		workboxObj.width = 400;
		workboxObj.height = 200;
	}
	
	if(workboxObj.width)
	{
		$('#workbox').css({'width': workboxObj.width + 'px' });
		
		var leftMargin = Math.round(workboxObj.width/2)*-1;		
		$('#workbox').css({'margin-left': leftMargin + 'px' });
	}
		
	if(workboxObj.height)
	{
		$('#workbox').css({'min-height': workboxObj.height + 'px' });
		
		$('#workbox').css({'top': '50%' });
		$('#workbox').css({'margin-top': '-' + workboxObj.height + 'px' });
	}
	*/
	
	$('#miniWorkbox').html('').addClass('workboxLoader').show();
	
	if(workboxObj.page)
	{
		$.get(workboxObj.page,'',function(data)
		{
			$('#miniWorkbox').removeClass('workboxLoader').html(data);
		});
	}
}

/*
* Close workbox and hide overlay
*/
function closeWorkbox()
{
	$('#workbox').hide();
	$('#overlay').hide();
}

/*
* Update the currency
*/
function changeCurrency(newSetting)
{		
	location.href = baseURL+'/actions.php?action=changeCurrency&setCurrency='+newSetting;
}

/*
* Update the language
*/
function changeLanguage(newSetting)
{
	location.href = baseURL+'/actions.php?action=changeLanguage&setLanguage='+newSetting;
}

/*
* Go to link or page
* Use: onclick="goto('{linkto page='index.php'}');"
*/
function goto(gotolink)
{
	location.href = gotolink;
}

/*
function setEqualHeights(id,mode)
{
	var tallest = 0;
	$(id).each(function(key,elem)
	{
		var elemHeight = $(elem).height();
		if(elemHeight > tallest) tallest = elemHeight;
		//$(elem).css({'min-height': 400});
	});
	$(id).each(function(key,elem)
	{
		if(mode == 'fixedHeight')
			$(elem).css('height',tallest+'px');
		else
			$(elem).css('min-height',tallest+'px');
	});
}
*/

/*
* Set equal heights or widths of items with the same class name
*/
function setEquals(id,parms)
{
	
	//parms.padding
	//parms.mode = min-height / height / width	
	var maximum = 0;
	if(!parms) var parms = {};
	if(!parms.mode) parms.mode = 'min-height';
	
	$(id).each(function(key,elem)
	{
		switch(parms.mode)
		{
			default:
			case 'min-height':
			case 'height':
				var elemVal = $(elem).height();
			break;
			case 'width':
				var elemVal = $(elem).width();
			break;
		}
		if(elemVal > maximum) maximum = elemVal;
	});
	$(id).each(function(key,elem)
	{
		$(elem).css(parms.mode,maximum+'px');
	});
	
	//alert(maximum);
}

/*
* Update credits display as options are selected
*/
function updateOptionsCredits()
{
	if($('workboxItemCredits') && $('#startingCredits').val())
	{
		var newCredits = parseFloat($('#startingCredits').val());
		
		// Handle selects
		$('#workboxItemForm select').each(function(index,elem)
		{
			newCredits = newCredits + Number($(elem).find('option:selected').attr('credits'));
		});
		
		// Handle checkboxes
		$('#workboxItemForm input[type=checkbox]:checked').each(function(index,elem)
		{
			if($(elem).attr('credits'))
				newCredits = newCredits + parseFloat($(elem).attr('credits'));
		});
		
		// Handle radios
		$('#workboxItemForm input[type=radio]:checked').each(function(index,elem)
		{
			if($(elem).attr('credits'))
				newCredits = newCredits + Number($(elem).attr('credits'));
		});		
		
		$('#workboxItemCredits').html(newCredits);
	}
}

/*
* Update price display as options are selected
*/
function updateOptionsPrice_old()
{
	if($('workboxItemPrice') && $('#rawPrice').val())
	{
		var newPrice = parseFloat($('#rawPrice').val());
		
		// Handle selects
		$('#workboxItemForm select').each(function(index,elem)
		{
			newPrice = newPrice + Number($(elem).find('option:selected').attr('price'));
		});
		
		// Handle checkboxes
		$('#workboxItemForm input[type=checkbox]:checked').each(function(index,elem)
		{
			if($(elem).attr('price'))
				newPrice = newPrice + parseFloat($(elem).attr('price'));
		});
		
		// Handle radios
		$('#workboxItemForm input[type=radio]:checked').each(function(index,elem)
		{
			if($(elem).attr('price'))
				newPrice = newPrice + Number($(elem).attr('price'));
		});		
		
		newPrice = Math.round(newPrice*1000)/1000;
		newPrice = currency_display(newPrice,1);
		$('#workboxItemPrice').html(newPrice);
	}
}

function updateOptionsPrice()
{
	if($('workboxItemPrice') && $('#rawPrice').val())
	{
		var newPrice = parseFloat($('#rawPrice').val());
		
		// Handle selects
		$('#workboxItemForm select').each(function(index,elem)
		{
			newPrice = newPrice + Number($(elem).find('option:selected').attr('price'));
		});
		
		// Handle checkboxes
		$('#workboxItemForm input[type=checkbox]:checked').each(function(index,elem)
		{
			if($(elem).attr('price'))
				newPrice = newPrice + parseFloat($(elem).attr('price'));
		});
		
		// Handle radios
		$('#workboxItemForm input[type=radio]:checked').each(function(index,elem)
		{
			if($(elem).attr('price'))
				newPrice = newPrice + Number($(elem).attr('price'));
		});		
		
		// Do tax
		
		// Currency conversion
		//numset.cur_decimal_places
		if($('#taxInc').val())
			newPrice = doTax(newPrice);
		
		newPrice = newPrice / numset.exchange_rate;
		
		//var decimalPlaces = 10 * numset.cur_decimal_places;		
		//newPrice = Math.round(newPrice*1000)/1000;
		newPrice = currency_display(newPrice,1);
		$('#workboxItemPrice').html(newPrice);
	}
}

function doTax(price)
{
	var tax = (price*(numset.tax_a/100)) + (price*(numset.tax_b/100)) + (price*(numset.tax_c/100));
	price+=Math.round(tax*100)/100; // Round tax to 2 decimal places
	//alert(Math.round(tax*100)/100);
	return price;
}


/*
* Populate state dropdown
*/
function getStateList(countryID,field)
{
	if(!field) field = 'state';
	
	if(countryID)
	{
		$.ajax({
			type: 'GET',
			url: baseURL+'/actions.php',
			data: {"action" : "stateList","countryID" : countryID},
			dataType: 'json',
			success: function(data)
			{					
				var stateOptions;
				stateOptions += '<option value="0"></option>';	
				$.each(data.states,function(key,state)
				{
					stateOptions += '<option value='+key+'>'+state+'</option>';				 
				});					
				$('#'+field).html(stateOptions);					
			}
		});
	}
}

/*
* Display a form error
*/
function displayFormError(field,errorNum)
{
	$(field).addClass('formError');
	
	if(errorNum != '0') var errorMessage = $(field).attr('errorMessage'+errorNum);
	
	//var existingContent = $(field).parent().html(); // Added these 2 lines instead of the one below to put the message after any other content in the container
	//$(field).parent().html(existingContent+'<p class="formErrorMessage">'+errorMessage+'</p>');
	
	//$(field).closest('.divTableRow').addClass('formError'); // Extra highlighting
	$(field).closest('div').addClass('formError'); // Extra highlighting\
	
	if(errorNum != '0') $(field).parent().append('<p class="formErrorMessage">'+errorMessage+'</p>'); // In the parent tag append the erorr message to what is already there
	
	//$('<p class="formErrorMessage">'+errorMessage+'</p>').insertAfter(field);
}


function registerHoverFunction()
{
	$(".showHoverWindow").hover(
		function()
		{
			createHoverWindow($(this));
			if(hoverWindowRequest) hoverWindowRequest.abort(); // Abort any previous requests
			
			$(this).parents('p').addClass('loader1Center');
			
			//$(this).parents('p').css('background-image','url('+imgPath+'/loader1.gif)');
			if(dimThumbsOnHover.status) $(this).stop().animate({opacity: dimThumbsOnHover.toOpacity},dimThumbsOnHover.speed);
		},
		function()
		{
			$('#hoverWindow').hide();
			$('#hoverWindowInner').html('');
			clearTimeout(hoverWindowTimeout);
			if(dimThumbsOnHover.status)
			{
				$(this).parents('p').removeClass('loader1Center');
				$(this).stop().animate({opacity: 1.0},dimThumbsOnHover.speed);
			}
		}
	);
}

function registerFollowCursor()
{
	if(hoverWindow.followCursor == true && browser.mobile == 0)
	{
		$('.mediaThumb').mousemove(function(event)
		{
			currentMousePos = {
				x: event.pageX - 20,
				y: event.pageY + 20
			};		
			positionHoverWindow($(this));
		});
	}	
}

/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Base64 = {
 
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = Base64._utf8_encode(input);
 
		while (i < input.length) {
 
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
 
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
 
			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}
 
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
 
		}
 
		return output;
	},
 
	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
 
		while (i < input.length) {
 
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
 
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
 
			output = output + String.fromCharCode(chr1);
 
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}
 
		}
 
		output = Base64._utf8_decode(output);
 
		return output;
 
	},
 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
 
}