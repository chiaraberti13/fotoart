<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		<div class="divTable contentContainer">
			<div class="divTableRow">
				<div class="divTableCell contentLeftColumn">
					{include file='memnav.tpl'}
				</div>
				<div class="divTableCell contentRightColumn">
					<div class="content">
						<h1>{$lang.subscriptions}</h1>
						
						{if $config.settings.subpage and $config.settings.subscriptions}<a href="{linkto page="featured.php?mode=subscriptions"}" class="buttonLink" style="float: right; margin-bottom: 10px;">{$lang.purchaseSub}</a>{/if}
						
						{if $memsubRows}
							<table class="dataTable">
								<tr>
									<th style="text-align: center">{$lang.subHeaderID}</th>
									<th>{$lang.subHeaderSubscript}</th>
									<th>{$lang.subHeaderExpires}</th>
									<th style="text-align: center">DOWNLOADS REMAINING</th>
									<th style="text-align: center">{$lang.subHeaderStatus}</th>
									<th style="text-align: center"></th>
								</tr>
								{foreach $memsubArray as $key => $memsub}
									<tr>
										<td style="text-align: center">{$memsub.msub_id}</td>
										<td class="workboxLinkAttach">{if $memsub.active}<a href="{$memsub.linkto}" class="workboxLink colorLink">{$memsub.name}</a>{else}{$memsub.name}{/if}</td>
										<td>{if $memsub.expired}<span class="highlightValue">{$memsub.expire_date_display}</span>{else}{$memsub.expire_date_display}{/if}</td>
										<td style="text-align: center">
											{if $memsub.total_downloads}
												{$memsub.totalRemaining}
											{else}
												{$lang.unlimited}
											{/if}
											<span>{if $memsub.perday} ({$memsub.todayRemaining} {$lang.today})</span>{/if}
										</td>
										<td style="text-align: center"><span class="highlightValue_{$memsub.status_lang}">{$lang.{$memsub.status_lang}}</span></td>
										<td style="text-align: center" class="workboxLinkAttach">{if $memsub.active}<a href="{$memsub.linkto}" class="workboxLink buttonLink">{$lang.renew}</a>{/if}</td>
									</tr>
								{/foreach}
							</table>
						{else}
							<p class="notice">{$lang.noSubs}</p>
						{/if}
					</div>
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>