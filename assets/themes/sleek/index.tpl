<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript">
		var featuredVideoOverVol = '{$config.featuredVideoOverVol}';
		var featuredVideoVolume = '{$config.featuredVideoVolume}';
		
		var featuredMedia = 
		{
			'fadeSpeed'		: {$config.settings.hpf_fade_speed},
			'interval'		: {$config.settings.hpf_inverval},
			'detailsDelay'	: {$config.settings.hpf_details_delay},
			'detailsDisTime': {$config.settings.hpf_details_distime}
		};
		
		function featuredVideoPlayer(mediaID,container)
		{
			//alert(container);
			
			//$("#featuredVideoPlayerContainer").css({ 'width':'250px','height':'250px' });
			
			jwplayer(container).setup(
			{
				'flashplayer': "{$baseURL}/assets/jwplayer/player.swf",
				'file': "{$baseURL}/video.php?mediaID="+mediaID,
				'autostart': '{$config.autoPlayFeaturedVid}',
				'type': 'video',
				'repeat': '{$config.settings.video_autorepeat}',
				'controlbar.position': '{$config.settings.video_controls}',
				'logo.file': '{$baseURL}/assets/watermarks/{$config.settings.vidpreview_wm}',
				'logo.hide': false,
				'logo.position': '{$config.settings.video_wmpos}',
				'stretching': 'uniform',
				'width': '100%',
				'height': '100%',
				'skin': '{$baseURL}/assets/jwplayer/skins/{$config.settings.video_skin}/{$config.settings.video_skin}.zip',
				'screencolor': '{$config.settings.video_bg_color}',
				'volume': '{$config.featuredVideoVolume}',
				'modes': [
					{ 'type': 'flash', src: '{$baseURL}/assets/jwplayer/player.swf' },
					{ 'type': 'html5' },
					{ 'type': 'download' }
				]
			});
		}
	</script>
	<script type="text/javascript" src="{$baseURL}/assets/javascript/index.js"></script>
	<script type="text/javascript" src="{$baseURL}/assets/javascript/gallery.js"></script>
	<style>
		#featuredVideoPlayerContainer{
			min-width: {$config.settings.hpf_width}px;
			min-height: {$config.settings.hpf_crop_to}px;
		}
	</style>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		{if $config.settings.hpfeaturedmedia and $featuredMedia}
		<div id="featuredRowContainer">
			<div class="center">
			
				{* Featured Media *}				
				<div id="featuredOneCell" style="width: {$config.settings.hpf_width}px; height: {$config.settings.hpf_crop_to}px;">
					<p id="featuredNext" class="opac50" style="z-index: 1000" onclick="featuredMediaRotator();">&nbsp;&raquo;&nbsp;</p>
					<div id="featuredOneContainer"></div>
					<ul id="featuredMediaList" class="opac60" style="width: {$config.settings.hpf_width-20}px;">
						{foreach $featuredMedia as $media}
							<li mediaType="{if $media.sampleVideo}video{else}image{/if}" image="image.php?mediaID={$media.encryptedID}=&type=featured&folderID={$media.encryptedFID}&size={$config.settings.hpf_width}&crop={$config.settings.hpf_crop_to}" encMediaID="{$media.encryptedID}" href="{$media.linkto}">{if $media.title.value or $media.description.value}<span class="title"><a href="{$media.linkto}">{$media.title.value}</a></span><br><span class="description">{$media.description.value}</span>{/if}</li>
						{/foreach}
					</ul>
				</div>
								
			</div>
		</div>
		{/if}
		<div id="contentContainer" class="center">
			{include file='search.row.tpl'}
			<div id="contentLeftContainer">
				<div>
					{include file='subnav.tpl'}
				</div>
			</div>
			<div id="contentRightContainer">
				<div>
					
					{* Newest Media Area *}
					{if $newestMediaRows}
						<div class="homepageMediaList">
							<h1>{if $config.settings.newestpage}<a href="{linkto page='gallery.php?mode=newest-media&page=1'}">{$lang.newestMedia}</a>{else}{$lang.newestMedia}{/if}{if $config.settings.rss_newest}<a href="{linkto page='rss.php?mode=newestMedia'}"><img src="{$imgPath}/rss.icon.small.png" class="rssH1Icon"></a>{/if}</h1>
							{foreach $newestMedia as $media}
								{include file='media.container.tpl'}
							{/foreach}
						</div>
					{/if}
					
					{* Popular Media Area *}
					{if $popularMediaRows}
						<div class="homepageMediaList">
							<h1>{if $config.settings.popularpage}<a href="{linkto page='gallery.php?mode=popular-media&page=1'}">{$lang.popularMedia}</a>{else}{$lang.popularMedia}{/if}{if $config.settings.rss_popular}<a href="{linkto page='rss.php?mode=popularMedia'}"><img src="{$imgPath}/rss.icon.small.png" class="rssH1Icon"></a>{/if}</h1>
							{foreach $popularMedia as $media}
								{include file='media.container.tpl'}
							{/foreach}
						</div>
					{/if}
					
					{* Random Media Area *}
					{if $randomMediaRows}
						<div class="homepageMediaList">
							<h1>{$lang.randomMedia}</h1>
							{foreach $randomMedia as $media}
								{include file='media.container.tpl'}
							{/foreach}
						</div>
					{/if}
					
					{if $subGalleriesData}
						<h1>{$lang.featuredGalleries}</h1>						
						<div id="galleryListContainer">
							{foreach $subGalleriesData as $subGallery}
								<div class="galleryContainer" style="width: {$config.settings.gallery_thumb_size}px">
									<div class="galleryDetailsContainer" style="width: {$config.settings.gallery_thumb_size}px; {if $galleriesData.$subGallery.galleryIcon}vertical-align: top{/if}">
										{if $galleriesData.$subGallery.galleryIcon}<p class="galleryIconContainer" style="min-width: {$galleriesData.$subGallery.galleryIcon.width}px; min-height: {$galleriesData.$subGallery.galleryIcon.height + 5}px;"><a href="{$galleriesData.$subGallery.linkto}"><img src="{$baseURL}/{$galleriesData.$subGallery.galleryIcon.imgSrc}"></a></p>{/if}{*old {productShot itemID=$subGallery itemType=gallery photoID=$galleriesData.$subGallery.galleryIcon.ip_id size=$config.settings.gallery_thumb_size} *}
										<p class="galleryDetails">{if $galleriesData.$subGallery.password}<img src="{$imgPath}/lock.png" class="lock">{/if}<a href="{$galleriesData.$subGallery.linkto}">{$galleriesData.$subGallery.name}</a>{if $config.settings.gallery_count}{if $galleriesData.$subGallery.gallery_count > 0 or $config.ShowZeroCounts}&nbsp;<span class="galleryMediaCount">({$galleriesData.$subGallery.gallery_count})</span>{/if}{/if}</p>
									</div>
									<!--gi: {$galleriesData.$subGallery.galleryIcon.imgSrc}-->
								</div>
							{/foreach}
						</div>
					{/if}
				</div>		
			</div>
		</div>
		{include file='footer.tpl'}
	</div>
</body>
</html>