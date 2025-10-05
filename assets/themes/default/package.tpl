<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.js"></script>
<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.items.js"></script>
<script type="text/javascript">
	setTimeout(function(){ $('#packageNotice').slideDown('200'); },500);
</script>
{if $noAccess}
	<p class="notice">{$lang.noAccess}</p>
{else}
	{if $packagesInCart and !$edit}
		<div id="packageNotice">
			<div style="float: left;"><img src="{$imgPath}/notice.icon.png" style="vertical-align: middle"> {$lang.newPackageMessage}: 
			{*
			<select id="packagesInCartDD" style="display: none;">
				<option>{$lang.newPackage}...</option>
				{foreach $packagesInCart as $cartItemID => $cartPack}
					<option value="package.php?id={$package.usePackageID}&mediaID={$useMediaID}&edit={$cartItemID}">{$package.name} ({$cartPack.itemsToFill} {$lang.leftToFill})</option>
				{/foreach}
			</select>
			*}
			</div>
			
			<div id="packagesInCartDD">
				<p class="newPackageLangBox"> <img src="{$imgPath}/down.arrow.png"></p>
				<ul>
					{foreach $packagesInCart as $cartItemID => $cartPack}
						<li href="{linkto page="package.php?id={$package.usePackageID}&mediaID={$useMediaID}&edit={$cartItemID}"}">
							<div class="packageFilledContainer">
								<div class="packageFilledBar"><p style="width: {$cartPack.package_media_percentage}%"></p></div><!-- {$cartPack.package_media_percentage} -->
							</div>
							{$package.name}
						</li>
					{/foreach}
				</ul>
			</div>
		</div>
	{/if}
	
	{* DO NOT REMOVE OR CHANGE *}
	<form class="cleanForm" id="workboxItemForm" action="{linkto page='cart.php'}" method="post">
	<img src="{$imgPath}/close.button.png" id="closeWorkbox">
	<input type="hidden" value="{$package.nativePrice}" id="rawPrice">
	<input type="hidden" value="{$package.taxInc}" id="taxInc">
	<input type="hidden" value="{$package.credits}" id="startingCredits">
	<input type="hidden" name="assignMediaID" value="{$media.encryptedID}" id="assignMediaID">
	<input type="hidden" name="assignFolderID" value="{$media.encryptedFID}" id="assignFolderID">
	<!-- For cart -->
	<input type="hidden" name="mode" value="add" id="mode"> 
	<input type="hidden" name="type" value="package" id="type">
	<input type="hidden" name="id" value="{$package.usePackageID}">
	<input type="hidden" name="mediaID" value="{$useMediaID}">
	<input type="hidden" name="unencryptedMediaID" value="{$media.media_id}" id="unencryptedMediaID">
	<input type="hidden" name="hasOptions" value="1">
	{* *}
	
	{if $edit}<input type="hidden" name="edit" value="{$edit}">{/if}
	
	<div class="divTable workboxItemTable">
		<div class="divTableRow">
			{if $package.photo or $media}
				<div class="divTableCell workboxLeftColumn">
					<p id="mainShotContainer">{if $media}<img src="{mediaImage mediaID=$media.encryptedID type=rollover folderID=$media.encryptedFID size=300}">{else}<img src="{productShot itemID=$package.pack_id itemType=pack photoID=$package.photo.id size=300}" id="mainShot">{/if}</p>
					{if $package.photos|count > 1 or ($media and $package.photos)}
						{if $media}<h3 class="productShotsHeader">{$lang.productShots}</h3>{/if}
						<p id="additionalShots">
						{foreach $package.photos as $key => $value}
							<a href="{productShot itemID=$package.pack_id itemType=pack photoID=$value.id size=300}"><img src="{productShot itemID=$package.pack_id itemType=pack photoID=$value.id size=70 crop=50}"></a>
						{/foreach}
						</p>
					{/if}
				</div>
			{/if}
			<div class="divTableCell workboxRightColumn">
				<h1>{$package.name}</h1>
				<!--<p style="float: right"><a href="" class="colorLink">Add Note</a></p>-->
				<p>{$package.description}</p>
				
				<div class="optionsScroller">
				
					{if $printRows}
						<h2>{$lang.prints}</h2>
						<ul class="packageItemsList">
						{foreach $prints as $printKey => $print}
							<input type="hidden" style="width: 70px;" name="packageItem[{$printKey}]" value="print-{$print.print_id}">
							<input type="hidden" style="width: 40px;" name="packageItemQuantity[{$printKey}]" value="{$print.quantityDisplay}">
							<input type="hidden" style="width: 40px;" name="packageItemMedia[{$printKey}]" value="{$print.existingMedia.media_id}" id="itemMedia-print{$printKey}">
							<li>
								<div class="divTable">
									<div class="divTableRow">
										<div class="divTableCell" style="text-align: center; position:relative">
											<img src="{$imgPath}/close.button6.png" class="packagePhotoClose" itemID="print{$printKey}" id="packagePhotoClose-print{$printKey}" {if $print.existingMedia}style="display: block"{/if}>
											{if $print.existingMedia}<img src="{mediaImage mediaID=$print.existingMedia.encryptedID type=thumb folderID=$print.existingMedia.encryptedFID size=50}" class="addPackagePhoto" itemID="print{$printKey}" id="itemPhoto-print{$printKey}">{else}<img src="{$imgPath}/blank.add.png" class="addPackagePhoto" itemID="print{$printKey}" id="itemPhoto-print{$printKey}">{/if}
										</div>
										<div class="divTableCell" style="text-align: right"><div class="mtag mtagLightGrey">{$print.quantityDisplay}</div></div>
										<div class="divTableCell">
											{$print.name}<!-- - {$printKey} --> {if $print.options}<a href="#" class="buttonLink optionsToggle" optionsGroup="printOptions{$printKey}" style="padding: 0 4px 0 4px;">+</a>{/if}
										</div>
									</div>
								</div>
								{if $print.options}
									<div class="divTable optionsTable" style="display: none;" id="printOptions{$printKey}">
										{foreach $print.options as $key => $optionGroup}
											{include file="options.container.tpl" prefix="{$printKey}"}
										{/foreach}
									</div>
								{/if}
							</li>
						{/foreach}
						</ul>
					{/if}
					
					{if $productRows}
						<h2>{$lang.products}</h2>
						<ul class="packageItemsList">
						{foreach $products as $productKey => $product}
							<input type="hidden" style="width: 40px;" name="packageItem[{$productKey}]" value="product-{$product.prod_id}">
							<input type="hidden" style="width: 40px;" name="packageItemQuantity[{$productKey}]" value="{$product.quantityDisplay}">
							{if $product.product_type == '1'}<input type="hidden" style="width: 40px;" name="packageItemMedia[{$productKey}]" value="{$product.existingMedia.media_id}" id="itemMedia-product{$productKey}">{/if}
							<li>
								<div class="divTable">
									<div class="divTableRow">
										<div class="divTableCell" style="text-align: center; position:relative">
											<img src="{$imgPath}/close.button6.png" class="packagePhotoClose" itemID="product{$productKey}" id="packagePhotoClose-product{$productKey}" {if $product.existingMedia}style="display: block"{/if}>
											{if $product.product_type == '1'}
												{if $product.existingMedia}
													<img src="{mediaImage mediaID=$product.existingMedia.encryptedID type=thumb folderID=$product.existingMedia.encryptedFID size=50}" class="addPackagePhoto" itemID="product{$productKey}" id="itemPhoto-product{$productKey}">
												{else}
													<img src="{$imgPath}/blank.add.png" class="addPackagePhoto" itemID="product{$productKey}" id="itemPhoto-product{$productKey}">
												{/if}
											{else}
												<!--<img src="{$imgPath}/spacer.png" style="width: 52px; height: 1px">-->
											{/if}
										</div>
										<div class="divTableCell" style="text-align: right"><div class="mtag mtagLightGrey">{$product.quantityDisplay}</div></div>
										<div class="divTableCell">{$product.name}<!-- - {$productKey}--> {if $product.options}<a href="#" class="buttonLink optionsToggle" optionsGroup="productOptions{$productKey}" style="padding: 0 4px 0 4px;">+</a>{/if}</div>
									</div>
								</div>
								{if $product.options}
									<div class="divTable optionsTable" style="width: 100%; display: none; margin-top: 10px;" id="productOptions{$productKey}">
										{foreach $product.options as $key => $optionGroup}
											{include file="options.container.tpl" prefix="{$productKey}"}
										{/foreach}
									</div>
								{/if}
							</li>
						{/foreach}
						</ul>
					{/if}
					
					{if $collectionRows}
						<h2>{$lang.collections}</h2>
						<ul class="packageItemsList">
						{foreach $collections as $collectionKey => $collection}
							<input type="hidden" style="width: 40px;" name="packageItem[{$collectionKey}]" value="collection-{$collection.coll_id}">
							<input type="hidden" style="width: 40px;" name="packageItemQuantity[{$collectionKey}]" value="1">
							<!--<input type="text" style="width: 40px;" name="packageItemMedia[{$collectionKey}]" value="">-->
							<li>
								<div class="divTable">
									<div class="divTableRow">
										<div class="divTableCell"></div>
										<div class="divTableCell" style="vertical-align: top"><div class="mtag mtagLightGrey">1</div></div>
										<div class="divTableCell">{$collection.name}<!-- - {$collectionKey}--> <a href="{$collection.viewCollectionLink}" class="colorLink">({$lang.viewCollection})</a><br><span class="dim">{$collection.description|truncate:160}</span></div>
									</div>
								</div>
							</li>
						{/foreach}
						</ul>
					{/if}
					
					{if $subscriptionRows}
						<h2>{$lang.subscriptions}</h2>
						<ul class="packageItemsList">
						{foreach $subscriptions as $subscriptionKey => $subscription}
							<input type="hidden" style="width: 40px;" name="packageItem[{$subscriptionKey}]" value="subscription-{$subscription.sub_id}">
							<input type="hidden" style="width: 40px;" name="packageItemQuantity[{$subscriptionKey}]" value="1">
							<!--<input type="text" style="width: 40px;" name="packageItemMedia[{$collectionKey}]" value="">-->
							<li>
								<div class="divTable">
									<div class="divTableRow">
										<div class="divTableCell"></div>
										<div class="divTableCell" style="vertical-align: top"><div class="mtag mtagLightGrey">1</div></div>
										<div class="divTableCell">{$subscription.name}<!-- - {$collectionKey}--> <br><span class="dim">{$subscription.description|truncate:160}</span></div>
									</div>
								</div>
							</li>
						{/foreach}
						</ul>
					{/if}
				
				</div>
			</div>
		</div>
	</div>
	</form>
{/if}
<div class="workboxActionButtons">{if $cartStatus}{if $package.price}{$lang.mediaLabelPrice}: <span class="price" id="workboxItemPrice">{$package.price.display}</span>{if $package.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $package.credits}&nbsp;&nbsp;{$lang.mediaLabelCredits}: <span class="price" id="workboxItemCredits">{$package.credits}</span>{/if}{if !$noAccess}<br><input type="button" value="{if $edit}{$lang.update}{else}{$lang.addToCart}{/if}" id="workboxAddToCart" class="colorButton"><!--* id=addToCartButton You can finish this package later.<br>On next page it would say "package added to your cart. You can edit or update this package before you checkout. Add a note to tag this package so you can recognize it later.-->{/if}{/if}</div>