<!DOCTYPE HTML>
<html>
<head>
	{include file='head.tpl'}
	<script type="text/javascript" src="{$baseURL}/assets/javascript/create.account.js"></script>
	<script>
		<!--
			$(function()
			{		
				/*
				* Display errors on fields with notices
				*/
				{if in_array('emailBlocked',$formNotice)}		displayFormError('#email',3);						{/if}
				{if in_array('emailExists',$formNotice)}		displayFormError('#email',2);						{/if}				
				{if in_array('noFirstName',$formNotice)}		displayFormError('#f_name','');						{/if}				
				{if in_array('noLastName',$formNotice)}			displayFormError('#l_name','');						{/if}				
				{if in_array('noEmail',$formNotice)}			displayFormError('#email','');						{/if}
				{if in_array('noCompName',$formNotice)}			displayFormError('#comp_name','');					{/if}
				{if in_array('noPhone',$formNotice)}			displayFormError('#phone','');						{/if}
				{if in_array('noWebsite',$formNotice)}			displayFormError('#website','');					{/if}
				{if in_array('noCountry',$formNotice)}			displayFormError('#country','');					{/if}				
				{if in_array('noAddress',$formNotice)}			displayFormError('#address','');					{/if}
				{if in_array('noCity',$formNotice)}				displayFormError('#city','');						{/if}
				{if in_array('noPostalCode',$formNotice)}		displayFormError('#postal_code','');				{/if}
				{if in_array('noPassword',$formNotice)}			displayFormError('#password','');					{/if}
				{if in_array('shortPassword',$formNotice)}		displayFormError('#password',3);					{/if}
				{if in_array('noSignupAgreement',$formNotice)}	displayFormError('#signupAgreement','');			{/if}
				{if in_array('captchaError',$formNotice)}		displayFormError('#recaptcha_response_field',2);	{/if}
				
			});
		-->
	</script>
