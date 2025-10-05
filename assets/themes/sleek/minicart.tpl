<script>
	$(function()
	{
		$('#closeMiniCart').click(function(event){
			closeMiniCart();
		});
	});
</script>
<img src="{$imgPath}/close.button.png" id="closeMiniCart">
{if $onlyLastAdded}
	<h1>{$lang.cartItemAdded}</h1><br><br>
{/if}
{if $cartItems}
	<div id="miniCartItemsList">
	{foreach $cartItems as $cartItem}
		<div class="divTable cartItemsList">
			<div class="divTableRow" id"cartItemRow{$cartItem.oi_id}">
				<div class="divTableCell thumbRow">
					{$cartItem.itemDetails.numOf}
					{if $cartItem.itemDetails.media}
						<a href="{linkto page="media.details.php?mediaID={$cartItem.itemDetails.media.useMediaID}"}"><img src="{$baseURL}/image.php?mediaID={$cartItem.itemDetails.media.encryptedID}=&type=icon&folderID={$cartItem.itemDetails.media.encryptedFID}&size=60" class="thumb"></a>
					{elseif $cartItem.itemDetails.photo}
						<img src="{productShot itemID=$cartItem.item_id itemType=$cartItem.itemTypeShort photoID=$cartItem.itemDetails.photo.id size=60}" class="thumb">
					{else}
						<img src="{$imgPath}/blank.cart.item.png">
					{/if}
				</div>
				<div class="divTableCell itemRow" style="position: relative">
					<h2><a href="{$baseURL}/{$cartItem.itemDetails.cartEditLink}" class="cartItemEditLink">{$cartItem.itemDetails.name}</a></h2>
					{*if $cartItem.item_type == 'package'}
						<div class="packageFilledContainer">
							<div class="packageFilledBar"><p style="width: {$cartItem.package_media_percentage}%"></p></div>
							{$cartItem.package_media_remaining} {$lang.leftToFill}
						</div>
					{/if*}
					{if $cartItem.item_type == 'digital'}
						<p class="cartItemDescription">
							{if $config.settings.display_license}{$lang.license}: {$cartItem.itemDetails.licenseLang}<br>{/if}
							{if $cartItem.itemDetails.width or $cartItem.itemDetails.height}{$cartItem.itemDetails.width} x {$cartItem.itemDetails.height} px <!--{if $cartItem.itemDetails.widthIC or $cartItem.itemDetails.heightIC}<em>( {$cartItem.itemDetails.widthIC} x {$cartItem.itemDetails.heightIC} @ {$config.dpiCalc} {$lang.dpi} )</em>{/if}--><br>{/if}
							{if $cartItem.itemDetails.format}{$lang.mediaLabelFormat}: {$cartItem.itemDetails.format}<br>{/if}
							{if $cartItem.itemDetails.dsp_type == 'video'}
								{if $cartItem.itemDetails.fps}{$lang.mediaLabelFPS}: {$cartItem.itemDetails.fps}<br>{/if}
								{if $cartItem.itemDetails.running_time}{$lang.mediaLabelRunningTime}: {$cartItem.itemDetails.running_time} {$lang.seconds}<br>{/if}
							{/if}
						</p>
					{else}
						<p class="cartItemDescription">{$cartItem.itemDetails.description|truncate:200}</p>
					{/if}
				</div>
			</div>
		</div>
	{/foreach}
	</div>
	<input type="button" value="{$lang.viewCartUpper}" class="colorButton viewCartButton">
{else}
	{$lang.cartNoItems}
{/if}
