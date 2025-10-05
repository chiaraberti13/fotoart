{if $pageID == 'homepage'}
	<div class="subNavFeaturedBox">
		{content id='homeWelcome'}
	</div>					
	<hr>

	{* Featured News Area *}
	{if $featuredNewsRows}
		<div class="subNavFeaturedBox" id="featuredNews">
			{if $config.settings.rss_news}<a href="{linkto page='rss.php?mode=news'}"><img src="{$imgPath}/rss.icon.small.png" id="homepageNewsRSS"></a>{/if}
			<h1>{$lang.news}</h1>
			<ul>
			{foreach $featuredNews as $news}
				<li><span>{$news.display_date}</span><br><a href="{$news.linkto}">{$news.title}</a></li>
			{/foreach}
			</ul>
			{if $config.settings.news}<p class="featuredBoxMore"><a href="{linkto page='news.php'}">{$lang.more} &raquo;</a></p>{/if}
		</div>
		<hr>
	{/if}
	
{/if}

{if $mainLevelGalleries}
	<div class="subNavFeaturedBox">
		<h1>{$lang.galleries}</h1>
		<ul id="subnavGalleriesList">
			{foreach $mainLevelGalleries as $galID => $gallery}
				<li><a href="{$galleriesData.$galID.linkto}">{$galleriesData.$galID.name}</a> {if $config.settings.gallery_count and $galleriesData.$galID.gallery_count}({$galleriesData.$galID.gallery_count}){/if}</li>
			{/foreach}
		</ul>
	</div>
	<hr>
{/if}

{if $contentPages|@count > 0}
	<div class="subNavFeaturedBox">
		<ul id="customPages">
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
	<hr>
{/if}

{foreach $contentBlocks as $content}
	{if $content.specType == 'sncb'}
		<div class="subNavFeaturedBox cbSubnav">
			<h1>{$content.name}</h1>
			<div>{$content.content}</div>
		</div>
		<hr>
	{/if}	
{/foreach}				

