<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.js"></script>
<script type="text/javascript" src="{$baseURL}/assets/javascript/workbox.private.js"></script>
<form class="cleanForm" method="post" action="actions.php" id="workboxForm" enctype="multipart/form-data">
<img src="{$imgPath}/close.button.png" id="closeWorkbox">
<input type="hidden" value="updateAccountInfo" name="action" id="action">
<input type="hidden" value="{$mode}" name="mode" id="mode">
<input type="hidden" value="{$member.umem_id}" name="umem_id" id="umem_id">
<input type="hidden" value="{$member.mem_id}" name="mem_id" id="mem_id">
<input type="hidden" value="{$member.membershipDetails.ms_id}" name="membership_id" id="membership_id">
{if $noAccess}
	<p class="notice">{$lang.noAccess}</p>
{else}
	<div id="editWorkbox">
		<h1>{$lang.editAccountInfo}</h1>
		<p>{$lang.editAccountInfoMes}</p>
		
		{* Edit personal information fields *}
		{if $mode == 'personalInfo'}
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.firstName}:</div>
					<div class="divTableCell"><input type="text" name="f_name" id="f_name" value="{$member.f_name}" require="require" errorMessage="{$lang.required}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.lastName}:</div>
					<div class="divTableCell"><input type="text" name="l_name" id="l_name" value="{$member.l_name}" require="require" errorMessage="{$lang.required}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.displayName}:</div>
					<div class="divTableCell"><input type="text" name="display_name" id="display_name" value="{$member.display_name}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.email}:</div>
					<div class="divTableCell"><input type="text" name="email" id="email" value="{$member.email}" require="require" errorMessage="{$lang.required}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.companyName}:</div>
					<div class="divTableCell"><input type="text" name="comp_name" id="comp_name" value="{$member.comp_name}" {if $regForm.formCompanyName.status == 2}require="require"{/if} errorMessage="{$lang.required}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.phone}:</div>
					<div class="divTableCell"><input type="text" name="phone" id="phone" value="{$member.phone}" {if $regForm.formPhone.status == 2}require="require"{/if} errorMessage="{$lang.required}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.website}:</div>
					<div class="divTableCell"><input type="text" name="website" id="website" value="{$member.website}" {if $regForm.formWebsite.status == 2}require="require"{/if} errorMessage="{$lang.required}"></div>
				</div>
			</div>
		{/if}
		
		{if $mode == 'batchUploader'}
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.batchUploader}:</div>
					<div class="divTableCell">
						<select id="batchUploader" name="batchUploader">
							<option value="1" {if $member.uploader == 1}selected="selected"{/if}>{$lang.uploader.1}</option>
							<option value="2" {if $member.uploader == 2}selected="selected"{/if}>{$lang.uploader.2}</option>
						</select>
					</div>
				</div>
			</div>
		{/if}
		
		{* Edit bio *}
		{if $mode == 'bio'}
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.bio}:</div>
					<div class="divTableCell"><textarea style="width: 700px; height: 230px;" name="bio_content">{$bio}</textarea></div>
				</div>
			</div>
		{/if}
				
		{* Edit address *}
		{if $mode == 'address'}
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.country}:</div>
					<div class="divTableCell">
						<select id="country" name="country" {if $regForm.formAddress.status == 2}require="require"{/if} errorMessage="{$lang.required}" style="width: 264px;">
							<option value=''></option>
							{html_options options=$countries selected=$selectedCountry}
						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.address}:</div>
					<div class="divTableCell"><input type="text" name="address" id="address" value="{$member.primaryAddress.address}" {if $regForm.formAddress.status == 2}require="require"{/if} errorMessage="{$lang.required}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"></div>
					<div class="divTableCell"><input type="text" name="address_2" id="address_2" value="{$member.primaryAddress.address_2}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.city}:</div>
					<div class="divTableCell"><input type="text" name="city" id="city" value="{$member.primaryAddress.city}" {if $regForm.formAddress.status == 2}require="require"{/if} errorMessage="{$lang.required}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.state}:</div>
					<div class="divTableCell" id="stateCell">
						<select id="state" name="state" {if $regForm.formAddress.status == 2}require="require"{/if} errorMessage="{$lang.required}" style="width: 264px;">
							<option></option>
							{html_options options=$states selected=$selectedState}
						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.zip}:</div>
					<div class="divTableCell"><input type="text" name="postal_code" id="postal_code" value="{$member.primaryAddress.postal_code}" {if $regForm.formAddress.status == 2}require="require"{/if} errorMessage="{$lang.required}"></div>
				</div>
			</div>
		{/if}
		
		{* Edit date/time preferences *}
		{if $mode == 'dateTime'}
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.timeZone}:</div>
					<div class="divTableCell">
						<select id="timeZone" name="timeZone" style="width: 120px;">
							{html_options options=$timeZone selected=$member.time_zone}
						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.dateFormat}:</div>
					<div class="divTableCell">
						<select id="dateFormat" name="dateFormat" style="width: 120px;">
							{html_options options=$dateFormat selected=$member.date_format}
						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.dateDisplay}:</div>
					<div class="divTableCell">
						<select id="dateDisplay" name="dateDisplay" style="width: 120px;">
							{html_options options=$dateDisplay selected=$member.date_display}
						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.clockFormat}:</div>
					<div class="divTableCell">
						<select id="clockFormat" name="clockFormat" style="width: 120px;">
							{html_options options=$clockFormat selected=$member.clock_format}
						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.numberDateSep}:</div>
					<div class="divTableCell">
						<select id="numberDateSep" name="numberDateSep" style="width: 120px;">
							{html_options options=$numberDateSep selected=$member.number_date_sep}
						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.daylightSavings}:</div>
					<div class="divTableCell"><input type="checkbox" name="daylightSavings" value="1" {if $member.daylight_savings}checked="checked"{/if}></div>
				</div>
			</div>
		{/if}
		
		{* Edit password *}
		{if $mode == 'password'}
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.currentPass}:</div>
					<div class="divTableCell"><input type="password" name="currentPass" id="currentPass" value="" require="require" errorMessage="{$lang.required}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.newPass}:</div>
					<div class="divTableCell"><input type="password" name="newPass" id="newPass" value="" require="require" errorMessage="{$lang.required}" errorMessage2="{$lang.accountInfoError1}" errorMessage3="{$lang.accountInfoError2}"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel">{$lang.vNewPass}:</div>
					<div class="divTableCell"><input type="password" name="vNewPass" id="vNewPass" value="" require="require" errorMessage="{$lang.required}" errorMessage2="{$lang.accountInfoError1}"></div>
				</div>
			</div>
		{/if}
		
		{* Edit avatar *}
		{if $mode == 'avatar'}
			<input type="hidden" name="maxAvatarFileSize" id="maxAvatarFileSize" value="{$maxAvatarFileSize}">
			<input type="hidden" name="fileExt" id="fileExt" value="{$fileExt}">			
			<input type="hidden" name="securityTimestamp" id="securityTimestamp" value="{$securityTimestamp}">
			<input type="hidden" name="securityToken" id="securityToken" value="{$securityToken}">
			
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.avatar}:</div>
					<div class="divTableCell">
						<ul>
							<li><img src="{memberAvatar memID=$member.mem_id size=150}" class="memberAvatar" id="editorAvatar"></li>
							<li id="avatarUploadContainer">
								<div id="avatarUploaderDiv" style="position: relative; margin-top: 10px;">
									<input id="avatarUploader" name="avatarUploader" type="file" buttonText="{$lang.uploadAvatar}">
									<!--<a href="" style="position: absolute; top: 10px; z-index: -1;" class="buttonLink">{$lang.uploadAvatar}</a>-->
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="divTableRow" {if !$member.avatar}style="display: none;"{/if} id="avatarDeleteDiv">
					<div class="divTableCell formFieldLabel">{$lang.delete}:</div>
					<div class="divTableCell"><input type="checkbox" name="delete" value="1" ></div>
				</div>
			</div>
		{/if}
		
		{* Edit MS *}
		{if $mode == 'membership'}
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.membership}:</div>
					<div class="divTableCell">
						{if $memberships|@count > 0}
							<ul class="membershipList" style="margin-left: 10px;">
								{foreach $memberships as $key => $membership}
									<li>
										<input type="radio" name="membership" id="membership_{$membership.ms_id}" class="membershipRadios" value="{$membership.ums_id}" {if $membership.ums_id == $selectedMembership}checked="checked"{/if}><label for="membership_{$membership.ms_id}"><strong>{$membership.name}</strong></label>
										<p class="membershipDetails">{if $membership.description}{$membership.description}<br>{/if}</p>
										<p class="membershipPriceDetails">
											{if $membership.mstype == 'free'}{$lang.mediaLabelPrice}: <span class="price">{$lang.free}</span>{/if}			
											{if $membership.trail_status}{$lang.freeTrial}: <span class="price {if $membership.trialUsed}strike{/if}">{$membership.trial_length_num} {$lang.{$membership.trial_length_period}}</span><br>{/if}											
											{if $membership.setupfee}{$lang.setupFee}: <span class="price {if $membership.feePaid}strike{/if}">{$membership.setupfee.display}</span>{if $membership.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}<br>{/if}											
											{if $membership.mstype == 'recurring'}{$lang.mediaLabelPrice}: <span class="price">{$membership.price.display}</span> {$lang.{$membership.period}}{if $membership.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if}
										</p>
									</li>
								{/foreach}
							</ul>
						{/if}
					</div>
				</div>
			</div>
		{/if}
		
		{* Edit commission *}
		{if $mode == 'commission'}
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel" style="vertical-align: top">{$lang.commissionMethod}:</div>
					<div class="divTableCell">
						<ul>
							{if $commissionTypes.paypal}<li><input type="radio" name="commissionType" value="1" id="commissionTypePayPal" {if $member.compay == 1}checked="checked"{/if}> <label for="commissionTypePayPal">{$lang.paypal}</label><br><div style="padding: 10px; background-color: #333; margin: 10px; color: #999; {if $member.compay != 1}display: none;{/if}" id="commissionPayPalEmail">{$lang.paypalEmail} <input type="text" name="paypalEmail" value="{$member.paypal_email}" style="width: 50px;"></div></li>{/if}
							{if $commissionTypes.check}<li><input type="radio" name="commissionType" value="2" id="commissionTypeCheck" {if $member.compay == 2}checked="checked"{/if}> <label for="commissionTypeCheck">{$lang.checkMO}</label></li>{/if}
							{if $commissionTypes.other}<li><input type="radio" name="commissionType" value="3" id="commissionTypeOther" {if $member.compay == 3}checked="checked"{/if}> <label for="commissionTypeOther">{$commissionTypes.otherName}</label></li>{/if}
						</ul>
					</div>
				</div>
			</div>
		{/if}
		
	</div>
{/if}
<div class="workboxActionButtons"><input type="button" value="{$lang.save}" id="saveWorkboxForm"></div>
</form>