<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 08:32:03
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/workbox.tpl" */ ?>
<?php /*%%SmartyHeaderCode:200245257468b37559305775-23724907%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '32fd384eb581e4b35ff50bbbc577d5bdd5e4d721' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/workbox.tpl',
      1 => 1757796003,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '200245257468b37559305775-23724907',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b3755a28e7d8_85013375',
  'variables' => 
  array (
    'baseURL' => 0,
    'access' => 0,
    'mode' => 0,
    'member' => 0,
    'securityTimestamp' => 0,
    'securityToken' => 0,
    'imgPath' => 0,
    'noAccess' => 0,
    'lang' => 0,
    'lightboxID' => 0,
    'mediaID' => 0,
    'lightbox' => 0,
    'lightboxes' => 0,
    'selectedLightbox' => 0,
    'lightboxItemID' => 0,
    'lightboxItem' => 0,
    'useMediaID' => 0,
    'notice' => 0,
    'cartPackageRows' => 0,
    'cartPackages' => 0,
    'package' => 0,
    'cartItemID' => 0,
    'cartItem' => 0,
    'newPackageRows' => 0,
    'newPackages' => 0,
    'loggedIn' => 0,
    'subTotalMin' => 0,
    'billID' => 0,
    'gateways' => 0,
    'gateway' => 0,
    'profileID' => 0,
    'album' => 0,
    'config' => 0,
    'shareLink' => 0,
    'contrAlbumID' => 0,
    'upSet' => 0,
    'saveMode' => 0,
    'contrSaveSessionForm' => 0,
    'contrImportFiles' => 0,
    'filePath' => 0,
    'sellDigital' => 0,
    'printRows' => 0,
    'productRows' => 0,
    'contrAlbums' => 0,
    'selectedLanguage' => 0,
    'activeLanguages' => 0,
    'displayLanguages' => 0,
    'language' => 0,
    'mediaTypesRows' => 0,
    'mediaTypes' => 0,
    'mediaType' => 0,
    'digitalCreditsCartStatus' => 0,
    'digitalCurrencyCartStatus' => 0,
    'priCurrency' => 0,
    'licenses' => 0,
    'key' => 0,
    'license' => 0,
    'digitalRows' => 0,
    'photoProfiles' => 0,
    'profile' => 0,
    'videoProfiles' => 0,
    'digital' => 0,
    'otherProfiles' => 0,
    'prints' => 0,
    'print' => 0,
    'printCreditsCartStatus' => 0,
    'printCurrencyCartStatus' => 0,
    'products' => 0,
    'product' => 0,
    'prodCreditsCartStatus' => 0,
    'prodCurrencyCartStatus' => 0,
    'media' => 0,
    'selectedGalleries' => 0,
    'galleryID' => 0,
    'selectedAlbum' => 0,
    'maxUploadSize' => 0,
    'originalMediaDS' => 0,
    'keywords' => 0,
    'keyword' => 0,
    'cartItemNotes' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b3755a28e7d8_85013375')) {function content_68b3755a28e7d8_85013375($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/function.html_options.php';
if (!is_callable('smarty_modifier_truncate')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/modifier.truncate.php';
?><script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/workbox.js"></script>
<?php if ($_smarty_tpl->tpl_vars['access']->value=='private'){?><script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/workbox.private.js"></script><?php }?>
<form class="cleanForm form-group" method="post" action="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/actions.php" id="workboxForm" enctype="multipart/form-data">
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['mode']->value;?>
" name="action" id="action">
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['mode']->value;?>
" name="mode" id="mode">
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['umem_id'];?>
" name="umem_id" id="umem_id">
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['member']->value['mem_id'];?>
" name="mem_id" id="mem_id">
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['securityTimestamp']->value;?>
" name="securityTimestamp" id="securityTimestamp">
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['securityToken']->value;?>
" name="securityToken" id="securityToken">

<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/close.button.png" id="closeWorkbox">
<!--<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/close.button.png" id="closeWorkbox">-->
<?php if ($_smarty_tpl->tpl_vars['noAccess']->value){?>
	<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['noAccess'];?>
</p>
<?php }else{ ?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='forgotPassword'){?>
		<div id="editWorkbox">
			<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['forgotPassword'];?>
</h1>
			<p class="notice" style="display: none;" id="emailPasswordSent"><?php echo $_smarty_tpl->tpl_vars['lang']->value['passwordSent'];?>
</p>
			<p class="notice" style="display: none;" id="emailPasswordFailed"><?php echo $_smarty_tpl->tpl_vars['lang']->value['passwordFailed'];?>
</p>
			
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['yourEmail'];?>
:</div>
					<div class="divTableCell"><input type="text" name="form[toEmail]" value="" id="toEmail" style="width: 250px;" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" /></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger" /> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['submit'];?>
" id="saveWorkboxForm" class="btn btn-xs btn-primary" /></div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='deleteLightbox'){?>
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['warning'];?>
</h1>
			<p class="dimMessage"><?php echo $_smarty_tpl->tpl_vars['lang']->value['lbDeleteVerify'];?>
</p>
			<div class="workboxActionButtons"><a href="<?php echo $_smarty_tpl->tpl_vars['lightboxID']->value;?>
" class="btn btn-xs btn-danger" id="deleteLightboxYes"><?php echo $_smarty_tpl->tpl_vars['lang']->value['yes'];?>
</a> <a href="#" class="btn btn-xs btn-primary closeWorkbox"><?php echo $_smarty_tpl->tpl_vars['lang']->value['no'];?>
</a></div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='deleteContrMedia'){?>
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['warning'];?>
</h1>
			<p class="dimMessage"><?php echo $_smarty_tpl->tpl_vars['lang']->value['cmDeleteVerify'];?>
</p>
			<div class="workboxActionButtons"><a href="<?php echo $_smarty_tpl->tpl_vars['mediaID']->value;?>
" class="closeWorkbox btn btn-xs btn-danger" id="deleteContrMediaYes"><?php echo $_smarty_tpl->tpl_vars['lang']->value['yes'];?>
</a> <a href="#" class="closeWorkbox btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['no'];?>
</a></div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='newLightbox'||$_smarty_tpl->tpl_vars['mode']->value=='editLightbox'){?>
		<div id="editWorkbox">
			<script>$('#lightboxName').focus();</script>
			<input type="hidden" name="ulightbox_id" value="<?php echo $_smarty_tpl->tpl_vars['lightbox']->value['ulightbox_id'];?>
">
			<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/close.button.png" id="closeWorkbox">
			<h1><?php if ($_smarty_tpl->tpl_vars['mode']->value=='editLightbox'){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['editLightbox'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['lang']->value['createNewLightbox'];?>
<?php }?></h1>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['name'];?>
:</div>
					<div class="divTableCell"><input type="text" name="lightboxName" value="<?php echo $_smarty_tpl->tpl_vars['lightbox']->value['name'];?>
" id="lightboxName" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelDesc'];?>
:</div>
					<div class="divTableCell"><textarea style="height: 50px;" name="lightboxNotes" id="lightboxNotes" class="form-control"><?php echo $_smarty_tpl->tpl_vars['lightbox']->value['notes'];?>
</textarea></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"> <input type="button" value="<?php if ($_smarty_tpl->tpl_vars['mode']->value=='newLightbox'){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['create'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['lang']->value['save'];?>
<?php }?>" id="saveWorkboxForm" class="btn btn-xs btn-primary"></div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='addToLightbox'){?>
		<div id="editWorkbox">
			<input type="hidden" name="mediaID" value="<?php echo $_smarty_tpl->tpl_vars['mediaID']->value;?>
">
			<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['addToLightbox'];?>
</h1>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['lightboxes'];?>
:</div>
					<div class="divTableCell">
					<select style="min-width: 258px;" name="lightbox" id="lightboxDropdown" class="form-control">
						<option value="0"><?php echo $_smarty_tpl->tpl_vars['lang']->value['newLightbox'];?>
...</option>
						<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['lightboxes']->value,'selected'=>$_smarty_tpl->tpl_vars['selectedLightbox']->value),$_smarty_tpl);?>

					</select>
					</div>
				</div>
				<div class="divTableRow newLightboxRow" <?php if ($_smarty_tpl->tpl_vars['selectedLightbox']->value){?>style="display: none;"<?php }?>>
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['name'];?>
:</div>
					<div class="divTableCell"><input type="text" name="lightboxName" value="<?php echo $_smarty_tpl->tpl_vars['lightbox']->value['name'];?>
" id="lightboxName" errorMessage1="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" style="min-width: 200px;" class="form-control"></div><!--require="require"-->
				</div>
				<div class="divTableRow newLightboxRow" <?php if ($_smarty_tpl->tpl_vars['selectedLightbox']->value){?>style="display: none;"<?php }?>>
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelDesc'];?>
:</div>
					<div class="divTableCell"><textarea style="height: 50px; min-width: 250px;" name="lightboxNotes" id="lightboxNotes" class="form-control"><?php echo $_smarty_tpl->tpl_vars['lightbox']->value['notes'];?>
</textarea></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"></div>
					<div class="divTableCell" style="text-align: right"><a href="#" class="colorLink" id="addNotesLink"><?php echo $_smarty_tpl->tpl_vars['lang']->value['addNotes'];?>
</a></div>
				</div>
				<div class="divTableRow" style="display: none;" id="mediaNotesRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['notes'];?>
:</div>
					<div class="divTableCell"><textarea style="height: 50px; min-width: 250px;" name="mediaNotes" id="mediaNotes" class="form-control"><?php echo $_smarty_tpl->tpl_vars['lightbox']->value['notes'];?>
</textarea></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['add'];?>
" id="saveWorkboxForm" class="btn btn-xs btn-primary"></div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='editLightboxItem'){?>
		<div id="editWorkbox">
			<input type="hidden" name="mediaID" value="<?php echo $_smarty_tpl->tpl_vars['mediaID']->value;?>
">
			<input type="hidden" name="lightboxItemID" id="lightboxItemID" value="<?php echo $_smarty_tpl->tpl_vars['lightboxItemID']->value;?>
">
			<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['editLightboxItem'];?>
</h1>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['notes'];?>
:</div>
					<div class="divTableCell"><textarea style="height: 50px; min-width: 250px;" name="mediaNotes" id="mediaNotes" class="form-control"><?php echo $_smarty_tpl->tpl_vars['lightboxItem']->value['notes'];?>
</textarea></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['removeFromLightbox'];?>
" mediaID="<?php echo $_smarty_tpl->tpl_vars['mediaID']->value;?>
" id="removeItemFromLightbox" class="btn btn-xs btn-danger"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['save'];?>
" id="saveWorkboxForm" class="btn btn-xs btn-primary"></div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='assignPackage'){?>
		<div id="editWorkbox" style="padding-right: 10px; margin-bottom: 40px;">
			<input type="hidden" name="useMediaID" value="<?php echo $_smarty_tpl->tpl_vars['useMediaID']->value;?>
" id="useMediaID">
			<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['assignToPackage'];?>
</h1>
			<?php if ($_smarty_tpl->tpl_vars['notice']->value){?>
				<p style="margin-top: 15px;"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaNotElidgiblePack'];?>
</p>
			<?php }?>
			
			<div class="assignToPackageListContainer">
				<?php if ($_smarty_tpl->tpl_vars['cartPackageRows']->value){?>
					<h3><?php echo $_smarty_tpl->tpl_vars['lang']->value['packagesInCart'];?>
:</h3>
					<div class="divTable assignToPackageList">
						<?php  $_smarty_tpl->tpl_vars['package'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['package']->_loop = false;
 $_smarty_tpl->tpl_vars['cartItemID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cartPackages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['package']->key => $_smarty_tpl->tpl_vars['package']->value){
$_smarty_tpl->tpl_vars['package']->_loop = true;
 $_smarty_tpl->tpl_vars['cartItemID']->value = $_smarty_tpl->tpl_vars['package']->key;
?>
							<div class="divTableRow" usePackageID="<?php echo $_smarty_tpl->tpl_vars['package']->value['usePackageID'];?>
" cartEditID="<?php echo $_smarty_tpl->tpl_vars['cartItemID']->value;?>
">
								<div class="divTableCell" style="vertical-align: top"><?php if ($_smarty_tpl->tpl_vars['package']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['package']->value['pack_id'],'itemType'=>'pack','photoID'=>$_smarty_tpl->tpl_vars['package']->value['photo']['id'],'size'=>75,'crop'=>50),$_smarty_tpl);?>
"><?php }?></div>
								<div class="divTableCell" style="width: 100%;">
									<h2><?php echo $_smarty_tpl->tpl_vars['package']->value['name'];?>
</h2>
									<div class="packageFilledContainer">
										<div class="packageFilledBar"><p style="width: <?php echo $_smarty_tpl->tpl_vars['package']->value['package_media_percentage'];?>
%"></p></div>
										<!--<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['package_media_filled'];?>
/<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['package_media_needed'];?>
 = --><strong><?php echo $_smarty_tpl->tpl_vars['package']->value['package_media_remaining'];?>
</strong> <?php echo $_smarty_tpl->tpl_vars['lang']->value['leftToFill'];?>

									</div>
									<p class="purchaseListDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['package']->value['description'],200);?>
</p>
								</div>
							</div>
						<?php } ?>
					</div>
				<?php }?>
				
				<?php if ($_smarty_tpl->tpl_vars['newPackageRows']->value){?>
					<h3><?php echo $_smarty_tpl->tpl_vars['lang']->value['startNewPackage'];?>
:</h3>				
					<div class="divTable assignToPackageList">
						<?php  $_smarty_tpl->tpl_vars['package'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['package']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['newPackages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['package']->key => $_smarty_tpl->tpl_vars['package']->value){
$_smarty_tpl->tpl_vars['package']->_loop = true;
?>
							<div class="divTableRow" usePackageID="<?php echo $_smarty_tpl->tpl_vars['package']->value['usePackageID'];?>
" cartEditID="0">
								<div class="divTableCell" style="vertical-align: top"><?php if ($_smarty_tpl->tpl_vars['package']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['package']->value['pack_id'],'itemType'=>'pack','photoID'=>$_smarty_tpl->tpl_vars['package']->value['photo']['id'],'size'=>75,'crop'=>50),$_smarty_tpl);?>
"><?php }?></div>
								<div class="divTableCell" style="width: 100%;">
									<h2><?php echo $_smarty_tpl->tpl_vars['package']->value['name'];?>
</h2>
									<p class="purchaseListDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['package']->value['description'],200);?>
</p>
									<p class="purchaseListPrice"><?php if ($_smarty_tpl->tpl_vars['package']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['package']->value['price']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['package']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['package']->value['credits']){?> <?php echo $_smarty_tpl->tpl_vars['lang']->value['priceCreditSep'];?>
 <?php }?><?php if ($_smarty_tpl->tpl_vars['package']->value['credits']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['package']->value['credits'];?>
 <sup><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelCredits'];?>
</sup></span><?php }?></p>
								</div>
							</div>
						<?php } ?>
					</div>
				<?php }?>
			</div>
				
		</div>
		<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='emailToFriend'){?>
		<div id="editWorkbox">
			<input type="hidden" name="mediaID" value="<?php echo $_smarty_tpl->tpl_vars['mediaID']->value;?>
">
			<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['emailToFriend'];?>
</h1>
			<p class="notice" style="display: none;" id="emailSentNotice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['emailToFriendSent'];?>
</p>
			<div class="divTable">
				<?php if (!$_smarty_tpl->tpl_vars['loggedIn']->value){?>
					<div class="divTableRow">
						<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['yourName'];?>
:</div>
						<div class="divTableCell"><input type="text" style="min-width: 250px;" name="form[fromName]" id="fromName" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"></div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['yourEmail'];?>
:</div>
						<div class="divTableCell"><input type="text" style="min-width: 250px;" name="form[fromEmail]" id="fromEmail" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"></div>
					</div>
				<?php }?>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['emailTo'];?>
:</div>
					<div class="divTableCell"><input type="text" style="min-width: 250px;" name="form[toEmail]" id="toEmail" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['message'];?>
:</div>
					<div class="divTableCell"><textarea style="height: 100px; min-width: 250px;" name="form[message]" id="emailMessage" class="form-control"></textarea></div>
				</div>
			</div>
			<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['send'];?>
" id="saveWorkboxForm" class="btn btn-xs btn-primary"></div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='creditsWarning'){?>
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['warning'];?>
</h1>
			<p class="dimMessage"><?php echo $_smarty_tpl->tpl_vars['lang']->value['creditsWarning'];?>
</p>
			<div class="workboxActionButtons"><!--<input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox"> --><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['purchaseCredits'];?>
" href="featured.php?mode=credits" class="goToButton"> <?php if (!$_smarty_tpl->tpl_vars['loggedIn']->value){?><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['loginCaps'];?>
" href="login.php" class="goToButton"><?php }?></div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='subtotalWarning'){?>
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['warning'];?>
</h1>
			<p class="dimMessage"><?php echo $_smarty_tpl->tpl_vars['lang']->value['subtotalWarning'];?>
 <?php echo $_smarty_tpl->tpl_vars['subTotalMin']->value['display'];?>
</p>
			<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='createOrLogin'){?>
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['haveAccountQuestion'];?>
</h1>
			<p class="dimMessage" style="line-height: 2;">
				<a href="login.php?jumpTo=cart"><?php echo $_smarty_tpl->tpl_vars['lang']->value['yesLogin'];?>
</a><br>
				<a href="create.account.php?jumpTo=cart"><?php echo $_smarty_tpl->tpl_vars['lang']->value['noCreateAccount'];?>
</a>
			</p>
			<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='billPayment'){?>
		<script>
			$(function()
			{
				$('#workboxForm li:first').addClass('paymentGatewaySelected'); // Select the first payment method
				
				$('#workboxForm input[type="radio"]').click(function()
				{
					$('#workboxForm li').removeClass('paymentGatewaySelected');
					$(this).closest('li').addClass('paymentGatewaySelected');
				});
				
				$('#workboxForm li').click(function()
				{
					$(this).children('input[type="radio"]').attr('checked','checked');
					$('#workboxForm li').removeClass('paymentGatewaySelected');
					$(this).addClass('paymentGatewaySelected');
				});
				
				$('#submitPaymentButton').click(function()
				{
					var paymentType = $('input[name="paymentType"]:checked').val();
					goto('bill.payment.php?billID='+$('#billID').val()+'&paymentType='+paymentType );
				});
			});
		</script>
		<input type="hidden" name="billID" id="billID" value="<?php echo $_smarty_tpl->tpl_vars['billID']->value;?>
">
		<div id="editWorkbox">
			<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['choosePaymentMethod'];?>
</h1>
			<div class="cartTotalList paymentGatewaysBox cartPaymentForm" style="border-top: 0; margin: 20px 0 20px 0;">
				<ul>
					<?php  $_smarty_tpl->tpl_vars['gateway'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['gateway']->_loop = false;
 $_smarty_tpl->tpl_vars['gatewayKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['gateways']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['gateway']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['gateway']->key => $_smarty_tpl->tpl_vars['gateway']->value){
$_smarty_tpl->tpl_vars['gateway']->_loop = true;
 $_smarty_tpl->tpl_vars['gatewayKey']->value = $_smarty_tpl->tpl_vars['gateway']->key;
 $_smarty_tpl->tpl_vars['gateway']->index++;
 $_smarty_tpl->tpl_vars['gateway']->first = $_smarty_tpl->tpl_vars['gateway']->index === 0;
?>
						<li>
							<input type="radio" name="paymentType" value="<?php echo $_smarty_tpl->tpl_vars['gateway']->value['id'];?>
" id="paymentGateway<?php echo $_smarty_tpl->tpl_vars['gateway']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['gateway']->first){?>checked="checked"<?php }?>>
							<?php if ($_smarty_tpl->tpl_vars['gateway']->value['logo']){?><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/logos/<?php echo $_smarty_tpl->tpl_vars['gateway']->value['id'];?>
.png"><?php }?>
							<label for="paymentGateway<?php echo $_smarty_tpl->tpl_vars['gateway']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['gateway']->value['displayName'];?>
</label>
							<p><?php echo $_smarty_tpl->tpl_vars['gateway']->value['publicDescription'];?>
</p>
						</li>
					<?php } ?>
				</ul>
			</div>
			<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['continue'];?>
" id="submitPaymentButton" class="btn btn-xs btn-success"></div>
		</div>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='unfinishedPackage'){?>
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> Incomplete Packages!</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				One or more of the packages in your cart have additional items left to fill before you can checkout.
			</p>
			<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
		</div>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='missingSearchTerms'){?>
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> Keywords Required</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				You must enter keywords before you begin your search.
			</p>
			<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
		</div>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='downloadExpired'){?>
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadExpired'];?>
</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				<?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadExpiredMes'];?>

			</p>
			<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
		</div>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='downloadsExceeded'){?>
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadMax'];?>
</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				<?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadMaxMes'];?>

			</p>
			<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
		</div>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='downloadNotApproved'){?>
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadNotApproved'];?>
</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				<?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadNotApprovedMes'];?>

			</p>
			<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
		</div>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='downloadNotAvailable'){?>
		<script>
			$(function()
			{
				// Request a download button
				$('#workboxRequestDownload').click(function()
				{
					//alert('test');
					
					if($('#loggedIn').val() == 1)
					{
						$(this).attr('disabled','disabled');  // Disable button to prevent duplicate submissions
						submitRequestDownloadForm();
					}
					else
					{
						$('#requestDownloadEmail').slideDown(100); // Slide open email field			
						$(this).unbind('click'); // Unbind previous click			
						$(this).click(function()
						{
							$(this).attr('disabled','disabled');  // Disable button to prevent duplicate submissions
							submitRequestDownloadForm();
						}); // Add new click			
						$(this).val($(this).attr('altText')); // Change button text
					}
					
				});
				
				$('#requestDownloadEmail').click(function()
				{
					$(this).val('');
				});
				
				// Submit the request for a download form
				function submitRequestDownloadForm()
				{
					//alert($("#workboxForm").serialize());
					$.ajax({
						type: 'POST',
						url: baseURL+'/actions.php',
						data: $("#workboxForm").serialize(),
						dataType: 'json',
						success: function(data)
						{	
							//alert('success');
							$('#workboxDownload').hide();
							$('#requestDownloadContainer').hide();
							$('#requestDownloadSuccess').show();
						}
					});
				}
			});
		</script>
		
		<div id="editWorkbox">
			<input type="hidden" name="action" value="emailForFile">
			<input type="hidden" name="mediaID" value="<?php echo $_smarty_tpl->tpl_vars['mediaID']->value;?>
">
			<input type="hidden" name="profileID" value="<?php echo $_smarty_tpl->tpl_vars['profileID']->value;?>
">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadNotAvail'];?>
</h1>
			<p class="dimMessage" style="line-height: 1.3;">
				<?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadNotAvailMes'];?>

			</p>
			<div class="workboxActionButtons">
				<div id="requestDownloadSuccess" class="notice" style="display: none; margin: 0; float: left;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['requestDownloadSuccess'];?>
</div>
				<div style="float: left;" id="requestDownloadContainer"><input type="text" name="requestDownloadEmail" id="requestDownloadEmail" style="display: none;" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['enterEmail'];?>
"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['requestDownload'];?>
" altText="<?php echo $_smarty_tpl->tpl_vars['lang']->value['submit'];?>
" id="workboxRequestDownload" class="btn btn-xs btn-primary"></div>
				<!--<input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox">-->
			</div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='contrNewAlbum'){?>
		<script>
			$(function()
			{
				$('#newAlbumPublic').click(function()
				{					
					$('#albumPasswordRow').toggle();
				});
			});
		</script>		
		<div id="editWorkbox">
			<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['newAlbum'];?>
</h1>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['albumName'];?>
:</div>
					<div class="divTableCell"><input type="text" name="newAlbumName" id="newAlbumName" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelDesc'];?>
:</div>
					<div class="divTableCell"><textarea style="height: 50px; width: 250px;" name="newAlbumDescription" id="newAlbumDescription" class="form-control"></textarea></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['makePublic'];?>
:</div>
					<div class="divTableCell" style="vertical-align: middle; padding-top: 18px;"><input type="checkbox" name="newAlbumPublic" id="newAlbumPublic" value="1"></div>
				</div>
				<div class="divTableRow" id="albumPasswordRow" style="display: none;">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['password'];?>
:</div>
					<div class="divTableCell"><input type="text" name="newAlbumPassword" id="newAlbumPassword" class="form-control" /><span style="font-size: 10px; color: #999"><?php echo $_smarty_tpl->tpl_vars['lang']->value['passwordLeaveBlank'];?>
</span></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['save'];?>
" id="saveWorkboxForm" class="btn btn-xs btn-primary"></div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='contrEditAlbum'){?>
		<script>
			$(function()
			{
				$('#albumPublic').click(function()
				{	
					if($('#albumPublic').is(':checked'))
					{
						$('.shareLinkDiv').show();
					}
					else
					{
						$('.shareLinkDiv').hide();
					}
				});
			});
		</script>
		<div id="editWorkbox">
			<input type="hidden" name="albumID" id="albumID" value="<?php echo $_smarty_tpl->tpl_vars['album']->value['ugallery_id'];?>
">
			<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['editAlbum'];?>
</h1>
			<div class="divTable">
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['albumName'];?>
:</div>
					<div class="divTableCell"><input type="text" name="albumName" id="albumName" value="<?php echo $_smarty_tpl->tpl_vars['album']->value['name'];?>
" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelDesc'];?>
:</div>
					<div class="divTableCell"><textarea style="height: 50px; width: 250px;" name="albumDescription" id="albumDescription" class="form-control"><?php echo $_smarty_tpl->tpl_vars['album']->value['description'];?>
</textarea></div>
				</div>
				<div class="divTableRow">
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['makePublic'];?>
:</div>
					<div class="divTableCell" style="vertical-align: middle; padding-top: 18px;"><input type="checkbox" name="albumPublic" id="albumPublic" value="1" <?php if ($_smarty_tpl->tpl_vars['album']->value['publicgal']){?>checked="checked"<?php }?>></div>
				</div>
				<?php if ($_smarty_tpl->tpl_vars['config']->value['contrPasswordAlbums']){?>
				<div class="divTableRow shareLinkDiv" <?php if (!$_smarty_tpl->tpl_vars['album']->value['publicgal']){?>style="display: none;"<?php }?>>
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['password'];?>
:</div>
					<div class="divTableCell"><input type="text" name="albumPassword" value="<?php echo $_smarty_tpl->tpl_vars['album']->value['password'];?>
" class="form-control"><span style="font-size: 10px; color: #999"><?php echo $_smarty_tpl->tpl_vars['lang']->value['passwordLeaveBlank'];?>
</span></div>
				</div>
				<?php }?>
				<div class="divTableRow rowSpacer"><div class="divTableCell"><!-- SPACER --></div></div>
				<div class="divTableRow shareLinkDiv" <?php if (!$_smarty_tpl->tpl_vars['album']->value['publicgal']){?>style="display: none;"<?php }?>>
					<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['link'];?>
:</div>
					<div class="divTableCell"><input type="text" name="shareLink" value="<?php echo $_smarty_tpl->tpl_vars['shareLink']->value;?>
" class="form-control"></div>
				</div>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeWorkbox btn btn-xs btn-danger"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['save'];?>
" id="saveWorkboxForm" class="btn btn-xs btn-primary"></div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='contrDeleteImportMedia'){?>
		<script>
			$(function()
			{
				$('#deleteMediaButton').click(function(event)
				{
					doDeleteImportMedia();
					closeWorkbox();
				});
			});
		</script>
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['warning'];?>
</h1>
			<p class="dimMessage"><?php echo $_smarty_tpl->tpl_vars['lang']->value['deleteMediaMes'];?>
</p>
		</div>
		<div class="workboxActionButtons"><input type="button" href="actions.php?action=contrDeleteAlbum&albumID=<?php echo $_smarty_tpl->tpl_vars['contrAlbumID']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['yes'];?>
" id="deleteMediaButton"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['no'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='contrDeleteAlbum'){?>
		<script>
			$(function()
			{
				$('#deleteAlbumButton').click(function(event)
				{
					goto($(this).attr('href'));
				});
			});
		</script>
		
		<div id="editWorkbox">
			<h1><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png" style="vertical-align: middle"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['warning'];?>
</h1>
			<p class="dimMessage"><?php echo $_smarty_tpl->tpl_vars['lang']->value['deleteAlbumMes'];?>
</p>
		</div>
		<div class="workboxActionButtons"><input type="button" href="actions.php?action=contrDeleteAlbum&albumID=<?php echo $_smarty_tpl->tpl_vars['contrAlbumID']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['yes'];?>
" id="deleteAlbumButton"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['no'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='contrUploadMedia'){?>
		<?php if ($_smarty_tpl->tpl_vars['member']->value['uploader']==2){?>
			<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
			<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/plupload/plupload.full.js"></script>
			<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
			<style type="text/css">
				@import url(<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css);
				.plupload_button.plupload_start, .plupload_header{ display:none; } /* Hide start button */
			</style>
		<?php }?>
		
		<script>			
			$(function()
			{				
				<?php if ($_smarty_tpl->tpl_vars['member']->value['uploader']==1){?> // Java				
					$('#startContrUpload').click(function(event)
					{					
						$(this).attr('disabled','disabled'); // Disable start button					
						var error = getUploader().startUpload(); // Start java uploader
						if(error != null) alert(error); // Output error if something goes wrong
					});
					
					$('.cancelUpload').click(function(event)
					{					
						closeWorkbox();
						stopJavaUpload();
					});
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['member']->value['uploader']==2){?> // Plupload
					plupload.addI18n({
							'Select files' : "<?php echo $_smarty_tpl->tpl_vars['lang']->value['plupAddFilesToQueue'];?>
",
							'Add files to the upload queue and click the start button.' : "<?php echo $_smarty_tpl->tpl_vars['lang']->value['plupAddFilesToQueue'];?>
",
							'Filename' : "<?php echo $_smarty_tpl->tpl_vars['lang']->value['plupFilename'];?>
",
							'Status' : "<?php echo $_smarty_tpl->tpl_vars['lang']->value['plupStatus'];?>
",
							'Size' : "<?php echo $_smarty_tpl->tpl_vars['lang']->value['plupSize'];?>
",
							'Add files' : "<?php echo $_smarty_tpl->tpl_vars['lang']->value['plupAddFiles'];?>
",
							'Start upload':"<?php echo $_smarty_tpl->tpl_vars['lang']->value['plupStartUplaod'];?>
",
							'Stop current upload' : "<?php echo $_smarty_tpl->tpl_vars['lang']->value['plupStopUpload'];?>
",
							'Start uploading queue' : "<?php echo $_smarty_tpl->tpl_vars['lang']->value['plupStartQueue'];?>
",
							'Drag files here.' : "<?php echo $_smarty_tpl->tpl_vars['lang']->value['plupDragFilesHere'];?>
"
					});	
					
					$('.cancelUpload').click(function(event)
					{					
						closeWorkbox();
					});
										
					$("#uploadContainer").pluploadQueue({
						// General settings
						runtimes : 'gears,html5,flash,silverlight,browserplus',
						url : '<?php echo $_smarty_tpl->tpl_vars['upSet']->value['handler'];?>
',
						max_file_size : '<?php echo $_smarty_tpl->tpl_vars['upSet']->value['maxFilesize'];?>
mb',
						chunk_size : '1mb',
						unique_names : false,				
						// Specify what files to browse for
						filters : [
							{ title : "Files", extensions : "<?php echo $_smarty_tpl->tpl_vars['upSet']->value['allowedFileTypes'];?>
" }
						],
						// Flash settings
						flash_swf_url : '<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/plupload/plupload.flash.swf',				
						// Silverlight settings
						silverlight_xap_url : '<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/plupload/plupload.silverlight.xap'
					});	
					
					var uploader = $("#uploadContainer").pluploadQueue();
					
					// preinit: attachCallbacks,
					
					$('#startContrUpload').click(function(event)
					{					
						uploader.start();
					});
					
					uploader.bind('UploadComplete', function(Up, File, Response)
					{
						$('#uploadMediaStep1').hide();
						var workboxPage = 'workbox.php?mode=contrAssignMediaDetails&saveMode=newUpload';
						workbox({ page : workboxPage, skipOverlay : true });						
					});
					
					uploader.bind('FilesAdded', function(Up, File, Response)
					{
						if(uploader.files.length > 0)
						{
							$('#startContrUpload').removeAttr('disabled');
						}
						else
						{
							$('#startContrUpload').attr('disabled','disabled');
						}
					});
					
					uploader.bind('FilesRemoved', function(Up, File, Response)
					{
						if(uploader.files.length > 0)
						{
							$('#startContrUpload').removeAttr('disabled');
						}
						else
						{
							$('#startContrUpload').attr('disabled','disabled');
						}
					});
					
					/*
					uploader.bind('BeforeUpload', function(up, file)
					{
						if('thumb' in file)
						  up.settings.resize = { width : 150, height : 150, quality : 100 };
						else
						  up.settings.resize = { width : 1600, height : 1600, quality : 100 };
					});
					
					uploader.bind('FileUploaded', function(up, file) {
						if(!('thumb' in file))
						{
							file.thumb = true;
							file.loaded = 0;
							file.percent = 0;
							file.status = plupload.QUEUED;
							up.trigger("QueueChanged");
							up.refresh();
						}
					});
					*/
															
				<?php }?>
				
			});
			
			/*
			function attachCallbacks(uploader)
			{
				uploader.bind('UploadComplete', function(Up, File, Response) {
					alert('test');
				});
			}
			*/
		</script>
		
		<div id="editWorkbox">
			<div id="uploadMediaStep1">
				<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['uploadMediaUpper'];?>
</h1>
				<div style="margin-bottom: 30px;" id="uploadContainer">
				<?php if ($_smarty_tpl->tpl_vars['member']->value['uploader']==1){?>
					<object name="jumpLoaderApplet" type="application/x-java-applet" height="400" width="100%" mayscript> 
						<param name="code" value="jmaster.jumploader.app.JumpLoaderApplet" > 
						<param name="archive" value="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/jumploader/jumploader_z.jar" > 
						<param name="mayscript" value="true" >
						<param name="ac_fireAppletInitialized" value="true" >
						<param name="vc_lookAndFeel" value="system">
						<param name="uc_uploadUrl" value="<?php echo $_smarty_tpl->tpl_vars['upSet']->value['handler'];?>
">                            
						<param name="uc_maxFileLength" value="<?php echo $_smarty_tpl->tpl_vars['upSet']->value['maxFilesize'];?>
">
						<param name="uc_maxFiles" value="<?php echo $_smarty_tpl->tpl_vars['upSet']->value['maxFiles'];?>
">
						<param name="uc_uploadScaledImagesNoZip" value="true">
						<param name="uc_uploadOriginalImage" value="true"> 
						<param name="uc_fileNamePattern" value="^.+\.(?i)(<?php echo $_smarty_tpl->tpl_vars['upSet']->value['allowedFileTypes'];?>
)$">
						<param name="vc_uploadViewMenuBarVisible" value="true">
						<param name="vc_uploadViewStartActionVisible" value="false">
						<param name="vc_mainViewFileListViewVisible" value="false">
						<param name="vc_mainViewFileListViewHeightPercent" value="20">
						<param name="ac_fireUploaderFileStatusChanged" value="true"> 
						<param name="ac_fireUploaderFileAdded" value="true">                             
						<param name="ac_fireUploaderFileRemoved" value="true">
						<param name="uc_uploadScaledImages" value="true">
						<param name="uc_scaledInstanceNames" value="<?php echo $_smarty_tpl->tpl_vars['upSet']->value['uc_scaledInstanceNames'];?>
">
						<param name="uc_scaledInstanceDimensions" value="<?php echo $_smarty_tpl->tpl_vars['upSet']->value['uc_scaledInstanceDimensions'];?>
">
						<param name="uc_scaledInstanceQualityFactors" value="<?php echo $_smarty_tpl->tpl_vars['upSet']->value['uc_scaledInstanceQualityFactors'];?>
">
						<param name="uc_imageSubsamplingFactor" value="20">
					</object>
				<?php }?>
				</div>
				<div style="margin-bottom: 30px; padding: 5px 0 0 5px;"><strong><?php echo $_smarty_tpl->tpl_vars['lang']->value['batchUploader'];?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['lang']->value['uploader'][$_smarty_tpl->tpl_vars['member']->value['uploader']];?>
 <a href="<?php echo linkto(array('page'=>"account.php"),$_smarty_tpl);?>
