<?php /* Smarty version Smarty-3.1.8, created on 2025-08-31 09:20:00
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/cart.mailin.tpl" */ ?>
<?php /*%%SmartyHeaderCode:64649888768b413c0a7c076-51667721%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8b6d037ef73d51acddd96dfbae0a38a1811c82b0' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/cart.mailin.tpl',
      1 => 1755094001,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '64649888768b413c0a7c076-51667721',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'baseURL' => 0,
    'stepNumber' => 0,
    'lang' => 0,
    'content' => 0,
    'debugMode' => 0,
    'shippingAddress' => 0,
    'shippingAddressKey' => 0,
    'shippingAddressValue' => 0,
    'billingAddress' => 0,
    'billingAddressKey' => 0,
    'billingAddressValue' => 0,
    'cartInfo' => 0,
    'cartInfoKey' => 0,
    'cartInfoValue' => 0,
    'subcartInfoKey' => 0,
    'subcartInfoValue' => 0,
    'cartTotals' => 0,
    'cartTotalsKey' => 0,
    'cartTotalsValue' => 0,
    'subcartTotalsKey' => 0,
    'subcartTotalsValue' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b413c0b2e014_87572350',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b413c0b2e014_87572350')) {function content_68b413c0b2e014_87572350($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/cart.mailin.js"></script>
	<script type="text/javascript">
		<!--
			$(function()
			{
				// Hide top nav so we can replace it with steps bar
				$('#searchBar').hide();
				$('#topNav').hide();
			});
		-->
	</script>
</head>
<body>
	<?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<div class="container">
		<div class="row">
			<ul class="cartStepsBar <?php if ($_smarty_tpl->tpl_vars['stepNumber']->value['b']){?>cartStepsBar25<?php }else{ ?>cartStepsBar33<?php }?>">
				<li class="off"><p><?php echo $_smarty_tpl->tpl_vars['stepNumber']->value['a'];?>
</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['cart'];?>
</div></li>
				<?php if ($_smarty_tpl->tpl_vars['stepNumber']->value['b']){?><li class="off shipping"><p><?php echo $_smarty_tpl->tpl_vars['stepNumber']->value['b'];?>
</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['shipping'];?>
</div></li><?php }?>
				<li class="off"><p><?php echo $_smarty_tpl->tpl_vars['stepNumber']->value['c'];?>
</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['reviewOrder'];?>
</div></li>
				<li class="on"><p><?php echo $_smarty_tpl->tpl_vars['stepNumber']->value['d'];?>
</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['payment'];?>
</div></li>
			</ul>
			<div class="container">

		
<div class="row">
			<div class="content" style="padding-left: 0; padding-right: 0; padding-bottom: 0;">
				<!--<h1>Cart > Shipping > Review Your Order</h1>-->
				<div class="divTable cartContainer" style="width: 100%">
					<div class="divTableRow">
						<div class="divTableCell">
							<?php echo $_smarty_tpl->tpl_vars['content']->value['body'];?>

						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell" style="padding-top: 10px;"><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['returnToSiteUpper'];?>
" style="float: right" id="cartReturnButton" class="colorButton"></div>
					</div>
				</div>
				
			</div>
			
			
			<?php if ($_smarty_tpl->tpl_vars['debugMode']->value){?>
				<div class="debug">
					<h1>Debug: Shipping Address</h1>
					<ul>
						<?php  $_smarty_tpl->tpl_vars['shippingAddressValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shippingAddressValue']->_loop = false;
 $_smarty_tpl->tpl_vars['shippingAddressKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['shippingAddress']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shippingAddressValue']->key => $_smarty_tpl->tpl_vars['shippingAddressValue']->value){
$_smarty_tpl->tpl_vars['shippingAddressValue']->_loop = true;
 $_smarty_tpl->tpl_vars['shippingAddressKey']->value = $_smarty_tpl->tpl_vars['shippingAddressValue']->key;
?>
							<li><strong><?php echo $_smarty_tpl->tpl_vars['shippingAddressKey']->value;?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['shippingAddressValue']->value;?>
</li>
						<?php } ?>
					</ul>
				</div>
				<div class="debug">
					<h1>Debug: Billing Address</h1>
					<ul>
						<?php  $_smarty_tpl->tpl_vars['billingAddressValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['billingAddressValue']->_loop = false;
 $_smarty_tpl->tpl_vars['billingAddressKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['billingAddress']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['billingAddressValue']->key => $_smarty_tpl->tpl_vars['billingAddressValue']->value){
$_smarty_tpl->tpl_vars['billingAddressValue']->_loop = true;
 $_smarty_tpl->tpl_vars['billingAddressKey']->value = $_smarty_tpl->tpl_vars['billingAddressValue']->key;
?>
							<li><strong><?php echo $_smarty_tpl->tpl_vars['billingAddressKey']->value;?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['billingAddressValue']->value;?>
</li>
						<?php } ?>
					</ul>
				</div>
				<div class="debug">
					<h1>Debug: Cart Info</h1>
					<ul>
						<?php  $_smarty_tpl->tpl_vars['cartInfoValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cartInfoValue']->_loop = false;
 $_smarty_tpl->tpl_vars['cartInfoKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cartInfo']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cartInfoValue']->key => $_smarty_tpl->tpl_vars['cartInfoValue']->value){
$_smarty_tpl->tpl_vars['cartInfoValue']->_loop = true;
 $_smarty_tpl->tpl_vars['cartInfoKey']->value = $_smarty_tpl->tpl_vars['cartInfoValue']->key;
?>
							<li>
								<strong><?php echo $_smarty_tpl->tpl_vars['cartInfoKey']->value;?>
:</strong> 
								<?php if (is_array($_smarty_tpl->tpl_vars['cartInfoValue']->value)){?>
									<ul>
										<?php  $_smarty_tpl->tpl_vars['subcartInfoValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['subcartInfoValue']->_loop = false;
 $_smarty_tpl->tpl_vars['subcartInfoKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cartInfoValue']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['subcartInfoValue']->key => $_smarty_tpl->tpl_vars['subcartInfoValue']->value){
$_smarty_tpl->tpl_vars['subcartInfoValue']->_loop = true;
 $_smarty_tpl->tpl_vars['subcartInfoKey']->value = $_smarty_tpl->tpl_vars['subcartInfoValue']->key;
?>
											<li><strong><?php echo $_smarty_tpl->tpl_vars['subcartInfoKey']->value;?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['subcartInfoValue']->value;?>
</li>
										<?php } ?>
									</ul>
								<?php }else{ ?>
									<?php echo $_smarty_tpl->tpl_vars['cartInfoValue']->value;?>

								<?php }?>
							</li>
						<?php } ?>
					</ul>
				</div>
				<div class="debug">
					<h1>Debug: Cart Totals</h1>
					<ul>
						<?php  $_smarty_tpl->tpl_vars['cartTotalsValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cartTotalsValue']->_loop = false;
 $_smarty_tpl->tpl_vars['cartTotalsKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cartTotals']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cartTotalsValue']->key => $_smarty_tpl->tpl_vars['cartTotalsValue']->value){
$_smarty_tpl->tpl_vars['cartTotalsValue']->_loop = true;
 $_smarty_tpl->tpl_vars['cartTotalsKey']->value = $_smarty_tpl->tpl_vars['cartTotalsValue']->key;
?>
							<li>
								<strong><?php echo $_smarty_tpl->tpl_vars['cartTotalsKey']->value;?>
:</strong> 
								<?php if (is_array($_smarty_tpl->tpl_vars['cartTotalsValue']->value)){?>
									<ul>
										<?php  $_smarty_tpl->tpl_vars['subcartTotalsValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['subcartTotalsValue']->_loop = false;
 $_smarty_tpl->tpl_vars['subcartTotalsKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cartTotalsValue']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['subcartTotalsValue']->key => $_smarty_tpl->tpl_vars['subcartTotalsValue']->value){
$_smarty_tpl->tpl_vars['subcartTotalsValue']->_loop = true;
 $_smarty_tpl->tpl_vars['subcartTotalsKey']->value = $_smarty_tpl->tpl_vars['subcartTotalsValue']->key;
?>
											<li><strong><?php echo $_smarty_tpl->tpl_vars['subcartTotalsKey']->value;?>
:</strong> <?php echo $_smarty_tpl->tpl_vars['subcartTotalsValue']->value;?>
</li>
										<?php } ?>
									</ul>
								<?php }else{ ?>
									<?php echo $_smarty_tpl->tpl_vars['cartTotalsValue']->value;?>

								<?php }?>
							</li>
						<?php } ?>
					</ul>
				</div>
			<?php }?>
			
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>