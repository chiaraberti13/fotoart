{if $message}
	{foreach $message as $messageLang}
		<div class="container messageBar alert alert-danger">{$lang.{$messageLang}} <p><a href="#" class="buttonLink btn btn-xs btn-danger">X</a></p></div>
	{/foreach}
{/if}
<div class="container infoBar" style="border: 1px solid #FFF;">	
	{* Header Search Box Area *}
		
	<div class="row">		
		{if $config.settings.search}
		
		<div class="col-md-5">
			<form role="search" action="{linkto page="search.php"}" method="get" id="searchFormTest" class="navbar-form">			
			<input type="hidden" name="clearSearch" value="true">
				<div class="input-group">					
					<input type="text" class="form-control" placeholder="{$lang.enterKeywords}" name="searchPhrase" id="searchPhrase">
					<div class="input-group-btn">
						<button class="btn btn-info">
							<span class="glyphicon glyphicon-search"></span>
						</button>
					</div>					
				</div>
				<div style="margin-top: 6px;">
					{if $currentGallery.gallery_id}<input type="checkbox" name="galleries" id="searchCurrentGallery" value="{$currentGallery.gallery_id}" checked="checked"><label for="searchCurrentGallery">{$lang.curGalleryOnly}</label>&nbsp;&nbsp;{/if}
					<!--<a href="{linkto page='search.php'}">{$lang.advancedSearch}</a>-->
					{* Event Search Link *}
					{if $config.settings.esearch}
						<a href="{linkto page="esearch.php"}">{$lang.eventSearch}</a>
					{/if}
				</div>
			</form>		
		</div>
		
		{/if}			

		<div class="col-md-7">
			{if $cartStatus}			
				<div class="nav navbar-right">
					<div id="headerCartBox">
						<div id="cartPreviewContainer">
							<div id="miniCartContainer"></div>
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
				</div>
			{/if}
	
{*spostato da 55 a 76 in header la lingua*}
			
			<ul class="nav navbar-nav navbar-right">
				{if $displayLanguages|@count > 1}
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">{$displayLanguages.$selectedLanguage}<b class="caret"></b></a>
					<ul class="dropdown-menu">
						{foreach $displayLanguages as $language}
							<li><a href="{linkto page="actions.php?action=changeLanguage&setLanguage={$language@key}"}">{$language}</a></li>
						{/foreach}
					</ul>
				</li>
				{/if}
				{if $displayCurrencies|@count > 1}
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">{$activeCurrencies.$selectedCurrency.name} ({$activeCurrencies.$selectedCurrency.code})<b class="caret"></b></a>
					<ul class="dropdown-menu">
						{foreach $displayCurrencies as $currency}
							<li><a href="{linkto page="actions.php?action=changeCurrency&setCurrency={$currency@key}"}">{$currency} ({$activeCurrencies.{$currency@key}.code})</a></li>
						{/foreach}
					</ul>
				</li>
				{/if}
			</ul>			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
{*fine spostato da 55 a 76 in header la lingua*}			
		</div>
	</div>
</div>