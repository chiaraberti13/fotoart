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
					
					<h1>{$lang.orders}</h1>
					<hr>
					{if $orderRows}
						<table class="dataTable">
							<tr>
								<th style="text-align: center">{$lang.orderNumUpper}</th>
								<th>{$lang.orderDateUpper}</th>
								<th>{$lang.billHeaderTotal}</th>
								<th style="text-align: center">{$lang.paymentUpper}</th>
								<th style="text-align: center">{$lang.billHeaderStatus}</th>
								<th style="text-align: center">{$lang.billHeaderInvoice}</th>
							</tr>
							{foreach $ordersArray as $key => $order}
								<tr>
									<td style="text-align: center"><a href="{linkto page="order.details.php?orderID={$order.uorder_id}"}" target="_blank" class="colorLink">{$order.order_number}</a></td>
									<td>{$order.order_date_display}</td>
									<td>{if $order.total.raw  > 0}{$order.total.display}<br>{/if}{if $order.credits_total   > 0}{$order.credits_total} <span class="credits">{$lang.credits}</span>{/if}</td>
									<td style="text-align: center"><span class="highlightValue_{$order.order_payment_lang}">{$lang.{$order.order_payment_lang}}</span></td>
									<td style="text-align: center"><span class="highlightValue_{$order.order_status_lang}">{$lang.{$order.order_status_lang}}</span></td>
									<td style="text-align: center"><a href="{linkto page="invoice.php?orderID={$order.uorder_id}"}" target="_blank" class="btn btn-xs btn-primary">{$lang.view}</a></td>
								</tr>
							{/foreach}
						</table>
					{else}
						<p class="notice">{$lang.noOrders}</p>
					{/if}
					
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>