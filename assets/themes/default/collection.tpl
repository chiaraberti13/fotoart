<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.js"></script>
<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.items.js"></script>
<form class="cleanForm" action="{linkto page='cart.php'}" id="workboxItemForm" method="post">
<img src="{$imgPath}/close.button.png" id="closeWorkbox">
<!-- For cart -->
<input type="hidden" name="mode" value="add" id="mode"> 
<input type="hidden" name="type" value="collection" id="type">
<input type="hidden" name="id" value="{$collection.useCollectionID}">
{if $edit}<input type="hidden" name="edit" value="{$edit}">{/if}

{if $noAccess}
	<p class="notice">{$lang.noAccess}</p>
{else}
	<div class="divTable workboxItemTable">
		<div class="divTableRow">
			{if $collection.photo}
				<div class="divTableCell workboxLeftColumn">
					<p id="mainShotContainer"><img src="{productShot itemID=$collection.coll_id itemType=coll photoID=$collection.photo.id size=300}" id="mainShot"></p>
					{if $collection.photos|count > 1}
						<p id="additionalShots">
						{foreach $collection.photos as $key => $value}
							<a href="{productShot itemID=$collection.coll_id itemType=coll photoID=$value.id size=300}"><img src="{productShot itemID=$collection.coll_id itemType=coll photoID=$value.id size=70 crop=50}"></a>
						{/foreach}
						</p>
					{/if}
				</div>
			{/if}
			<div class="divTableCell workboxRightColumn">
				<h1>{$collection.name}</h1>
				<p>{$collection.description}</p>
			</div>
		</div>
	</div>
{/if}
<div class="workboxActionButtons">
	{if $collection.price}{$lang.mediaLabelPrice}: <span class="price" id="workboxItemPrice">{$collection.price.display}</span>{if $collection.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}
	{if $collection.credits}&nbsp;&nbsp;{$lang.mediaLabelCredits}: <span class="price" id="workboxItemCredits">{$collection.credits}</span>{/if}
	<br><a href="{$collection.viewCollectionLink}" class="buttonLink">{$lang.viewCollection}</a>&nbsp;{if $cartStatus and !$noAccess and !$edit}<input type="button" value="{$lang.addToCart}" id="workboxAddToCart" class="colorButton">{/if}
</div>
</form>