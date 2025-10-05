<?php /* Smarty version Smarty-3.1.8, created on 2025-08-30 22:02:23
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/cart.shipping.tpl" */ ?>
<?php /*%%SmartyHeaderCode:171920343068b374ef1950d1-19775009%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '78a363e8301d4df8ec81bd15fadc4ebb5b876222' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/cart.shipping.tpl',
      1 => 1755093996,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '171920343068b374ef1950d1-19775009',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'baseURL' => 0,
    'lang' => 0,
    'loggedIn' => 0,
    'config' => 0,
    'addressExists' => 0,
    'member' => 0,
    'shippingAddress' => 0,
    'countries' => 0,
    'shippingStates' => 0,
    'billingAddress' => 0,
    'billingStates' => 0,
    'debugMode' => 0,
    'cartInfo' => 0,
    'cartTotals' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b374ef3dce36_76529121',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b374ef3dce36_76529121')) {function content_68b374ef3dce36_76529121($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_options')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/function.html_options.php';
?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/cart.shipping.js"></script>
</head>
<body>
	<?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate ('header3.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
		
		<div class="container">
			<form id="shippingAddressesForm" action="cart.review.php" method="post" class="cleanForm form-group">
			<div class="row">
				<div class="col-md-12">
			
					<ul class="cartStepsBar cartStepsBar25">
						<li class="off" style="cursor: pointer"><p>1</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['cart'];?>
</div></li>
						<li class="on"><p>2</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['shipping'];?>
</div></li>
						<li class="off"><p>3</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['reviewOrder'];?>
</div></li>
						<li class="off"><p>4</p><div><?php echo $_smarty_tpl->tpl_vars['lang']->value['payment'];?>
</div></li>
					</ul>
				</div>
			</div>				
			
			<div class="row">
				<div class="col-md-8">				
					
					
					<?php if (!$_smarty_tpl->tpl_vars['loggedIn']->value&&$_smarty_tpl->tpl_vars['config']->value['settings']['display_login']){?><div class="cartLoginRequest"><a href="login.php?jumpTo=cart" class="buttonLink"><?php echo $_smarty_tpl->tpl_vars['lang']->value['login'];?>
</a>, <a href="create.account.php?jumpTo=cart" class="buttonLink"><?php echo $_smarty_tpl->tpl_vars['lang']->value['createAccount'];?>
</a> <?php echo $_smarty_tpl->tpl_vars['lang']->value['continueNoAccount'];?>
...</div><?php }?>
					
					
					<div class="cartContainer">
												
						<?php echo $_smarty_tpl->tpl_vars['lang']->value['enterShipInfo'];?>
:
						<h2 class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['shipTo'];?>
:</h2>							
						<input type="hidden" name="shippingInfo" value="1">
						<input type="hidden" name="addressExists" value="<?php echo $_smarty_tpl->tpl_vars['addressExists']->value;?>
">
						<?php if ($_smarty_tpl->tpl_vars['addressExists']->value){?>
							<ul>
								<li style="padding-left: 80px;">
									<input type="radio" name="memberShippingAddress" value="" checked="checked" style="margin-left: -20px;">
									<strong><?php echo $_smarty_tpl->tpl_vars['member']->value['f_name'];?>
 <?php echo $_smarty_tpl->tpl_vars['member']->value['l_name'];?>
</strong><br>
									<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['address'];?>
<br>
									<?php if ($_smarty_tpl->tpl_vars['member']->value['primaryAddress']['address_2']){?><?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['address_2'];?>
<br><?php }?>
									<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['city'];?>
, <?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['state'];?>
 <?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['postal_code'];?>
<br>
									<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['country'];?>
 
									<!--<a href="" class="colorLink">[<?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
]</a>-->
								</li>
							</ul>
							<input type="hidden" name="shippingCountry" id="shippingCountry" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['countryID'];?>
">
							<input type="hidden" name="shippingState" id="shippingState" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['stateID'];?>
">
							<input type="hidden" name="shippingPostalCode" id="shippingPostalCode" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['postalCode'];?>
">
						<?php }else{ ?>
							<div class="divTable">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['country'];?>
: 
									</div>
									<div class="divTableCell">
										<select id="shippingCountry" name="shippingCountry" style="width: 306px;" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control">
											<option></option>
											<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['countries']->value,'selected'=>$_smarty_tpl->tpl_vars['shippingAddress']->value['countryID']),$_smarty_tpl);?>

										</select>
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['firstName'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="shippingFirstName" name="shippingFirstName" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['firstName'];?>
" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['lastName'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="shippingLastName" name="shippingLastName" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['lastName'];?>
" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['address'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="shippingAddress" name="shippingAddress" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['address'];?>
" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										 
									</div>
									<div class="divTableCell">
										<input type="text" id="shippingAddress2" name="shippingAddress2" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['address2'];?>
" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['city'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="shippingCity" name="shippingCity" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['city'];?>
" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['state'];?>
: 
									</div>
									<div class="divTableCell">									
										<select id="shippingState" name="shippingState" style="width: 306px;" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
"  class="form-control">
											<?php if ($_smarty_tpl->tpl_vars['shippingAddress']->value['stateID']){?>
												<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['shippingStates']->value,'selected'=>$_smarty_tpl->tpl_vars['shippingAddress']->value['stateID']),$_smarty_tpl);?>

											<?php }else{ ?>
												<option value="0"><?php echo $_smarty_tpl->tpl_vars['lang']->value['chooseCountryFirst'];?>
</option>
											<?php }?>												
										</select> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['zip'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="shippingPostalCode" name="shippingPostalCode" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['postalCode'];?>
" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['email'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="shippingEmail" name="shippingEmail" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['email'];?>
" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['phone'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="shippingPhone" name="shippingPhone" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['phone'];?>
" require="require" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
							</div>
						<?php }?>
						<h2 class="infoHeader"><?php echo $_smarty_tpl->tpl_vars['lang']->value['billTo'];?>
:</h2>
						<?php if ($_smarty_tpl->tpl_vars['addressExists']->value){?>
							<ul>
								<li style="padding-left: 80px;">
									<input type="radio" name="memberBillingAddress" value="" checked="checked" style="margin-left: -20px;">
									<strong><?php echo $_smarty_tpl->tpl_vars['member']->value['f_name'];?>
 <?php echo $_smarty_tpl->tpl_vars['member']->value['l_name'];?>
</strong><br>
									<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['address'];?>
<br>
									<?php if ($_smarty_tpl->tpl_vars['member']->value['primaryAddress']['address_2']){?><?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['address_2'];?>
<br><?php }?>
									<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['city'];?>
, <?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['state'];?>
 <?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['postal_code'];?>
<br>
									<?php echo $_smarty_tpl->tpl_vars['member']->value['primaryAddress']['country'];?>
 
									<!--<a href="" class="colorLink">[<?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
]</a>-->
								</li>
							</ul>
						<?php }else{ ?>
							<div class="divTable" style="margin-bottom: 10px;">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel"><input type="radio" name="duplicateInfo" value="1" id="duplicateInfo1" checked="checked" class="duplicateInfo"></div>
									<div class="divTableCell" style="padding-top: 14px;"><label for="duplicateInfo1"><?php echo $_smarty_tpl->tpl_vars['lang']->value['sameAsShipping'];?>
</label></div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel"><input type="radio" name="duplicateInfo" value="0" id="duplicateInfo0" class="duplicateInfo"></div>
									<div class="divTableCell" style="padding-top: 14px;"><label for="duplicateInfo0"><?php echo $_smarty_tpl->tpl_vars['lang']->value['differentAddress'];?>
</label></div>
								</div>
							</div>
							
							<div class="divTable" id="billingInfoForm" style="display: none;">
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['country'];?>
: 
									</div>
									<div class="divTableCell">
										<select id="billingCountry" name="billingCountry" style="width: 306px;" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control">
											<option></option>
											<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['countries']->value,'selected'=>$_smarty_tpl->tpl_vars['billingAddress']->value['countryID']),$_smarty_tpl);?>

										</select>
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['firstName'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="billingFirstName" name="billingFirstName" value="<?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['firstName'];?>
" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['lastName'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="billingLastName" name="billingLastName" value="<?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['lastName'];?>
" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['address'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="billingAddress" name="billingAddress" value="<?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['address'];?>
" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										 
									</div>
									<div class="divTableCell">
										<input type="text" id="billingAddress2" name="billingAddress2" value="<?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['address2'];?>
" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['city'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="billingCity" name="billingCity" value="<?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['city'];?>
" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['state'];?>
: 
									</div>
									<div class="divTableCell">											
										<select id="billingState" name="billingState" style="width: 306px;" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control" >
											<?php if ($_smarty_tpl->tpl_vars['billingAddress']->value['stateID']){?>
												<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['billingStates']->value,'selected'=>$_smarty_tpl->tpl_vars['billingAddress']->value['stateID']),$_smarty_tpl);?>

											<?php }else{ ?>
												<option value="0"><?php echo $_smarty_tpl->tpl_vars['lang']->value['chooseCountryFirst'];?>
</option>
											<?php }?>
										</select> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['zip'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="billingPostalCode" name="billingPostalCode" value="<?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['postalCode'];?>
" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['email'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="billingEmail" name="billingEmail" value="<?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['email'];?>
" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
								<div class="divTableRow">
									<div class="divTableCell formFieldLabel">
										<?php echo $_smarty_tpl->tpl_vars['lang']->value['phone'];?>
: 
									</div>
									<div class="divTableCell">
										<input type="text" id="billingPhone" name="billingPhone" value="<?php echo $_smarty_tpl->tpl_vars['billingAddress']->value['phone'];?>
" errorMessage="<?php echo $_smarty_tpl->tpl_vars['lang']->value['required'];?>
" class="form-control"> 
									</div>
								</div>
							</div>
						<?php }?>
					</div>	
				</div>
							
				<div class="col-md-4 cartTotalColumn">
					<h2 style="padding-left: 10px; margin-bottom: 0;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['shippingOptions'];?>
:</h2>
					<div class="cartTotalList shippingMethodBox" id="shippingMethods"></div>
					<input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['continue'];?>
" style="float: right" id="cartContinueButton" class="btn btn-xs btn-success">
				</div>
				</form>
			</div>
			</div>
			
			<?php if ($_smarty_tpl->tpl_vars['debugMode']->value){?>
				<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['shippingAddress']->value,'title'=>'Shipping Address'),$_smarty_tpl);?>

				<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['billingAddress']->value,'title'=>'Billing Address'),$_smarty_tpl);?>

				<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['cartInfo']->value,'title'=>'Cart Info'),$_smarty_tpl);?>

				<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['cartTotals']->value,'title'=>'Cart Totals'),$_smarty_tpl);?>

			<?php }?>
			
		</div>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>