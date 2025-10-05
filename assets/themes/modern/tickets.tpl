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
					
					<h1>{$lang.supportTickets}</h1>
					<hr>
					<a href="{linkto page="ticket.new.php"}" class="btn btn-xs btn-success" style="float: right; margin-bottom: 10px;">{$lang.newTicketButton}</a>
					
					{if $ticketRows}
						<table class="dataTable">
							<tr>
								<th style="text-align: center">{$lang.id|upper}</th>
								<th>{$lang.summary|upper}</th>
								<th>{$lang.lastUpdated|upper}</th>
								<th style="text-align: center">{$lang.status|upper}</th>
							</tr>
							{foreach $ticketsArray as $key => $ticket}
								<tr>
									<td style="text-align: center;"><a href="{linkto page="ticket.details.php?id={$ticket.ticket_id}"}" class="colorLink">{$ticket.ticket_id}</a></td>
									<td>{if !$ticket.viewed}<span class="highlightValue">*</span> {/if}<a href="{linkto page="ticket.details.php?id={$ticket.ticket_id}"}">{$ticket.summary|truncate:60}</a></td>
									<td class="ticketUpdated">{$ticket.lastupdated} <br>{$lang.by} <span>{if $ticket.updatedby}{$config.settings.business_name}{else}{$member.f_name} {$member.l_name}{/if}</span></td>
									<td style="text-align: center">{if $ticket.status == 1 or $ticket.status == 2}<strong>{$lang.open}</strong>{else}<span class="highlightValue">{$lang.closed}</span>{/if}</td>
								</tr>
							{/foreach}
						</table>
						<p style="margin-top: 10px; color: #888"><span class="highlightValue">*</span> {$lang.ticketUnreadMes}</p>
					{else}
						<p class="notice">{$lang.ticketUnderAccount}</p>
					{/if}
					
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>