<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 09:46:51
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/memnav.tpl" */ ?>
<?php /*%%SmartyHeaderCode:209605795068b374be0c8dd8-97996285%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '27a8677d4bdcc604f77217e47933146f103dcdb8' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/memnav.tpl',
      1 => 1757795998,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '209605795068b374be0c8dd8-97996285',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b374be18bb33_47161985',
  'variables' => 
  array (
    'member' => 0,
    'lang' => 0,
    'creditSystem' => 0,
    'pageID' => 0,
    'contrAlbums' => 0,
    'album' => 0,
    'cartStatus' => 0,
    'lightboxSystem' => 0,
    'ticketSystem' => 0,
    'config' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b374be18bb33_47161985')) {function content_68b374be18bb33_47161985($_smarty_tpl) {?><div class="col-md-3">

	<div class="subNavFeaturedBox" style="padding: 20px; overflow: auto; background-color: #f5f5f5">
		<p style="line-height: 1.5;">
			
			<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['avatar']){?><img src="<?php echo memberAvatar(array('memID'=>$_smarty_tpl->tpl_vars['member']->value['mem_id'],'size'=>100),$_smarty_tpl);?>
" class="memberAvatar" id="memNavAvatar" style="float: left;"><?php }?>
			<?php echo $_smarty_tpl->tpl_vars['lang']->value['loggedInAs'];?>
:<br>
			<strong><a href="<?php echo linkto(array('page'=>"members.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['member']->value['f_name'];?>
 <?php echo $_smarty_tpl->tpl_vars['member']->value['l_name'];?>
</a></strong>
			<br><br>
			<p style="float: right"><a href="<?php echo linkto(array('page'=>"account.php"),$_smarty_tpl);?>
"class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['editProfile'];?>
</a></p>
		</p>
	</div>
	
	<?php if ($_smarty_tpl->tpl_vars['creditSystem']->value){?>
		<div class=" yourCredits" style="padding: 20px;">
			<div><?php echo $_smarty_tpl->tpl_vars['lang']->value['yourCredits'];?>
</div>
			<div class="myCredits" style="margin-top: -10px;"><?php if ($_smarty_tpl->tpl_vars['member']->value['credits']){?><?php echo $_smarty_tpl->tpl_vars['member']->value['credits'];?>
<?php }else{ ?>0<?php }?></div><div style="float: right"><a href="<?php echo linkto(array('page'=>"featured.php?mode=credits"),$_smarty_tpl);?>
" class="btn btn-xs btn-success"><?php echo $_smarty_tpl->tpl_vars['lang']->value['purchaseCredits'];?>
</a></div>
		</div>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']||$_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_uploads']){?>
	<div class="subNavFeaturedBox">
		<!--<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['contributors'];?>
</h1>-->
		<ul id="contrSubNav">
			<li>
				<a href="<?php echo linkto(array('page'=>"contributor.my.media.php?mode=all"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['contMedia'];?>
</a>
				<?php if ($_smarty_tpl->tpl_vars['pageID']->value=='contributorMyMedia'){?>
					<ul>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']){?><li><a href="<?php echo linkto(array('page'=>"contributor.my.media.php?mode=pending"),$_smarty_tpl);?>
" class="lighterLink"><?php echo $_smarty_tpl->tpl_vars['lang']->value['approvalStatus0'];?>
</a></li><?php }?>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']){?><li><a href="<?php echo linkto(array('page'=>"contributor.my.media.php?mode=failed"),$_smarty_tpl);?>
" class="lighterLink"><?php echo $_smarty_tpl->tpl_vars['lang']->value['approvalStatus2'];?>
</a></li><?php }?>
						
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['personal_galleries']||$_smarty_tpl->tpl_vars['member']->value['membershipDetails']['admin_galleries']){?><li><a href="<?php echo linkto(array('page'=>"contributor.my.media.php?mode=orphaned"),$_smarty_tpl);?>
" class="lighterLink"><?php echo $_smarty_tpl->tpl_vars['lang']->value['orphanedMedia'];?>
</a></li><?php }?>
						<li><a href="<?php echo linkto(array('page'=>"contributor.my.media.php?mode=last"),$_smarty_tpl);?>
" class="lighterLink"><?php echo $_smarty_tpl->tpl_vars['lang']->value['lastBatch'];?>
</a></li>
						<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['personal_galleries']){?>
							<?php  $_smarty_tpl->tpl_vars['album'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['album']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['contrAlbums']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['album']->key => $_smarty_tpl->tpl_vars['album']->value){
$_smarty_tpl->tpl_vars['album']->_loop = true;
?>
								<li class="contrGalleries"><a href="<?php echo linkto(array('page'=>"contributor.my.media.php?mode=album&albumID=".($_smarty_tpl->tpl_vars['album']->value['ugallery_id'])),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['album']->value['name'];?>
</a></li>
							<?php } ?>
							<li><a href="workbox.php?mode=contrNewAlbum" class="contrNewAlbum lighterLink"><?php echo $_smarty_tpl->tpl_vars['lang']->value['newAlbum'];?>
...</a></li>
						<?php }?>
					</ul>
				<?php }?>
			</li>
			<li><a href="<?php echo linkto(array('page'=>"contributor.add.media.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['contUploadNewMedia'];?>
</a></li>
			<?php if ($_smarty_tpl->tpl_vars['member']->value['membershipDetails']['allow_selling']){?><li><a href="<?php echo linkto(array('page'=>"contributor.sales.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['contViewSales'];?>
</a></li><?php }?>
		</ul>
	</div>
	<?php }?>
	
	<div class="subNavFeaturedBox">
		<ul id="contrSubNav">
			<li><a href="<?php echo linkto(array('page'=>"members.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['myAccount'];?>
</a></li>
			<?php if ($_smarty_tpl->tpl_vars['cartStatus']->value){?><li><a href="<?php echo linkto(array('page'=>"orders.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['orders'];?>
</a></li><?php }?>
			<li><a href="<?php echo linkto(array('page'=>"download.history.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['downloadHistory'];?>
</a></li>
			<li><a href="<?php echo linkto(array('page'=>"bills.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['bills'];?>
</a></li>
			<li><a href="<?php echo linkto(array('page'=>"account.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['accountInfo'];?>
</a></li>
			<?php if ($_smarty_tpl->tpl_vars['lightboxSystem']->value){?><li><a href="<?php echo linkto(array('page'=>"lightboxes.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['lightboxes'];?>
</a></li><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['ticketSystem']->value){?><li><a href="<?php echo linkto(array('page'=>"tickets.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['supportTickets'];?>
</a></li><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['subscriptions']){?><li><a href="<?php echo linkto(array('page'=>"member.subs.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['subscriptions'];?>
</a></li><?php }?>
			<li><a href="<?php echo linkto(array('page'=>"login.php?cmd=logout"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['logout'];?>
</a></li>
		</ul>
	</div>
</div><?php }} ?>