<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<meta name="robots" content="nofollow" />
	<script type="text/javascript" src="{$baseURL}/assets/javascript/cart.js"></script>
	<script language="javascript" type="text/javascript"></script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div class="contentContainer">
			<div class="content" style="padding-left: 0; padding-right: 0;">
				<h1>{$lang.cart}</h1>
				<form method="post" action="cart.process.php" id="cartForm">
				<input type="hidden" name="creditsNeededForCheckout" id="creditsNeededForCheckout">
				<input type="hidden" name="creditsNeededToCheckout" id="creditsNeededToCheckout" value="{$cartTotals.creditsTotal}">
				<input type="hidden" name="creditsAvailableAtCheckout" id="creditsAvailableAtCheckout" value="{$cartTotals.creditsAvailableAtCheckout}">
				<input type="hidden" name="lowSubtotalWarning" id="lowSubtotalWarning" value="{$lowSubtotalWarning}">
				<input type="hidden" name="accountWorkbox" id="accountWorkbox" value="{$accountWorkbox}">
				
				{if $cartItems|count > 0}
				
				{if $mode == 'add'}<div class="cartItemAddedMessage">{$lang.cartItemAdded} <input type="button" value="{$lang.continueShopUpper}" href="{$continueShoppingButton.linkto}" class="colorButton backLink" style="float: right;"></div>{/if}
				
				{if $promotions}
					<div class="galleryFeaturedItemsContainer cartPromotions">
						<img src="{$imgPath}/page.curl.png" class="pageCurl">
						<h1>{$lang.promotions}</h1>
						{foreach $promotions as $promo}
							<div class="featuredPageItem featuredPromos workboxLinkAttach">
								<h2><a href="{$promo.linkto}" class="workboxLink">{$promo.name}</a></h2>
								<p class="description">
									{if $promo.photo}<img src="{productShot itemID=$promo.promo_id itemType=promo photoID=$promo.photo.id size=70}">{/if}
									{$promo.description|truncate:30}<br><br>
									<!--{if $promo.autoapply}<span class="promoUse">*{$lang.autoApply}</span>{elseif $promo.promo_code}<span class="promoUse">*{$lang.useCoupon}<strong>: {$promo.promo_code}</strong></span>{/if}-->
								</p>
							</div>
						{/foreach}
					</div>
				{/if}
				
				<div class="divTable cartContainer">
					<div class="divTableRow">
						<div class="divTableCell" style="padding-right: 10px;">
							{if $cartItems|count > 4}<!--<input type="button" value="{$lang.update}" style="float: right; margin-bottom: 10px; clear: both;" class="updateQuantitiesButton colorButton">-->{/if}
							{foreach $cartItems as $cartItem}
								<div class="cartItemContainer">
									<div class="divTable cartItemsList">
										<div class="divTableRow" id"cartItemRow{$cartItem.oi_id}">
											<div class="divTableCell thumbRow">
												{$cartItem.itemDetails.numOf}
												{if $cartItem.itemDetails.media}
													<a href="{linkto page="media.details.php?mediaID={$cartItem.itemDetails.media.useMediaID}"}"><img src="image.php?mediaID={$cartItem.itemDetails.media.encryptedID}=&type=icon&folderID={$cartItem.itemDetails.media.encryptedFID}&size=60" class="thumb"></a>
												{elseif $cartItem.itemDetails.photo}
													<img src="{productShot itemID=$cartItem.item_id itemType=$cartItem.itemTypeShort photoID=$cartItem.itemDetails.photo.id size=60}" class="thumb">
												{else}
													<img src="{$imgPath}/blank.cart.item.png">
												{/if}
											</div>
											<div class="divTableCell itemRow" style="position: relative">
												{if $config.settings.cart_notes}<p class="cartAddNotes" cartItemID="{$cartItem.oi_id}"><img src="{$imgPath}/note.icon.png" title="{$lang.notes}"></p>{/if}
												<h2><a href="{$cartItem.itemDetails.cartEditLink}" class="cartItemEditLink">{$cartItem.itemDetails.name}</a><!--<img src="{$imgPath}/comment.icon.png" title="Add Comment" style="margin-left: 5px;">--></h2>
												{if $cartItem.item_type == 'package'}
													<input type="hidden" name="packageItemsLeftToFill[]" value="{$cartItem.package_media_remaining}" class="checkPackageFill" packageID="{$cartItem.item_id}">
													<div class="packageFilledContainer" id="packageFilledContainer{$cartItem.item_id}">
														<div class="packageFilledBar"><p style="width: {$cartItem.package_media_percentage}%"></p></div>
														<!--{$cartItem.package_media_filled}/{$cartItem.package_media_needed} = --><strong>{$cartItem.package_media_remaining}</strong> {$lang.leftToFill}
													</div>
												{/if}
												{if $cartItem.item_type == 'digital'}
													<p class="cartItemDescription">
														{if $config.settings.display_license}{$lang.license}: <strong>{$cartItem.itemDetails.licenseLang}</strong><br>{/if}
														{if $cartItem.itemDetails.width or $cartItem.itemDetails.height}<strong>{$cartItem.itemDetails.width} x {$cartItem.itemDetails.height} px</strong> <!--{if $cartItem.itemDetails.widthIC or $cartItem.itemDetails.heightIC}<em>( {$cartItem.itemDetails.widthIC} x {$cartItem.itemDetails.heightIC} @ {$config.dpiCalc} {$lang.dpi} )</em>{/if}--><br>{/if}
														{if $cartItem.itemDetails.format}{$lang.mediaLabelFormat}: <strong>{$cartItem.itemDetails.format}</strong><br>{/if}
														{if $cartItem.itemDetails.dsp_type == 'video'}
															{if $cartItem.itemDetails.fps}{$lang.mediaLabelFPS}: <strong>{$cartItem.itemDetails.fps}</strong><br>{/if}
															{if $cartItem.itemDetails.running_time}{$lang.mediaLabelRunningTime}: <strong>{$cartItem.itemDetails.running_time}</strong> {$lang.seconds}<br>{/if}
														{/if}
													</p>
												{else}
													<p class="cartItemDescription">{$cartItem.itemDetails.description|truncate:200}</p>
												{/if}
											</div>
											<div class="divTableCell quantityRow"><!--<a href="" class="buttonLink">-</a>--><input type="text" value="{$cartItem.quantity}" name="quantity[{$cartItem.oi_id}]" class="quantity" {if $cartItem.item_type == 'collection' or $cartItem.item_type == 'digital' or $cartItem.item_type == 'subscription' or $cartItem.itemDetails.multiple == 0}disabled="disabled"{/if}><!--<a href="" class="buttonLink">+</a>--></div>
											<div class="divTableCell priceRow">
												<div class="cartPriceContainer">
													{if $cartItem.payTypeCount > 1}
														<ul class="payType">
															{if $cartItem.usePayType == 'cur'}
																<li cartItemID="{$cartItem.encryptedID}" payType="cred">{$lang.use} <strong>{$cartItem.lineItemCreditsTotal} {$lang.credits}</strong></li>
															{else}
																<li cartItemID="{$cartItem.encryptedID}" payType="cur">{$lang.use} <strong>{$cartItem.lineItemPriceTotalLocal.display}</strong></li>
															{/if}
														</ul>
													{/if}
													<span class="price" style="font-size: 13px;">
													{if $cartItem.usePayType == 'cur'}
														{* Currency *}
														{if $cartItem.payTypeCount > 1}
															{$cartItem.lineItemPriceTotalLocal.display}{if $cartItem.taxInc}*{/if} <img src="{$imgPath}/down.arrow.png">
														{else}
															{$cartItem.lineItemPriceTotalLocal.display}{if $cartItem.taxInc}*{/if}
														{/if}
													{else}
														{* Credits *}
														{if $cartItem.payTypeCount > 1}
															{$cartItem.lineItemCreditsTotal} <sup>{$lang.credits}</sup> <img src="{$imgPath}/down.arrow.png">
														{else}
															{$cartItem.lineItemCreditsTotal} <sup>{$lang.credits}</sup>
														{/if}
													{/if}
													</span>
													{if $cartItem.quantity > 1}<br><span class="cartPriceEach">({if $cartItem.usePayType == 'cur'}{$cartItem.lineItemPriceEachLocal.display}{else}{$cartItem.lineItemCreditsEach}{/if} {$lang.each})</span>{/if}
												</div>
											</div>
											<div class="divTableCell discountsRow">
												{if $cartItem.discountPercentage}<span class="cartItemSavings">{$cartItem.discountPercentage}% Savings</span><br>{/if}
												{if $cartItem.freeItems}<span class="cartItemSavings">{$cartItem.freeItems} Free</span>{/if}
											</div>
											<div class="divTableCell actionsRow">
												{if $cartItem.item_type == 'package' or $cartItem.item_type == 'print' or $cartItem.item_type == 'product'}<input type="button" href="{$cartItem.itemDetails.cartEditLink}" class="buttonLink cartItemEditLink" value="{$lang.edit}"><br>{/if}
												<input type="button" href="cart.php?mode=remove&cid={$cartItem.encryptedID}" class="buttonLink cartItemRemoveLink" value="{$lang.remove}">
											</div>
										</div>
										{if $cartItem.itemDetails.additionalQuantity}
											<div class="divTableRow">
												<div class="divTableCell"></div>
												<div class="divTableCell">{$cartItem.itemDetails.name} (Discounted)</div>
												<div class="divTableCell">{$cartItem.itemDetails.additionalQuantity}</div>
												<div class="divTableCell"><span class="price">{$cartItem.itemDetails.additionalTotal.display}</span></div>
											</div>
										{/if}
									</div>
									{if $cartItem.has_options}
										<div class="cartItemDetailsContainer">
											<a href="{$cartItem.oi_id}" itemType="{$cartItem.item_type}" class="buttonLink cartItemDetailsButton">+</a> {if $cartItem.item_type == 'package'}{$lang.viewPackOptions}{else}{$lang.viewOptions}{/if}
											<!--<a href="{$cartItem.itemDetails.cartEditLink}" class="colorLink cartItemEditLink" style="float: right;">[Edit]</a>-->
											<div style="display: none" id="optionsBox{$cartItem.oi_id}" class="optionsBox"></div>
											<!--ID: {$cartItem.oi_id} - Type: {$cartItem.item_type}-->
										</div>
									{/if}
									{* Debug Area *}{if $debugMode}{debugOutput value=$cartItem title='Cart Item'}{/if}
								</div>
							{/foreach}
							{if $cartTotals.taxInPrices}<p style="float: left;">* {$lang.includesTax}<!--: {if $cartTotals.taxA}{$lang.taxAName}: {$tax.tax_a_default}%{/if} {if $cartTotals.taxB}{$lang.taxBName}: {$tax.tax_b_default}%{/if} {if $cartTotals.taxC}{$lang.taxCName}: {$tax.tax_c_default}%{/if}--></p>{/if}<input type="button" value="{$lang.update}" style="float: right; margin-top: 6px;" class="updateQuantitiesButton colorButton">
						</div>
						<div class="divTableCell cartTotalColumn">
							{if $creditSystem}
								<div class="cartTotalList yourCredits">
									<div>{$lang.yourCredits}</div>
									<div class="myCredits">{if $member.credits}{$member.credits}{else}0{/if}</div><div style="float: right"><input type="button" href="featured.php?mode=credits" value="{$lang.purchaseCredits}" class="buyCreditsButton"></div>
								</div>
							{/if}
							
							<div class="cartTotalList promotionsBox">
								<p>{$lang.discountCode} <input type="text" name="couponCode" id="couponCode"> <input type="button" value="{$lang.apply}" id="applyCouponButton" style=" float: right;" class="colorButton"></p>
								{if $cartCouponsArray}
									<ul>
										{foreach $cartCouponsArray as $couponKey => $coupon}
											<li>{$coupon.name} <input type="button" href="cart.php?cartMode=removeCoupon&couponID={$coupon.promo_id}" value="{$lang.remove}" class="removeCouponButton"></li>{if !$coupon.autoapply}<!-- use for auto apply or not -->{/if}
										{/foreach}
									</ul>
								{/if}
							</div>
							
							{if $config.settings.customer_taxid}
								<div class="cartTotalList promotionsBox" style="padding-bottom: 20px;">
									<h2>{$lang.vatIDNumber}:</h2>
									<div><input type="text" name="taxID" id="taxID" value="{$shippingAddress.email}" style="width: 300px;"> <!--<input type="button" value="{$lang.apply}" id="applyCouponButton" style=" float: right;" class="colorButton">--></div>
								</div>
							{/if}
							
							{if $config.settings.cart_notes}
								<div class="cartTotalList promotionsBox" style="padding-bottom: 20px;">
									<h2>{$lang.notes}:</h2>
									<div><textarea name="cartNotes" id="cartNotes" style="width: 300px;">{$cartInfo.cartNotes}</textarea> <!--<input type="button" value="{$lang.apply}" id="applyCouponButton" style=" float: right;" class="colorButton">--></div>
								</div>
							{/if}
							
							<div class="cartTotalList">
								
								{if $priCurrency.currency_id != $selectedCurrency}<div class="cartTotalListWarning"><img src="{$imgPath}/notice.icon.png"/>{$lang.cartTotalListWarning}</div>{/if}
								
								{if $cartTotals.priceSubTotal}
									<div class="divTable">
										<div class="divTableRow">
											<div class="divTableCell">{$lang.subtotal}:</div>
											<div class="divTableCell"><span class="price">{$cartTotals.subTotalLocal.display}</span></div>
										</div>
										{if $cartTotals.taxTotal}
											{if $cartTotals.taxA}
												<div class="divTableRow">
													<div class="divTableCell">{$lang.estimated} {$lang.taxAName}<!-- ({$tax.tax_a_default}%)-->:</div>
													<div class="divTableCell"><span class="{if $cartTotals.clearTax}strike{/if}">{$cartTotals.taxALocal.display}</span></div>
												</div>
											{/if}
											{if $cartTotals.taxB}
												<div class="divTableRow">
													<div class="divTableCell">{$lang.estimated} {$lang.taxBName}<!-- ({$tax.tax_b_default}%)-->:</div>
													<div class="divTableCell"><span class="{if $cartTotals.clearTax}strike{/if}">{$cartTotals.taxBLocal.display}</span></div>
												</div>
											{/if}
											{if $cartTotals.taxC}
												<div class="divTableRow">
													<div class="divTableCell">{$lang.estimated} {$lang.taxCName}<!-- ({$tax.tax_c_default}%)-->:</div>
													<div class="divTableCell"><span class="{if $cartTotals.clearTax}strike{/if}">{$cartTotals.taxCLocal.display}</span></div>
												</div>
											{/if}
										{/if}
										{if $cartTotals.totalDiscounts}
											<div class="divTableRow">
												<div class="divTableCell">{$lang.discounts}:</div>
												<div class="divTableCell"><span class="cartTotalDiscounts">-{$cartTotals.totalDiscountsLocal.display}</span></div>
											</div>
										{/if}
										<!--
										<div class="divTableRow">
											<div class="divTableCell">Shipping:</div>
											<div class="divTableCell" style="text-align: right"><span class="price">TBD</span></div>
										</div>
										-->
										<div class="divTableRow">
											<div class="divTableCell"><span class="price">{$lang.total}:</span></div>
											<div class="divTableCell"><span class="price">{$cartTotals.totalLocal.display}</span></div>
										</div>
									</div>
								{/if}
								
								{if $cartTotals.creditsSubTotal}	
									<div class="divTable">
										<div class="divTableRow">
											<div class="divTableCell">{$lang.creditsSubtotal}:</div>
											<div class="divTableCell"><span class="price">{$cartTotals.creditsSubTotal}</span></div>
										</div>
										{if $cartTotals.totalCreditsDiscounts}
											<div class="divTableRow">
												<div class="divTableCell">{$lang.creditsDiscounts}:</div>
												<div class="divTableCell"><span class="cartTotalDiscounts">-{$cartTotals.totalCreditsDiscounts}</span></div>
											</div>
										{/if}
										<div class="divTableRow">
											<div class="divTableCell"><span class="price">{$lang.credits}:</span></div>
											<div class="divTableCell"><span class="price">{$cartTotals.creditsTotal}</span></div>
										</div>
									</div>
								{/if}
								<input type="button" value="{$lang.checkout}" style="float: right" id="cartCheckoutButton" class="colorButton">
							</div>
						</div>
					</div>
				</div>
				</form>
				{else}
					{$lang.cartNoItems}
				{/if}
								
				{* Debug Area *}
				{if $debugMode}
					{debugOutput value=$uniqueOrderID title='uniqueOrderID'}
					{debugOutput value=$cartID title='cartID'}
					{debugOutput value=$invoiceID title='invoiceID'}
					{debugOutput value=$cartItemRows title='cartItemRows'}
					{debugOutput value=$cartTotals title='Cart Variables'}
					{debugOutput value=$packagesInCartSession title='Packages IDs In Cart'}
					{debugOutput value=$cartCouponsArray title='Coupons'}
				{/if}
				
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>