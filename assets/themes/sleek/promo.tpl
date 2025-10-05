<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.js"></script>
<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.items.js"></script>
<form class="cleanForm" id="workboxItemForm">
<img src="{$imgPath}/close.button.png" id="closeWorkbox">
{if $noAccess}
	<p class="notice">{$lang.noAccess}</p>
{else}
	<div class="divTable workboxItemTable">
		<div class="divTableRow">
			{if $promo.photo}
				<div class="divTableCell workboxLeftColumn">
					<p id="mainShotContainer"><img src="{productShot itemID=$promo.promo_id itemType=promo photoID=$promo.photo.id size=300}" id="mainShot"></p>
					{if $promo.photos|count > 1}
						<p id="additionalShots">
						{foreach $promo.photos as $key => $value}
							<a href="{productShot itemID=$promo.promo_id itemType=promo photoID=$value.id size=300}"><img src="{productShot itemID=$promo.promo_id itemType=promo photoID=$value.id size=70 crop=50}"></a>
						{/foreach}
						</p>
					{/if}
				</div>
			{else}
				<div class="divTableCell workboxLeftColumn" style="margin: 0; padding: 0;"></div>
			{/if}
			<div class="divTableCell workboxRightColumn">
				<h1>{$promo.name}</h1>
				<p>{$promo.description}</p>
				{if $promo.autoapply}<br><br><span class="promoUseB">*{$lang.autoApply}</span>{elseif $promo.promo_code}<br><br><span class="promoUseB">*{$lang.useCoupon}</span>: <span class="promoUseC">{$promo.promo_code}</span>{/if}
				{if $promo.oneuse and !$loggedIn}<br><br><span class="promoUseB">*{$lang.couponLoginWarn}</span>{/if}
			</div>
		</div>
	</div>
{/if}
<div class="workboxActionButtons">{if !$promo.autoapply}<p class="moreInfo"><a href="{linkto page="cart.php?cartMode=applyCouponCode&couponCode={$promo.promo_code}"}" class="buttonLink">{$lang.apply}</a></p>{/if}</div>
</form>