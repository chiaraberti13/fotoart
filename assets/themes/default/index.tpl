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
		
		{* Featured Media & Welcome Message Area *}
		<div id="featuredMedia">
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell hpWelcomeMessage">{content id='homeWelcome'}</div>
					{if $config.settings.hpfeaturedmedia and $featuredMedia}
						<div class="divTableCell" id="featuredOneCell" style="width: {$config.settings.hpf_width}px; height: {$config.settings.hpf_crop_to}px;">
							<p id="featuredNext" class="opac50" style="z-index: 1000" onclick="featuredMediaRotator();">&nbsp;&raquo;&nbsp;</p>
							<div id="featuredOneContainer"></div>
							<ul id="featuredMediaList" class="opac60" style="width: {$config.settings.hpf_width-20}px;">
								{foreach $featuredMedia as $media}
									<li mediaType="{if $media.sampleVideo}video{else}image{/if}" image="image.php?mediaID={$media.encryptedID}=&type=featured&folderID={$media.encryptedFID}&size={$config.settings.hpf_width}&crop={$config.settings.hpf_crop_to}" encMediaID="{$media.encryptedID}" href="{$media.linkto}">{if $media.title.value or $media.description.value}<span class="title"><a href="{$media.linkto}">{$media.title.value}</a></span><br><span class="description">{$media.description.value}</span>{/if}</li>
								{/foreach}
							</ul>
						</div>
					{/if}
				</div>
			</div>
		</div>
		
		<div id="featureBoxes" class="divTable">
			<div class="divTableRow">
				
				{* Site Stats Area *}
				<div class="divTableCell hpFeatureBox" {if (!$featuredContributors and !$config.settings.members_online) and $siteStats}style="width: 100%;"{elseif (!$featuredContributors or !$config.settings.members_online) and $siteStats}style="width: 50%;"{elseif $siteStats}style="width: 33%;"{/if}>
					{if $siteStats}
						<img src="{$imgPath}/feature.box.a.icon.png">
						<h1>{$lang.siteStats}</h1>
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell">{$lang.members}:</div>
								<div class="divTableCell"><strong>{$siteStats.members}</strong></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell">{$lang.media}:</div>
								<div class="divTableCell"><strong>{$siteStats.media}</strong></div>
							</div>
							{*if $siteStats.contributors}
								<div class="divTableRow">
									<div class="divTableCell">{$lang.contributors}:</div>
									<div class="divTableCell"><strong>200</strong></div>
								</div>
							{/if*}
							<div class="divTableRow">
								<div class="divTableCell">{$lang.visits}:</div>
								<div class="divTableCell"><strong>{$siteStats.visits}</strong></div>
							</div>
						</div>
					{/if}&nbsp;
				</div>
				
				{* Members Online Area *}
				<div class="divTableCell hpFeatureBox" {if (!$featuredContributors and !$siteStats) and $config.settings.members_online}style="width: 100%;"{elseif (!$featuredContributors or !$siteStats) and $config.settings.members_online}style="width: 50%;"{elseif $config.settings.members_online}style="width: 33%;"{/if}>
					{if $config.settings.members_online}
						<img src="{$imgPath}/feature.box.b.icon.png">
						<h1>{$lang.membersOnline}</h1>
						<div>{if $membersOnline}{foreach $membersOnline as $member}{$member.f_name} {$member.l_name} <span class="time">({$member.lastSeen} {$lang.minutesAgo})</span>{if !$member@last},{/if} {/foreach}{else}{$lang.none}{/if}</div>
					{/if}&nbsp;
				</div>
				
				{* Featured Contributors Area *}
				<div class="divTableCell hpFeatureBox" style="{if (!$config.settings.members_online and !$siteStats) and $featuredContributors}width: 100%;{elseif (!$config.settings.members_online or !$siteStats) and $featuredContributors}width: 50%;{elseif $featuredContributors}width: 33%;{/if}">
					{if $featuredContributors}
						<div style="min-height: 100px;">
							<div class="divTable">
								<div class="divTableRow">
									<div class="divTableCell" style="vertical-align:top;">
										<div id="featuredIcon"><img src="{$imgPath}/feature.box.c.icon.png" id="contributorAvatar"></div>
									</div>
									<div class="divTableCell" style="vertical-align:top; padding-left: 10px;">		
										<h1>{$lang.contributors}</h1>
										{foreach $featuredContributors as $contributor}
											<!--{if $contributor.avatar}<img src="{memberAvatar memID=$contributor.mem_id size=70 crop=70}">{/if}--><a href="{linkto page="contributors.php?id={$contributor.useID}&seoName={$contributor.seoName}"}" {if $contributor.avatar}memID="{$contributor.mem_id}" class="hpContributorLink"{/if}>{$contributor.display_name}</a>{if !$contributor@last},{/if}
										{/foreach}
										<p class="more"><a href="{linkto page="contributors.php"}">{$lang.more}</a> &raquo;</p>
									</div>
								</div>
							</div>
						</div>
					{/if}&nbsp;
				</div>
				
			</div>
		</div>
		<div class="divTable contentContainer">
			<div class="divTableRow">
				<div class="divTableCell contentLeftColumn">
					{include file='subnav.tpl'}
					
					{* Featured Prints Area *}													
					{if $featuredPrintsRows}
						<div class="subNavFeaturedBox">
							<h1>{$lang.featuredPrints}</h1>
							<div class="divTable">
								{foreach $featuredPrints as $print}
									<div class="divTableRow workboxLinkAttach">
										<div class="divTableCell">{if $print.photo}<img src="{productShot itemID=$print.print_id itemType=print photoID=$print.photo.id size=50 crop=40}">{/if}</div>
										<div class="divTableCell">
											<h2><a href="{$print.linkto}" class="workboxLink">{$print.name}</a></h2>
											<p class="featuredDescription">{$print.description|truncate:60}</p>
											<p class="featuredPrice">{if $print.price}<span class="price">{$print.price.display}</span>{if $print.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_print} {$lang.priceCreditSep} {/if}{if $print.credits}<span class="price">{$print.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
										</div>
									</div>
								{/foreach}
							</div>						
							{if $config.settings.printpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=prints'}">{$lang.more}</a></p>{/if}
						</div>
					{/if}
							
					{* Featured Products Area *}						
					{if $featuredProductsRows}
						<div class="subNavFeaturedBox">
							<h1>{$lang.featuredProducts}</h1>
							<div class="divTable">
								{foreach $featuredProducts as $product}
									<div class="divTableRow workboxLinkAttach">
										<div class="divTableCell">{if $product.photo}<img src="{productShot itemID=$product.prod_id itemType=prod photoID=$product.photo.id size=50 crop=40}">{/if}</div>
										<div class="divTableCell">
											<h2><a href="{$product.linkto}" class="workboxLink">{$product.name}</a></h2>
											<p class="featuredDescription">{$product.description|truncate:60}</p>
											<p class="featuredPrice">{if $product.price}<span class="price">{$product.price.display}</span>{if $product.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_prod} {$lang.priceCreditSep} {/if}{if $product.credits}<span class="price">{$product.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
										</div>
									</div>
								{/foreach}
							</div>						
							{if $config.settings.prodpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=products'}">{$lang.more}</a></p>{/if}
						</div>
					{/if}
						
					{* Featured Packages Area *}								
					{if $featuredPackagesRows}
						<div class="subNavFeaturedBox">
							<h1>{$lang.featuredPackages}</h1>
							<div class="divTable">
								{foreach $featuredPackages as $package}
									<div class="divTableRow workboxLinkAttach">
										<div class="divTableCell">{if $package.photo}<img src="{productShot itemID=$package.pack_id itemType=pack photoID=$package.photo.id size=50 crop=40}">{/if}</div>
										<div class="divTableCell">
											<h2><a href="{$package.linkto}" class="workboxLink">{$package.name}</a></h2>
											<p class="featuredDescription">{$package.description|truncate:60}</p>
											<p class="featuredPrice">{if $package.price}<span class="price">{$package.price.display}</span>{if $package.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_pack} {$lang.priceCreditSep} {/if}{if $package.credits}<span class="price">{$package.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
										</div>
									</div>
								{/foreach}
							</div>					
							{if $config.settings.packpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=packages'}">{$lang.more}</a></p>{/if}
						</div>
					{/if}
					
					{* Featured Collections Area *}								
					{if $featuredCollectionsRows}
						<div class="subNavFeaturedBox">
							<h1>{$lang.featuredCollections}</h1>
							<div class="divTable">
								{foreach $featuredCollections as $collection}
									<div class="divTableRow workboxLinkAttach">
										<div class="divTableCell">{if $collection.photo}<img src="{productShot itemID=$collection.coll_id itemType=coll photoID=$collection.photo.id size=50 crop=40}">{/if}</div>
										<div class="divTableCell">
											<h2><a href="{$collection.linkto}" class="workboxLink">{$collection.name}</a></h2>
											<p class="featuredDescription">{$collection.description|truncate:60}</p>
											<p class="featuredPrice">{if $collection.price}<span class="price">{$collection.price.display}</span>{if $collection.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_coll} {$lang.priceCreditSep} {/if}{if $collection.credits}<span class="price">{$collection.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
										</div>
									</div>
								{/foreach}
							</div>					
							{if $config.settings.collpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=collections'}">{$lang.more}</a></p>{/if}
						</div>
					{/if}
					
					{* Featured Promotions Area *}								
					{if $featuredPromotionsRows}
						<div class="subNavFeaturedBox">
							<h1>{$lang.promotions}</h1>
							<div class="divTable">
								{foreach $featuredPromotions as $promotion}
									<div class="divTableRow workboxLinkAttach">
										<div class="divTableCell">{if $promotion.photo}<img src="{productShot itemID=$promotion.promo_id itemType=promo photoID=$promotion.photo.id size=50 crop=40}">{/if}</div>
										<div class="divTableCell">
											<h2><a href="{$promotion.linkto}" class="workboxLink">{$promotion.name}</a></h2>
											<p class="featuredDescription">{$promotion.description|truncate:60}</p>
											<p class="featuredPrice">{if $promotion.price}<span class="price">{$promotion.price.display}</span>{/if}</p>
										</div>
									</div>
								{/foreach}
							</div>					
							{if $config.settings.promopage}<p class="featuredBoxMore"><a href="{linkto page='promotions.php'}">{$lang.more}</a></p>{/if}
						</div>
					{/if}
					
					{* Featured Subscriptions Area *}								
					{if $featuredSubscriptionsRows}
						<div class="subNavFeaturedBox">
							<h1>{$lang.featuredSubscriptions}</h1>
							<div class="divTable">
								{foreach $featuredSubscriptions as $subscription}
									<div class="divTableRow workboxLinkAttach">
										<div class="divTableCell">{if $subscription.photo}<img src="{productShot itemID=$subscription.sub_id itemType=sub photoID=$subscription.photo.id size=50 crop=40}">{/if}</div>
										<div class="divTableCell">
											<h2><a href="{$subscription.linkto}" class="workboxLink">{$subscription.name}</a></h2>
											<p class="featuredDescription">{$subscription.description|truncate:60}</p>
											<p class="featuredPrice">{if $subscription.price}<span class="price">{$subscription.price.display}</span>{if $subscription.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_sub} {$lang.priceCreditSep} {/if}{if $subscription.credits}<span class="price">{$subscription.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
										</div>
									</div>
								{/foreach}
							</div>					
							{if $config.settings.subpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=subscriptions'}">{$lang.more}</a></p>{/if}
						</div>
					{/if}
					
					{* Featured Credits Area *}								
					{if $featuredCreditsRows}
						<div class="subNavFeaturedBox">
							<h1>{$lang.featuredCredits}</h1>
							<div class="divTable">
								{foreach $featuredCredits as $credits}
									<div class="divTableRow workboxLinkAttach">
										<div class="divTableCell">{if $credits.photo}<img src="{productShot itemID=$credits.credit_id itemType=credit photoID=$credits.photo.id size=50 crop=40}">{/if}</div>
										<div class="divTableCell">
											<h2><a href="{$credits.linkto}" class="workboxLink">{$credits.name}</a></h2>
											<p class="featuredDescription">{$credits.description|truncate:60}</p>
											<p class="featuredPrice">{if $credits.price}<span class="price">{$credits.price.display}</span>{if $credits.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}</p>
										</div>
									</div>
								{/foreach}
							</div>					
							{if $config.settings.creditpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=credits'}">{$lang.more}</a></p>{/if}
						</div>
					{/if}	
													
				</div>
				<div class="divTableCell contentRightColumn">					
					
					{* Featured News Area *}
					{if $featuredNewsRows}
						<div id="featuredNews">
							{if $config.settings.rss_news}<a href="{linkto page='rss.php?mode=news'}"><img src="{$imgPath}/rss.icon.large.png" id="homepageNewsRSS"></a>{/if}
							{if $config.settings.news}<p class="moreNews"><a href="{linkto page='news.php'}">{$lang.moreNews} <img src="{$imgPath}/more.news.arrow.png"></a></p>{/if}
							<div class="divTable">
							{foreach $featuredNews as $news}
								<div class="divTableRow">
									<div class="divTableCell">{$news.display_date}</div>
									<div class="divTableCell">|</div>
									<div class="divTableCell"><a href="{$news.linkto}">{$news.title}</a></div>
								</div>
							{/foreach}
							</div>
						</div>
					{/if}
					
					{* Newest Media Area *}
					{if $newestMediaRows}
						<h1>{if $config.settings.newestpage}<a href="{linkto page='gallery.php?mode=newest-media&page=1'}">{$lang.newestMedia}</a>{else}{$lang.newestMedia}{/if}{if $config.settings.rss_newest}<a href="{linkto page='rss.php?mode=newestMedia'}"><img src="{$imgPath}/rss.icon.small.png" class="rssH1Icon"></a>{/if}</h1>
						<div class="homepageMediaList">
							{foreach $newestMedia as $media}
								{include file='media.container.tpl'}
							{/foreach}
						</div>
					{/if}
					
					{* Popular Media Area *}
					{if $popularMediaRows}
						<h1>{if $config.settings.popularpage}<a href="{linkto page='gallery.php?mode=popular-media&page=1'}">{$lang.popularMedia}</a>{else}{$lang.popularMedia}{/if}{if $config.settings.rss_popular}<a href="{linkto page='rss.php?mode=popularMedia'}"><img src="{$imgPath}/rss.icon.small.png" class="rssH1Icon"></a>{/if}</h1>
						<div class="homepageMediaList">
							{foreach $popularMedia as $media}
								{include file='media.container.tpl'}
							{/foreach}
						</div>
					{/if}
					
					{* Random Media Area *}
					{if $randomMediaRows}
						<h1>{$lang.randomMedia}</h1>
						<div class="homepageMediaList">
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