</head>
<body>
	{include file='overlays.tpl'}
	<div id="container">
		{include file='header.tpl'}
		{include file='header2.tpl'}		
		
		<div class="container">
			<div class="row">
				{include file='subnav.tpl'}		
				<div class="col-md-9">
				
					<div class="content">
						<h1>{$lang.createAccount}</h1>
						<hr>
						{$lang.createAccountMessage}
						<form id="createAccountForm" class="cleanForm form-group" action="create.account.php" method="post">	
						<input type="hidden" name="showMemberships" value="{$showMemberships}">
						<input type="hidden" name="msID" value="{$msID}">					
						<h2 class="infoHeader">{$lang.generalInfo}</h2>
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel"><span class="requiredMark">*</span> {$lang.firstName}:</div>
								<div class="divTableCell"><input type="text" id="f_name" name="f_name" value="{$form.f_name}" require="require" errorMessage="{$lang.required}" style="width: 306px;" class="form-control"></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel"><span class="requiredMark">*</span> {$lang.lastName}:</div>
								<div class="divTableCell"><input type="text" id="l_name" name="l_name" value="{$form.l_name}" require="require" errorMessage="{$lang.required}" class="form-control"></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel"><span class="requiredMark">*</span> {$lang.email}:</div>
								<div class="divTableCell"><input type="text" id="email" name="email" value="{$form.email}" require="require" errorMessage="{$lang.required}" errorMessage2="{$lang.accountInfoError12}" errorMessage3="{$lang.accountInfoError13}" class="form-control"></div>
							</div>
							{if $regForm.formPhone.status}
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel">{if $regForm.formPhone.status == 2}<span class="requiredMark">*</span> {/if}{$lang.phone}:</div>
								<div class="divTableCell"><input type="text" id="phone" name="phone" value="{$form.phone}" {if $regForm.formPhone.status == 2}require="require"{/if} errorMessage="{$lang.required}" class="form-control"></div>
							</div>
							{/if}
							{if $regForm.formCompanyName.status}
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{if $regForm.formCompanyName.status == 2}<span class="requiredMark">*</span> {/if}{$lang.companyName}:</div>
									<div class="divTableCell"><input type="text" id="comp_name" name="comp_name" value="{$form.comp_name}" {if $regForm.formCompanyName.status == 2}require="require"{/if} errorMessage="{$lang.required}" class="form-control"></div>
								</div>
							{/if}
							{if $regForm.formWebsite.status}
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{if $regForm.formWebsite.status == 2}<span class="requiredMark">*</span> {/if}{$lang.website}:</div>
									<div class="divTableCell"><input type="text" id="website" name="website" value="{$form.website}" {if $regForm.formWebsite.status == 2}require="require"{/if} errorMessage="{$lang.required}" class="form-control"></div>
								</div>
							{/if}
						</div>
						{if $regForm.formAddress.status}
							<br><h2 class="infoHeader">{$lang.address}</h2>
							<div class="divTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{if $regForm.formAddress.status == 2}<span class="requiredMark">*</span> {/if}{$lang.country}:</div>
									<div class="divTableCell">
										<select id="country" name="country" style="width: 306px;" class="form-control" {if $regForm.formAddress.status == 2}require="require"{/if} errorMessage="{$lang.required}">
											<option></option>
											{html_options options=$countries selected=$form.country}
										</select>
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{if $regForm.formAddress.status == 2}<span class="requiredMark">*</span> {/if}{$lang.address}:</div>
									<div class="divTableCell">
										<input type="text" id="address" name="address" {if $regForm.formAddress.status == 2}require="require"{/if} value="{$form.address}" errorMessage="{$lang.required}" class="form-control">
										<input type="text" name="address_2" id="address_2" value="{$form.address_2}" style="margin-top: 6px;" class="form-control">
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{if $regForm.formAddress.status == 2}<span class="requiredMark">*</span> {/if}{$lang.city}:</div>
									<div class="divTableCell"><input type="text" id="city" name="city" value="{$form.city}" {if $regForm.formAddress.status == 2}require="require"{/if} errorMessage="{$lang.required}" class="form-control"></div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{if $regForm.formAddress.status == 2}<span class="requiredMark">*</span> {/if}{$lang.state}:</div>
									<div class="divTableCell">
										<select id="state" name="state" style="width: 306px;" class="form-control" {if $regForm.formAddress.status == 2}require="require"{/if} errorMessage="{$lang.required}" >
											<option>{$lang.chooseCountryFirst}</option>
											{if $states}
												{html_options options=$states selected=$form.state}
											{/if}
										</select>
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">{if $regForm.formAddress.status == 2}<span class="requiredMark">*</span> {/if}{$lang.zip}:</div>
									<div class="divTableCell"><input type="text" id="postal_code" name="postal_code" value="{$form.postal_code}" {if $regForm.formAddress.status == 2}require="require"{/if} errorMessage="{$lang.required}" class="form-control"></div>
								</div>								
							</div>
						{/if}
						<br><h2 class="infoHeader">{$lang.password}</h2>
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel"><span class="requiredMark">*</span> {$lang.password}:</div>
								<div class="divTableCell"><input type="password" id="password" name="password" require="require" errorMessage="{$lang.required}" errorMessage2="{$lang.accountInfoError1}" errorMessage3="{$lang.accountInfoError2}" style="width: 306px;" class="form-control"></div>
							</div>
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel"><span class="requiredMark">*</span> {$lang.verifyPass}:</div>
								<div class="divTableCell"><input type="password" id="vpassword" name="vpassword" require="require" errorMessage="{$lang.required}" class="form-control"></div>
							</div>
						</div>
						
						{if $config.settings.captcha}
						<br><h2 class="infoHeader"></h2>
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel" style="vertical-align: top; width: 114px;"><span class="requiredMark">*</span> {$lang.captcha}:</div>
								<div class="divTableCell captcha">{include file='captcha.tpl'}</div>
							</div>
						</div>
						{/if}
						
						{if $regForm.formSignupAgreement.status}
						<br><h2 class="infoHeader"></h2>
						<div class="divTable">
							<div class="divTableRow">
								<div class="divTableCell formFieldLabel" style="vertical-align: top; width: 114px;">{if $regForm.formSignupAgreement.status == 2}<span class="requiredMark">*</span> {/if}{$lang.agreements}:</div>
								<div class="divTableCell" style="padding-top: 14px;"><input type="checkbox" name="signupAgreement" id="signupAgreement" value="1" {if $regForm.formSignupAgreement.status == 2}require="require"{/if} errorMessage="{$lang.required}"> {$lang.readAgree} <a href="{linkto page="content.php?id=11"}" class="colorLink" target="_blank">{content id='signupAgreement' titleOnly=1}</a></div>
							</div>
						</div>
						{/if}
						
						{if $showMemberships}{*$memberships|@count > 1 and *}
							<br><h2 class="infoHeader">{$lang.membership}</h2>
							<ul class="membershipList">
								{foreach $memberships as $key => $membership}
									<li>
										<input type="radio" name="membership" id="membership_{$membership.ms_id}" value="{$membership.ums_id}" {if $membership.ums_id == $selectedMembership}checked="checked"{/if}><label for="membership_{$membership.ms_id}">{$membership.name}</label> <a href="membership.php?id={$membership.ums_id}" class="colorLink membershipWorkbox" style="float: right;">[{$lang.details|upper}]</a>
										<p class="membershipDetails">{if $membership.description}{$membership.description|truncate:300}<br>{/if}</p>
										<p class="membershipPriceDetails">
											{if $membership.mstype == 'free'}{$lang.mediaLabelPrice}: <span class="price">{$lang.free}</span>{/if}			
											{if $membership.trail_status}{$lang.freeTrial}: <span class="price">{$membership.trial_length_num} {$lang.{$membership.trial_length_period}}</span><br>{/if}											
											{if $membership.setupfee}{$lang.setupFee}: <span class="price">{$membership.setupfee.display}</span><br>{/if}											
											{if $membership.mstype == 'recurring'}{$lang.mediaLabelPrice}: <span class="price">{$membership.price.display}</span> {$lang.{$membership.period}}{/if}
										</p>
									</li>
								{/foreach}
							</ul>
						{/if}
						<p><span class="requiredMark">* {$lang.required}</span></p>	
						<input type="submit" value="{$lang.submit}" class="btn btn-xs btn-primary" style="float: right;">
						</form>
					</div>
				</div>
			</div>
		</div>
		{include file='footer.tpl'}
    </div>
</body>
</html>