" class="colorLink">[<?php echo $_smarty_tpl->tpl_vars['lang']->value['change'];?>
]</a></div>
				<div class="workboxActionButtons" style="clear: both;"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="cancelUpload btn btn-xs btn-danger"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['startUpper'];?>
" id="startContrUpload" disabled="disabled" class="btn btn-xs btn-primary"></div>
			</div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='contrAssignMediaDetails'){?>
		<script>
			$(function()
			{				
				//alert('test');
				registerDG({ id : 'contrUploadDetailsDG' }); // Register the data group contrUploadDetailsDG
				registerAssignDetailsButtons();
				
				var selectedGalleries = [];
				
				<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_galleries']){?>
					loadContrGalleries(); // Load galleries if admin galleries is turned on
				<?php }?>
				
				$('#originalLicense').change(function()
				{
					var ltype = $('#originalLicense option:selected').attr('ltype');

					if(ltype == 'fr')
						$('#originalPriceContainer').hide();
					else
						$('#originalPriceContainer').show();
				});
				
				$('.moreLangs').clicktoggle(function()
					{
						var control = $(this).attr('control');
						$('#'+control).show();			
						$(this).attr('value','--');
					},
					function()
					{
						var control = $(this).attr('control');
						$('#'+control).hide();			
						$(this).attr('value','+');
					}
				);
				
				$('.keywordEntry').click(function()
				{
					var control = ($(this).attr('id').split('-'))[1]; // Find the language that we are controlling
					var keywords = ($('#keywordInput-'+control).val()).split(',');	// Split keywords by comma
					$('#keywordInput-'+control).val(''); // Clear the input
					
					if($(keywords).length > 0)
						$('#keywordsContainer-'+control).show(); // Show the container
					
					$(keywords).each(function(key,elem)
					{
						if(elem)
						{
							elem = $.trim(elem); // Trim whitespace
							$('#keywordsContainer-'+control).append('<p class="removeKeyword"><input type="hidden" name="keyword['+control+'][]" value="'+elem+'">'+elem+' <img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/close.button.png"></p>');
						}
					});
					
					registerKeywordRemove(); // Register the remove function
				});
				
				//registerKeywordRemove(); // Register this function when first loaded - Only needed on an edit
				
				$('#albumPublic').click(function()
				{	
					if($('#albumPublic').is(':checked'))
					{
						$('#albumPasswordRow').show();
					}
					else
					{
						$('#albumPasswordRow').hide();
					}
				});
			
			});
			
			function registerKeywordRemove()
			{
				$('.removeKeyword').unbind('click');				
				$('.removeKeyword').click(function()
				{
					$(this).remove();
				});	
			}
		</script>
		
		<div id="editWorkbox">
			<div id="contrImportContainerWB" style="display: none;">
				<p style="font-size: 24px; margin-bottom: 20px;" id="importSavingMes"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/loader1.gif"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['saving'];?>
