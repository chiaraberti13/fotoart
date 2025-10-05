<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		{include file='header2.tpl'}
		<div class="container">
			<h1>{if $collection.photo}<img src="{productShot itemID=$collection.coll_id itemType=coll photoID=$collection.photo.id size=70}" class="h1PhotoHeader">{/if} {$collection.name}</h1>
			<p class="h1PriceCart">
				{if $collection.price}{$lang.mediaLabelPrice}: <span class="price">{$collection.price.display}</span>{if $collection.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if} {if $collection.credits}&nbsp;&nbsp;{$lang.mediaLabelCredits}:<span class="price">{$collection.credits}</span>{/if}
				{if $cartStatus}&nbsp;&nbsp;&nbsp;<a href="{$collection.addToCartLink}" class="btn btn-xs btn-primary" rel="nofollow">{$lang.addToCart}</a>{/if}
			</p>
			<p>{$collection.description}</p>			
			{if $mediaRows}
				{include file="paging.tpl" paging=$mediaPaging}
				<div id="mediaListContainer">
					{foreach $mediaArray as $media}
						{include file='media.container.tpl'}
					{/foreach}
				</div>
				{include file="paging.tpl" paging=$mediaPaging}
			{else}
				<p class="notice">{$lang.noMedia}</p>
			{/if}
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>