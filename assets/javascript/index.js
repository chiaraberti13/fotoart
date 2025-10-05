$(function()
{	
	
	featuredMediaRotator(); // Start the featured media rotator
	
	/*
	* Change the contributors default graphic to the avatar of the contributor when their name is hovered over
	*/
	$(".hpContributorLink").hover(
		function()
		{
			var memID = $(this).attr('memID');
			
			$('#featuredIcon').addClass('loader2');
			$('#contributorAvatar')
				.hide()
				.attr('src',baseURL+'/avatar.php?memID='+memID+'&size=100&crop=71&hcrop=70') // Swap in member avatar
				.load(function(){ $(this).show(); });
		},
		function()
		{
			$('#featuredIcon').css("background-color","transparent"); // .css("background-image","none")
			$('#featuredIcon').removeClass('loader2');
			$('#contributorAvatar').attr('src',imgPath+'/feature.box.c.icon.png'); // Return to normal graphic
		}
	);
	
	$("#featuredOneCell").hover(
		function()
		{
			featuredOneDetailsDisplay();
			clearInterval(featuredMediaTimer);
		},
		function()
		{
			$('#featuredMediaList').slideUp(300);
			clearInterval(featuredMediaTimer);
			featuredMediaTimer = setInterval('featuredMediaRotator()',featuredMedia.interval);
		}
	);
});

/*
* Featured media rotator
*/
function featuredOneDetailsDisplay()
{
	clearTimeout(detailsPopup);
	$('#featuredMediaList li').hide();
	var currentElement = $('#featuredMediaList li:nth-child('+currentFeatured+')');
	if($(currentElement).find('a').html())
	{
		$(currentElement).show();
		$('#featuredMediaList').slideDown(100,'swing');			
		detailsPopup = setTimeout(function(){ $('#featuredMediaList').slideUp(300); },featuredMedia.detailsDisTime);
	}
}


var currentFeatured;
var featuredMediaListItem=1;
var detailsPopup;
var featuredMediaTimer;
function featuredMediaRotator()
{
	var featuredMediaListLength = $('#featuredMediaList li').size();
	
	if(featuredMediaListLength > 0)
	{
		clearInterval(featuredMediaTimer);
		
		$('#featuredMediaList').slideUp(300);
		var currentElement = $('#featuredMediaList li:nth-child('+featuredMediaListItem+')');	
		var mediaType = $(currentElement).attr('mediaType');
		var src = $(currentElement).attr('image');
		currentFeatured = featuredMediaListItem;
		
		//var src = $(currentElement).find('a').attr('href');		
		$("#featuredOneContainer").fadeOut(featuredMedia.fadeSpeed,function()
		{
			if(mediaType == 'video')
			{
				//alert('videoFound');
				var mediaID = $(currentElement).attr('encMediaID');
				
				
				//var videoContent = $("<div id='featuredVideoPlayerContainer'>video</div>");
				$("#featuredOneContainer").html("<div id='featuredVideoPlayerContainer'>video</div>").fadeIn(featuredMedia.fadeSpeed);
				
				$("#featuredOneContainer").unbind('click');
				/*
				$("#featuredOneContainer").click(function()
				{
					//goto($(currentElement).attr('href'));
				});
				*/
				
				//$(currentElement).html("<div id='featuredVideoPlayerContainer'>test</div>");
				featuredVideoPlayer(mediaID,"featuredVideoPlayerContainer");
				
				$("#featuredOneContainer").unbind('mouseover');
				
				
				if(!browser.iOS)// fix for iOS
				{
					$("#featuredOneContainer").mouseover(function()
					{	
						jwplayer("featuredVideoPlayerContainer").setVolume(featuredVideoOverVol);
					})
					.mouseout(function()
					{
						jwplayer("featuredVideoPlayerContainer").setVolume(featuredVideoVolume);
					});
				}
				
				setTimeout(featuredOneDetailsDisplay,featuredMedia.detailsDelay);
			}
			else
			{
				var img = new Image();
				img.src = src;				
				$(img).load(function()
				{
					$("#featuredOneContainer").html(img).fadeIn(featuredMedia.fadeSpeed);
					
					$("#featuredOneContainer").unbind('click');
					$("#featuredOneContainer").click(function()
					{
						goto($(currentElement).attr('href'));
					});
					
					setTimeout(featuredOneDetailsDisplay,featuredMedia.detailsDelay);
				});
			}
		});
		
		if(featuredMediaListItem >= featuredMediaListLength)
			featuredMediaListItem = 1;
		else
			featuredMediaListItem++;
		
		if(featuredMediaListLength > 1)
			featuredMediaTimer = setInterval('featuredMediaRotator()',featuredMedia.interval);
	}
}