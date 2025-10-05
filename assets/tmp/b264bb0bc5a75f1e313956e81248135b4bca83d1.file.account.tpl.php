<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 09:46:56
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/account.tpl" */ ?>
<?php /*%%SmartyHeaderCode:57390051468b375aad338d6-48684037%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b264bb0bc5a75f1e313956e81248135b4bca83d1' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/account.tpl',
      1 => 1757796001,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '57390051468b375aad338d6-48684037',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b375aae6ec18_29779149',
  'variables' => 
  array (
    'lang' => 0,
    'notice' => 0,
    'signupDateDisplay' => 0,
    'lastLoginDisplay' => 0,
    'member' => 0,
    'membership' => 0,
    'displayLanguages' => 0,
    'selectedLanguage' => 0,
    'displayCurrencies' => 0,
    'selectedCurrency' => 0,
    'config' => 0,
    'exampleDateDisplay' => 0,
    'commissionTypeName' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b375aae6ec18_29779149')) {function content_68b375aae6ec18_29779149($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/function.html_options.php';
?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</head>
<body>
	<?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate ('header2.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
		
		<div class="container">
			<div class="row">
				<?php echo $_smarty_tpl->getSubTemplate ('memnav.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
				<div class="col-md-9">
					
					<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['accountInfo'];?>
</h1>
					<hr>
					<?php if ($_smarty_tpl->tpl_vars['notice']->value=='accountUpdated'){?>
						<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['accountUpdated'];?>
</p>
					<?php }?>
					<ul class="accountInfoList">
						<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['signupDate'];?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['signupDateDisplay']->value;?>
</li>
						<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['lastLogin'];?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['lastLoginDisplay']->value;?>
</li>
					</ul>
					
					<ul class="accountInfoList">
						<li class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['generalInfo'];?>
</li>
						<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['name'];?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['member']->value['f_name'];?>
 <?php echo $_smarty_tpl->tpl_vars['member']->value['l_name'];?>
</li>
						<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['email'];?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['member']->value['email'];?>
</li>
						<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['displayName'];?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['member']->value['display_name'];?>
</li>
						<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['companyName'];?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['member']->value['comp_name'];?>
</li>
						<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['website'];?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['member']->value['website'];?>
</li>
						<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['phone'];?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['member']->value['phone'];?>
</li>
						<li class="editLink"><a href="<?php echo linkto(array('page'=>"account.edit.php?mode=personalInfo"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
</a></li>
					</ul>
					
					<ul class="accountInfoList">
						<li class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['address'];?>
</li>
						<li>
							<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['address'];?>
<br>
							<?php if ($_smarty_tpl->tpl_vars['member']->value['primaryAddress']['address_2']){?><?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['address_2'];?>
<br><?php }?>
							<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['city'];?>
, <?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['state'];?>
 <?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['postal_code'];?>
<br>
							<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['country'];?>

						</li>
						<li class="editLink"><a href="<?php echo linkto(array('page'=>"account.edit.php?mode=address"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
</a></li>
					</ul>
					
					<ul class="accountInfoList">
						<li class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['membership'];?>
</li>
						<li><a href="<?php echo linkto(array('page'=>"membership.php?id=".($_smarty_tpl->tpl_vars['membership']->value['ums_id'])),$_smarty_tpl);?>
" class="membershipWorkbox"><strong><?php echo $_smarty_tpl->tpl_vars['membership']->value['name'];?>
</strong></a> <?php if ($_smarty_tpl->tpl_vars['membership']->value['msExpired']){?><span class="highlightValue">(expired)</span> <a href="<?php echo linkto(array('page'=>"account.edit.php?mode=membership"),$_smarty_tpl);?>
" class="colorLink accountInfoWorkbox">[<?php echo $_smarty_tpl->tpl_vars['lang']->value['renew'];?>
]</a><?php }?></li>
						<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['expires'];?>
:</strong> <?php if ($_smarty_tpl->tpl_vars['membership']->value['msExpired']){?><span class="highlightValue"><?php echo $_smarty_tpl->tpl_vars['membership']->value['msExpireDate'];?>
</span><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['membership']->value['msExpireDate'];?>
<?php }?></span></li>
						<li class="editLink"><a href="<?php echo linkto(array('page'=>"account.edit.php?mode=membership"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
</a></li>
					</ul>
					
					<ul class="accountInfoList">
						<li class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['preferences'];?>
</li>
						<?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?>
							<li>
								<strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['preferredLang'];?>
:</strong> 
								<select id="languageSelector">
									<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['displayLanguages']->value,'selected'=>$_smarty_tpl->tpl_vars['selectedLanguage']->value),$_smarty_tpl);?>

								</select>
							</li>
						<?php }?>
						<?php if (count($_smarty_tpl->tpl_vars['displayCurrencies']->value)>1){?>
							<li>
								<strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['preferredCurrency'];?>
:</strong> 
								<select id="currencySelector">
									<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['displayCurrencies']->value,'selected'=>$_smarty_tpl->tpl_vars['selectedCurrency']->value),$_smarty_tpl);?>

								</select>
							</li>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['dt_member_override']){?>
							<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['dateTime'];?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['exampleDateDisplay']->value;?>
</strong> <a href="<?php echo linkto(array('page'=>"account.edit.php?mode=dateTime"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
</a></li>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']||$_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_uploads']){?>
							<li>
								<strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['batchUploader'];?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['lang']->value['uploader'][$_smarty_tpl->tpl_vars['member']->value['uploader']];?>
 <a href="<?php echo linkto(array('page'=>"account.edit.php?mode=batchUploader"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
</a>
							</li>
						<?php }?>
						<li class="editLink">&nbsp;</li>
					</ul>
					
					<ul class="accountInfoList">
						<li class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['actions'];?>
</li>
						<li><a href="<?php echo linkto(array('page'=>"account.edit.php?mode=password"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['changePass'];?>
</a></li>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['avatar']){?><li><a href="<?php echo linkto(array('page'=>"account.edit.php?mode=avatar"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['changeAvatar'];?>
</a></li><?php }?>
						<li class="editLink">&nbsp;</li>
					</ul>
					
					
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['bio']){?>
						<ul class="accountInfoList">
							<li class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['bio'];?>
</li>
							<li><?php if ($_smarty_tpl->tpl_vars['member']->value['bio_content']){?><?php echo $_smarty_tpl->tpl_vars['member']->value['bio_content'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['lang']->value['none'];?>
<?php }?></li>
							<li class="editLink"><a href="<?php echo linkto(array('page'=>"account.edit.php?mode=bio"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
</a></li>
						</ul>
					<?php }?>
					
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']){?>	
						<ul class="accountInfoList">
							<li class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['contributorSettings'];?>
</li>
							<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['commissionMethod'];?>
</strong>: <?php echo $_smarty_tpl->tpl_vars['commissionTypeName']->value;?>
</li>
							<li><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['commission'];?>
</strong>: <?php echo $_smarty_tpl->tpl_vars['member']->value['com_level'];?>
%</li>
							<li class="editLink"><a href="<?php echo linkto(array('page'=>"account.edit.php?mode=commission"),$_smarty_tpl);?>
" class="accountInfoWorkbox btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
</a></li>
						</ul>
					<?php }?>
					
				</div>
			</div>
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>