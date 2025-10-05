$(function()
{	
	$('.featuredPageItem img').load(function()
	{
		setEquals('.featuredPageItem'); // After the images all load do a final height resize
	});
	
	setEquals('.featuredPageItem'); // Backup for IE
});