$(function()
{
	$('#navGalleries').addClass('selectedNav');
	
	if(fadeInThumbnails.status){ $('.galleryIconContainer img').css('opacity',0); }
	$('.galleryIconContainer img').load(function()
	{
		$(this).stop().animate({ opacity: 1.0 },fadeInThumbnails.speed);
		setEquals('.galleryDetailsContainer',{ 'mode' : 'height' });
	});
	setEquals('.galleryDetailsContainer',{ 'mode' : 'height' });
	
	$('.galleryFeaturedPrints img').load(function()
	{
		setEquals('.galleryFeaturedPrints'); // After the images all load do a final height resize
	});				
	setEquals('.galleryFeaturedPrints'); // Backup for IE
	
	$('.galleryFeaturedProducts img').load(function()
	{
		setEquals('.galleryFeaturedProducts'); // After the images all load do a final height resize
	});				
	setEquals('.galleryFeaturedProducts'); // Backup for IE
	
	$('.galleryFeaturedCollections img').load(function()
	{
		setEquals('.galleryFeaturedCollections'); // After the images all load do a final height resize
	});				
	setEquals('.galleryFeaturedCollections'); // Backup for IE
	
	$('#gallerySortBy, #gallerySortType').change(function()
	{
		$('#galleryForm').submit();
	});
	
});

$(window).load(function() // Fix for IE using the back button
{	
	setTimeout(function(){ $('.galleryIconContainer img').stop().animate({ opacity: 1.0 },fadeInThumbnails.speed); },fadeInThumbnails.speed);
	setEquals('.galleryDetailsContainer',{ 'mode' : 'height' });
});