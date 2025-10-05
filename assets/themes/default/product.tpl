<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.js"></script>
<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.items.js"></script>
<form class="cleanForm" action="{linkto page='cart.php'}" id="workboxItemForm" method="post">
<img src="{$imgPath}/close.button.png" id="closeWorkbox">
<input type="hidden" value="{$product.nativePrice}" id="rawPrice">
<input type="hidden" value="{$product.taxInc}" id="taxInc">
<input type="hidden" value="{$product.credits}" id="startingCredits">
<!-- For cart -->
<input type="hidden" name="mode" value="add" id="mode"> 
<input type="hidden" name="type" value="product" id="type">
<input type="hidden" name="id" value="{$product.useProductID}">
<input type="hidden" name="mediaID" value="{$useMediaID}">
{if $edit}<input type="hidden" name="edit" value="{$edit}">{/if}

{if $noAccess}
	<p class="notice">{$lang.noAccess}</p>
{else}
	<div class="divTable workboxItemTable">
		<div class="divTableRow">
			{if $product.photo or $media or $discountRows}
				<div class="divTableCell workboxLeftColumn">
					<p id="mainShotContainer">{if $media}<img src="{mediaImage mediaID=$media.encryptedID type=rollover folderID=$media.encryptedFID size=300}">{else}<img src="{productShot itemID=$product.prod_id itemType=prod photoID=$product.photo.id size=300}" id="mainShot">{/if}</p>
					{if $product.photos|count > 1 or ($media and $product.photos)}
						{if $media}<h3 class="productShotsHeader">{$lang.productShots}</h3>{/if}
						<p id="additionalShots">
							{foreach $product.photos as $key => $value}
								<a href="{productShot itemID=$product.prod_id itemType=prod photoID=$value.id size=300}"><img src="{productShot itemID=$product.prod_id itemType=prod photoID=$value.id size=70 crop=50}"></a>
							{/foreach}
						</p>
					{/if}
					{if $discountRows}
						<ul class="workboxItemDiscounts">
							<li>{$product.name|truncate:60} Discounts</li>
							{foreach $discountsArray as $discount}
								<li><img src="{$imgPath}/coupon.icon.png">Buy <span>{$discount.start_discount_number}</span> or more and save <span>{$discount.discount_percent}%</span></li>
							{/foreach}
						</ul>
					{/if}
				</div>
			{/if}
			<div class="divTableCell workboxRightColumn">
				<h1>{$product.name}</h1>
				<p>{$product.description}</p>
				
				<div class="optionsScroller">
				
					{if $product.options}
						<input type="hidden" name="hasOptions" value="1">
						<h2>{$lang.options}</h2>
						<div class="divTable optionsTable" style="width: 100%;">		
							{foreach $product.options as $key => $optionGroup}
								{include file="options.container.tpl"}
							{/foreach}
						</div>
						<hr>
					{/if}
					
				</div>
				
			</div>
		</div>
	</div>
<div class="workboxActionButtons">
	{if $product.price}{$lang.mediaLabelPrice}: <span class="price" id="workboxItemPrice">{$product.price.display}</span>{if $product.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}
	{if $product.credits}&nbsp;&nbsp;{$lang.mediaLabelCredits}: <span class="price" id="workboxItemCredits">{$product.credits}</span>{/if}
	{if $cartStatus}{if $cartButton}<br><input type="button" value="{if $edit}{$lang.update}{else}{$lang.addToCart}{/if}" id="workboxAddToCart" class="colorButton">{else}<br><p class="noItemCartWarning"><img src="{$imgPath}/notice.icon.png">{$lang.noItemCartWarning} <input type="button" value="{$lang.addToCart}" disabled="disabled"></p>{/if}{/if}
</div>
{/if}
</form>