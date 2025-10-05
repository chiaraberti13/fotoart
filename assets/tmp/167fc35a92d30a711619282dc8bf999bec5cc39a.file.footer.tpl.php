<?php /* Smarty version Smarty-3.1.8, created on 2025-09-13 20:55:13
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:102610868668b36e07c46de5-90742803%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '167fc35a92d30a711619282dc8bf999bec5cc39a' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/footer.tpl',
      1 => 1757796006,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '102610868668b36e07c46de5-90742803',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b36e07d1ec54_65331934',
  'variables' => 
  array (
    'contentBlocks' => 0,
    'lang' => 0,
    'baseURL' => 0,
    'config' => 0,
    'imgPath' => 0,
    'pageID' => 0,
    'theme' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b36e07d1ec54_65331934')) {function content_68b36e07d1ec54_65331934($_smarty_tpl) {?><footer>
	<?php if ($_smarty_tpl->tpl_vars['contentBlocks']->value['customBlockFooter']){?>
		<div><?php echo $_smarty_tpl->tpl_vars['contentBlocks']->value['customBlockFooter']['content'];?>
</div>
	<?php }?>
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<?php echo $_smarty_tpl->tpl_vars['lang']->value['copyright'];?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['business_name'];?>
</a><br><?php echo $_smarty_tpl->tpl_vars['lang']->value['reserved'];?>

			</div>
			<div class="col-md-3">
				<?php if (addon('rss')){?>
				<ul>
				
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['rss_newest']){?><li><a href="<?php echo linkto(array('page'=>'rss.php?mode=newestMedia'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['newestMedia'];?>
</a></li><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['rss_newest']){?><li><a href="<?php echo linkto(array('page'=>'rss.php?mode=popularMedia'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['popularMedia'];?>
</a></li><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['rss_featured_media']){?><li><a href="<?php echo linkto(array('page'=>'rss.php?mode=featuredMedia'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['featuredMedia'];?>
</a></li><?php }?>
				</ul>
				<?php }?>
			</div>
			<div class="col-md-3">
				<ul style="margin-bottom: 10px;">
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['contact']){?><li><a href="<?php echo linkto(array('page'=>"contact.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['contactUs'];?>
</a></li><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['aboutpage']){?><li><a href="<?php echo linkto(array('page'=>"about.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['aboutUs'];?>
</a></li><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['forum_link']){?><li><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['forum_link'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['forum'];?>
</a></li><?php }?>					
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['tospage']){?><li><a href="<?php echo linkto(array('page'=>'terms.of.use.php'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['termsOfUse'];?>
</a></li><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['pppage']){?><li><a href="<?php echo linkto(array('page'=>'privacy.policy.php'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['privacyPolicy'];?>
</a></li><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['papage']){?><li><a href="<?php echo linkto(array('page'=>'purchase.agreement.php'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['purchaseAgreement'];?>
</a></li><?php }?>
				</ul>
				<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['facebook_link']){?><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['facebook_link'];?>
" target="_blank"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/facebook.icon.png" width="20" title="Facebook"></a><?php }?>&nbsp;<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['twitter_link']){?><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['twitter_link'];?>
" target="_blank"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/twitter.icon.png" width="20" title="Twitter"></a><?php }?>
			</div>
			<div class="col-md-3">
				<ul style="margin-bottom: 10px;">
					<li><a href="<?php echo linkto(array('page'=>"content.php?id=4"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['footercfunction'];?>
</a></li>
				
					<li><a href="<?php echo linkto(array('page'=>"content.php?id=77"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['footerinfofoto'];?>
</a></li>
					<li><a href="<?php echo linkto(array('page'=>"content.php?id=24"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['footerfaq'];?>
</a></li>
				
					
				</ul>
				<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['facebook_link']){?><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['facebook_link'];?>
" target="_blank"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/facebook.icon.png" width="20" title="Facebook"></a><?php }?>&nbsp;<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['twitter_link']){?><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['twitter_link'];?>
" target="_blank"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/twitter.icon.png" width="20" title="Twitter"></a><?php }?>
			</div>
			<div class="col-md-3 text-right">
				<?php if (!addon('unbrand')){?>
					<!-- Powered By PhotoStore | Sell Your Photos Online -->
					<p id="poweredBy">Powered By <a href="http://www.ktools.net/photostore/" target="_blank" class="photostoreLink" title="Powered By PhotoStore | Sell Your Photos Online">PhotoStore</a><br><a href="http://www.ktools.net/photostore/" target="_blank" class="sellPhotos">Sell Photos Online</a></p>
				<?php }?>
			</div>
		</div>
	</div>
	<div id="statsCode"><?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['stats_html'];?>
</div>
</footer>
<?php if ($_smarty_tpl->tpl_vars['pageID']->value!='photoPuzzle'){?>
	<script src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/themes/<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
/js/bootstrap.min.js"></script>
<?php }else{ ?>
	<script src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/bootstrap.min.js"></script>
    <script src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/photo.puzzle.js"></script>
    <script src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/jquery.Jcrop.js"></script>
    <script src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/dmuploader.min.js"></script>
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['fotomoto']){?><script type="text/javascript" src="//widget.fotomoto.com/stores/script/<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['fotomoto'];?>
.js"></script><?php }?><?php }} ?>