{*
Notes:
- 	If a cookie exists for a current member then $member.xxx will be available before logging in
- 	Check for login status with {if $loggedIn}your content here{/if}
- 	Display currency with {displayCurrency value=125.54 exchange=true} exchange is an optional attribute
	and controls exchange rate conversion
-	Show page ID with {$pageID}
*}
{nocache}
<div id="header">
	<div id="logoContainer"><a href="{linkto page="index.php"}"><img src="{$mainLogo}" id="mainLogo"></a></div>
	<div id="headerMemberDetailsArea">
		{* Language Selector Area *}
		{if $displayLanguages|@count > 1}
			<div id="languageSelector">
				&nbsp;&nbsp;|&nbsp;&nbsp;{$lang.language}{if $activeLanguages.$selectedLanguage.flag}<img src="{$baseURL}/assets/languages/{$selectedLanguage}/flag.png">{/if}
				<ul class="dropshadowdark">
					{foreach $displayLanguages as $language}
						<li {if $language@key == $selectedLanguage}id="selectedLanguage"{/if}>{if $activeLanguages.{$language@key}.flag}<img src="{$baseURL}/assets/languages/{$language@key}/flag.png">{/if}<a href="{linkto page="actions.php?action=changeLanguage&setLanguage={$language@key}"}">{$language}</a></li>
					{/foreach}
				</ul>
			</div>
			{* Using a select dropdown instead
				<select id="languageSelector">
					{html_options options=$displayLanguages selected=$selectedLanguage}
				</select>
			*}
		{/if}
		
		{* Login Status & Name *}
		{if $config.settings.display_login}
			{if $loggedIn}
				<strong><a href="{linkto page="members.php"}">{$member.f_name} {$member.l_name}</a></strong>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{linkto page="login.php?cmd=logout"}">{$lang.logout}</a>
			{else}
				<a href="{linkto page="login.php?jumpTo=members"}">{$lang.login}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{linkto page="create.account.php?jumpTo=members"}">{$lang.createAccount}</a>
			{/if}
			
		{/if}
		
		{* Lightbox Link *}
		{if $lightboxSystem}
			&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{linkto page="lightboxes.php"}">{$lang.lightboxes}</a>
		{/if}
	</div>
</div>

{* Top nav area *}
<div id="topNav" style="overflow: auto;">
	<ul style="float: left">
		{if $config.settings.news}<li id="navNews"><a href="{linkto page="news.php"}">{$lang.news}</a></li>{/if}
		{if $featuredTab}
			<li id="featuredNavButton">
				<a href="#">{$lang.featuredItems}</a>
				<ul>
					{if $config.settings.featuredpage}<li id="featuredSubnavMedia"><a href="{linkto page="gallery.php?mode=featured-media&page=1"}">{$lang.mediaNav}</a></li>{/if}
					{if $config.settings.printpage}<li id="featuredSubnavPrints"><a href="{linkto page="featured.php?mode=prints"}">{$lang.prints}</a></li>{/if}
					{if $config.settings.prodpage}<li id="featuredSubnavProducts"><a href="{linkto page="featured.php?mode=products"}">{$lang.products}</a></li>{/if}
					{if $config.settings.packpage}<li id="featuredSubnavPackages"><a href="{linkto page="featured.php?mode=packages"}">{$lang.packages}</a></li>{/if}
					{if $config.settings.collpage}<li id="featuredSubnavCollections"><a href="{linkto page="featured.php?mode=collections"}">{$lang.collections}</a></li>{/if}
					{if $config.settings.subpage and $config.settings.subscriptions}<li id="featuredSubnavSubscriptions"><a href="{linkto page="featured.php?mode=subscriptions"}">{$lang.subscriptions}</a></li>{/if}
					{if $config.settings.creditpage}<li id="featuredSubnavCredits"><a href="{linkto page="featured.php?mode=credits"}">{$lang.credits}</a></li>{/if}
				</ul>
			</li>
		{/if}
		<li id="navGalleries"><a href="{linkto page="gallery.php?mode=gallery"}">{$lang.galleries}</a></li>
		{if $config.settings.newestpage}<li id="navNewestMedia"><a href="{linkto page="gallery.php?mode=newest-media&page=1"}">{$lang.newestMedia}</a></li>{/if}
		{if $config.settings.popularpage}<li id="navPopularMedia"><a href="{linkto page="gallery.php?mode=popular-media&page=1"}">{$lang.popularMedia}</a></li>{/if}
		{if addon('contr') && {$contribLink} == 1}<li id="navContributors"><a href="{linkto page="contributors.php"}">{$lang.contributors}</a></li>{/if}
		{if $config.settings.promopage}<li id="navPromotions"><a href="{linkto page="promotions.php"}">{$lang.promotions}</a></li>{/if}
		{if $config.settings.aboutpage}<li id="navAboutUs"><a href="{linkto page="about.php"}">{$lang.aboutUs}</a></li>{/if}
		{if $config.settings.contact}<li id="navContactUs"><a href="{linkto page="contact.php"}">{$lang.contactUs}</a></li>{/if}
		{if $config.settings.forum_link}<li id="navForum"><a href="{$config.settings.forum_link}">{$lang.forum}</a></li>{/if}
	</ul>
	<div id="social" style="float: right; text-align: right; margin-right: 10px; margin-top: 4px;">{if $config.settings.facebook_link}<a href="{$config.settings.facebook_link}" target="_blank"><img src="{$imgPath}/facebook.icon.png" width="20" title="Facebook" style="padding-top: 6px;"></a>{/if}&nbsp;{if $config.settings.twitter_link}<a href="{$config.settings.twitter_link}" target="_blank"><img src="{$imgPath}/twitter.icon.png" width="20" title="Twitter"></a>{/if}</div>
