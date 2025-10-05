<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.js"></script>
<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.items.js"></script>
<form class="cleanForm form-group" action="{linkto page='cart.php'}" id="workboxItemForm" method="post">
<img src="{$imgPath}/close.button.png" id="closeWorkbox">
<!-- For cart -->
<input type="hidden" name="mode" value="add" id="mode"> 
<input type="hidden" name="type" value="subscription" id="type">
<input type="hidden" name="id" value="{$subscription.useSubscriptionID}">
{if $edit}<input type="hidden" name="edit" value="{$edit}">{/if}

{if $noAccess}
	<p class="notice">{$lang.noAccess}</p>
{else}
	<div class="container">
		<div class="row">
			{if $subscription.photo}
				<div class="col-md-5 workboxLeftColumn">
					<p id="mainShotContainer"><img src="{productShot itemID=$subscription.sub_id itemType=sub photoID=$subscription.photo.id size=300}" id="mainShot"></p>
					{if $subscription.photos|count > 1}
						<p id="additionalShots">
						{foreach $subscription.photos as $key => $value}
							<a href="{productShot itemID=$subscription.sub_id itemType=sub photoID=$value.id size=300}"><img src="{productShot itemID=$subscription.sub_id itemType=sub photoID=$value.id size=70 crop=50}"></a>
						{/foreach}
						</p>
					{/if}
				</div>
			{/if}
			<div class="col-md-7 workboxRightColumn">
				<h1>{$subscription.name}</h1>
				<p>{$subscription.description}</p>
			</div>
		</div>
	</div>
{/if}
<div class="workboxActionButtons">{if $subscription.price}{$lang.mediaLabelPrice}: <span class="price" id="workboxItemPrice">{$subscription.price.display}</span>{if $subscription.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}{if $subscription.credits}&nbsp;&nbsp;{$lang.mediaLabelCredits}: <span class="price" id="workboxItemCredits">{$subscription.credits}</span>{/if}{if !$noAccess and !$edit and $cartStatus}<br><input type="button" value="{$lang.addToCart}" id="workboxAddToCart" class="btn btn-xs btn-primary">{/if}</div>
</form>