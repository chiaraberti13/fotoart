<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 11:36:59
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/account.edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:157107205168c6a8db958734-67752878%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0e8d1894cd3edab4f69eaa26d7d42085017e9dac' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/account.edit.tpl',
      1 => 1757796009,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '157107205168c6a8db958734-67752878',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'baseURL' => 0,
    'imgPath' => 0,
    'mode' => 0,
    'member' => 0,
    'noAccess' => 0,
    'lang' => 0,
    'regForm' => 0,
    'bio' => 0,
    'countries' => 0,
    'selectedCountry' => 0,
    'states' => 0,
    'selectedState' => 0,
    'timeZone' => 0,
    'dateFormat' => 0,
    'dateDisplay' => 0,
    'clockFormat' => 0,
    'numberDateSep' => 0,
    'maxAvatarFileSize' => 0,
    'fileExt' => 0,
    'securityTimestamp' => 0,
    'securityToken' => 0,
    'memberships' => 0,
    'membership' => 0,
    'selectedMembership' => 0,
    'commissionTypes' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68c6a8dbb63a61_21753332',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68c6a8dbb63a61_21753332')) {function content_68c6a8dbb63a61_21753332($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/function.html_options.php';
?><script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/workbox.js"></script>
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/workbox.private.js"></script>
<form class="cleanForm" method="post" action="actions.php" id="workboxForm" enctype="multipart/form-data">
<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/close.button.png" id="closeWorkbox">
<input type="hidden" value="updateAccountInfo" name="action" id="action">
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['mode']->value;?>
" name="mode" id="mode">
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['umem_id'];?>
" name="umem_id" id="umem_id">
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['mem_id'];?>
" name="mem_id" id="mem_id">
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['membershipDetails']['ms_id'];?>
" name="membership_id" id="membership_id">
<?php if ($_smarty_tpl->tpl_vars['noAccess']->value){?>
	<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['noAccess'];?>
</p>
<?php }else{ ?>
	<div id="editWorkbox">
		<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['editAccountInfo'];?>
</h1>
		<p><?php echo $_smarty_tpl->tpl_vars['lang']->value['editAccountInfoMes'];?>
</p>
		
		
		<?php if ($_smarty_tpl->tpl_vars['mode']->value=='personalInfo'){?>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['firstName'];?>
:</div>
					<div class="divTableCell"><input type="text" name="f_name" id="f_name" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['f_name'];?>
" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['lastName'];?>
:</div>
					<div class="divTableCell"><input type="text" name="l_name" id="l_name" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['l_name'];?>
" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['displayName'];?>
:</div>
					<div class="divTableCell"><input type="text" name="display_name" id="display_name" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['display_name'];?>
"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['email'];?>
:</div>
					<div class="divTableCell"><input type="text" name="email" id="email" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['email'];?>
" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['companyName'];?>
:</div>
					<div class="divTableCell"><input type="text" name="comp_name" id="comp_name" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['comp_name'];?>
" <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formCompanyName']['status']==2){?>require="require"<?php }?> errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['phone'];?>
:</div>
					<div class="divTableCell"><input type="text" name="phone" id="phone" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['phone'];?>
" <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formPhone']['status']==2){?>require="require"<?php }?> errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['website'];?>
:</div>
					<div class="divTableCell"><input type="text" name="website" id="website" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['website'];?>
" <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formWebsite']['status']==2){?>require="require"<?php }?> errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
"></div>
				</div>
			</div>
		<?php }?>
		
		<?php if ($_smarty_tpl->tpl_vars['mode']->value=='batchUploader'){?>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['batchUploader'];?>
:</div>
					<div class="divTableCell">
						<select id="batchUploader" name="batchUploader">
							<option value="1" <?php if ($_smarty_tpl->tpl_vars['member']->value['uploader']==1){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['uploader'][1];?>
</option>
							<option value="2" <?php if ($_smarty_tpl->tpl_vars['member']->value['uploader']==2){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['uploader'][2];?>
</option>
						</select>
					</div>
				</div>
			</div>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['mode']->value=='bio'){?>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel" style="vertical-align: top"><?php echo $_smarty_tpl->tpl_vars['lang']->value['bio'];?>
:</div>
					<div class="divTableCell"><textarea style="width: 700px; height: 230px;" name="bio_content"><?php echo $_smarty_tpl->tpl_vars['bio']->value;?>
</textarea></div>
				</div>
			</div>
		<?php }?>
				
		
		<?php if ($_smarty_tpl->tpl_vars['mode']->value=='address'){?>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['country'];?>
:</div>
					<div class="divTableCell">
						<select id="country" name="country" <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?>require="require"<?php }?> errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" style="width: 264px;">
							<option value=''></option>
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['countries']->value,'selected'=>$_smarty_tpl->tpl_vars['selectedCountry']->value),$_smarty_tpl);?>

						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['address'];?>
:</div>
					<div class="divTableCell"><input type="text" name="address" id="address" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['address'];?>
" <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?>require="require"<?php }?> errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"></div>
					<div class="divTableCell"><input type="text" name="address_2" id="address_2" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['address_2'];?>
"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['city'];?>
:</div>
					<div class="divTableCell"><input type="text" name="city" id="city" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['city'];?>
" <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?>require="require"<?php }?> errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['state'];?>
:</div>
					<div class="divTableCell" id="stateCell">
						<select id="state" name="state" <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?>require="require"<?php }?> errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" style="width: 264px;">
							<option></option>
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['states']->value,'selected'=>$_smarty_tpl->tpl_vars['selectedState']->value),$_smarty_tpl);?>

						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['zip'];?>
