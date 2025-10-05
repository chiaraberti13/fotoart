{if $shippingMethods}
	{* Debug Area *}
	{if $debugMode}
		<div class="debug">
			<h1>Debug: Shipping</h1>
			<ul>
				<li><strong>Clear Shipping:</strong> {$cartTotals.clearShipping}</li>
				<li><strong>Shipping Percentage:</strong> {$shipPercentage}</li>
				<li><strong>Country ID:</strong> {$postVars.shippingCountry}</li>
				<li><strong>State ID:</strong> {$postVars.shippingState}</li>
				<li><strong>Zip:</strong> {$postVars.shippingPostalCode}</li>
				<li><strong>Shippable Items Subtotal:</strong> {$cartTotals.shippableTotal}</li>
				<li><strong>Shippable Items:</strong> {$cartTotals.shippableCount}</li>
				<li><strong>Shippable Weight:</strong> {$cartTotals.shippableWeight}</li>
			</ul>
		</div>
	{/if}
	
	<ul>
		{foreach $shippingMethods as $shippingMethodKey => $shippingMethod}
			<li>
				<input type="radio" name="shippingMethod" id="shippingMethod{$shippingMethodKey}" value="{$shippingMethodKey}" {if $shippingMethod@first}checked="checked"{/if}> <label for="shippingMethod{$shippingMethodKey}">{$shippingMethod.title}</label>
				<p>{if $shippingMethod.description}{$shippingMethod.description}<br>{/if}</p>
				<p style="float: right;">{if $cartTotals.clearShipping}<span class="price">FREE</span>{/if} <span class="{if !$cartTotals.clearShipping}price{/if} {if $cartTotals.clearShipping}strike{/if}">{$shippingMethod.price.display}</span> {if $shippingMethod.taxInc}<span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}</p>
			</li>
		{/foreach}
	</ul>
{else}
	<p class="notice">{$lang.noShipMethod}</p>
{/if}