<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.js"></script>
<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.items.js"></script>
<form class="cleanForm form-group" action="{linkto page='cart.php'}" id="workboxItemForm" method="post">
<img src="{$imgPath}/close.button.png" id="closeWorkbox">
<input type="hidden" value="{$print.nativePrice}" id="rawPrice">
<input type="hidden" value="{$print.taxInc}" id="taxInc">
<input type="hidden" value="{$print.credits}" id="startingCredits">

<!-- For cart -->
<input type="hidden" name="mode" value="add" id="mode"> 
<input type="hidden" name="type" value="print" id="type">
<input type="hidden" name="id" value="{$print.usePrintID}">
<input type="hidden" name="mediaID" value="{$useMediaID}">
{if $edit}<input type="hidden" name="edit" value="{$edit}">{/if}

{if $noAccess}
	<p class="notice">{$lang.noAccess}</p>
{else}
	
	<div class="container">
		<div class="row">
			
			{if $print.photo or $media or $discountRows}
				<div class="col-md-5 workboxLeftColumn">	
				{if $print.photo or $media}
					<p id="mainShotContainer">{if $media}<img src="{mediaImage mediaID=$media.encryptedID type=rollover folderID=$media.encryptedFID size=300}">{else}<img src="{productShot itemID=$print.print_id itemType=print photoID=$print.photo.id size=300}" id="mainShot">{/if}</p>
					{if $print.photos|count > 1 or ($media and $print.photos)}
						{if $media}<h3 class="productShotsHeader">{$lang.productShots}</h3>{/if}
						<p id="additionalShots">
							{*if $media}<a href="{mediaImage mediaID=$media.encryptedID type=rollover folderID=$media.encryptedFID size=300}"><img src="{mediaImage mediaID=$media.encryptedID type=rollover folderID=$media.encryptedFID  size=70 crop=50}"></a>{/if*}
							{foreach $print.photos as $key => $value}
								<a href="{productShot itemID=$print.print_id itemType=print photoID=$value.id size=300}"><img src="{productShot itemID=$print.print_id itemType=print photoID=$value.id size=70 crop=50}"></a>
							{/foreach}							
						</p>
					{/if}
				{/if}
				{if $discountRows}
					<ul class="workboxItemDiscounts">
						<li>{$print.name|truncate:60} Discounts</li>
						{foreach $discountsArray as $discount}
							<li><img src="{$imgPath}/coupon.icon.png">Buy <span>{$discount.start_discount_number}</span> or more and save <span>{$discount.discount_percent}%</span></li>
						{/foreach}
					</ul>
				{/if}
				</div>
			{/if}
				
			<div class="col-md-7 workboxRightColumn">	
				<h1>{$print.name}</h1>
				<p>{$print.description}</p>
				
				<div class="optionsScroller">
				
					{if $print.options}
						<input type="hidden" name="hasOptions" value="1">
						<h2>{$lang.options}</h2>
						<div class="divTable optionsTable" style="width: 100%;">		
							{foreach $print.options as $key => $optionGroup}
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
		{if $print.price}{$lang.mediaLabelPrice}: <span class="price" id="workboxItemPrice">{$print.price.display}</span>{if $print.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}
		{if $print.credits}&nbsp;&nbsp;{$lang.mediaLabelCredits}: <span class="price" id="workboxItemCredits">{$print.credits}</span>{/if}
		{if $cartStatus}{if $media}<br><input type="button" value="{if $edit}{$lang.update}{else}{$lang.addToCart}{/if}" id="workboxAddToCart" class="btn btn-xs btn-primary">{else}<br><p class="noItemCartWarning"><img src="{$imgPath}/notice.icon.png">{$lang.noItemCartWarning}<br><input type="button" value="{$lang.addToCart}" disabled="disabled" class="btn btn-xs btn-primary"></p>{/if}{/if}
	</div>
{/if}
</form>