...</p>
				<div>
					<div id="contrImportStatusRow">
						<p><a href="#" id="contrMediaImportDetails"><?php echo $_smarty_tpl->tpl_vars['lang']->value['details'];?>
</a></p>
						<div id="loaderContainer"><p></p></div>
					</div>
					<div id="contrImportLog" style="display: none;">
						<ul id="contrImportLogList">
							<li style="display: none;"></li>		
						</ul>
					</div>
				</div>
				<div class="workboxActionButtons" style="clear: both;"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['close'];?>
" class="closeWorkbox btn btn-xs btn-danger" disabled="disabled"></div>
			</div>
			<div id="contrMediaDetailsContainer">
				<input type="hidden" name="saveMode" id="saveMode" value="<?php echo $_smarty_tpl->tpl_vars['saveMode']->value;?>
">
				<input type="hidden" name="contrSaveSessionForm" id="contrSaveSessionForm" value="<?php echo $_smarty_tpl->tpl_vars['contrSaveSessionForm']->value;?>
">
				<?php  $_smarty_tpl->tpl_vars['filePath'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['filePath']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contrImportFiles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['filePath']->key => $_smarty_tpl->tpl_vars['filePath']->value){
$_smarty_tpl->tpl_vars['filePath']->_loop = true;
?>
					<input type="hidden" name="files[]" value="<?php echo $_smarty_tpl->tpl_vars['filePath']->value;?>
" class="uploadFiles">
				<?php } ?>
				<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['addMediaDetails'];?>