:</div>
					<div class="divTableCell"><input type="text" name="postal_code" id="postal_code" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['postal_code'];?>
" <?php if ($_smarty_tpl->tpl_vars['regForm']->value['formAddress']['status']==2){?>require="require"<?php }?> errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
"></div>
				</div>
			</div>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['mode']->value=='dateTime'){?>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['timeZone'];?>
:</div>
					<div class="divTableCell">
						<select id="timeZone" name="timeZone" style="width: 120px;">
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['timeZone']->value,'selected'=>$_smarty_tpl->tpl_vars['member']->value['time_zone']),$_smarty_tpl);?>

						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['dateFormat'];?>
:</div>
					<div class="divTableCell">
						<select id="dateFormat" name="dateFormat" style="width: 120px;">
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['dateFormat']->value,'selected'=>$_smarty_tpl->tpl_vars['member']->value['date_format']),$_smarty_tpl);?>

						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['dateDisplay'];?>
:</div>
					<div class="divTableCell">
						<select id="dateDisplay" name="dateDisplay" style="width: 120px;">
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['dateDisplay']->value,'selected'=>$_smarty_tpl->tpl_vars['member']->value['date_display']),$_smarty_tpl);?>

						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['clockFormat'];?>
:</div>
					<div class="divTableCell">
						<select id="clockFormat" name="clockFormat" style="width: 120px;">
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['clockFormat']->value,'selected'=>$_smarty_tpl->tpl_vars['member']->value['clock_format']),$_smarty_tpl);?>

						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['numberDateSep'];?>
:</div>
					<div class="divTableCell">
						<select id="numberDateSep" name="numberDateSep" style="width: 120px;">
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['numberDateSep']->value,'selected'=>$_smarty_tpl->tpl_vars['member']->value['number_date_sep']),$_smarty_tpl);?>

						</select>
					</div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['daylightSavings'];?>
