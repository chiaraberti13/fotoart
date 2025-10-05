<?php /* Smarty version Smarty-3.1.8, created on 2025-08-30 22:02:23
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/shipping.methods.tpl" */ ?>
<?php /*%%SmartyHeaderCode:97980928668b374ef7a5bb3-74741417%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dc20910d0c015904173a3a4ecda78bcc1f38478d' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/shipping.methods.tpl',
      1 => 1755093994,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '97980928668b374ef7a5bb3-74741417',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'shippingMethods' => 0,
    'debugMode' => 0,
    'cartTotals' => 0,
    'shipPercentage' => 0,
    'postVars' => 0,
    'shippingMethodKey' => 0,
    'shippingMethod' => 0,
    'lang' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b374ef81d669_43269976',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b374ef81d669_43269976')) {function content_68b374ef81d669_43269976($_smarty_tpl) {?><?php if ($_smarty_tpl->tpl_vars['shippingMethods']->value){?>
	
	<?php if ($_smarty_tpl->tpl_vars['debugMode']->value){?>
		<div class="debug">
			<h1>Debug: Shipping</h1>
			<ul>
				<li><strong>Clear Shipping:</strong> <?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['clearShipping'];?>
</li>
				<li><strong>Shipping Percentage:</strong> <?php echo $_smarty_tpl->tpl_vars['shipPercentage']->value;?>
</li>
				<li><strong>Country ID:</strong> <?php echo $_smarty_tpl->tpl_vars['postVars']->value['shippingCountry'];?>
</li>
				<li><strong>State ID:</strong> <?php echo $_smarty_tpl->tpl_vars['postVars']->value['shippingState'];?>
</li>
				<li><strong>Zip:</strong> <?php echo $_smarty_tpl->tpl_vars['postVars']->value['shippingPostalCode'];?>
</li>
				<li><strong>Shippable Items Subtotal:</strong> <?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['shippableTotal'];?>
</li>
				<li><strong>Shippable Items:</strong> <?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['shippableCount'];?>
</li>
				<li><strong>Shippable Weight:</strong> <?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['shippableWeight'];?>
</li>
			</ul>
		</div>
	<?php }?>
	
	<ul>
		<?php  $_smarty_tpl->tpl_vars['shippingMethod'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shippingMethod']->_loop = false;
 $_smarty_tpl->tpl_vars['shippingMethodKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['shippingMethods']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['shippingMethod']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['shippingMethod']->key => $_smarty_tpl->tpl_vars['shippingMethod']->value){
$_smarty_tpl->tpl_vars['shippingMethod']->_loop = true;
 $_smarty_tpl->tpl_vars['shippingMethodKey']->value = $_smarty_tpl->tpl_vars['shippingMethod']->key;
 $_smarty_tpl->tpl_vars['shippingMethod']->index++;
 $_smarty_tpl->tpl_vars['shippingMethod']->first = $_smarty_tpl->tpl_vars['shippingMethod']->index === 0;
?>
			<li>
				<input type="radio" name="shippingMethod" id="shippingMethod<?php echo $_smarty_tpl->tpl_vars['shippingMethodKey']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['shippingMethodKey']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['shippingMethod']->first){?>checked="checked"<?php }?>> <label for="shippingMethod<?php echo $_smarty_tpl->tpl_vars['shippingMethodKey']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['shippingMethod']->value['title'];?>
</label>
				<p><?php if ($_smarty_tpl->tpl_vars['shippingMethod']->value['description']){?><?php echo $_smarty_tpl->tpl_vars['shippingMethod']->value['description'];?>
<br><?php }?></p>
				<p style="float: right;"><?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['clearShipping']){?><span class="price">FREE</span><?php }?> <span class="<?php if (!$_smarty_tpl->tpl_vars['cartTotals']->value['clearShipping']){?>price<?php }?> <?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['clearShipping']){?>strike<?php }?>"><?php echo $_smarty_tpl->tpl_vars['shippingMethod']->value['price']['display'];?>
</span> <?php if ($_smarty_tpl->tpl_vars['shippingMethod']->value['taxInc']){?><span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?></p>
			</li>
		<?php } ?>
	</ul>
<?php }else{ ?>
	<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['noShipMethod'];?>
</p>
<?php }?><?php }} ?>