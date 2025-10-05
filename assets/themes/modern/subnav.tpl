<div class="col-md-3 hidden-xs">
	<!--<a href="#"><img class="img-responsive img-circle" src="images/panda.png"></a>-->
	
	{if $featuredNewsRows}
		<div class="subNavFeaturedBox" id="featuredNews">
			<h2>{$lang.news} {if $config.settings.rss_news}<a href="{linkto page='rss.php?mode=news'}" class="btn btn-xxs btn-warning">{$lang.rss}</a>{/if}</h2>
			<ul>
			{foreach $featuredNews as $news}
				<li><span class="newsDate">{$news.display_date}</span><br><a href="{$news.linkto}">{$news.title}</a></li>
			{/foreach}
			</ul>
			{if $config.settings.news}<p class="text-right"><a href="{linkto page='news.php'}" class="btn btn-xs btn-primary">{$lang.more} &raquo;</a></p>{/if}
		</div>
	{/if}
	
	{if $galleriesData}
		<nav style="margin-bottom: 20px;">		
			<h3>{$lang.galleries}</h3>
			<ul class="">
				{foreach $mainLevelGalleries as $galID => $gallery}
					<li><a href="{$galleriesData.$galID.linkto}">{$galleriesData.$galID.name} {if $config.settings.gallery_count and $galleriesData.$galID.gallery_count}({$galleriesData.$galID.gallery_count}){/if}</a></li>
				{/foreach}
			</ul>		
		</nav>
	{/if}
	
	{if $contentPages|@count > 0}
		<hr>
		<div class="">
			<ul>
			{foreach $contentPages as $content}
				<li>
				{if $content.linked}
				<a href="{$content.linked}" target="_blank">{$content.name}</a>
				{else}
				<a href="{linkto page="content.php?id={$content.content_id}"}">{$content.name}</a>
				{/if}
				</li>
			{/foreach}
			</ul>
		</div>
	{/if}
	
	{foreach $contentBlocks as $content}
		{if $content.specType == 'sncb'}		
			<div class="subNavCustomContent">
				<h3>{$content.name}</h3>
				<div>{$content.content}</div>
			</div>
		{/if}
	{/foreach}
	
	{* Featured Prints Area *}												
	{if $featuredPrintsRows and $pageID != 'featured'}
		<div id="featuredPrints" class="subNavFeaturedBox">
			<h2>{$lang.featuredPrints}</h2>
			{foreach $featuredPrints as $print}
				<div class="workboxLinkAttach subNavFeaturedItem">
					{if $print.photo}<img src="{productShot itemID=$print.print_id itemType=print photoID=$print.photo.id size=125}">{/if}
					<h3><a href="{$print.linkto}" class="workboxLink">{$print.name}</a></h3>
					<p class="featuredDescription">{$print.description|truncate:60}</p>
					<p class="featuredPrice">{if $print.price}<span class="price">{$print.price.display}</span>{if $print.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_print} {$lang.priceCreditSep} {/if}{if $print.credits}<span class="price">{$print.credits} {$lang.mediaLabelCredits}</span>{/if}</p>
				</div>
			{/foreach}					
			{if $config.settings.printpage}<p class="text-right"><a href="{linkto page='featured.php?mode=prints'}" class="btn btn-xs btn-primary">{$lang.more} &raquo;</a></p>{/if}
		</div>
	{/if}
			
	{* Featured Products Area *}						
	{if $featuredProductsRows and $pageID != 'featured'}
		<div id="featuredProducts" class="subNavFeaturedBox">
			<h2>{$lang.featuredProducts}</h2>
			{foreach $featuredProducts as $product}
				<div class="workboxLinkAttach subNavFeaturedItem">
					{if $product.photo}<img src="{productShot itemID=$product.prod_id itemType=prod photoID=$product.photo.id size=125}" class="lnFeaturedPS">{/if}
					<h3><a href="{$product.linkto}" class="workboxLink">{$product.name}</a></h3>
					<p>{$product.description|truncate:60}</p>
					<p class="featuredPrice">{if $product.price}<span class="price">{$product.price.display}</span>{if $product.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_prod} {$lang.priceCreditSep} {/if}{if $product.credits}<span class="price">{$product.credits} {$lang.mediaLabelCredits}</span>{/if}</p>
				</div>
			{/foreach}						
			{if $config.settings.prodpage}<p class="text-right"><a href="{linkto page='featured.php?mode=products'}" class="btn btn-xs btn-primary">{$lang.more} &raquo;</a></p>{/if}
		</div>
	{/if}
	
	{* Featured Packages Area *}													
	{if $featuredPackagesRows and $pageID != 'featured'}
		<div id="featuredPackages" class="subNavFeaturedBox">
			<h2>{$lang.featuredPackages}</h2>
			{foreach $featuredPackages as $package}
				<div class="workboxLinkAttach subNavFeaturedItem">
					{if $package.photo}<img src="{productShot itemID=$package.pack_id itemType=pack photoID=$package.photo.id size=125}" class="lnFeaturedPS">{/if}
					<h3><a href="{$package.linkto}" class="workboxLink">{$package.name}</a></h3>
					<p>{$package.description|truncate:60}</p>
					<p class="featuredPrice">{if $package.price}<span class="price">{$package.price.display}</span>{if $package.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_pack} {$lang.priceCreditSep} {/if}{if $package.credits}<span class="price">{$package.credits} {$lang.mediaLabelCredits}</span>{/if}</p>
				</div>
			{/foreach}					
			{if $config.settings.packpage}<p class="text-right"><a href="{linkto page='featured.php?mode=packages'}" class="btn btn-xs btn-primary">{$lang.more} &raquo;</a></p>{/if}
		</div>
	{/if}
	
	{* Featured Collections Area *}													
	{if $featuredCollectionsRows and $pageID != 'featured'}
		<div id="featuredCollections" class="subNavFeaturedBox">
			<h2>{$lang.featuredCollections}</h2>
			{foreach $featuredCollections as $collection}
				<div class="workboxLinkAttach subNavFeaturedItem">					
					{if $collection.photo}<img src="{productShot itemID=$collection.coll_id itemType=coll photoID=$collection.photo.id size=125}" class="lnFeaturedPS">{/if}
					<h3><a href="{$collection.linkto}" class="workboxLink">{$collection.name}</a></h3>
					<p>{$collection.description|truncate:60}</p>
					<p class="featuredPrice">{if $collection.price}<span class="price">{$collection.price.display}</span>{if $collection.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_coll} {$lang.priceCreditSep} {/if}{if $collection.credits}<span class="price">{$collection.credits} {$lang.mediaLabelCredits}</span>{/if}</p>
				</div>
			{/foreach}
			{if $config.settings.printpage}<p class="text-right"><a href="{linkto page='featured.php?mode=prints'}" class="btn btn-xs btn-primary">{$lang.more} &raquo;</a></p>{/if}
		</div>
	{/if}
	
	{* Featured Promotions Area *}								
	{if $featuredPromotionsRows and $pageID != 'featured'}
		<div id="featuredPromos" class="subNavFeaturedBox">
			<h2>{$lang.promotions}</h2>
			{foreach $featuredPromotions as $promotion}
				<div class="workboxLinkAttach subNavFeaturedItem">
					{if $promotion.photo}<img src="{productShot itemID=$promotion.promo_id itemType=promo photoID=$promotion.photo.id size=125}" class="lnFeaturedPS">{/if}
					<h3><a href="{$promotion.linkto}">{$promotion.name}</a></h3>
					<p>{$promotion.description|truncate:60}</p>
					<p class="featuredPrice">{if $promotion.price}<span class="price">{$promotion.price.display}</span>{/if}</p>
				</div>
			{/foreach}				
			{if $config.settings.promopage}<p class="text-right"><a href="{linkto page='promotions.php'}" class="btn btn-xs btn-primary">{$lang.more} &raquo;</a></p>{/if}
		</div>
	{/if}
	
	{* Featured Subscriptions Area *}								
	{if $featuredSubscriptionsRows and $pageID != 'featured'}
		<div id="featuredSubs" class="subNavFeaturedBox">
			<h2>{$lang.featuredSubscriptions}</h2>
			{foreach $featuredSubscriptions as $subscription}
				<div class="workboxLinkAttach subNavFeaturedItem">				
					{if $subscription.photo}<img src="{productShot itemID=$subscription.sub_id itemType=sub photoID=$subscription.photo.id size=125}" class="lnFeaturedPS">{/if}
					<h3><a href="{$subscription.linkto}" class="workboxLink">{$subscription.name}</a></h3>
					<p>{$subscription.description|truncate:60}</p>
					<p class="featuredPrice">{if $subscription.price}<span class="price">{$subscription.price.display}</span>{if $subscription.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_sub} {$lang.priceCreditSep} {/if}{if $subscription.credits}<span class="price">{$subscription.credits} {$lang.mediaLabelCredits}</span>{/if}</p>
				</div>
			{/foreach}			
			{if $config.settings.subpage}<p class="text-right"><a href="{linkto page='featured.php?mode=subscriptions'}" class="btn btn-xs btn-primary">{$lang.more} &raquo;</a></p>{/if}
		</div>
	{/if}
	
	{* Featured Credits Area *}								
	{if $featuredCreditsRows and $pageID != 'featured'}
		<div id="featuredCredits" class="subNavFeaturedBox">
			<h2>{$lang.featuredCredits}</h2>
			{foreach $featuredCredits as $credits}
				<div class="workboxLinkAttach subNavFeaturedItem">
					{if $credits.photo}<img src="{productShot itemID=$credits.credit_id itemType=credit photoID=$credits.photo.id size=125}" class="lnFeaturedPS">{/if}
					<h3><a href="{$credits.linkto}" class="workboxLink">{$credits.name}</a></h3>
					<p>{$credits.description|truncate:60}</p>
					<p class="featuredPrice">{if $credits.price}<span class="price">{$credits.price.display}</span>{if $credits.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}</p>
				</div>
			{/foreach}				
			{if $config.settings.creditpage}<p class="text-right"><a href="{linkto page='featured.php?mode=credits'}" class="btn btn-xs btn-primary">{$lang.more} &raquo;</a></p>{/if}
		</div>
	{/if}
	
	
						
</div>