:</div>
					<div class="divTableCell"><input type="checkbox" name="daylightSavings" value="1" <?php if ($_smarty_tpl->tpl_vars['member']->value['daylight_savings']){?>checked="checked"<?php }?>></div>
				</div>
			</div>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['mode']->value=='password'){?>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['currentPass'];?>
:</div>
					<div class="divTableCell"><input type="password" name="currentPass" id="currentPass" value="" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['newPass'];?>
:</div>
					<div class="divTableCell"><input type="password" name="newPass" id="newPass" value="" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" errorMessage2="<?php echo $_smarty_tpl->tpl_vars['lang']->value['accountInfoError1'];?>
" errorMessage3="<?php echo $_smarty_tpl->tpl_vars['lang']->value['accountInfoError2'];?>
"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['vNewPass'];?>
:</div>
					<div class="divTableCell"><input type="password" name="vNewPass" id="vNewPass" value="" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" errorMessage2="<?php echo $_smarty_tpl->tpl_vars['lang']->value['accountInfoError1'];?>
"></div>
				</div>
			</div>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['mode']->value=='avatar'){?>
			<input type="hidden" name="maxAvatarFileSize" id="maxAvatarFileSize" value="<?php echo $_smarty_tpl->tpl_vars['maxAvatarFileSize']->value;?>
">
			<input type="hidden" name="fileExt" id="fileExt" value="<?php echo $_smarty_tpl->tpl_vars['fileExt']->value;?>
">
			<input type="hidden" name="securityTimestamp" id="securityTimestamp" value="<?php echo $_smarty_tpl->tpl_vars['securityTimestamp']->value;?>
">
			<input type="hidden" name="securityToken" id="securityToken" value="<?php echo $_smarty_tpl->tpl_vars['securityToken']->value;?>
">
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel" style="vertical-align: top"><?php echo $_smarty_tpl->tpl_vars['lang']->value['avatar'];?>
:</div>
					<div class="divTableCell">
						<ul>
							<li><img src="<?php echo memberAvatar(array('memID'=>$_smarty_tpl->tpl_vars['member']->value['mem_id'],'size'=>150),$_smarty_tpl);?>
" class="memberAvatar" id="editorAvatar"></li>
							<li id="avatarUploadContainer">
								<div id="avatarUploaderDiv" style="position: relative; margin-top: 10px;">
									<input id="avatarUploader" name="avatarUploader" type="file" buttonText="<?php echo $_smarty_tpl->tpl_vars['lang']->value['uploadAvatar'];?>
">
									<!--<a href="" style="position: absolute; top: 10px; z-index: -1;" class="buttonLink"><?php echo $_smarty_tpl->tpl_vars['lang']->value['uploadAvatar'];?>
</a>-->
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="divTableRow" <?php if (!$_smarty_tpl->tpl_vars['member']->value['avatar']){?>style="display: none;"<?php }?> id="avatarDeleteDiv">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['delete'];?>
:</div>
					<div class="divTableCell"><input type="checkbox" name="delete" value="1" ></div>
				</div>
			</div>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['mode']->value=='membership'){?>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel" style="vertical-align: top"><?php echo $_smarty_tpl->tpl_vars['lang']->value['membership'];?>
:</div>
					<div class="divTableCell">
						<?php if (count($_smarty_tpl->tpl_vars['memberships']->value)>0){?>
							<ul class="membershipList" style="margin-left: 10px;">
								<?php  $_smarty_tpl->tpl_vars['membership'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['membership']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['memberships']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['membership']->key => $_smarty_tpl->tpl_vars['membership']->value){
$_smarty_tpl->tpl_vars['membership']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['membership']->key;
?>
									<li>
										<input type="radio" name="membership" id="membership_<?php echo $_smarty_tpl->tpl_vars['membership']->value['ms_id'];?>
" class="membershipRadios" value="<?php echo $_smarty_tpl->tpl_vars['membership']->value['ums_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['membership']->value['ums_id']==$_smarty_tpl->tpl_vars['selectedMembership']->value){?>checked="checked"<?php }?>><label for="membership_<?php echo $_smarty_tpl->tpl_vars['membership']->value['ms_id'];?>
"><strong><?php echo $_smarty_tpl->tpl_vars['membership']->value['name'];?>
</strong></label>
										<p class="membershipDetails"><?php if ($_smarty_tpl->tpl_vars['membership']->value['description']){?><?php echo $_smarty_tpl->tpl_vars['membership']->value['description'];?>
<br><?php }?></p>
										<p class="membershipPriceDetails">
											<?php if ($_smarty_tpl->tpl_vars['membership']->value['mstype']=='free'){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelPrice'];?>
: <span class="price"><?php echo $_smarty_tpl->tpl_vars['lang']->value['free'];?>
</span><?php }?>			
											<?php if ($_smarty_tpl->tpl_vars['membership']->value['trail_status']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['freeTrial'];?>
: <span class="price <?php if ($_smarty_tpl->tpl_vars['membership']->value['trialUsed']){?>strike<?php }?>"><?php echo $_smarty_tpl->tpl_vars['membership']->value['trial_length_num'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['membership']->value['trial_length_period']];?>
</span><br><?php }?>											
											<?php if ($_smarty_tpl->tpl_vars['membership']->value['setupfee']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['setupFee'];?>
: <span class="price <?php if ($_smarty_tpl->tpl_vars['membership']->value['feePaid']){?>strike<?php }?>"><?php echo $_smarty_tpl->tpl_vars['membership']->value['setupfee']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['membership']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><br><?php }?>											
											<?php if ($_smarty_tpl->tpl_vars['membership']->value['mstype']=='recurring'){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelPrice'];?>
: <span class="price"><?php echo $_smarty_tpl->tpl_vars['membership']->value['price']['display'];?>
</span> <?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['membership']->value['period']];?>
<?php if ($_smarty_tpl->tpl_vars['membership']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?>
										</p>
									</li>
								<?php } ?>
							</ul>
						<?php }?>
					</div>
				</div>
			</div>
		<?php }?>
		
		
		<?php if ($_smarty_tpl->tpl_vars['mode']->value=='commission'){?>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel" style="vertical-align: top"><?php echo $_smarty_tpl->tpl_vars['lang']->value['commissionMethod'];?>
:</div>
					<div class="divTableCell">
						<ul>
							<?php if ($_smarty_tpl->tpl_vars['commissionTypes']->value['paypal']){?><li><input type="radio" name="commissionType" value="1" id="commissionTypePayPal" <?php if ($_smarty_tpl->tpl_vars['member']->value['compay']==1){?>checked="checked"<?php }?>> <label for="commissionTypePayPal"><?php echo $_smarty_tpl->tpl_vars['lang']->value['paypal'];?>
</label><br><div style="padding: 10px; background-color: #333; margin: 10px; color: #999; <?php if ($_smarty_tpl->tpl_vars['member']->value['compay']!=1){?>display: none;<?php }?>" id="commissionPayPalEmail"><?php echo $_smarty_tpl->tpl_vars['lang']->value['paypalEmail'];?>
 <input type="text" name="paypalEmail" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['paypal_email'];?>
" style="width: 50px;"></div></li><?php }?>
							<?php if ($_smarty_tpl->tpl_vars['commissionTypes']->value['check']){?><li><input type="radio" name="commissionType" value="2" id="commissionTypeCheck" <?php if ($_smarty_tpl->tpl_vars['member']->value['compay']==2){?>checked="checked"<?php }?>> <label for="commissionTypeCheck"><?php echo $_smarty_tpl->tpl_vars['lang']->value['checkMO'];?>
</label></li><?php }?>
							<?php if ($_smarty_tpl->tpl_vars['commissionTypes']->value['other']){?><li><input type="radio" name="commissionType" value="3" id="commissionTypeOther" <?php if ($_smarty_tpl->tpl_vars['member']->value['compay']==3){?>checked="checked"<?php }?>> <label for="commissionTypeOther"><?php echo $_smarty_tpl->tpl_vars['commissionTypes']->value['otherName'];?>
</label></li><?php }?>
						</ul>
					</div>
				</div>
			</div>
		<?php }?>
		
	</div>
<?php }?>
<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['save'];?>
" id="saveWorkboxForm" class="btn btn-xs btn-primary"></div>
</form><?php }} ?>