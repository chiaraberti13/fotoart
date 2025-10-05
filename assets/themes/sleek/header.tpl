	<div id="topNavContainer">
		<div class="center">			
			{if $config.settings.facebook_link or $config.settings.twitter_link}<div id="social" style="float: right; margin-top: 8px; margin-left: 20px;">{if $config.settings.facebook_link}<a href="{$config.settings.facebook_link}" target="_blank"><img src="{$imgPath}/facebook.icon.png" width="20" title="Facebook"></a>{/if}&nbsp;{if $config.settings.twitter_link}<a href="{$config.settings.twitter_link}" target="_blank"><img src="{$imgPath}/twitter.icon.png" width="20" title="Twitter"></a>{/if}</div>{/if}
			{* Header Details Cart Area *}
			{if $cartStatus}
				<div id="cartPreviewContainer">
					<div id="miniCartContainer">...</div>
					<div>
						<div style="float: left;">{$lang.cart}:&nbsp;&nbsp; <a href="{linkto page="cart.php"}" class="viewCartLink"><span id="cartItemsCount">{$cartTotals.itemsInCart}</span>&nbsp;{$lang.items} </a> &nbsp; </div>
						<div style="float: left; display:{if $cartTotals.priceSubTotal or $cartTotals.creditsSubTotalPreview}block{else}none{/if};" id="cartPreview">
							<a href="{linkto page="cart.php"}" class="viewCartLink">
							<span id="cartPreviewPrice" style="{if !$currencySystem}display: none;{/if}">{$cartTotals.priceSubTotalPreview.display}</span>
							{if $creditSystem and $currencySystem} + {/if}
							<span id="cartPreviewCredits" style="{if !$creditSystem}display: none;{/if}">{$cartTotals.creditsSubTotalPreview} </span> {if $creditSystem}{$lang.credits}{/if}
							</a>
						</div>
					</div>
				</div>
			{/if}
			<ul id="topNav">
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
			
		</div>
	</div>
	<div id="secondRowContainer">
		<div class="center">
			<a href="{linkto page="index.php"}"><img src="{$mainLogo}" id="mainLogo"></a>
			<ul id="secondRowNav">
				{if $displayLanguages|@count > 1}
				<li>
					<div id="languageSelector" class="clSelector">
						<div class="currentDDSelection">{$displayLanguages.$selectedLanguage} <img src="{$imgPath}/dropdown.arrow.png"></div>
						<ul class="clDropDown">
							{foreach $displayLanguages as $language}
								<li {if $language@key == $selectedLanguage}id="selectedLanguage"{/if}>{*if $activeLanguages.{$language@key}.flag}<img src="{$baseURL}/assets/languages/{$language@key}/flag.png">{/if*}<a href="{linkto page="actions.php?action=changeLanguage&setLanguage={$language@key}"}">{$language}</a></li>
							{/foreach}
						</ul>
						
					</div>
					<div style="float: left">{$lang.language}: </div>			
				</li>
				{/if}
				{if $displayCurrencies|@count > 1}
				<li>
					<div id="currencySelector" class="clSelector">
						<div class="currentDDSelection">{$activeCurrencies.$selectedCurrency.name} ({$activeCurrencies.$selectedCurrency.code}) <img src="{$imgPath}/dropdown.arrow.png"></div>
						<ul class="clDropDown">
							{foreach $displayCurrencies as $currency}
								<li {if $currency@key == $selectedCurrency}id="selectedCurrency"{/if}><a href="{linkto page="actions.php?action=changeCurrency&setCurrency={$currency@key}"}">{$currency} ({$activeCurrencies.{$currency@key}.code})</a></li><!--onclick="changeCurrency('{$currency@key}');"-->
							{/foreach}
						</ul>
					</div>				
					<div style="float: left">{$lang.currency}: </div>
				</li>
				{/if}
				
				{* Login Status & Name *}
				<li>
					<ul id="tnLoginArea">
						{if $config.settings.display_login}
							{if $loggedIn}
								<li><a href="{linkto page="members.php"}">{$member.f_name} {$member.l_name}</a></li>
								<li><a href="{linkto page="login.php?cmd=logout"}">{$lang.logout}</a></li>
							{else}
								<li><a href=""><a href="{linkto page="login.php?jumpTo=members"}">{$lang.login}</a></a></li>
								<li><a href="{linkto page="create.account.php?jumpTo=members"}">{$lang.createAccount}</a></li>
							{/if}
						{/if}
						{* Lightbox Link *}
						{if $lightboxSystem}
							<li><a href="{linkto page="lightboxes.php"}">{$lang.lightboxes}</a></li>
						{/if}						
					</ul>
				</li>
			</ul>
		</div>
	</div>
	{if $message}
		{foreach $message as $messageLang}
			<div class="messageBar"><img src="{$imgPath}/notice.icon.png">{$lang.{$messageLang}} <a href="#" class="buttonLink">X</a></div>
		{/foreach}
	{/if}