</h1><br> 
				<div id="contrUploadDetailsDG" style="margin-bottom: 30px;">
					<ul class="tabs">
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['personal_galleries']){?><li container="albumsDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['ablum'];?>
</li><?php }?>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_galleries']){?><li container="galleriesDGC" id="galleriesTab"><?php echo $_smarty_tpl->tpl_vars['lang']->value['galleries'];?>
</li><?php }?>
						<li container="detailsDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['details'];?>
</li>
						<?php if ($_smarty_tpl->tpl_vars['sellDigital']->value){?><li container="pricingDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['pricing'];?>
</li><?php }?>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_prints']&&$_smarty_tpl->tpl_vars['printRows']->value){?><li container="printsDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['prints'];?>
</li><?php }?>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_products']&&$_smarty_tpl->tpl_vars['productRows']->value){?><li container="productsDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['products'];?>
</li><?php }?>
					</ul>
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['personal_galleries']){?>
						<div class="dataGroupContainer" id="albumsDGC">							
							<br><br><?php echo $_smarty_tpl->tpl_vars['lang']->value['selectAlbumMes'];?>

							<div class="divTable ablumTypeTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="none" checked="checked" id="ablumTypeNone" class="albumType"></div>
									<div class="divTableCell"><label for="ablumTypeNone"><?php echo $_smarty_tpl->tpl_vars['lang']->value['none'];?>
</label></div>
								</div>
								<div class="divTableRow rowSpacer"><div class="divTableCell"><!-- SPACER --></div></div>
								<div class="divTableRow opac40">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="new" id="ablumTypeNew" class="albumType"></div>
									<div class="divTableCell">
										<label for="ablumTypeNew">
											<?php echo $_smarty_tpl->tpl_vars['lang']->value['newAlbum'];?>
<br>
											<input type="text" name="newAlbumName" style="margin-top: 6px;" class="form-control">
										</label>										
										<div class="divTable" style="margin: 0; display: none;" id="newAlbumSettings">
											<div class="divTableRow">
												<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['makePublic'];?>
:</div>
												<div class="divTableCell" style="vertical-align: middle; padding-top: 18px;"><input type="checkbox" name="albumPublic" id="albumPublic" value="1"></div>
											</div>
											<div class="divTableRow" id="albumPasswordRow" style="display: none;">
												<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['password'];?>
:</div>
												<div class="divTableCell"><input type="text" name="newAlbumPassword" id="newAlbumPassword" class="form-control" /><span style="font-size: 10px; color: #999"><?php echo $_smarty_tpl->tpl_vars['lang']->value['passwordLeaveBlank'];?>
</span></div>
											</div>
										</div>
									</div>
								</div>
								<?php if ($_smarty_tpl->tpl_vars['contrAlbums']->value){?>
								<div class="divTableRow rowSpacer"><div class="divTableCell"><!-- SPACER --></div></div>
								<div class="divTableRow opac40">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="existing" id="ablumTypeCurrent" class="albumType"></div>
									<div class="divTableCell">
										<label for="ablumTypeCurrent">
											<?php echo $_smarty_tpl->tpl_vars['lang']->value['myAlbums'];?>
<br>
											<select style="width: 258px; margin-top: 6px;" name="albumID" class="form-control">
												<?php  $_smarty_tpl->tpl_vars['album'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['album']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contrAlbums']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['album']->key => $_smarty_tpl->tpl_vars['album']->value){
$_smarty_tpl->tpl_vars['album']->_loop = true;
?>
													<option value="<?php echo $_smarty_tpl->tpl_vars['album']->value['gallery_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['album']->value['name'];?>
</option>
												<?php } ?>
											</select>
										</label>										
									</div>
								</div>
								<?php }?>
							</div>
						</div>
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_galleries']){?>
						<div class="dataGroupContainer" id="galleriesDGC">
							<br><br><?php echo $_smarty_tpl->tpl_vars['lang']->value['selectGalleriesMes'];?>

							<select id="gallerySelector" multiple="multiple" name="contrGalleries[]" style="width: 100%; height: 200px; margin-top: 20px; margin-bottom: 20px;">
								
							</select>
						</div>
					<?php }?>
					<div class="dataGroupContainer" id="detailsDGC" style="overflow: auto;">
						<div style="float: left">
							<div class="divTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">Title</div>
									<div class="divTableCell formFieldList">
										<ul>
											<li><input type="text" name="title[<?php echo $_smarty_tpl->tpl_vars['activeLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]['id'];?>
]"> <?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?>&nbsp;<?php echo $_smarty_tpl->tpl_vars['activeLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]['name'];?>
 <input type="button" value="+" style="margin-top: 3px;" control="titles" class="moreLangs"><?php }?></li>
										</ul>
										<?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?>
										<ul style="display: none" id="titles">
											<?php if (count($_smarty_tpl->tpl_vars['activeLanguages']->value)>1){?>
												<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['activeLanguages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
													<?php if ($_smarty_tpl->tpl_vars['language']->key!=$_smarty_tpl->tpl_vars['selectedLanguage']->value){?><li><input type="text" name="title[<?php echo $_smarty_tpl->tpl_vars['language']->key;?>
]"> &nbsp;<?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</li><?php }?>
												<?php } ?>
											<?php }?>
										</ul>
										<?php }?>									
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 10px;"></div></div><!-- SPACER -->
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">Description</div>
									<div class="divTableCell formFieldList">
										<ul>
											<li><textarea style="width: 250px; height: 80px; float: left;" name="description[<?php echo $_smarty_tpl->tpl_vars['activeLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]['id'];?>
]"></textarea> <?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?> &nbsp; <?php echo $_smarty_tpl->tpl_vars['activeLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]['name'];?>
 <input type="button" value="+" style="margin-top: 3px;" control="descriptions" class="moreLangs"><?php }?></li>
										</ul>
										<?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?>
										<ul style="display: none" id="descriptions">
											<?php if (count($_smarty_tpl->tpl_vars['activeLanguages']->value)>1){?>
												<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['activeLanguages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
													<?php if ($_smarty_tpl->tpl_vars['language']->key!=$_smarty_tpl->tpl_vars['selectedLanguage']->value){?><li><textarea style="width: 250px; height: 80px; float: left;" name="description[<?php echo $_smarty_tpl->tpl_vars['language']->key;?>
]"></textarea> &nbsp; <?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</li><?php }?>
												<?php } ?>
											<?php }?>
										</ul>
										<?php }?>
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 10px;"></div></div><!-- SPACER -->
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">Keywords</div>
									<div class="divTableCell formFieldList">										
										<ul>
											<li>
												<div><input type="text" id="keywordInput-<?php echo $_smarty_tpl->tpl_vars['selectedLanguage']->value;?>
"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['addUpper'];?>
" id="keywordAdd-<?php echo $_smarty_tpl->tpl_vars['selectedLanguage']->value;?>
" class="keywordEntry" style="margin: -4px 0 0 -44px;"> <?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?> &nbsp; <?php echo $_smarty_tpl->tpl_vars['activeLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]['name'];?>
 <input type="button" value="+" style="margin-top: 3px;" control="keywords" class="moreLangs"><?php }?></div>
												<div class="keywordsContainer" id="keywordsContainer-<?php echo $_smarty_tpl->tpl_vars['selectedLanguage']->value;?>
" style="display: none;"></div>
											</li>
										</ul>
										<?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?>
										<ul style="display: none" id="keywords">
											<?php if (count($_smarty_tpl->tpl_vars['activeLanguages']->value)>1){?>
												<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['activeLanguages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
													<?php if ($_smarty_tpl->tpl_vars['language']->key!=$_smarty_tpl->tpl_vars['selectedLanguage']->value){?>
														<li>
															<div><input type="text" id="keywordInput-<?php echo $_smarty_tpl->tpl_vars['language']->key;?>
"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['addUpper'];?>
" id="keywordAdd-<?php echo $_smarty_tpl->tpl_vars['language']->key;?>
" class="keywordEntry" style="margin: -4px 0 0 -44px;"> &nbsp; <?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</div>
															<div class="keywordsContainer" id="keywordsContainer-<?php echo $_smarty_tpl->tpl_vars['language']->key;?>
" style="display: none;"></div>
														</li>
													<?php }?>
												<?php } ?>
											<?php }?>
										</ul>
										<?php }?>									
									</div>
								</div>
							</div>
						</div>
						<div style="float: left">
							<div class="divTable">
								<?php if ($_smarty_tpl->tpl_vars['mediaTypesRows']->value&&$_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']){?>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: top"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelMediaTypes'];?>
</div>
									<div class="divTableCell">
										<ul>
											<?php  $_smarty_tpl->tpl_vars['mediaType'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['mediaType']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['mediaTypes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['mediaType']->key => $_smarty_tpl->tpl_vars['mediaType']->value){
$_smarty_tpl->tpl_vars['mediaType']->_loop = true;
?>
												<li style="margin-bottom: 6px;"><input type="checkbox" name="mediaTypes[<?php echo $_smarty_tpl->tpl_vars['mediaType']->value['mt_id'];?>
]" id="mediaType<?php echo $_smarty_tpl->tpl_vars['mediaType']->value['mt_id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['mediaType']->value['mt_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['mediaType']->value['selected']){?>checked="checked"<?php }?>> <label for="mediaType<?php echo $_smarty_tpl->tpl_vars['mediaType']->value['mt_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['mediaType']->value['name'];?>
</label></li>
											<?php } ?>
										</ul>
									</div>
								</div>
								<?php }?>
							</div>
						</div>
					</div>
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']){?>
						<div class="dataGroupContainer" id="pricingDGC">
							<br><br><?php echo $_smarty_tpl->tpl_vars['lang']->value['chooseItemsMes'];?>
