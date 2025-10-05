<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/cart.js"></script>
	<script type="text/javascript" src="{$baseURL}/assets/javascript/order.details.js"></script>
	<script type="text/javascript">
		$(function()
		{ 
			// Hide top nav so we can replace it with steps bar
			$('#searchBar').hide();
			$('#topNav').hide();
		});
	</script>
</head>
<body>
	<input type="hidden" id="loggedIn" name="loggedIn" value="{$loggedIn}">
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		{include file='header2.tpl'}
		
		<div class="container">	
		
			<div class="orderDetailsHeader"><h1>{$lang.yourOrder}<a href="invoice.php?orderID={$orderInfo.uorder_id}" class="btn btn-xs btn-primary" target="_blank" style="float: right">{$lang.viewInvoice}</a></h1></div>
			
			<div class="orderDetailsBoxesContainer">
				<div class="orderDetailsBoxes" style="width: 33%">
					<div class="cartTotalList">					
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell">{$lang.orderNumber}:</div>
								<div class="divTableCell">{$orderInfo.order_number}</div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell">{$lang.orderPlaced}:</div>
								<div class="divTableCell">{$orderInfo.orderPlacedDate}</div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell">{$lang.orderStatus}:</div>
								<div class="divTableCell"><span class="highlightValue_{$orderInfo.orderStatusLang}">{$lang.{$orderInfo.orderStatusLang}}</span></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell">{$lang.paymentStatus}:</div>
								<div class="divTableCell"><span class="highlightValue_{$invoiceInfo.paymentStatusLang}">{$lang.{$invoiceInfo.paymentStatusLang}}</span></div>
							</div>
						</div><!--	payment info here -->
					</div>
					
				</div>
							
				<div class="orderDetailsBoxes" style="width: 34%;">
					<div class="cartTotalList">
						{if $invoiceInfo.subtotal}
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell">{$lang.subtotal}:</div>
								<div class="divTableCell"><span class="price">{$invoiceInfo.priceSubTotal}</span></div>
							</div>
							{if $invoiceInfo.shippable}
								<div class="divTableRow">
									<div class="divTableCell">{$lang.shipping}:</div>
									<div class="divTableCell"><span>{$invoiceInfo.shippingTotal}</span></div>
								</div>
							{/if}
							{if $invoiceInfo.taxa_cost > 0}
								<div class="divTableRow">
									<div class="divTableCell">{$lang.taxAName}<!-- ({$tax.tax_a_default}%)-->:</div>
									<div class="divTableCell"><span>{$invoiceInfo.taxA}</span></div>
								</div>
							{/if}
							{if $invoiceInfo.taxb_cost > 0}
								<div class="divTableRow">
									<div class="divTableCell">{$lang.taxBName}<!-- ({$tax.tax_b_default}%)-->:</div>
									<div class="divTableCell"><span>{$invoiceInfo.taxB}</span></div>
								</div>
							{/if}
							{if $invoiceInfo.taxc_cost > 0}
								<div class="divTableRow">
									<div class="divTableCell">{$lang.taxCName}<!-- ({$tax.tax_c_default}%)-->:</div>
									<div class="divTableCell"><span>{$invoiceInfo.taxC}</span></div>
								</div>
							{/if}
							{if $invoiceInfo.discounts_total > 0}
								<div class="divTableRow">
									<div class="divTableCell">{$lang.discounts}:</div>
									<div class="divTableCell"><span class="cartTotalDiscounts">{$invoiceInfo.discountsTotal}</span></div>
								</div>
							{/if}
							<div class="divTableRow">
								<div class="divTableCell"><span class="price">{$lang.total}:</span></div>
								<div class="divTableCell"><span class="price">{$invoiceInfo.total}</span></div>
							</div>
						</div>
						{/if}	
						{if $invoiceInfo.creditsSubTotal}
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell">{$lang.creditsSubtotal}:</div>
								<div class="divTableCell"><span class="price">{$invoiceInfo.creditsSubTotal}</span></div>
							</div>
							{if $invoiceInfo.discounts_credits_total}
								<div class="divTableRow">
									<div class="divTableCell">{$lang.creditsDiscounts}:</div>
									<div class="divTableCell"><span class="cartTotalDiscounts">{$invoiceInfo.totalCreditsDiscounts}</span></div>
								</div>
							{/if}
							<div class="divTableRow">
								<div class="divTableCell"><span class="price">{$lang.credits}:</span></div>
								<div class="divTableCell"><span class="price">{$invoiceInfo.creditsTotal}</span></div>
							</div>
						</div>
						{/if}
						
						*{$lang.totalsShown} <strong>{$adminCurrency.code}</strong>
					</div>
					
				</div>
				
				<div class="orderDetailsBoxes" style="width: 33%">
					<div style="padding-top: 5px;">
						<h2>{$lang.billTo}:</h2>
						<p>
							<strong>{$invoiceInfo.bill_name}</strong><br>
							{$invoiceInfo.bill_address}<br>
							{if $invoiceInfo.bill_address2}{$invoiceInfo.bill_address2}<br>{/if}
							{$invoiceInfo.bill_city}, {$invoiceInfo.bill_state} {$invoiceInfo.bill_zip}<br>
							{$invoiceInfo.bill_country}<br>
							<!--
							{if $shippingAddress.phone}{$shippingAddress.phone}<br>{/if}
							{if $shippingAddress.email}{$shippingAddress.email}<br>{/if}
							-->
						</p>
						{if $invoiceInfo.shippable}
							<h2 style="margin-top: 20px;">{$lang.shipTo}:</h2>
							<p>
								<strong>{$invoiceInfo.ship_name}</strong><br>
								{$invoiceInfo.ship_address}<br>
								{if $invoiceInfo.ship_address2}{$invoiceInfo.ship_address2}<br>{/if}
								{$invoiceInfo.ship_city}, {$invoiceInfo.ship_state} {$invoiceInfo.ship_zip}<br>
								{$invoiceInfo.ship_country}<br>
								<!--
								{if $shippingAddress.phone}{$shippingAddress.phone}<br>{/if}
								{if $shippingAddress.email}{$shippingAddress.email}<br>{/if}
								-->
							</p>
						{/if}
					</div>
				</div>
			</div>
			
			<div class="messageBox"><img src="{$imgPath}/notice.icon.png">{$lang.orderInfoshipnotes}{$orderInfo.mem_notes}</div>
			
			
			{if $digitalInvoiceItems}
				<div class="container cartContainer">				
					<input type="hidden" name="orderID" id="orderID" value="{$invoiceInfo.order_id}">
					<h2 style="margin-top: 10px;">{$lang.downloads}</h2>
					
					{foreach $digitalInvoiceItems as $invoiceItemKey => $invoiceItem}
					<div class="row" style="background-color: #efefef; margin-bottom: 3px;">
						<div class="col-md-2 cartThumbColumn">
							{if $invoiceItem.itemDetails.media}
								<a href="media.details.php?mediaID={$invoiceItem.itemDetails.media.useMediaID}"><img src="image.php?mediaID={$invoiceItem.itemDetails.media.encryptedID}=&type=icon&folderID={$invoiceItem.itemDetails.media.encryptedFID}==&size=60" class="thumb"></a>
							{elseif $invoiceItem.itemDetails.photo}
								<img src="{productShot itemID=$invoiceItem.item_id itemType=$invoiceItem.itemTypeShort photoID=$invoiceItem.itemDetails.photo.id size=60}" class="thumb">
							{else}
								<img src="{$imgPath}/blank.cart.item.png">
							{/if}
						</div>
						<div class="col-md-6">
							<h2>{$invoiceItem.itemDetails.name}</h2>
							{if $invoiceItem.item_type == 'digital'}
								<p class="cartItemDescription">
									{if $config.settings.display_license}{$lang.license}: <strong>{$invoiceItem.itemDetails.licenseLang}</strong><br>{/if}
									{if $invoiceItem.itemDetails.width or $invoiceItem.itemDetails.height}<strong>{$invoiceItem.itemDetails.width} x {$invoiceItem.itemDetails.height} px</strong> <!--{if $cartItem.itemDetails.widthIC or $cartItem.itemDetails.heightIC}<em>( {$cartItem.itemDetails.widthIC} x {$cartItem.itemDetails.heightIC} @ {$config.dpiCalc} {$lang.dpi} )</em>{/if}--><br>{/if}
									{if $invoiceItem.itemDetails.format}{$lang.mediaLabelFormat}: <strong>{$invoiceItem.itemDetails.format}</strong><br>{/if}
									{if $invoiceItem.itemDetails.dsp_type == 'video'}
										{if $invoiceItem.itemDetails.fps}{$lang.mediaLabelFPS}: <strong>{$invoiceItem.itemDetails.fps}</strong><br>{/if}
										{if $invoiceItem.itemDetails.running_time}{$lang.mediaLabelRunningTime}: <strong>{$invoiceItem.itemDetails.running_time}</strong> {$lang.seconds}<br>{/if}
									{/if}
									{if $invoiceItem.itemDetails.media.model_release_form}<br><a href="../assets/files/releases/{$invoiceItem.itemDetails.media.model_release_form}" target="_blank">{$lang.mediaLabelRelease}</a>{/if}
									{if $invoiceItem.itemDetails.media.prop_release_form}<br><a href="../assets/files/releases/{$invoiceItem.itemDetails.media.prop_release_form}" target="_blank">{$lang.mediaLabelPropRelease}</a>{/if}
								</p>
							{else}
								<p class="cartItemDescription">{$invoiceItem.itemDetails.description|truncate:200}</p>
							{/if}
							{if $debugMode}{debugOutput value=$invoiceItem title='Item Details'}{/if}
							
							{if $invoiceItem.item_type == 'collection'}<div id="collectionList{$invoiceItemKey}" style="display: none; padding: 0; max-height: 350px; min-height: 40px; overflow: auto;" class="cartItemDetailsContainer"></div>{/if}
						</div>
						<div class="col-md-2">
							<span class="price" style="font-size: 13px; cursor: auto">
							{if $invoiceItem.paytype == 'cur'}
								{$invoiceItem.lineItemPriceTotal}{if $invoiceItem.taxInc}*{/if}
							{else}
								{$invoiceItem.lineItemCreditsTotal} <sup>{$lang.credits}</sup>
							{/if}
							</span>
						</div>
						<div class="col-md-2 orderDownloadColumn">
							<input 
								type="button" 
								value="{if $invoiceItem.downloadableStatus == '4'}{$lang.request}{else}{$lang.downloadUpper}{/if}" 
								class="orderDownloadButton btn btn-xs btn-primary" 
								downloadableStatus="{$invoiceItem.downloadableStatus}" 
								key="{$invoiceItem.downloadKey}" 
								invoiceItemID="{$invoiceItemKey}" 
							/>
						</div>						
					</div>
				{/foreach}
				<!--<input type="button" class="colorButton editButton" value="{$lang.edit}" style="float: right; margin-top: 6px;">-->
				</div>
			{/if}
			
			{if $physicalInvoiceItems}			
				<div class="container cartContainer">
					<h2 style="margin-top: 10px;">{$lang.items}</h2>					
					{foreach $physicalInvoiceItems as $invoiceItemKey => $invoiceItem}
						{if $invoiceItem.photoPuzzleImg}
							{$bPP = 1}
						{/if}
				
					<div class="row" style="background-color: #efefef; margin-bottom: 3px;">
						<div class="col-md-2 cartThumbColumn">
							{if $invoiceItem.itemDetails.media && !$bPP}
								<a href="media.details.php?mediaID={$invoiceItem.itemDetails.media.useMediaID}"><img src="image.php?mediaID={$invoiceItem.itemDetails.media.encryptedID}=&type=icon&folderID={$invoiceItem.itemDetails.media.encryptedFID}==&size=60" class="thumb"></a>
							{elseif $invoiceItem.itemDetails.photo}
								<img src="{productShot itemID=$invoiceItem.item_id itemType=$invoiceItem.itemTypeShort photoID=$invoiceItem.itemDetails.photo.id size=60}" class="thumb">
							{elseif $bPP}
								<img src="{$invoiceItem.photoPuzzleImg}" class="thumb" style="width: 150px;">
							{else}
								<img src="{$imgPath}/blank.cart.item.png"><!-- Spacer -->
							{/if}
						</div>
						<div class="col-md-6">
							<h2>{$invoiceItem.itemDetails.name}</h2>
							<p class="cartItemDescription">{$invoiceItem.itemDetails.description|truncate:200}</p>
							
							{if $invoiceItem.has_options && !$bPP}
							<div class="cartItemDetailsContainerInline" style="margin-top: 6px; padding-left: 0; border-top: none;">
								<a href="{$invoiceItem.oi_id}" itemType="{$invoiceItem.item_type}" downloadOrderID="{$orderInfo.uorder_id}" class="buttonLink cartItemDetailsButton">+</a> {if $invoiceItem.item_type == 'package'}{$lang.viewPackOptions}{else}{$lang.viewOptions}{/if}
								<!--<a href="{$cartItem.itemDetails.cartEditLink}" class="colorLink cartItemEditLink" style="float: right;">[Edit]</a>-->
								<div style="display: none" id="optionsBox{$invoiceItem.oi_id}" class="optionsBox"></div>
								<!--ID: {$cartItem.oi_id} - Type: {$cartItem.item_type}-->
							</div>
							{elseif $invoiceItem.has_options && $bPP}
								<div class="cartItemDetailsContainerInline">
								{foreach $invoiceItem.photoPuzzleGruppoOpzione as $k => $gruppoOpzione}
									<span>{$lang.$gruppoOpzione}: {$invoiceItem.photoPuzzleOpzione[$k]}</span>
								{/foreach}
								</div>
							{/if}
							{*if $invoiceItem.has_options}{include file='item.options.tpl'}{/if*}
							{if $debugMode}{debugOutput value=$invoiceItem title='Item Details'}{/if}
						</div>
						<div class="col-md-1">
							{$invoiceItem.quantity}
						</div>
						<div class="col-md-1">
							<span class="price" style="font-size: 13px; cursor: auto">
							{if $invoiceItem.paytype == 'cur'}
								{$invoiceItem.lineItemPriceTotal}{if $invoiceItem.taxInc}*{/if}
							{else}
								{$invoiceItem.lineItemCreditsTotal} <sup>{$lang.credits}</sup>
							{/if}
							</span>
						</div>
						<div class="col-md-2 orderDownloadColumn">
							{if $invoiceItem.physical_item}{$lang.{$invoiceItem.shippingStatusLang}}{/if}
						</div>
					</div>
				{/foreach}				
				<!--<input type="button" class="colorButton editButton" value="{$lang.edit}" style="float: right; margin-top: 6px;">-->
			{/if}
			<p class="totalsShownFooter">*{$lang.totalsShown} <strong>{$adminCurrency.code}</strong></p>
			</div>
		</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>