{* Featured Prints Area *}													
{if $featuredPrintsRows and $pageID != 'featured'}
	<div class="subNavFeaturedBox">
		<h1>{$lang.featuredPrints}</h1>
		<div class="divTable">
			{foreach $featuredPrints as $print}
				<div class="divTableRow workboxLinkAttach">
					<div class="divTableCell">{if $print.photo}<img src="{productShot itemID=$print.print_id itemType=print photoID=$print.photo.id size=50 crop=40}">{/if}</div>
					<div class="divTableCell">
						<h3><a href="{$print.linkto}" class="workboxLink">{$print.name}</a></h3>
						<p class="featuredDescription">{$print.description|truncate:60}</p>
						<p class="featuredPrice">{if $print.price}<span class="price">{$print.price.display}</span>{if $print.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_print} {$lang.priceCreditSep} {/if}{if $print.credits}<span class="price">{$print.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
					</div>
				</div>
			{/foreach}
		</div>						
		{if $config.settings.printpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=prints'}">{$lang.more} &raquo;</a></p>{/if}
	</div>
	<hr>
{/if}
		
{* Featured Products Area *}						
{if $featuredProductsRows and $pageID != 'featured'}
	<div class="subNavFeaturedBox">
		<h1>{$lang.featuredProducts}</h1>
		<div class="divTable">
			{foreach $featuredProducts as $product}
				<div class="divTableRow workboxLinkAttach">
					<div class="divTableCell">{if $product.photo}<img src="{productShot itemID=$product.prod_id itemType=prod photoID=$product.photo.id size=50 crop=40}">{/if}</div>
					<div class="divTableCell">
						<h3><a href="{$product.linkto}" class="workboxLink">{$product.name}</a></h3>
						<p class="featuredDescription">{$product.description|truncate:60}</p>
						<p class="featuredPrice">{if $product.price}<span class="price">{$product.price.display}</span>{if $product.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_prod} {$lang.priceCreditSep} {/if}{if $product.credits}<span class="price">{$product.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
					</div>
				</div>
			{/foreach}
		</div>						
		{if $config.settings.prodpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=products'}">{$lang.more} &raquo;</a></p>{/if}
	</div>
	<hr>
{/if}
	
{* Featured Packages Area *}								
{if $featuredPackagesRows and $pageID != 'featured'}
	<div class="subNavFeaturedBox">
		<h1>{$lang.featuredPackages}</h1>
		<div class="divTable">
			{foreach $featuredPackages as $package}
				<div class="divTableRow workboxLinkAttach">
					<div class="divTableCell">{if $package.photo}<img src="{productShot itemID=$package.pack_id itemType=pack photoID=$package.photo.id size=50 crop=40}">{/if}</div>
					<div class="divTableCell">
						<h3><a href="{$package.linkto}" class="workboxLink">{$package.name}</a></h3>
						<p class="featuredDescription">{$package.description|truncate:60}</p>
						<p class="featuredPrice">{if $package.price}<span class="price">{$package.price.display}</span>{if $package.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_pack} {$lang.priceCreditSep} {/if}{if $package.credits}<span class="price">{$package.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
					</div>
				</div>
			{/foreach}
		</div>					
		{if $config.settings.packpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=packages'}">{$lang.more} &raquo;</a></p>{/if}
	</div>
	<hr>
{/if}

{* Featured Collections Area *}								
{if $featuredCollectionsRows and $pageID != 'featured'}
	<div class="subNavFeaturedBox">
		<h1>{$lang.featuredCollections}</h1>
		<div class="divTable">
			{foreach $featuredCollections as $collection}
				<div class="divTableRow workboxLinkAttach">
					<div class="divTableCell">{if $collection.photo}<img src="{productShot itemID=$collection.coll_id itemType=coll photoID=$collection.photo.id size=50 crop=40}">{/if}</div>
					<div class="divTableCell">
						<h3><a href="{$collection.linkto}" class="workboxLink">{$collection.name}</a></h3>
						<p class="featuredDescription">{$collection.description|truncate:60}</p>
						<p class="featuredPrice">{if $collection.price}<span class="price">{$collection.price.display}</span>{if $collection.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_coll} {$lang.priceCreditSep} {/if}{if $collection.credits}<span class="price">{$collection.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
					</div>
				</div>
			{/foreach}
		</div>					
		{if $config.settings.collpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=collections'}">{$lang.more} &raquo;</a></p>{/if}
	</div>
	<hr>
{/if}

{* Featured Promotions Area *}								
{if $featuredPromotionsRows and $pageID != 'featured'}
	<div class="subNavFeaturedBox">
		<h1>{$lang.promotions}</h1>
		<div class="divTable">
			{foreach $featuredPromotions as $promotion}
				<div class="divTableRow workboxLinkAttach">
					<div class="divTableCell">{if $promotion.photo}<img src="{productShot itemID=$promotion.promo_id itemType=promo photoID=$promotion.photo.id size=50 crop=40}">{/if}</div>
					<div class="divTableCell">
						<h3><a href="{$promotion.linkto}" class="workboxLink">{$promotion.name}</a></h3>
						<p class="featuredDescription">{$promotion.description|truncate:60}</p>
						<p class="featuredPrice">{if $promotion.price}<span class="price">{$promotion.price.display}</span>{/if}</p>
					</div>
				</div>
			{/foreach}
		</div>					
		{if $config.settings.promopage}<p class="featuredBoxMore"><a href="{linkto page='promotions.php'}">{$lang.more} &raquo;</a></p>{/if}
	</div>
	<hr>
{/if}

{* Featured Subscriptions Area *}								
{if $featuredSubscriptionsRows and $pageID != 'featured'}
	<div class="subNavFeaturedBox">
		<h1>{$lang.featuredSubscriptions}</h1>
		<div class="divTable">
			{foreach $featuredSubscriptions as $subscription}
				<div class="divTableRow workboxLinkAttach">
					<div class="divTableCell">{if $subscription.photo}<img src="{productShot itemID=$subscription.sub_id itemType=sub photoID=$subscription.photo.id size=50 crop=40}">{/if}</div>
					<div class="divTableCell">
						<h3><a href="{$subscription.linkto}" class="workboxLink">{$subscription.name}</a></h3>
						<p class="featuredDescription">{$subscription.description|truncate:60}</p>
						<p class="featuredPrice">{if $subscription.price}<span class="price">{$subscription.price.display}</span>{if $subscription.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $config.settings.cart == 3 and $config.settings.credits_sub} {$lang.priceCreditSep} {/if}{if $subscription.credits}<span class="price">{$subscription.credits} <sup>{$lang.mediaLabelCredits}</sup></span>{/if}</p>
					</div>
				</div>
			{/foreach}
		</div>					
		{if $config.settings.subpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=subscriptions'}">{$lang.more} &raquo;</a></p>{/if}
	</div>
	<hr>
{/if}

{* Featured Credits Area *}								
{if $featuredCreditsRows and $pageID != 'featured'}
	<div class="subNavFeaturedBox">
		<h1>{$lang.featuredCredits}</h1>
		<div class="divTable">
			{foreach $featuredCredits as $credits}
				<div class="divTableRow workboxLinkAttach">
					<div class="divTableCell">{if $credits.photo}<img src="{productShot itemID=$credits.credit_id itemType=credit photoID=$credits.photo.id size=50 crop=40}">{/if}</div>
					<div class="divTableCell">
						<h3><a href="{$credits.linkto}" class="workboxLink">{$credits.name}</a></h3>
						<p class="featuredDescription">{$credits.description|truncate:60}</p>
						<p class="featuredPrice">{if $credits.price}<span class="price">{$credits.price.display}</span>{if $credits.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}</p>
					</div>
				</div>
			{/foreach}
		</div>					
		{if $config.settings.creditpage}<p class="featuredBoxMore"><a href="{linkto page='featured.php?mode=credits'}">{$lang.more} &raquo;</a></p>{/if}
	</div>
	<hr>
{/if}


{*if $pageID != 'homepage'*}
	{* Featured Contributors Area *}
	{if $featuredContributors}
		<div class="subNavFeaturedBox" id="subNavContributors">
			<h1>{$lang.showcasedContributors}</h1>
			<div class="divTable" style="margin-bottom: 20px;">
			{foreach $featuredContributors as $contributor}
				<div class="divTableRow">
					<div class="divTableCell"><img src="{memberAvatar memID=$contributor.mem_id size=30 crop=30}"></div>
					<div class="divTableCell"><a href="{linkto page="contributors.php?id={$contributor.useID}&seoName={$contributor.seoName}"}">{$contributor.display_name}</a></div>
				</div>
			{/foreach}
			</div>
		</div>
		<hr>						
	{/if}
	
	{* Members Online Area *}
	{if $config.settings.members_online}
		<div class="subNavFeaturedBox" id="subNavOnlineMembers">
			<h1>{$lang.membersOnline}</h1>
			<ul>
				{if $membersOnline}
					{foreach $membersOnline as $member}
						<li>{$member.display_name} <span class="time">({$member.lastSeen} {$lang.minutesAgo})</span></li>
					{/foreach}
				{else}
					<li>{$lang.none}</li>
				{/if}
			</ul>
		</div>
		<hr>
	{/if}
	
	{* Site Stats Area *}
	{if $siteStats}
		<div class="subNavStatsBox" id="subNavStats">
			<h1>{$lang.siteStats}</h1>
			<div class="divTable" style="width: 100%">
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
		</div>
	{/if}	
{*/if*}