</div>
<div id="searchBar">
	{* Header Search Box Area *}
	{if $config.settings.search}
		<form action="{linkto page="search.php"}" method="get" id="searchFormTest">
		<input type="hidden" name="clearSearch" value="true">
		<div class="headerSearchBox"><input type="text" id="searchPhrase" name="searchPhrase" class="searchInputBox" value="{$lang.enterKeywords}"></div>
		{if $currentGallery.gallery_id}<div class="headerSearchBox headerSearchBoxCG"><input type="checkbox" name="galleries" id="searchCurrentGallery" value="{$currentGallery.gallery_id}" checked="checked"> <label for="searchCurrentGallery">{$lang.curGalleryOnly}</label></p></div>{/if}
		<div class="eyeGlass"></div>
		<div class="headerSearchBox headerSearchBoxOption"><a href="{linkto page='search.php'}">{$lang.advancedSearch}</a></div>
		</form>
	{/if}
	
	{* Event Search Link *}
	{if $config.settings.esearch}
		<div class="headerSearchBox"><a href="{linkto page="esearch.php"}">{$lang.eventSearch}</a></div>
	{/if}
	
	{* Header Details Cart Area *}
	{if $cartStatus}
		<div id="headerCartBox">
			{if $displayCurrencies|@count > 1}
				<div id="currencySelector">
					<span id="currentCurrency">{$lang.currency}</span>
					<ul class="dropshadowdark">
						{foreach $displayCurrencies as $currency}
							<li {if $currency@key == $selectedCurrency}id="selectedCurrency"{/if}><a href="{linkto page="actions.php?action=changeCurrency&setCurrency={$currency@key}"}">{$currency} ({$activeCurrencies.{$currency@key}.code})</a></li><!--onclick="changeCurrency('{$currency@key}');"-->
						{/foreach}
					</ul>
				</div>
				{* Using a select dropdown instead
					<select id="currencySelector">
						{html_options options=$displayCurrencies selected=$selectedCurrency}
					</select>
				*}
			{/if}
			<div id="cartPreviewContainer">
				<div id="miniCartContainer">LOADING</div>
				<div style="float: left; position: relative;" class="viewCartLink"><p id="cartItemsCount">{$cartTotals.itemsInCart}</p><a href="{linkto page="cart.php"}"><img src="{$imgPath}/cart.icon.png" alt="{$lang.cart}"></a></div>
				<div style="float: left; display:{if $cartTotals.priceSubTotal or $cartTotals.creditsSubTotalPreview}block{else}none{/if};" id="cartPreview">
					<a href="{linkto page="cart.php"}" class="viewCartLink">
					<span id="cartPreviewPrice" style="{if !$currencySystem}display: none;{/if}">{$cartTotals.priceSubTotalPreview.display}</span><!-- with tax {$cartTotals.totalLocal.display}-->
					{if $creditSystem and $currencySystem} + {/if}
					<span id="cartPreviewCredits" style="{if !$creditSystem}display: none;{/if}">{$cartTotals.creditsSubTotalPreview} </span> {if $creditSystem}{$lang.credits}{/if}
					</a>
				</div>
			</div>
		</div>
	{/if}
</div>

{if $message}
	{foreach $message as $messageLang}
		<div class="messageBar"><img src="{$imgPath}/notice.icon.png">{$lang.{$messageLang}} <a href="#" class="buttonLink">X</a></div>
	{/foreach}
{/if}
{/nocache}