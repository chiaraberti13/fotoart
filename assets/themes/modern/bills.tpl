<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/bills.js"></script>	
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
					
					<h1>{$lang.bills}</h1>
					<hr>
					{if $notice}
						<p class="notice" style="margin-bottom: 14px;">{$lang.{$notice}}</p>
					{/if}
					
					{if $billRows}
						<table class="dataTable">
							<tr>
								<th>{$lang.billHeaderInvoice}</th>
								<th>{$lang.billHeaderDate}</th>
								<th>{$lang.billHeaderDueDate}</th>
								<th style="text-align: center">{$lang.billHeaderTotal}</th>
								<th style="text-align: center">{$lang.billHeaderStatus}</th>
								<th></th>
							</tr>
							{foreach $billsArray as $key => $bill}
								<tr>
									<td><a href="{linkto page="invoice.php?billID={$bill.ubill_id}"}" target="_blank" class="colorLink">{$bill.invoice_number}</a></td>
									<td>{$bill.invoice_date_display}</td>
									<td>{if $bill.past_due}<span class="highlightValue">{$bill.due_date_display}</span>{else}{$bill.due_date_display}{/if}</td>
									<td style="text-align: center">{$bill.total.display}</td>
									<td style="text-align: center"><span class="highlightValue_{$bill.payment_status_lang}">{$lang.{$bill.payment_status_lang}}</span></td>
									<td>{if $bill.payment_status == 2}<input type="button" value="{$lang.pay}" billID="{$bill.ubill_id}" class="payButton btn btn-xs btn-success">{/if}</td>
								</tr>
							{/foreach}
						</table>
					{else}
						<p class="notice">{$lang.noBills}</p>
					{/if}
					
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>