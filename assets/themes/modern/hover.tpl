{if $media.videoStatus}
	<div id="hoverMediaContainer" class="hoverMediaContainer" style="width: {$config.settings.video_rollover_width}px; height: {$config.settings.video_rollover_height}px; background-image: none;"><p id="vidContainer"></p></div>
	<script type="text/javascript">
		$(function()
		{	
			setTimeout(function()
			{
				//./assets/library/2011-11-30/samples/video_cpx-jd3.mp4
				//'flashplayer': "./assets/jwplayer/player.swf",
				//'screencolor': '{$config.settings.video_bg_color}',
				//video.php?mediaID={$media.encryptedID}
				//$('#vidContainer').text('test');
				
				jwplayer("vidContainer").setup(
				{
					'file': "{$media.videoInfo.url}",
					'autostart': false,
					'type': 'video',
					'repeat': 'always',
					'controlbar.position': 'none',
					'stretching': 'uniform',
					'logo.file': '{$baseURL}/assets/watermarks/{$config.settings.vidrollover_wm}',
					'logo.hide': false,
					'logo.position': '{$config.settings.video_wmpos}',
					'width': '100%',
					'height': '100%',
					'volume': 100,
					'modes': [
						{ 'type': {if $media.videoInfo.vidsample_extension == 'flv' or $config.forceFlashVideoPlayer}'flash', src: '{$baseURL}/assets/jwplayer/player.swf'{else}'html5'{/if} },
						{ 'type': 'download' }
					]
				});
				
				jwplayer("vidContainer").play();
				
				//$('#vidContainer').text('test');
				
				{if $config.settings.video_autoresize}
					jwplayer("vidContainer").onMeta(function()
					{
						vidWindowResize("vidContainer");
					});
				{/if}
				
			},100); // Added a short delay to allow for the hover window to fade in first
			//alert('done');
		});
	</script>
{else}
	<p id="hoverMediaContainer" class="hoverMediaContainer"><img src="{if $media.rolloverCachedLink}{$media.rolloverCachedLink}{else}{mediaImage mediaID=$media.encryptedID type=rollover folderID=$media.encryptedFID}{/if}" class="hoverThumb" style="width: {$media.width}px; height: {$media.height}px;"></p>
{/if}
{if $media.details or $media.showRating}
	<div>
		<ul class="mediaContent">
		{foreach $media.details as $detail}
			{if $detail.value != ''}			
				<li>
				<span class="mediaDetailLabel mediaDetailLabel{$detail@key}">{$detail.lang}</span>: <span class="mediaDetailValue mediaDetailValue{$detail@key}">
					{if $detail@key == 'owner'}
						{$detail.value.displayName}
					{else}				
						{$detail.value|truncate:40}
					{/if}
				</span>
				</li>
				
			{/if}
		{/foreach}
		{if $media.showRating}
			<li>
				<p class="ratingStarsContainer" id="ratingStarsContainer{$media@key}">
					{foreach $media.rating.stars as $stars}<img src="{$imgPath}/star.{$stars}.png" class="ratingStar">{/foreach}
					&nbsp;<span class="mediaDetailValue"><strong>{$media.rating.average}</strong>/{$config.RatingStars} ({$media.rating.votes} {$lang.votes})</span><br>
				</p>
			</li>
		{else}
			<li style="display: none;"></li><!-- Added so that last-child doesn't float right unless it is ratings-->
		{/if}
		</ul>
	</div>
{/if}