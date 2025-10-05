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
			<div class="row">
				{include file='memnav.tpl'}		
				<div class="col-md-9">
					
					<h1>{$lang.contViewSales}</h1>
					<hr>
					{if $notice}
						<p class="notice" style="margin-bottom: 14px;">{$lang.{$notice}}</p>
					{/if}
					
					{if $saleRows}
						<table class="dataTable">
							<tr>
								<th style="text-align: center" class="hidden-xs">{$lang.id}</th>
								<th>{$lang.itemUpper}</th>
								<th>{$lang.orderDateUpper}</th>
								<th style="text-align: center">{$lang.commissionUpper}</th>
								<th style="text-align: center">{$lang.billHeaderStatus}</th>
							</tr>
							{foreach $salesArray as $key => $sale}
								<tr>
									<td style="text-align: center" class="hidden-xs">{$sale.com_id}</td>
									<td>{if $sale.media.encryptedID}<a href="media.details.php?mediaID={$sale.media.useMediaID}"><img src="{mediaImage mediaID=$sale.media.encryptedID type=thumb folderID=$sale.media.encryptedFID size=35}" class="genericImgBorder" style="vertical-align: middle; margin-right: 6px;"></a>{else}<img src="{$imgPath}/missing.photo.png" class="genericImgBorder" style="vertical-align: middle; margin-right: 6px; width: 35px;">{/if} <strong>{$sale.itemName}</strong> ({$sale.item_type})</td>
									<td>{$sale.orderDateDisplay}</td>
									<td style="text-align: center">{$sale.commissionDisplay}</td>
									<td style="text-align: center"><span class="highlightValue_{$sale.statusDisplayLang}">{$lang.{$sale.statusDisplayLang}}</span></td>
								</tr>
							{/foreach}
						</table>
						<div class="contrSalesTableFooter">{$lang.paid}: <span class="price">{$runningPaidTotalDisplay}</span> &nbsp;|&nbsp; {$lang.unpaid}: <span class="priceAlt">{$runningUnpaidTotalDisplay}</span></div>
					{else}
						<p class="notice">{$lang.noSales}</p>
					{/if}
					
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>