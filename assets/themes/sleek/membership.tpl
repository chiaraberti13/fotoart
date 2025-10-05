<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.js"></script>
<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.items.js"></script>
<form class="cleanForm" id="workboxItemForm">
<img src="{$imgPath}/close.button.png" id="closeWorkbox">
{if $noAccess}
	<p class="notice">{$lang.noAccess}</p>
{else}
	<div class="divTable workboxItemTable">
		<div class="divTableRow">
			<div class="divTableCell workboxRightColumn membershipDetails" style="text-align: left;">
				<h1>{$membership.name}</h1>
				<p>{$membership.description}</p>
			</div>
		</div>
	</div>
	<div class="workboxActionButtons">
		<p style="text-align: left">
			{if $membership.mstype == 'free'}{$lang.mediaLabelPrice}: <span class="price">{$lang.free}</span>{/if}
			
			{if $membership.trail_status}{$lang.freeTrial}: <span class="price {if $membership.trialUsed}strike{/if}">{$membership.trial_length_num} {$lang.{$membership.trial_length_period}}</span><br>{/if}
			
			{if $membership.setupfee}{$lang.setupFee}: <span class="price">{$membership.setupfee.display}</span>{if $membership.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}<br>{/if}
			
			{if $membership.mstype == 'recurring'}{$lang.mediaLabelPrice}: <span class="price">{$membership.price.display}</span> {$lang.{$membership.period}}{if $membership.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}
		</p>
	</div>
{/if}
</form>