<br><br><br>
							
							<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['contr_digital']){?>
							<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['original'];?>
</h1>
							<div class="divTable contrItemSelectTable">
								<div class="divTableRow">
									<div class="divTableCell"><input type="checkbox" name="original" value="1" class="contrItem" id="contrDigitalOriginal" checked="checked"></div>
									<div class="divTableCell">
										<label for="contrDigitalOriginal"><?php echo $_smarty_tpl->tpl_vars['lang']->value['original'];?>
</label>
									</div>
									<div class="divTableCell">
										<div class="pricingInfo" style="display: block">
											<div id="originalPriceContainer">
												<?php if ($_smarty_tpl->tpl_vars['digitalCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="originalCredits" style="width: 100px;"><p></p></div><?php }?>
												<?php if ($_smarty_tpl->tpl_vars['digitalCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="originalPrice" style="width: 100px;"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
											</div>
											<div <?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['contr_col']){?>style="display: none;"<?php }?>>
												<select id="originalLicense" name="originalLicense">
													<?php  $_smarty_tpl->tpl_vars['license'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['license']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['licenses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['license']->key => $_smarty_tpl->tpl_vars['license']->value){
$_smarty_tpl->tpl_vars['license']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['license']->key;
?>
														<option value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" ltype="<?php echo $_smarty_tpl->tpl_vars['license']->value['lic_purchase_type'];?>
"><?php echo $_smarty_tpl->tpl_vars['license']->value['name'];?>
</option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
							</div>
							<?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['digitalRows']->value['photo']&&$_smarty_tpl->tpl_vars['member']->value['membershipDetails']['additional_sizes']){?>
								<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['photoProfiles'];?>
</h1>
								<div class="divTable contrItemSelectTable">
									<?php  $_smarty_tpl->tpl_vars['profile'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['profile']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['photoProfiles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['profile']->key => $_smarty_tpl->tpl_vars['profile']->value){
$_smarty_tpl->tpl_vars['profile']->_loop = true;
?>
										<input type="hidden" name="digitalLicense[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['license_id'];?>
">
										<div class="divTableRow opac40">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="contrItem" id="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
"></div>
											<div class="divTableCell">
												<label for="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['profile']->value['name'];?>
</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo">
													<ul>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['licenseType'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['licenseLang'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['width']||$_smarty_tpl->tpl_vars['profile']->value['height']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['resolution'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['width'];?>
x<?php echo $_smarty_tpl->tpl_vars['profile']->value['height'];?>
<?php echo $_smarty_tpl->tpl_vars['lang']->value['px'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['format']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFormat'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['format'];?>
</strong></li><?php }?>
													</ul>
													<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']!='fr'&&$_smarty_tpl->tpl_vars['profile']->value['license']!='cu'){?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="digitalCredits[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;"><p></p></div><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="digitalPrice[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
													<?php }?>
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									<?php } ?>
								</div>
							<?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['digitalRows']->value['video']&&$_smarty_tpl->tpl_vars['member']->value['membershipDetails']['additional_sizes']){?>
								<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['videoProfiles'];?>
</h1>
								<div class="divTable contrItemSelectTable">
									<?php  $_smarty_tpl->tpl_vars['profile'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['profile']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['videoProfiles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['profile']->key => $_smarty_tpl->tpl_vars['profile']->value){
$_smarty_tpl->tpl_vars['profile']->_loop = true;
?>
										<input type="hidden" name="digitalLicense[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['license_id'];?>
">
										<div class="divTableRow opac40">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="contrItem" id="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
"></div>
											<div class="divTableCell">
												<label for="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['profile']->value['name'];?>
</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo">
													<ul>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['licenseType'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['licenseLang'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['width']||$_smarty_tpl->tpl_vars['profile']->value['height']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['resolution'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['width'];?>
x<?php echo $_smarty_tpl->tpl_vars['profile']->value['height'];?>
<?php echo $_smarty_tpl->tpl_vars['lang']->value['px'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['format']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFormat'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['format'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['dsp_type']=='video'){?>
															<?php if ($_smarty_tpl->tpl_vars['profile']->value['fps']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFPS'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['digital']->value['fps'];?>
</strong></li><?php }?>
															<?php if ($_smarty_tpl->tpl_vars['profile']->value['running_time']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelRunningTime'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['digital']->value['running_time'];?>
</strong> <?php echo $_smarty_tpl->tpl_vars['lang']->value['seconds'];?>
</li><?php }?>
														<?php }?>
													</ul>
													<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']!='fr'&&$_smarty_tpl->tpl_vars['profile']->value['license']!='cu'){?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="digitalCredits[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;"><p></p></div><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="digitalPrice[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
													<?php }?>
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									<?php } ?>
								</div>
							<?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['digitalRows']->value['other']&&$_smarty_tpl->tpl_vars['member']->value['membershipDetails']['additional_sizes']){?>
								<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['otherProfiles'];?>
</h1>
								<div class="divTable contrItemSelectTable">
									<?php  $_smarty_tpl->tpl_vars['profile'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['profile']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['otherProfiles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['profile']->key => $_smarty_tpl->tpl_vars['profile']->value){
$_smarty_tpl->tpl_vars['profile']->_loop = true;
?>
										<input type="hidden" name="digitalLicense[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['license_id'];?>
">
										<div class="divTableRow opac40">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="contrItem" id="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
"></div>
											<div class="divTableCell">
												<label for="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['profile']->value['name'];?>
</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo">
													<ul>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['licenseType'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['licenseLang'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['width']||$_smarty_tpl->tpl_vars['profile']->value['height']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['resolution'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['width'];?>
x<?php echo $_smarty_tpl->tpl_vars['profile']->value['height'];?>
<?php echo $_smarty_tpl->tpl_vars['lang']->value['px'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['format']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFormat'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['format'];?>
</strong></li><?php }?>
													</ul>
													<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']!='fr'&&$_smarty_tpl->tpl_vars['profile']->value['license']!='cu'){?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="digitalCredits[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;"><p></p></div><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="digitalPrice[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
													<?php }?>
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									<?php } ?>
								</div>
							<?php }?>
							
						</div>
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_prints']&&$_smarty_tpl->tpl_vars['printRows']->value){?>
						<div class="dataGroupContainer" id="printsDGC">
							<br><br><?php echo $_smarty_tpl->tpl_vars['lang']->value['chooseItemsMes'];?>
<br>
							<div class="divTable contrItemSelectTable">
								<?php  $_smarty_tpl->tpl_vars['print'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['print']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['prints']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['print']->key => $_smarty_tpl->tpl_vars['print']->value){
$_smarty_tpl->tpl_vars['print']->_loop = true;
?>
									<div class="divTableRow opac40">
										<div class="divTableCell"><input type="checkbox" name="print[]" value="<?php echo $_smarty_tpl->tpl_vars['print']->value['print_id'];?>
" class="contrItem" id="contrPrint<?php echo $_smarty_tpl->tpl_vars['print']->value['print_id'];?>
"></div>
										<div class="divTableCell">
											<label for="contrPrint<?php echo $_smarty_tpl->tpl_vars['print']->value['print_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['print']->value['name'];?>
</label>
										</div>
										<div class="divTableCell">
											<div class="pricingInfo">
												<ul>
													<?php if ($_smarty_tpl->tpl_vars['print']->value['description']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelDesc'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['print']->value['description'];?>
</strong></li><?php }?>
												</ul>
												<?php if ($_smarty_tpl->tpl_vars['printCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="printCredits[<?php echo $_smarty_tpl->tpl_vars['print']->value['print_id'];?>
]" style="width: 100px;"><p></p></div><?php }?>
												<?php if ($_smarty_tpl->tpl_vars['printCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="printPrice[<?php echo $_smarty_tpl->tpl_vars['print']->value['print_id'];?>
]" style="width: 100px;"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
											</div>
										</div>
									</div>
									<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
								<?php } ?>
							</div>
						</div>
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_products']&&$_smarty_tpl->tpl_vars['productRows']->value){?>
						<div class="dataGroupContainer" id="productsDGC">
							<br><br><?php echo $_smarty_tpl->tpl_vars['lang']->value['chooseItemsMes'];?>
<br>
							<div class="divTable contrItemSelectTable">
								<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
									<div class="divTableRow opac40">
										<div class="divTableCell"><input type="checkbox" name="product[]" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['prod_id'];?>
" class="contrItem" id="contrProd<?php echo $_smarty_tpl->tpl_vars['product']->value['prod_id'];?>
"></div>
										<div class="divTableCell">
											<label for="contrProd<?php echo $_smarty_tpl->tpl_vars['product']->value['prod_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
</label>
										</div>
										<div class="divTableCell">
											<div class="pricingInfo">
												<ul>
													<?php if ($_smarty_tpl->tpl_vars['product']->value['description']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelDesc'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['product']->value['description'];?>
</strong></li><?php }?>
												</ul>
												<?php if ($_smarty_tpl->tpl_vars['prodCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="productCredits[<?php echo $_smarty_tpl->tpl_vars['product']->value['prod_id'];?>
]" style="width: 100px;"><p></p></div><?php }?>
												<?php if ($_smarty_tpl->tpl_vars['prodCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="productPrice[<?php echo $_smarty_tpl->tpl_vars['product']->value['prod_id'];?>
]" style="width: 100px;"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
											</div>
										</div>
									</div>
									<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
								<?php } ?>
							</div>
						</div>
					<?php }?>
				</div>
				<div class="workboxActionButtons" style="clear: both;"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeImportWorkbox btn btn-xs btn-danger"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['save'];?>
" class="saveContrAssignMediaDetails btn btn-xs btn-primary"></div>
			</div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='contrFailedApproval'){?>
		<div id="editWorkbox">
			<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['media']->value['approvalStatusLang']];?>
</h1>
			<p class="notice">
				<?php if ($_smarty_tpl->tpl_vars['media']->value['approval_message']){?><?php echo $_smarty_tpl->tpl_vars['media']->value['approval_message'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['lang']->value['noDetailsMes'];?>
<?php }?>
			</p>
			<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['close'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='contrMailinMedia'){?>
		<div id="editWorkbox">
			<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['mailInMedia'];?>
</h1>
			<p class="dimMessage">
				<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['contr_cd2_mes'];?>

				<br><br>
				<strong><?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_name'];?>
</strong><br>
				<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_address'];?>
<br>
				<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['business_address2']){?><?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_address2'];?>
<br><?php }?>
				<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_city'];?>
, <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_state'];?>
 <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_zip'];?>
<br>
				<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_country'];?>

				<br><br>
			</p>
			<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['close'];?>
" class="closeWorkbox btn btn-xs btn-danger"></div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='editContrMedia'){?>
		<script>
			function setSelectedGalleries()
			{	
				<?php  $_smarty_tpl->tpl_vars['galleryID'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['galleryID']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['selectedGalleries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['galleryID']->key => $_smarty_tpl->tpl_vars['galleryID']->value){
$_smarty_tpl->tpl_vars['galleryID']->_loop = true;
?>
					selectedGalleries[<?php echo $_smarty_tpl->tpl_vars['galleryID']->key;?>
] = 'galleryTree<?php echo $_smarty_tpl->tpl_vars['galleryID']->value;?>
';
				<?php } ?>
			}
			
			$(function()
			{				
				registerDG({ id : 'contrUploadDetailsDG' }); // Register the data group contrUploadDetailsDG
				registerAssignDetailsButtons();
				registerKeywordRemove();
				setSelectedGalleries();
				
				sampleUploader('#thumbUploader');
				sampleUploader('#videoUploader'); 
				sampleUploader('#propReleaseUploader');
				sampleUploader('#modelReleaseUploader');
				
				<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_galleries']){?>
					loadContrGalleries(); // Load galleries if admin galleries is turned on
				<?php }?>
				
				$('#contrMediaTypeSelection').change(function()
				{
					var profile = $(this).val();
					
					if(profile == 'video')
					{
						$('.typeVideo').show();
						$('#videoButton').show();
					}
					else
					{
						$('.typeVideo').hide();
						$('#videoButton').hide();
					}
				});
				
				$('#originalLicense').change(function()
				{
					var ltype = $('#originalLicense option:selected').attr('ltype');

					if(ltype == 'fr')
						$('#originalPriceContainer').hide();
					else
						$('#originalPriceContainer').show();
				});
				
				$('.moreLangs').clicktoggle(function()
					{
						var control = $(this).attr('control');
						$('#'+control).show();			
						$(this).attr('value','--');
					},
					function()
					{
						var control = $(this).attr('control');
						$('#'+control).hide();			
						$(this).attr('value','+');
					}
				);
				
				$('.keywordEntry').click(function()
				{
					var control = ($(this).attr('id').split('-'))[1]; // Find the language that we are controlling
					var keywords = ($('#keywordInput-'+control).val()).split(',');	// Split keywords by comma
					$('#keywordInput-'+control).val(''); // Clear the input
					
					if($(keywords).length > 0)
						$('#keywordsContainer-'+control).show(); // Show the container
					
					var mediaID = $('#mediaID').val();
					
					$(keywords).each(function(key,elem)
					{
						if(elem)
						{							
							elem = $.trim(elem); // Trim whitespace
							
							$.ajax({
								type: 'POST',
								url: baseURL+'/actions.php',
								data: 'action=addContrKeyword&mediaID='+mediaID+'&keyLang='+control+'&keyword='+elem,
								dataType: 'json',
								success: function(data)
								{
									$('#keywordsContainer-'+control).append('<p class="removeKeyword" keyID="'+data.keyID+'"><input type="hidden" name="keyword['+control+'][]" value="'+elem+'">'+elem+' <img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/close.button.png"></p>');
									registerKeywordRemove(); // Register the remove function
								}
							});
						}
					});
				});
				
				$('.attachFile').click(function(event)
				{
					$('#attachFileUploaderContainer').show();
					var dspID = $(this).attr('dspID');
					sampleUploader('#dspUploader',dspID);
					scroll(0,0);
				});
				
				$('.deleteAttachFile').click(function(event)
				{
					var dspID = $(this).attr('dspID');
					var mediaID = $('#mediaID').val();
					
					$.ajax({
						type: 'POST',
						url: baseURL+'/actions.php',
						data: 'action=deleteDSP&mediaID='+mediaID+'&dspID='+dspID,
						dataType: 'json',
						success: function(data)
						{	
							$('#detachButton-'+dspID).hide();					
							$('#attachButton-'+dspID).show();
						}
					});
					
				});
				
				$('.attachFileCloseButton').click(function(event)
				{					
					$('#dspUploader').uploadify('destroy');
				});
				
				//registerKeywordRemove(); // Register this function when first loaded - Only needed on an edit
			});
			
			function registerKeywordRemove()
			{
				$('.removeKeyword').unbind('click');				
				$('.removeKeyword').click(function()
				{
					var keyID = $(this).attr('keyID');
					
					$.ajax({
						type: 'POST',
						url: baseURL+'/actions.php',
						data: 'action=removeContrKeyword&keyID='+keyID,
						dataType: 'json',
						success: function(data)
						{
							
						}
					});
					
					$(this).remove();
				});	
			}
		</script>
		
		<div id="editWorkbox">
			<div id="attachFileUploaderContainer" class="sampleUploaderBox">
				<div class="attachFilePopup">
					<?php echo $_smarty_tpl->tpl_vars['lang']->value['attachMessage'];?>
<br><br>
					<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/close.button.png" class="closeButton closeSampleUploaderBox attachFileCloseButton">
					<input id="dspUploader" name="dspUploader" type="file" buttonText="<?php echo $_smarty_tpl->tpl_vars['lang']->value['browse'];?>
...">
				</div>
			</div>
			<div id="contrMediaDetailsContainer">
				<input type="hidden" name="saveMode" id="saveMode" value="<?php echo $_smarty_tpl->tpl_vars['saveMode']->value;?>
">
				<input type="hidden" name="contrSaveSessionForm" id="contrSaveSessionForm" value="<?php echo $_smarty_tpl->tpl_vars['contrSaveSessionForm']->value;?>
">
				<input type="hidden" name="albumTypeOriginal" id="albumTypeOriginal" value="<?php echo $_smarty_tpl->tpl_vars['selectedAlbum']->value;?>
">
				<input type="hidden" name="mediaID" id="mediaID" value="<?php echo $_smarty_tpl->tpl_vars['media']->value['media_id'];?>
">				
				<input type="hidden" name="maxUploadSize" id="maxUploadSize" value="<?php echo $_smarty_tpl->tpl_vars['maxUploadSize']->value;?>
">
				<!--<input type="text" name="dspID" id="dspID" value="">-->
				<input type="hidden" name="originalMediaDS" id="originalMediaDS" value="<?php echo $_smarty_tpl->tpl_vars['originalMediaDS']->value;?>
">
				
				
				
				
				<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['editMediaDetails'];?>
</h1><br> 
				<div id="contrUploadDetailsDG" style="margin-bottom: 30px;">
					<ul class="tabs">
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['personal_galleries']){?><li container="albumsDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['ablum'];?>
</li><?php }?>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_galleries']){?><li container="galleriesDGC" id="galleriesTab"><?php echo $_smarty_tpl->tpl_vars['lang']->value['galleries'];?>
</li><?php }?>
						<li container="detailsDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['details'];?>
</li>
						<?php if ($_smarty_tpl->tpl_vars['sellDigital']->value){?><li container="pricingDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['pricing'];?>
</li><?php }?>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_prints']&&$_smarty_tpl->tpl_vars['printRows']->value){?><li container="printsDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['prints'];?>
</li><?php }?>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_products']&&$_smarty_tpl->tpl_vars['productRows']->value){?><li container="productsDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['products'];?>
</li><?php }?>
					</ul>
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['personal_galleries']){?>
						<div class="dataGroupContainer" id="albumsDGC">			
							<br><br><?php echo $_smarty_tpl->tpl_vars['lang']->value['selectAlbumMes'];?>

							<div class="divTable ablumTypeTable">
								<div class="divTableRow <?php if ($_smarty_tpl->tpl_vars['selectedAlbum']->value){?>opac40<?php }?>">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="none" <?php if (!$_smarty_tpl->tpl_vars['selectedAlbum']->value){?>checked="checked"<?php }?> id="ablumTypeNone" class="albumType"></div>
									<div class="divTableCell"><label for="ablumTypeNone"><?php echo $_smarty_tpl->tpl_vars['lang']->value['none'];?>
</label></div>
								</div>
								<div class="divTableRow rowSpacer"><div class="divTableCell"><!-- SPACER --></div></div>
								<div class="divTableRow opac40">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="new" id="ablumTypeNew" class="albumType"></div>
									<div class="divTableCell">
										<label for="ablumTypeNew">
											<?php echo $_smarty_tpl->tpl_vars['lang']->value['newAlbum'];?>
<br>
											<input type="text" name="newAlbumName" style="margin-top: 6px;">
										</label>
									</div>
								</div>
								<?php if ($_smarty_tpl->tpl_vars['contrAlbums']->value){?>
								<div class="divTableRow rowSpacer"><div class="divTableCell"><!-- SPACER --></div></div>
								<div class="divTableRow <?php if (!$_smarty_tpl->tpl_vars['selectedAlbum']->value){?>opac40<?php }?>">
									<div class="divTableCell formFieldLabel"><input type="radio" name="albumType" value="existing" <?php if ($_smarty_tpl->tpl_vars['selectedAlbum']->value){?>checked="checked"<?php }?> id="ablumTypeCurrent" class="albumType"></div>
									<div class="divTableCell">
										<label for="ablumTypeCurrent">
											My Albums<br>
											<select style="width: 258px; margin-top: 6px;" name="albumID">
												<?php  $_smarty_tpl->tpl_vars['album'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['album']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contrAlbums']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['album']->key => $_smarty_tpl->tpl_vars['album']->value){
$_smarty_tpl->tpl_vars['album']->_loop = true;
?>
													<option value="<?php echo $_smarty_tpl->tpl_vars['album']->value['gallery_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['album']->value['gallery_id']==$_smarty_tpl->tpl_vars['selectedAlbum']->value){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['album']->value['name'];?>
</option>
												<?php } ?>
											</select>
										</label>										
									</div>
								</div>
								<?php }?>
							</div>
						</div>
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_galleries']){?>
						<div class="dataGroupContainer" id="galleriesDGC">
							<br><br><?php echo $_smarty_tpl->tpl_vars['lang']->value['selectGalleriesMes'];?>

							<select id="gallerySelector" multiple="multiple" name="contrGalleries[]" style="width: 100%; height: 200px; margin-top: 20px; margin-bottom: 20px;">
								
							</select>
						</div>
					<?php }?>
					<div class="dataGroupContainer" id="detailsDGC" style="overflow: auto;">
						<div>
							<div class="divTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelTitle'];?>
</div>
									<div class="divTableCell formFieldList">
										<ul>
											<li><input type="text" name="title[<?php echo $_smarty_tpl->tpl_vars['activeLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]['id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['media']->value['title'];?>
"> <?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?>&nbsp;<?php echo $_smarty_tpl->tpl_vars['activeLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]['name'];?>
 <input type="button" value="+" style="margin-top: 3px;" control="titles" class="moreLangs btn btn-xs btn-primary"><?php }?></li>
										</ul>
										<?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?>
										<ul style="display: none" id="titles">
											<?php if (count($_smarty_tpl->tpl_vars['activeLanguages']->value)>1){?>
												<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['activeLanguages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
													<?php if ($_smarty_tpl->tpl_vars['language']->key!=$_smarty_tpl->tpl_vars['selectedLanguage']->value){?><li><input type="text" name="title[<?php echo $_smarty_tpl->tpl_vars['language']->key;?>
]" value="<?php echo $_smarty_tpl->tpl_vars['media']->value['titleLang'][$_smarty_tpl->tpl_vars['language']->key];?>
"> &nbsp;<?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</li><?php }?>
												<?php } ?>
											<?php }?>
										</ul>
										<?php }?>									
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 10px;"></div></div><!-- SPACER -->
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelDesc'];?>
</div>
									<div class="divTableCell formFieldList">
										<ul>
											<li><textarea style="width: 250px; height: 80px; float: left;" name="description[<?php echo $_smarty_tpl->tpl_vars['activeLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]['id'];?>
]"><?php echo $_smarty_tpl->tpl_vars['media']->value['description'];?>
</textarea> <?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?> &nbsp; <?php echo $_smarty_tpl->tpl_vars['activeLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]['name'];?>
 <input type="button" value="+" style="margin-top: 3px;" control="descriptions" class="moreLangs btn btn-xs btn-primary"><?php }?></li>
										</ul>
										<?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?>
										<ul style="display: none" id="descriptions">
											<?php if (count($_smarty_tpl->tpl_vars['activeLanguages']->value)>1){?>
												<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['activeLanguages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
													<?php if ($_smarty_tpl->tpl_vars['language']->key!=$_smarty_tpl->tpl_vars['selectedLanguage']->value){?><li><textarea style="width: 250px; height: 80px; float: left;" name="description[<?php echo $_smarty_tpl->tpl_vars['language']->key;?>
]"><?php echo $_smarty_tpl->tpl_vars['media']->value['descriptionLang'][$_smarty_tpl->tpl_vars['language']->key];?>
</textarea> &nbsp; <?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</li><?php }?>
												<?php } ?>
											<?php }?>
										</ul>
										<?php }?>
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 10px;"></div></div><!-- SPACER -->
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelKeys'];?>
</div>
									<div class="divTableCell formFieldList">										
										<ul>
											<li>
												<div><input type="text" id="keywordInput-<?php echo $_smarty_tpl->tpl_vars['selectedLanguage']->value;?>
"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['addUpper'];?>
" id="keywordAdd-<?php echo $_smarty_tpl->tpl_vars['selectedLanguage']->value;?>
" class="keywordEntry btn btn-xs btn-primary" style="margin: -4px 0 0 -44px;"> <?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?> &nbsp; <?php echo $_smarty_tpl->tpl_vars['activeLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]['name'];?>
 <input type="button" value="+" style="margin-top: 3px;" control="keywords" class="moreLangs btn btn-xs btn-primary"><?php }?></div>
												<div class="keywordsContainer" id="keywordsContainer-<?php echo $_smarty_tpl->tpl_vars['selectedLanguage']->value;?>
" <?php if (!$_smarty_tpl->tpl_vars['keywords']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]){?>style="display: none;<?php }?>">
													<?php  $_smarty_tpl->tpl_vars['keyword'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['keyword']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['keywords']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['keyword']->key => $_smarty_tpl->tpl_vars['keyword']->value){
$_smarty_tpl->tpl_vars['keyword']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['keyword']->key;
?>
														<p class="removeKeyword" keyID="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"><input type="hidden" name="keyword[<?php echo $_smarty_tpl->tpl_vars['selectedLanguage']->value;?>
][]" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
 <img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/close.button.png"></p>
													<?php } ?>
												</div>
											</li>
										</ul>
										<?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?>
										<ul style="display: none" id="keywords">
											<?php if (count($_smarty_tpl->tpl_vars['activeLanguages']->value)>1){?>
												<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['activeLanguages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
													<?php if ($_smarty_tpl->tpl_vars['language']->key!=$_smarty_tpl->tpl_vars['selectedLanguage']->value){?>
														<li>
															<div><input type="text" id="keywordInput-<?php echo $_smarty_tpl->tpl_vars['language']->key;?>
"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['addUpper'];?>
" id="keywordAdd-<?php echo $_smarty_tpl->tpl_vars['language']->key;?>
" class="keywordEntry btn btn-xs btn-primary" style="margin: -4px 0 0 -44px;"> &nbsp; <?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</div>
															<div class="keywordsContainer" id="keywordsContainer-<?php echo $_smarty_tpl->tpl_vars['language']->key;?>
" <?php if (!$_smarty_tpl->tpl_vars['keywords']->value[$_smarty_tpl->tpl_vars['language']->key]){?>style="display: none;<?php }?>">
																<?php  $_smarty_tpl->tpl_vars['keyword'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['keyword']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['keywords']->value[$_smarty_tpl->tpl_vars['language']->key]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['keyword']->key => $_smarty_tpl->tpl_vars['keyword']->value){
$_smarty_tpl->tpl_vars['keyword']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['keyword']->key;
?>
																	<p class="removeKeyword" keyID="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"><input type="hidden" name="keyword[<?php echo $_smarty_tpl->tpl_vars['language']->key;?>
][]" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
 <img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/close.button.png"></p>
																<?php } ?>
															</div>
														</li>
													<?php }?>
												<?php } ?>
											<?php }?>
										</ul>
										<?php }?>									
									</div>
								</div>
							</div>
						</div>
						<div class="detailsColumn2">
							<div id="thumbUploaderContainer" class="sampleUploaderBox">
								<div style="padding: 20px;">
									<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/close.button.png" class="closeButton closeSampleUploaderBox">
									<img src="<?php echo mediaImage(array('mediaID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedID'],'type'=>'thumb','folderID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedFID'],'size'=>160,'seo'=>$_smarty_tpl->tpl_vars['media']->value['seoName']),$_smarty_tpl);?>
