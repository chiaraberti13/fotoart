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
						<h1>{$lang.welcome} {$member.f_name} {$member.l_name}</h1>
						
						<ul class="accountInfoList">
							<li class="infoHeader">{$lang.notices}</li>
							<li>{$lang.lastLogin}: <strong>{$lastLoginDisplay}</strong></li>
							{if $ticketSystem and $tickets}<li><span class="highlightValue">{$tickets}</span> {$lang.newTicketsMessage} <a href="{linkto page="tickets.php"}" class="colorLink">[{$lang.view}]</a></li>{/if}
							<!--<li><span class="highlightValue">0</span> {$lang.newSales} <a href="{linkto page="contr.sales.php"}" class="colorLink">[{$lang.view}]</a></li>-->
							{if $bills}<li><span class="highlightValue">{$bills}</span> {$lang.unpaidBills} <a href="{linkto page="bills.php"}" class="colorLink">[{$lang.view}]</a></li>{/if}
							{if $member.membership != 1 and $membership.msExpired}<li><span class="highlightValue">Expired</span> - {$lang.msExpired}: <strong>{$membership.name}</strong> <a href="{linkto page="account.edit.php?mode=membership"}" class="colorLink accountInfoWorkbox">[{$lang.renew}]</a></li>{/if}
						</ul>
						
						{if $memberSpecGallery}
							<ul class="accountInfoList">
								<li class="infoHeader">{$lang.myGalleries}</li>
								{foreach $memberSpecGallery as $gallery}
									<li><a href="{$gallery.linkto}">{$gallery.name}</a></li>
								{/foreach}
								</li>
							</ul>
						{/if}
						
						{if $member.membership != 1}
							<ul class="accountInfoList">
								<li class="infoHeader">{$lang.membership}</li>
								<li><a href="{linkto page="membership.php?id={$membership.ums_id}"}" class="membershipWorkbox"><strong>{$membership.name}</strong></a> {if $membership.msExpired}<!--<span class="highlightValue">(expired)</span>--> <a href="{linkto page="account.edit.php?mode=membership"}" class="colorLink accountInfoWorkbox">[{$lang.renew}]</a>{/if}</li>
								<li><strong>{$lang.expires}:</strong> {if $membership.msExpired}<span class="highlightValue">{$membership.msExpireDate}</span>{else}{$membership.msExpireDate}{/if}</li>
								<li class="editLink"><a href="{linkto page="account.edit.php?mode=membership"}" class="colorLink accountInfoWorkbox">[{$lang.edit}]</a></li>
							</ul>
						{/if}
						
						{if $member.membershipDetails.allow_selling}
							<ul class="accountInfoList">
								<li class="infoHeader">{$lang.contributors}</li>
								<li><span class="highlightValue">{$sales}</span> {$lang.newSales} <a href="{linkto page="contributor.sales.php"}" class="colorLink">[{$lang.view}]</a></li>
							</ul>
						{/if}
					</div>
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>