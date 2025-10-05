{if $downloadOrderID}
	<script type="text/javascript">
		$(function(){ assignDownloadButtonActions(); });
	</script>
{/if}

<!--Rows: {$packageItemsRows}-->
<!--{$invoiceItem.item_type}-->
{if $invoiceItem.item_type == 'package'}
	
	{if $invoiceItem.packagePrintsArray}
		<h2>{$lang.prints}</h2>
		<div class="divTable">	
		{foreach $invoiceItem.packagePrintsArray as $printKey => $print}
			<div class="divTableRow">
				<div class="divTableCell">{if $print.media}<img src="{mediaImage mediaID=$print.media.encryptedID type=thumb folderID=$print.media.encryptedFID size=40}">{else}<img src="{$imgPath}/blank.question.png">{/if}</div>
				<div class="divTableCell">
					<span class="mtag mtagDarkGrey">{$print.incQuantity}</span> {$print.name}<!--(DBID: {$print.oi_id})-->
					{if $print.options}
						<ul class="packageOptions">
							{foreach $print.options as $option}
								<li><span>{$option.groupName}</span>: {$option.name}</li>
							{/foreach}
						</ul>
					{/if}
				</div>
			</div>
		{/foreach}
		</div>
	{/if}
	
	{if $invoiceItem.packageProductsArray}
		<h2>{$lang.products}</h2>
		<div class="divTable">
		{foreach $invoiceItem.packageProductsArray as $productKey => $product}
			<div class="divTableRow">
				<div class="divTableCell">
					{if $product.product_type == '1'}
						{if $product.media}<img src="{mediaImage mediaID=$product.media.encryptedID type=thumb folderID=$product.media.encryptedFID size=40}">{else}<img src="{$imgPath}/blank.question.png">{/if}
					{/if}
				</div>
				<div class="divTableCell">
					<div class="mtag mtagDarkGrey">{$product.incQuantity}</div> {$product.name}<!--(DBID: {$product.oi_id})-->
					{if $product.options}
						<ul class="packageOptions">
							{foreach $product.options as $option}
								<li><span>{$option.groupName}</span>: {$option.name}</li>
							{/foreach}
						</ul>
					{/if}
				</div>
			</div>
		{/foreach}
		</div>
	{/if}
	
	{if $invoiceItem.packageCollectionsArray}
		<h2>{$lang.collections}</h2>
		<div class="divTable">
		{foreach $invoiceItem.packageCollectionsArray as $collectionKey => $collection}
			<div class="divTableRow">
				<div class="divTableCell"></div>
				<div class="divTableCell">
					<div class="mtag mtagDarkGrey">{$collection.incQuantity}</div> {$collection.name} <!-- (DBID: {$collection.oi_id})-->
					
					{if $collection.downloadKey}
						<!--{$collection.downloadKey}-->
						<input 
							type="button" 
							value="{$lang.downloadUpper}" 
							class="orderDownloadButton" 
							downloadableStatus="{$collection.downloadableStatus}" 
							key="{$collection.downloadKey}" 
							invoiceItemID="{$invoiceItemID}"  
						/>
					{/if}
				</div>
			</div>
		{/foreach}
		</div>
	{/if}
	
	{if $invoiceItem.packageSubscriptionsArray}
		<h2>{$lang.subscriptions}</h2>
		<div class="divTable">
		{foreach $invoiceItem.packageSubscriptionsArray as $subscriptionKey => $subscription}
			<div class="divTableRow">
				<div class="divTableCell"></div>
				<div class="divTableCell">
					<div class="mtag mtagDarkGrey">{$subscription.incQuantity}</div> {$subscription.name} <br> {$subscription.description}
				</div>
			</div>
		{/foreach}
		</div>
	{/if}
	
{else}
	{if $invoiceItem.optionsArray}
		<ul>
			{foreach $invoiceItem.optionsArray as $option}
				<li><span>{$option.groupName}</span>: {$option.name}</li>
			{/foreach}
		</ul>
	{/if}
{/if}