/*
* Javascript specific to this theme
*/

var themeJS = true; // Check and make sure the theme JS is loaded

/*
* Run when page loads
*/
$(function()
{
	/*
	* Top nav buttons
	*/
	$('#topNav li').click(function(event)
	{		
		goto($(this).find('a:first').attr('href'));
	});
});

/*
* Automatically open all nodes in the gallery tree when it is loaded
*/
var autoExpandGalleryTree = false;

/*
* Settings for the javascript functions of this script
*/

/*
* Fade thumbnails in when loaded
* Speed: speed at which the thumbnails will fade in - higher is longer
*/
var fadeInThumbnails = 
	{
		'status'	: false,
		'speed'		: 1000
	};

/*
* Dim thumbnails when you roll over them
* Speed: speed at which the thumbs dim - higher is longer
* toOpacity: the opacity to which the thumbs will dim
*/
var dimThumbsOnHover = 
	{
		'status'	: true,
		'speed'		: 700,
		'toOpacity'	: 0.4
	};
	
/*
* Settings for the hoverview window
* delay: delay on rollover before the hover window is loaded (milliseconds)
* fade: fade hover in - otherwise there will be no fade when loaded
* fadeSpeed: Speed at which the hover window fades in (milliseconds)
* xOffset/yOffset: The X and Y offset of the hover window from the cursor
*/	
var hoverWindow =
	{
		'followCursor'	: true,
		'delay'			: 50,
		'fade'			: true,
		'fadeSpeed'		: 200,
		'xOffset' 		: 3,
		'yOffset' 		: -15
	}