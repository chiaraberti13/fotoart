<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/cart.shipping.js"></script>
</head>
<body>
	
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div id="contentContainer" class="center">
			<div class="content">
				
				<ul class="cartStepsBar cartStepsBar25">
					<li class="off" style="cursor: pointer"><p>1</p><div>{$lang.cart}</div></li>
					<li class="on"><p>2</p><div>{$lang.shipping}</div></li>
					<li class="off"><p>3</p><div>{$lang.reviewOrder}</div></li>
					<li class="off"><p>4</p><div>{$lang.payment}</div></li>
				</ul>
				
				<div>
					<!--<h1{if !$loggedIn} style="margin-bottom: 0;"{/if}>Shipping</h1>-->
					
					{if !$loggedIn and $config.settings.display_login}<div class="cartLoginRequest"><a href="login.php?jumpTo=cart" class="buttonLink">{$lang.login}</a> <a href="create.account.php?jumpTo=cart" class="buttonLink">{$lang.createAccount}</a> {$lang.continueNoAccount}...</div>{/if}
					
					<form id="shippingAddressesForm" action="cart.review.php" method="post" class="cleanForm">
					<div class="divTable cartContainer" style="width: 100%;">
						<div class="divTableRow">
							<div class="divTableCell" style="padding-right: 10px; width: 560px; text-align: left;">						
								{$lang.enterShipInfo}:<br><br>
								<h1 class="infoHeader">{$lang.shipTo}:</h1>							
								<input type="hidden" name="shippingInfo" value="1">
								<input type="hidden" name="addressExists" value="{$addressExists}">
								{if $addressExists}
									<ul>
										<li style="padding-left: 80px;">
											<input type="radio" name="memberShippingAddress" value="" checked="checked" style="margin-left: -20px;">
											<strong>{$member.f_name} {$member.l_name}</strong><br>
											{$member.primaryAddress.address}<br>
											{if $member.primaryAddress.address_2}{$member.primaryAddress.address_2}<br>{/if}
											{$member.primaryAddress.city}, {$member.primaryAddress.state} {$member.primaryAddress.postal_code}<br>
											{$member.primaryAddress.country} 
											<!--<a href="" class="colorLink">[{$lang.edit}]</a>-->
										</li>
									</ul>
									<input type="hidden" name="shippingCountry" id="shippingCountry" value="{$shippingAddress.countryID}">
									<input type="hidden" name="shippingState" id="shippingState" value="{$shippingAddress.stateID}">
									<input type="hidden" name="shippingPostalCode" id="shippingPostalCode" value="{$shippingAddress.postalCode}">
								{else}
									<div class="divTable">
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.country}: 
											</div>
											<div class="divTableCell">
												<select id="shippingCountry" name="shippingCountry" style="width: 316px;" require="require" errorMessage="{$lang.required}">
													<option></option>
													{html_options options=$countries selected=$shippingAddress.countryID}
												</select>
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.firstName}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="shippingFirstName" name="shippingFirstName" value="{$shippingAddress.firstName}" require="require" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.lastName}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="shippingLastName" name="shippingLastName" value="{$shippingAddress.lastName}" require="require" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.address}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="shippingAddress" name="shippingAddress" value="{$shippingAddress.address}" require="require" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												 
											</div>
											<div class="divTableCell">
												<input type="text" id="shippingAddress2" name="shippingAddress2" value="{$shippingAddress.address2}" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.city}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="shippingCity" name="shippingCity" value="{$shippingAddress.city}" require="require" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.state}: 
											</div>
											<div class="divTableCell">									
												<select id="shippingState" name="shippingState" style="width: 316px;" errorMessage="{$lang.required}" >
													{if $shippingAddress.stateID}
														{html_options options=$shippingStates selected=$shippingAddress.stateID}
													{else}
														<option value="0">{$lang.chooseCountryFirst}</option>
													{/if}												
												</select> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.zip}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="shippingPostalCode" name="shippingPostalCode" value="{$shippingAddress.postalCode}" require="require" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.email}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="shippingEmail" name="shippingEmail" value="{$shippingAddress.email}" require="require" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.phone}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="shippingPhone" name="shippingPhone" value="{$shippingAddress.phone}" require="require" errorMessage="{$lang.required}"> 
											</div>
										</div>
									</div>
								{/if}
								
								<h1 class="infoHeader">{$lang.billTo}:</h1>
								{if $addressExists}
									<ul>
										<li style="padding-left: 80px;">
											<input type="radio" name="memberBillingAddress" value="" checked="checked" style="margin-left: -20px;">
											<strong>{$member.f_name} {$member.l_name}</strong><br>
											{$member.primaryAddress.address}<br>
											{if $member.primaryAddress.address_2}{$member.primaryAddress.address_2}<br>{/if}
											{$member.primaryAddress.city}, {$member.primaryAddress.state} {$member.primaryAddress.postal_code}<br>
											{$member.primaryAddress.country} 
											<!--<a href="" class="colorLink">[{$lang.edit}]</a>-->
										</li>
									</ul>
								{else}
									<div class="divTable" style="margin-bottom: 10px;">
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel" style="padding-top: 0px;"><input type="radio" name="duplicateInfo" value="1" id="duplicateInfo1" checked="checked" class="duplicateInfo"></div>
											<div class="divTableCell"><label for="duplicateInfo1">{$lang.sameAsShipping}</label></div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel" style="padding-top: 0px;"><input type="radio" name="duplicateInfo" value="0" id="duplicateInfo0" class="duplicateInfo"></div>
											<div class="divTableCell"><label for="duplicateInfo0">{$lang.differentAddress}</label></div>
										</div>
									</div>
									
									<div class="divTable" id="billingInfoForm" style="display: none;">
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.country}: 
											</div>
											<div class="divTableCell">
												<select id="billingCountry" name="billingCountry" style="width: 316px;" errorMessage="{$lang.required}">
													<option></option>
													{html_options options=$countries selected=$billingAddress.countryID}
												</select>
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.firstName}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="billingFirstName" name="billingFirstName" value="{$billingAddress.firstName}" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.lastName}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="billingLastName" name="billingLastName" value="{$billingAddress.lastName}" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.address}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="billingAddress" name="billingAddress" value="{$billingAddress.address}" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												 
											</div>
											<div class="divTableCell">
												<input type="text" id="billingAddress2" name="billingAddress2" value="{$billingAddress.address2}" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.city}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="billingCity" name="billingCity" value="{$billingAddress.city}" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.state}: 
											</div>
											<div class="divTableCell">											
												<select id="billingState" name="billingState" style="width: 316px;" errorMessage="{$lang.required}" >
													{if $billingAddress.stateID}
														{html_options options=$billingStates selected=$billingAddress.stateID}
													{else}
														<option value="0">{$lang.chooseCountryFirst}</option>
													{/if}
												</select> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.zip}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="billingPostalCode" name="billingPostalCode" value="{$billingAddress.postalCode}" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.email}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="billingEmail" name="billingEmail" value="{$billingAddress.email}" errorMessage="{$lang.required}"> 
											</div>
										</div>
										<div class="divTableRow">
											<div class="divTableCell formFieldLabel">
												{$lang.phone}: 
											</div>
											<div class="divTableCell">
												<input type="text" id="billingPhone" name="billingPhone" value="{$billingAddress.phone}" errorMessage="{$lang.required}"> 
											</div>
										</div>
									</div>
								{/if}
								
							</div>
							<div class="divTableCell cartTotalColumn">
								<h2 style="padding-left: 10px; margin-bottom: 0;">{$lang.shippingOptions}:</h2>
								<div class="cartTotalList shippingMethodBox" id="shippingMethods"></div>
								<div class="cartTotalList">
									<input type="button" value="{$lang.continue}" style="float: right" id="cartContinueButton" class="colorButton">
								</div>							
							</div>						
						</div>
					</div>
					</form>
				</div>
				
				{* Debug Area *}
				{if $debugMode}
					{debugOutput value=$shippingAddress title='Shipping Address'}
					{debugOutput value=$billingAddress title='Billing Address'}
					{debugOutput value=$cartInfo title='Cart Info'}
					{debugOutput value=$cartTotals title='Cart Totals'}
				{/if}
				
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>