" alt="<?php echo $_smarty_tpl->tpl_vars['media']->value['title'];?>
" class="similarMedia" id="contrMediaThumbnail">
									<div id="sampleUploaderDiv" style="clear: both; position: relative;">
										<input id="thumbUploader" name="thumbUploader" type="file" buttonText="<?php echo $_smarty_tpl->tpl_vars['lang']->value['uploadThumb'];?>
">
										<!--<a href="#" style="position: absolute; z-index: 1; margin-top: -30px;" class="buttonLink"><?php echo $_smarty_tpl->tpl_vars['lang']->value['uploadAvatarXXX'];?>
Upload Thumbnail</a>-->
									</div>
								</div>
							</div>
							<div id="videoUploaderContainer" class="sampleUploaderBox">
								<div style="padding: 20px;">
									<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/close.button.png" class="closeButton closeSampleUploaderBox">
									<input id="videoUploader" name="videoUploader" type="file" buttonText="<?php echo $_smarty_tpl->tpl_vars['lang']->value['uploadVideo'];?>
">
								</div>
							</div>
							<div class="samplesButtons">
								<p id="thumbButton"><?php echo $_smarty_tpl->tpl_vars['lang']->value['thumbnail'];?>
</p>
								<p id="videoButton" <?php if ($_smarty_tpl->tpl_vars['media']->value['dsp_type']!='video'){?>style="display: none;"<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['videoSample'];?>
</p>
							</div>
							<div class="divTable" style="clear: both;">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: middle"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaProfile'];?>
</div>
									<div class="divTableCell">
										<select style="width: 100%;" name="dsp_type" id="contrMediaTypeSelection">
											<option value="photo" <?php if ($_smarty_tpl->tpl_vars['media']->value['dsp_type']=='photo'){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['photo'];?>
</option>
											<option value="video" <?php if ($_smarty_tpl->tpl_vars['media']->value['dsp_type']=='video'){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['video'];?>
</option>
											<option value="other" <?php if ($_smarty_tpl->tpl_vars['media']->value['dsp_type']=='other'){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['other'];?>
</option>
										</select>
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 15px;"><!-- SPACER --></div></div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: middle"><?php echo $_smarty_tpl->tpl_vars['lang']->value['width'];?>
</div>
									<div class="divTableCell" style="vertical-align: middle">
										<input type="text" name="width" value="<?php echo $_smarty_tpl->tpl_vars['media']->value['width'];?>
" style="min-width: 50px; width: 50px;"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['px'];?>

									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: middle"><?php echo $_smarty_tpl->tpl_vars['lang']->value['height'];?>
</div>
									<div class="divTableCell" style="vertical-align: middle">
										<input type="text" name="height" value="<?php echo $_smarty_tpl->tpl_vars['media']->value['height'];?>
" style="min-width: 50px; width: 50px;"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['px'];?>

									</div>
								</div>
								<div class="divTableRow typeVideo" <?php if ($_smarty_tpl->tpl_vars['media']->value['dsp_type']!='video'){?>style="display: none;"<?php }?>>
									<div class="divTableCell formFieldLabel" style="vertical-align: middle"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFPS'];?>
</div>
									<div class="divTableCell">
										<input type="text" name="fps" value="<?php echo $_smarty_tpl->tpl_vars['media']->value['fps'];?>
" style="min-width: 50px; width: 50px;">
									</div>
								</div>
								<div class="divTableRow typeVideo" <?php if ($_smarty_tpl->tpl_vars['media']->value['dsp_type']!='video'){?>style="display: none;"<?php }?>>
									<div class="divTableCell formFieldLabel" style="vertical-align: middle"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFormat'];?>
</div>
									<div class="divTableCell">
										<input type="text" name="format" value="<?php echo $_smarty_tpl->tpl_vars['media']->value['format'];?>
" style="min-width: 50px; width: 50px;">
									</div>
								</div>
								<div class="divTableRow typeVideo" <?php if ($_smarty_tpl->tpl_vars['media']->value['dsp_type']!='video'){?>style="display: none;"<?php }?>>
									<div class="divTableCell formFieldLabel" style="vertical-align: middle"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelRunningTime'];?>
</div>
									<div class="divTableCell" style="vertical-align: middle">
										<input type="text" name="running_time" value="<?php echo $_smarty_tpl->tpl_vars['media']->value['running_time'];?>
" style="min-width: 50px; width: 50px;"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['seconds'];?>

									</div>
								</div>
								<div class="divTableRow typeVideo" <?php if ($_smarty_tpl->tpl_vars['media']->value['dsp_type']!='video'){?>style="display: none;"<?php }?>>
									<div class="divTableCell formFieldLabel" style="vertical-align: middle"><?php echo $_smarty_tpl->tpl_vars['lang']->value['hd'];?>
</div>
									<div class="divTableCell">
										<input type="checkbox" name="hd" value="1" <?php if ($_smarty_tpl->tpl_vars['media']->value['hd']){?>checked="checked"<?php }?>>
									</div>
								</div>
								<div class="divTableRow"><div class="divTableCell" style="height: 15px;"><!-- SPACER --></div></div>
								<?php if ($_smarty_tpl->tpl_vars['mediaTypesRows']->value&&$_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']){?>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: top"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelMediaTypes'];?>
</div>
									<div class="divTableCell">
										<ul>
											<?php  $_smarty_tpl->tpl_vars['mediaType'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['mediaType']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['mediaTypes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['mediaType']->key => $_smarty_tpl->tpl_vars['mediaType']->value){
$_smarty_tpl->tpl_vars['mediaType']->_loop = true;
?>
												<li style="margin-bottom: 6px;"><input type="checkbox" name="mediaTypes[<?php echo $_smarty_tpl->tpl_vars['mediaType']->value['mt_id'];?>
]" id="mediaType<?php echo $_smarty_tpl->tpl_vars['mediaType']->value['mt_id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['mediaType']->value['mt_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['mediaType']->value['selected']){?>checked="checked"<?php }?>> <label for="mediaType<?php echo $_smarty_tpl->tpl_vars['mediaType']->value['mt_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['mediaType']->value['name'];?>
</label></li>
											<?php } ?>
										</ul>
									</div>
								</div>
								<?php }?>
								<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']){?>
								<div class="divTableRow"><div class="divTableCell" style="height: 15px;"><!-- SPACER --></div></div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: top"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelCopyright'];?>
</div>
									<div class="divTableCell">
										<textarea name="copyright" style="width: 100%; height: 50px;"><?php echo $_smarty_tpl->tpl_vars['media']->value['copyright'];?>
</textarea>
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: top"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelRelease'];?>
</div>
									<div class="divTableCell" style="padding-top: 15px;">
										<input type="checkbox" name="modelRelease" id="modelRelease" value="1" <?php if ($_smarty_tpl->tpl_vars['media']->value['model_release_status']){?>checked="checked"<?php }?> style="float: left;">
										<div id="modelReleaseUploaderDiv" style="float: left;">
											<input id="modelReleaseUploader" name="modelReleaseUploader" type="file" buttonText="<?php echo $_smarty_tpl->tpl_vars['lang']->value['uploadFile'];?>
">
											<?php if ($_smarty_tpl->tpl_vars['media']->value['model_release_form']){?><div id="modelReleaseFileDiv"><a href="./assets/files/releases/<?php echo $_smarty_tpl->tpl_vars['media']->value['model_release_form'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['media']->value['model_release_form'];?>
</a> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['delete'];?>
" class="deleteRelease" rType="model"></div><?php }?>
										</div>
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel" style="vertical-align: top"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelPropRelease'];?>
</div>
									<div class="divTableCell" style="padding-top: 18px;">
										<input type="checkbox" name="propRelease" id="propRelease" value="1" <?php if ($_smarty_tpl->tpl_vars['media']->value['prop_release_status']){?>checked="checked"<?php }?> style="float: left;">
										<div id="propReleaseUploaderDiv" style="float: left;">
											<input id="propReleaseUploader" name="propReleaseUploader" type="file" buttonText="<?php echo $_smarty_tpl->tpl_vars['lang']->value['uploadFile'];?>
">
											<?php if ($_smarty_tpl->tpl_vars['media']->value['prop_release_form']){?><div id="propReleaseFileDiv"><a href="./assets/files/releases/<?php echo $_smarty_tpl->tpl_vars['media']->value['prop_release_form'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['media']->value['prop_release_form'];?>
</a> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['delete'];?>
" class="deleteRelease" rType="prop"></div><?php }?>
										</div>
									</div>
								</div>
								<?php }?>
							</div>
						</div>
					</div>
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']){?>
						<div class="dataGroupContainer" id="pricingDGC">
							<br><br><?php echo $_smarty_tpl->tpl_vars['lang']->value['chooseItemsMes'];?>
<br><br><br>
							
							<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['contr_digital']){?>
							<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['original'];?>
</h1>
							<div class="divTable contrItemSelectTable">
								<div class="divTableRow <?php if (!$_smarty_tpl->tpl_vars['media']->value['orgSelected']){?>opac40<?php }?>">
									<div class="divTableCell"><input type="checkbox" name="original" value="1" class="contrItem" id="contrDigitalOriginal" <?php if ($_smarty_tpl->tpl_vars['media']->value['orgSelected']){?>checked="checked"<?php }?>></div>
									<div class="divTableCell">
										<label for="contrDigitalOriginal"><?php echo $_smarty_tpl->tpl_vars['lang']->value['original'];?>
</label>
									</div>
									<div class="divTableCell">
										<div class="pricingInfo" <?php if ($_smarty_tpl->tpl_vars['media']->value['orgSelected']){?>style="display: block"<?php }?>>
											<div id="originalPriceContainer" <?php if ($_smarty_tpl->tpl_vars['media']->value['license']=='fr'){?>style="display: none"<?php }?>>
												<?php if ($_smarty_tpl->tpl_vars['digitalCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="originalCredits" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['media']->value['setCredits'];?>
"><p></p></div><?php }?>
												<?php if ($_smarty_tpl->tpl_vars['digitalCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="originalPrice" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['media']->value['setPrice'];?>
"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
											</div>
											<div <?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['contr_col']){?>style="display: none;"<?php }?>>
												<select id="originalLicense" name="originalLicense">
													<?php  $_smarty_tpl->tpl_vars['license'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['license']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['licenses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['license']->key => $_smarty_tpl->tpl_vars['license']->value){
$_smarty_tpl->tpl_vars['license']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['license']->key;
?>
														<option value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" ltype="<?php echo $_smarty_tpl->tpl_vars['license']->value['lic_purchase_type'];?>
" <?php if ($_smarty_tpl->tpl_vars['license']->value['selected']){?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['license']->value['name'];?>
</option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
							</div>
							<?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['digitalRows']->value['photo']&&$_smarty_tpl->tpl_vars['member']->value['membershipDetails']['additional_sizes']){?>
								<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['photoProfiles'];?>
</h1>
								<div class="divTable contrItemSelectTable">
									<?php  $_smarty_tpl->tpl_vars['profile'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['profile']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['photoProfiles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['profile']->key => $_smarty_tpl->tpl_vars['profile']->value){
$_smarty_tpl->tpl_vars['profile']->_loop = true;
?>
										<input type="hidden" name="digitalFilename[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['filename'];?>
">
										<input type="hidden" name="digitalLicense[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['license_id'];?>
">
										<div class="divTableRow <?php if (!$_smarty_tpl->tpl_vars['profile']->value['selected']){?>opac40<?php }?>">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="contrItem" id="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['profile']->value['selected']){?>checked="checked"<?php }?>></div>
											<div class="divTableCell">
												<label for="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['profile']->value['name'];?>
</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo" <?php if ($_smarty_tpl->tpl_vars['profile']->value['selected']){?>style="display: block"<?php }?>>
													<ul>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['licenseType'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['licenseLang'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['width']||$_smarty_tpl->tpl_vars['profile']->value['height']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['resolution'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['width'];?>
x<?php echo $_smarty_tpl->tpl_vars['profile']->value['height'];?>
<?php echo $_smarty_tpl->tpl_vars['lang']->value['px'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['format']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFormat'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['format'];?>
</strong></li><?php }?>
													</ul>
													<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']!='fr'&&$_smarty_tpl->tpl_vars['profile']->value['license']!='cu'){?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="digitalCredits[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['setCredits'];?>
"><p></p></div><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="digitalPrice[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['setPrice'];?>
"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
													<?php }?>
													<div class="attachFileContainer">
														<p id="detachButton-<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" style="<?php if ($_smarty_tpl->tpl_vars['profile']->value['fileExists']){?>display: block;<?php }?>"><?php echo $_smarty_tpl->tpl_vars['lang']->value['fileAttached'];?>
 <?php if ($_smarty_tpl->tpl_vars['profile']->value['fileExists']){?>(<?php echo $_smarty_tpl->tpl_vars['profile']->value['filename'];?>
)<?php }?> <input type="button" value="X" dspID="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="deleteAttachFile"></p>
														<p id="attachButton-<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" style="<?php if (!$_smarty_tpl->tpl_vars['profile']->value['fileExists']){?>display: block;<?php }?>"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['attachFile'];?>
" dspID="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="attachFile"></p>
													</div>
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									<?php } ?>
								</div>
							<?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['digitalRows']->value['video']&&$_smarty_tpl->tpl_vars['member']->value['membershipDetails']['additional_sizes']){?>
								<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['videoProfiles'];?>
</h1>
								<div class="divTable contrItemSelectTable">
									<?php  $_smarty_tpl->tpl_vars['profile'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['profile']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['videoProfiles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['profile']->key => $_smarty_tpl->tpl_vars['profile']->value){
$_smarty_tpl->tpl_vars['profile']->_loop = true;
?>
										<input type="hidden" name="digitalFilename[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['filename'];?>
">
										<input type="hidden" name="digitalLicense[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['license_id'];?>
">
										<div class="divTableRow <?php if (!$_smarty_tpl->tpl_vars['profile']->value['selected']){?>opac40<?php }?>">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="contrItem" id="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['profile']->value['selected']){?>checked="checked"<?php }?>></div>
											<div class="divTableCell">
												<label for="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['profile']->value['name'];?>
</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo" <?php if ($_smarty_tpl->tpl_vars['profile']->value['selected']){?>style="display: block"<?php }?>>
													<ul>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['licenseType'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['licenseLang'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['width']||$_smarty_tpl->tpl_vars['profile']->value['height']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['resolution'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['width'];?>
x<?php echo $_smarty_tpl->tpl_vars['profile']->value['height'];?>
<?php echo $_smarty_tpl->tpl_vars['lang']->value['px'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['format']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFormat'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['format'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['dsp_type']=='video'){?>
															<?php if ($_smarty_tpl->tpl_vars['profile']->value['fps']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFPS'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['digital']->value['fps'];?>
</strong></li><?php }?>
															<?php if ($_smarty_tpl->tpl_vars['profile']->value['running_time']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelRunningTime'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['digital']->value['running_time'];?>
</strong> <?php echo $_smarty_tpl->tpl_vars['lang']->value['seconds'];?>
</li><?php }?>
														<?php }?>
													</ul>
													<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']!='fr'&&$_smarty_tpl->tpl_vars['profile']->value['license']!='cu'){?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="digitalCredits[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['setCredits'];?>
"><p></p></div><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="digitalPrice[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['setPrice'];?>
"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
													<?php }?>
													<div class="attachFileContainer">
														<p id="detachButton-<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" style="<?php if ($_smarty_tpl->tpl_vars['profile']->value['fileExists']){?>display: block;<?php }?>"><?php echo $_smarty_tpl->tpl_vars['lang']->value['fileAttached'];?>
 <?php if ($_smarty_tpl->tpl_vars['profile']->value['fileExists']){?>(<?php echo $_smarty_tpl->tpl_vars['profile']->value['filename'];?>
)<?php }?> <input type="button" value="X" dspID="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="deleteAttachFile"></p>
														<p id="attachButton-<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" style="<?php if (!$_smarty_tpl->tpl_vars['profile']->value['fileExists']){?>display: block;<?php }?>"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['attachFile'];?>
" dspID="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="attachFile"></p>
													</div>
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									<?php } ?>
								</div>
							<?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['digitalRows']->value['other']&&$_smarty_tpl->tpl_vars['member']->value['membershipDetails']['additional_sizes']){?>
								<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['otherProfiles'];?>
</h1>
								<div class="divTable contrItemSelectTable">
									<?php  $_smarty_tpl->tpl_vars['profile'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['profile']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['otherProfiles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['profile']->key => $_smarty_tpl->tpl_vars['profile']->value){
$_smarty_tpl->tpl_vars['profile']->_loop = true;
?>
										<input type="hidden" name="digitalFilename[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['filename'];?>
">
										<input type="hidden" name="digitalLicense[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['license_id'];?>
">
										<div class="divTableRow <?php if (!$_smarty_tpl->tpl_vars['profile']->value['selected']){?>opac40<?php }?>">
											<div class="divTableCell"><input type="checkbox" name="digital[]" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="contrItem" id="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['profile']->value['selected']){?>checked="checked"<?php }?>></div>
											<div class="divTableCell">
												<label for="contrDSP<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['profile']->value['name'];?>
</label>
											</div>
											<div class="divTableCell">
												<div class="pricingInfo" <?php if ($_smarty_tpl->tpl_vars['profile']->value['selected']){?>style="display: block"<?php }?>>
													<ul>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['licenseType'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['licenseLang'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['width']||$_smarty_tpl->tpl_vars['profile']->value['height']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['resolution'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['width'];?>
x<?php echo $_smarty_tpl->tpl_vars['profile']->value['height'];?>
<?php echo $_smarty_tpl->tpl_vars['lang']->value['px'];?>
</strong></li><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['profile']->value['format']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFormat'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['profile']->value['format'];?>
</strong></li><?php }?>
													</ul>
													<?php if ($_smarty_tpl->tpl_vars['profile']->value['license']!='fr'&&$_smarty_tpl->tpl_vars['profile']->value['license']!='cu'){?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="digitalCredits[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['setCredits'];?>
"><p></p></div><?php }?>
														<?php if ($_smarty_tpl->tpl_vars['digitalCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="digitalPrice[<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
]" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['profile']->value['setPrice'];?>
"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
													<?php }?>
													<div class="attachFileContainer">
														<p id="detachButton-<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" style="<?php if ($_smarty_tpl->tpl_vars['profile']->value['fileExists']){?>display: block;<?php }?>"><?php echo $_smarty_tpl->tpl_vars['lang']->value['fileAttached'];?>
 <?php if ($_smarty_tpl->tpl_vars['profile']->value['fileExists']){?>(<?php echo $_smarty_tpl->tpl_vars['profile']->value['filename'];?>
)<?php }?> <input type="button" value="X" dspID="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="deleteAttachFile"></p>
														<p id="attachButton-<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" style="<?php if (!$_smarty_tpl->tpl_vars['profile']->value['fileExists']){?>display: block;<?php }?>"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['attachFile'];?>
" dspID="<?php echo $_smarty_tpl->tpl_vars['profile']->value['ds_id'];?>
" class="attachFile"></p>
													</div>
												</div>
											</div>
										</div>
										<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>										
									<?php } ?>
								</div>
							<?php }?>
							
						</div>
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_prints']&&$_smarty_tpl->tpl_vars['printRows']->value){?>
						<div class="dataGroupContainer" id="printsDGC">
							<br><br><?php echo $_smarty_tpl->tpl_vars['lang']->value['chooseItemsMes'];?>
<br>
							<div class="divTable contrItemSelectTable">
								<?php  $_smarty_tpl->tpl_vars['print'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['print']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['prints']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['print']->key => $_smarty_tpl->tpl_vars['print']->value){
$_smarty_tpl->tpl_vars['print']->_loop = true;
?>
									<div class="divTableRow <?php if (!$_smarty_tpl->tpl_vars['print']->value['selected']){?>opac40<?php }?>">
										<div class="divTableCell"><input type="checkbox" name="print[]" value="<?php echo $_smarty_tpl->tpl_vars['print']->value['print_id'];?>
" class="contrItem" id="contrPrint<?php echo $_smarty_tpl->tpl_vars['print']->value['print_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['print']->value['selected']){?>checked="checked"<?php }?>></div>
										<div class="divTableCell">
											<label for="contrPrint<?php echo $_smarty_tpl->tpl_vars['print']->value['print_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['print']->value['name'];?>
</label>
										</div>
										<div class="divTableCell">
											<div class="pricingInfo" <?php if ($_smarty_tpl->tpl_vars['print']->value['selected']){?>style="display: block"<?php }?>>
												<ul>
													<?php if ($_smarty_tpl->tpl_vars['print']->value['description']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelDesc'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['print']->value['description'];?>
</strong></li><?php }?>
												</ul>
												<?php if ($_smarty_tpl->tpl_vars['printCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="printCredits[<?php echo $_smarty_tpl->tpl_vars['print']->value['print_id'];?>
]" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['print']->value['setCredits'];?>
"><p></p></div><?php }?>
												<?php if ($_smarty_tpl->tpl_vars['printCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="printPrice[<?php echo $_smarty_tpl->tpl_vars['print']->value['print_id'];?>
]" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['print']->value['setPrice'];?>
"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
											</div>
										</div>
									</div>
									<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
								<?php } ?>
							</div>
						</div>
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_products']&&$_smarty_tpl->tpl_vars['productRows']->value){?>
						<div class="dataGroupContainer" id="productsDGC">
							<br><br><?php echo $_smarty_tpl->tpl_vars['lang']->value['chooseItemsMes'];?>
<br>
							<div class="divTable contrItemSelectTable">
								<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
									<div class="divTableRow <?php if (!$_smarty_tpl->tpl_vars['product']->value['selected']){?>opac40<?php }?>">
										<div class="divTableCell"><input type="checkbox" name="product[]" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['prod_id'];?>
" class="contrItem" id="contrProd<?php echo $_smarty_tpl->tpl_vars['product']->value['prod_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['product']->value['selected']){?>checked="checked"<?php }?>></div>
										<div class="divTableCell">
											<label for="contrProd<?php echo $_smarty_tpl->tpl_vars['product']->value['prod_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
</label>
										</div>
										<div class="divTableCell">
											<div class="pricingInfo" <?php if ($_smarty_tpl->tpl_vars['product']->value['selected']){?>style="display: block"<?php }?>>
												<ul>
													<?php if ($_smarty_tpl->tpl_vars['product']->value['description']){?><li><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelDesc'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['product']->value['description'];?>
</strong></li><?php }?>
												</ul>
												<?php if ($_smarty_tpl->tpl_vars['prodCreditsCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <input type="text" name="productCredits[<?php echo $_smarty_tpl->tpl_vars['product']->value['prod_id'];?>
]" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['setCredits'];?>
"><p></p></div><?php }?>
												<?php if ($_smarty_tpl->tpl_vars['prodCurrencyCartStatus']->value){?><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['price'];?>
 <input type="text" name="productPrice[<?php echo $_smarty_tpl->tpl_vars['product']->value['prod_id'];?>
]" style="width: 100px;" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['setPrice'];?>
"> <?php echo $_smarty_tpl->tpl_vars['priCurrency']->value['code'];?>
<p></p></div><?php }?>
											</div>
										</div>
									</div>
									<div class="divTableRow rowSpacerCIST"><div class="divTableCell"><!-- SPACER --></div></div>
								<?php } ?>
							</div>
						</div>
					<?php }?>
				</div>
				<div class="workboxActionButtons" style="clear: both;"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cancel'];?>
" class="closeImportWorkbox btn btn-xs btn-danger"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['save'];?>
" id="saveWorkboxForm" class="btn btn-xs btn-primary"></div>
			</div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['mode']->value=='cartAddNotes'){?>
		<div id="editWorkbox">
			<input type="hidden" name="cartItemID" value="<?php echo $_smarty_tpl->tpl_vars['cartItemID']->value;?>
">
			<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['notes'];?>
</h1>
			<p class="notice" style="display: none;" id="emailPasswordSent"><?php echo $_smarty_tpl->tpl_vars['lang']->value['passwordSent'];?>
</p>
			<p class="notice" style="display: none;" id="emailPasswordFailed"><?php echo $_smarty_tpl->tpl_vars['lang']->value['passwordFailed'];?>
</p>
			<div style="padding-bottom: 30px; padding-top: 6px;">
				<textarea style="width: 450px; height: 50px;" name="cartItemNotes"><?php echo $_smarty_tpl->tpl_vars['cartItemNotes']->value;?>
</textarea>
			</div>
		</div>
		<div class="workboxActionButtons"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['close'];?>
" class="closeWorkbox btn btn-xs btn-danger" /> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['save'];?>
" id="saveWorkboxForm" class="btn btn-xs btn-primary" /></div>
	<?php }?>

<?php }?>
</form><?php }} ?>