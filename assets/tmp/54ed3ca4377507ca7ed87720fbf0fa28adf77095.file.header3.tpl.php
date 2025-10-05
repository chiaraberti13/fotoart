<?php /* Smarty version Smarty-3.1.8, created on 2025-09-13 21:15:20
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/header3.tpl" */ ?>
<?php /*%%SmartyHeaderCode:38881540068b36e51e45311-29380879%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '54ed3ca4377507ca7ed87720fbf0fa28adf77095' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/header3.tpl',
      1 => 1757795999,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '38881540068b36e51e45311-29380879',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b36e51ebcd87_14537384',
  'variables' => 
  array (
    'message' => 0,
    'messageLang' => 0,
    'lang' => 0,
    'config' => 0,
    'cartStatus' => 0,
    'cartTotals' => 0,
    'imgPath' => 0,
    'currencySystem' => 0,
    'creditSystem' => 0,
    'displayLanguages' => 0,
    'selectedLanguage' => 0,
    'language' => 0,
    'displayCurrencies' => 0,
    'selectedCurrency' => 0,
    'activeCurrencies' => 0,
    'currency' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b36e51ebcd87_14537384')) {function content_68b36e51ebcd87_14537384($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['message']->value){?>
	<?php  $_smarty_tpl->tpl_vars['messageLang'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['messageLang']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['message']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['messageLang']->key => $_smarty_tpl->tpl_vars['messageLang']->value){
$_smarty_tpl->tpl_vars['messageLang']->_loop = true;
?>
		<div class="container messageBar alert alert-danger"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['messageLang']->value];?>
 <p><a href="#" class="buttonLink btn btn-xs btn-danger">X</a></p></div>
	<?php } ?>
<?php }?>
<div class="container infoBar" style="border: 1px solid #FFF;">	
	
		
	<div class="row">		
		<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['search']){?>
		
		<div class="col-md-5">
			
		</div>
		
		<?php }?>			

		<div class="col-md-7">
			<?php if ($_smarty_tpl->tpl_vars['cartStatus']->value){?>			
				<div class="nav navbar-right">
					<div id="headerCartBox">
						<div id="cartPreviewContainer">
							<div id="miniCartContainer"></div>
							<div style="float: left; position: relative;" class="viewCartLink"><p id="cartItemsCount"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['itemsInCart'];?>
</p><a href="<?php echo linkto(array('page'=>"cart.php"),$_smarty_tpl);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/cart.icon.png" alt="<?php echo $_smarty_tpl->tpl_vars['lang']->value['cart'];?>
"></a></div>
							<div style="float: left; display:<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['priceSubTotal']||$_smarty_tpl->tpl_vars['cartTotals']->value['creditsSubTotalPreview']){?>block<?php }else{ ?>none<?php }?>;" id="cartPreview">
								<a href="<?php echo linkto(array('page'=>"cart.php"),$_smarty_tpl);?>
" class="viewCartLink">
								<span id="cartPreviewPrice" style="<?php if (!$_smarty_tpl->tpl_vars['currencySystem']->value){?>display: none;<?php }?>"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['priceSubTotalPreview']['display'];?>
</span><!-- with tax <?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['totalLocal']['display'];?>
-->
								<?php if ($_smarty_tpl->tpl_vars['creditSystem']->value&&$_smarty_tpl->tpl_vars['currencySystem']->value){?> + <?php }?>
								<span id="cartPreviewCredits" style="<?php if (!$_smarty_tpl->tpl_vars['creditSystem']->value){?>display: none;<?php }?>"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['creditsSubTotalPreview'];?>
 </span> <?php if ($_smarty_tpl->tpl_vars['creditSystem']->value){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
<?php }?>
								</a>
							</div>
						</div>
					</div>
				</div>
			<?php }?>
	

			
			<ul class="nav navbar-nav navbar-right">
				<?php if (count($_smarty_tpl->tpl_vars['displayLanguages']->value)>1){?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_smarty_tpl->tpl_vars['displayLanguages']->value[$_smarty_tpl->tpl_vars['selectedLanguage']->value];?>
<b class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['displayLanguages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value){
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
							<li><a href="<?php echo linkto(array('page'=>"actions.php?action=changeLanguage&setLanguage=".($_smarty_tpl->tpl_vars['language']->key)),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['language']->value;?>
</a></li>
						<?php } ?>
					</ul>
				</li>
				<?php }?>
				<?php if (count($_smarty_tpl->tpl_vars['displayCurrencies']->value)>1){?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_smarty_tpl->tpl_vars['activeCurrencies']->value[$_smarty_tpl->tpl_vars['selectedCurrency']->value]['name'];?>
 (<?php echo $_smarty_tpl->tpl_vars['activeCurrencies']->value[$_smarty_tpl->tpl_vars['selectedCurrency']->value]['code'];?>
)<b class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php  $_smarty_tpl->tpl_vars['currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['displayCurrencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency']->key => $_smarty_tpl->tpl_vars['currency']->value){
$_smarty_tpl->tpl_vars['currency']->_loop = true;
?>
							<li><a href="<?php echo linkto(array('page'=>"actions.php?action=changeCurrency&setCurrency=".($_smarty_tpl->tpl_vars['currency']->key)),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['currency']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['activeCurrencies']->value[$_smarty_tpl->tpl_vars['currency']->key]['code'];?>
)</a></li>
						<?php } ?>
					</ul>
				</li>
				<?php }?>
			</ul>			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		</div>
	</div>
</div><?php }} ?>