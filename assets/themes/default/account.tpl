<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript">
		<!--
			$(function()
			{
				
			});
		-->
	</script>
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
						<h1>{$lang.accountInfo}</h1>
						{if $notice == 'accountUpdated'}
							<p class="notice">{$lang.accountUpdated}</p>
						{/if}
						<ul class="accountInfoList">
							<li><strong>{$lang.signupDate}:</strong> {$signupDateDisplay}</li>
							<li><strong>{$lang.lastLogin}:</strong> {$lastLoginDisplay}</li>
						</ul>
						
						<ul class="accountInfoList">
							<li class="infoHeader">{$lang.generalInfo}</li>
							<li><strong>{$lang.name}:</strong> {$member.f_name} {$member.l_name}</li>
							<li><strong>{$lang.email}:</strong> {$member.email}</li>
							<li><strong>{$lang.displayName}:</strong> {$member.display_name}</li>
							<li><strong>{$lang.companyName}:</strong> {$member.comp_name}</li>
							<li><strong>{$lang.website}:</strong> {$member.website}</li>
							<li><strong>{$lang.phone}:</strong> {$member.phone}</li>
							<li class="editLink"><a href="{linkto page="account.edit.php?mode=personalInfo"}" class="colorLink accountInfoWorkbox">[{$lang.edit}]</a></li>
						</ul>
						
						<ul class="accountInfoList">
							<li class="infoHeader">{$lang.address}</li>
							<li>
								{$member.primaryAddress.address}<br>
								{if $member.primaryAddress.address_2}{$member.primaryAddress.address_2}<br>{/if}
								{$member.primaryAddress.city}, {$member.primaryAddress.state} {$member.primaryAddress.postal_code}<br>
								{$member.primaryAddress.country}
							</li>
							<li class="editLink"><a href="{linkto page="account.edit.php?mode=address"}" class="colorLink accountInfoWorkbox">[{$lang.edit}]</a></li>
						</ul>
						
						<ul class="accountInfoList">
							<li class="infoHeader">{$lang.membership}</li>
							<li><a href="{linkto page="membership.php?id={$membership.ums_id}"}" class="membershipWorkbox"><strong>{$membership.name}</strong></a> {if $membership.msExpired}<span class="highlightValue">(expired)</span> <a href="{linkto page="account.edit.php?mode=membership"}" class="colorLink accountInfoWorkbox">[{$lang.renew}]</a>{/if}</li>
							<li><strong>{$lang.expires}:</strong> {if $membership.msExpired}<span class="highlightValue">{$membership.msExpireDate}</span>{else}{$membership.msExpireDate}{/if}</span></li>
							<li class="editLink"><a href="{linkto page="account.edit.php?mode=membership"}" class="colorLink accountInfoWorkbox">[{$lang.edit}]</a></li>
						</ul>
						
						<ul class="accountInfoList">
							<li class="infoHeader">{$lang.preferences}</li>
							{if $displayLanguages|@count > 1}
								<li>
									<strong>{$lang.preferredLang}:</strong> 
									<select id="languageSelector">
										{html_options options=$displayLanguages selected=$selectedLanguage}
									</select>
								</li>
							{/if}
							{if $displayCurrencies|@count > 1}
								<li>
									<strong>{$lang.preferredCurrency}:</strong> 
									<select id="currencySelector">
										{html_options options=$displayCurrencies selected=$selectedCurrency}
									</select>
								</li>
							{/if}
							{if $config.settings.dt_member_override}
								<li><strong>{$lang.dateTime}:</strong> {$exampleDateDisplay}</strong> <a href="{linkto page="account.edit.php?mode=dateTime"}" class="colorLink accountInfoWorkbox">[{$lang.edit}]</a></li>
							{/if}
							{if $member.membershipDetails.allow_selling or $member.membershipDetails.allow_uploads}
								<li>
									<strong>{$lang.batchUploader}:</strong> {$lang.uploader.{$member.uploader}} <a href="{linkto page="account.edit.php?mode=batchUploader"}" class="colorLink accountInfoWorkbox">[{$lang.edit}]</a>
								</li>
							{/if}
							<li class="editLink">&nbsp;</li>
						</ul>
						
						<ul class="accountInfoList">
							<li class="infoHeader">{$lang.actions}</li>
							<li><a href="{linkto page="account.edit.php?mode=password"}" class="colorLink accountInfoWorkbox">{$lang.changePass}</a></li>
							{if $member.membershipDetails.avatar}<li><a href="{linkto page="account.edit.php?mode=avatar"}" class="colorLink accountInfoWorkbox">{$lang.changeAvatar}</a></li>{/if}
							<li class="editLink">&nbsp;</li>
						</ul>
						
						{* Member Bio *}
						{if $member.membershipDetails.bio}
							<ul class="accountInfoList">
								<li class="infoHeader">{$lang.bio}</li>
								<li>{if $member.bio_content}{$member.bio_content}{else}{$lang.none}{/if}</li>
								<li class="editLink"><a href="{linkto page="account.edit.php?mode=bio"}" class="colorLink accountInfoWorkbox">[{$lang.edit}]</a></li>
							</ul>
						{/if}
						
						{if $member.membershipDetails.allow_selling}	
							<ul class="accountInfoList">
								<li class="infoHeader">{$lang.contributorSettings}</li>
								<li><strong>{$lang.commissionMethod}</strong>: {$commissionTypeName}</li>
								<li><strong>{$lang.commission}</strong>: {$member.com_level}%</li>
								<li class="editLink"><a href="{linkto page="account.edit.php?mode=commission"}" class="colorLink accountInfoWorkbox">[{$lang.edit}]</a></li>
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