<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.js"></script>
<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.items.js"></script>
<form class="cleanForm form-group" action="{linkto page='cart.php'}" id="workboxItemForm" method="post">
<img src="{$imgPath}/close.button.png" id="closeWorkbox">
<!-- For cart -->
<input type="hidden" name="mode" value="add" id="mode"> 
<input type="hidden" name="type" value="credits" id="type">
<input type="hidden" name="id" value="{$credit.useCreditsID}">
{if $edit}<input type="hidden" name="edit" value="{$edit}">{/if}

{if $noAccess}
	<p class="notice">{$lang.noAccess}</p>
{else}
	<div class="container">
		<div class="row">
			{if $credit.photo}
				<div class="col-md-5 workboxLeftColumn">
					<p id="mainShotContainer"><img src="{productShot itemID=$credit.credit_id itemType=credit photoID=$credit.photo.id size=300}" id="mainShot"></p>
					{if $credit.photos|count > 1}
						<p id="additionalShots">
						{foreach $credit.photos as $key => $value}
							<a href="{productShot itemID=$credit.credit_id itemType=credit photoID=$value.id size=300}"><img src="{productShot itemID=$credit.credit_id itemType=credit photoID=$value.id size=70 crop=50}"></a>
						{/foreach}
						</p>
					{/if}
				</div>
			{/if}
			<div class="col-md-7 workboxRightColumn">	
				<h1>{$credit.name}</h1>
				<p>{$credit.description}</p>
			</div>
		</div>
	</div>
{/if}
{if $cartStatus}<div class="workboxActionButtons">{if $credit.price}{$lang.mediaLabelPrice}: <span class="price" id="workboxItemPrice">{$credit.price.display}</span>{if $credit.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if !$noAccess and !$edit}<br><input type="button" value="{$lang.addToCart}" id="workboxAddToCart" class="btn btn-xs btn-primary">{/if}</div>{/if}
</form>