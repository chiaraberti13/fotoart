<?php /* Smarty version Smarty-3.1.8, created on 2025-09-13 20:55:13
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:108325373368b36e07aa1931-24133820%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '736fd55b796f880f76648bd05e408a77948c40c9' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/header.tpl',
      1 => 1757796007,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '108325373368b36e07aa1931-24133820',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b36e07b841f5_09915902',
  'variables' => 
  array (
    'mainLogo' => 0,
    'config' => 0,
    'lang' => 0,
    'featuredTab' => 0,
    'imgPath' => 0,
    'contribLink' => 0,
    'loggedIn' => 0,
    'member' => 0,
    'lightboxSystem' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b36e07b841f5_09915902')) {function content_68b36e07b841f5_09915902($_smarty_tpl) {?>	<nav class="navbar  navbar-static-top">
		<div class="container"><!-- Container is centered in page -->
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a href="<?php echo linkto(array('page'=>"index.php"),$_smarty_tpl);?>
" class="navbar-brand"><img src="<?php echo $_smarty_tpl->tpl_vars['mainLogo']->value;?>
" id="mainLogo" class="img-responsive" style="margin-top: -6px;"></a>
			</div>
			
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav topNav">
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['news']){?><li id="navNews"><a href="<?php echo linkto(array('page'=>"news.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['news'];?>
</a></li><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['featuredTab']->value){?>
						<li id="featuredNavButton" class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_smarty_tpl->tpl_vars['lang']->value['featuredItems'];?>
<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['featuredpage']){?><li id="featuredSubnavMedia"><a href="<?php echo linkto(array('page'=>"gallery.php?mode=featured-media&page=1"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaNav'];?>
</a></li><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['printpage']){?><li id="featuredSubnavPrints"><a href="<?php echo linkto(array('page'=>"featured.php?mode=prints"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['prints'];?>
</a></li><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['prodpage']){?><li id="featuredSubnavProducts"><a href="<?php echo linkto(array('page'=>"featured.php?mode=products"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['products'];?>
</a></li><?php }?>
								<!--<li class="divider"></li>
								<li class="dropdown-header">Nav header</li>-->
								<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['packpage']){?><li id="featuredSubnavPackages"><a href="<?php echo linkto(array('page'=>"featured.php?mode=packages"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['packages'];?>
</a></li><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['collpage']){?><li id="featuredSubnavCollections"><a href="<?php echo linkto(array('page'=>"featured.php?mode=collections"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['collections'];?>
</a></li><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['subpage']&&$_smarty_tpl->tpl_vars['config']->value['settings']['subscriptions']){?><li id="featuredSubnavSubscriptions"><a href="<?php echo linkto(array('page'=>"featured.php?mode=subscriptions"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['subscriptions'];?>
</a></li><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['creditpage']){?><li id="featuredSubnavCredits"><a href="<?php echo linkto(array('page'=>"featured.php?mode=credits"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
</a></li><?php }?>
							</ul>
						</li>							
					<?php }?>
					
	
			
		










			

                    <li id="navGalleries"><a href="<?php echo linkto(array('page'=>"photo.puzzle.php"),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/upload25.png" alt="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cart'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaNavCarica'];?>
</a></li>
				 			
				
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['popularpage']){?><li id="navPopularMedia"><a href="<?php echo linkto(array('page'=>"gallery.php?mode=popular-media&page=1"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['popularMedia'];?>
</a></li><?php }?>
					<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['contribLink']->value;?>
<?php $_tmp1=ob_get_clean();?><?php if (addon('contr')&&$_tmp1==1){?><li id="navContributors"><a href="<?php echo linkto(array('page'=>"contributors.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['contributors'];?>
</a></li><?php }?>
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['promopage']){?><li id="navPromotions"><a href="<?php echo linkto(array('page'=>"promotions.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['promotions'];?>
</a></li><?php }?>
					
					<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['display_login']){?>
						<?php if ($_smarty_tpl->tpl_vars['loggedIn']->value){?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_smarty_tpl->tpl_vars['member']->value['f_name'];?>
 <?php echo $_smarty_tpl->tpl_vars['member']->value['l_name'];?>
<b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="<?php echo linkto(array('page'=>"members.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['myAccount'];?>
</a></li>
									<?php if ($_smarty_tpl->tpl_vars['lightboxSystem']->value){?><li><a href="<?php echo linkto(array('page'=>"lightboxes.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['lightboxes'];?>
</a></li><?php }?>
									<li><a href="<?php echo linkto(array('page'=>"login.php?cmd=logout"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['logout'];?>
</a></li>
								</ul>
							</li>
						<?php }else{ ?>
							<?php if ($_smarty_tpl->tpl_vars['lightboxSystem']->value){?><li><a href="<?php echo linkto(array('page'=>"lightboxes.php"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['lightboxes'];?>
</a></li><?php }?>
							<li><a href="<?php echo linkto(array('page'=>"login.php?jumpTo=members"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['login'];?>
</a></li>
							<!--<li><a href="<?php echo linkto(array('page'=>"create.account.php?jumpTo=members"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['createAccount'];?>
</a></li>-->
						<?php }?>
					<?php }?>
				</li>
					
				</ul>
			</div>
		</div>
	
	</nav><?php }} ?>