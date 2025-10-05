<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 08:32:00
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/cart.tpl" */ ?>
<?php /*%%SmartyHeaderCode:187191967568b36e51ac7743-80981622%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'daef138868623b2b86654b18e78938015e480fbf' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/cart.tpl',
      1 => 1757795997,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '187191967568b36e51ac7743-80981622',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b36e51e36236_13307608',
  'variables' => 
  array (
    'baseURL' => 0,
    'lang' => 0,
    'cartTotals' => 0,
    'lowSubtotalWarning' => 0,
    'accountWorkbox' => 0,
    'cartItems' => 0,
    'mode' => 0,
    'continueShoppingButton' => 0,
    'promotions' => 0,
    'promo' => 0,
    'cartItem' => 0,
    'bPP' => 0,
    'imgPath' => 0,
    'config' => 0,
    'gruppoOpzione' => 0,
    'k' => 0,
    'debugMode' => 0,
    'tax' => 0,
    'creditSystem' => 0,
    'member' => 0,
    'cartCouponsArray' => 0,
    'coupon' => 0,
    'shippingAddress' => 0,
    'cartInfo' => 0,
    'priCurrency' => 0,
    'selectedCurrency' => 0,
    'uniqueOrderID' => 0,
    'cartID' => 0,
    'invoiceID' => 0,
    'cartItemRows' => 0,
    'packagesInCartSession' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b36e51e36236_13307608')) {function content_68b36e51e36236_13307608($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/modifier.truncate.php';
?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<meta name="robots" content="nofollow" />
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/cart.js"></script>
</head>
<body>
	<?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate ('header3.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1><?php echo $_smarty_tpl->tpl_vars['lang']->value['cart'];?>
</h1>
					<hr>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-8">
					
					<form method="post" action="cart.process.php" id="cartForm" class="form-group">
					<input type="hidden" name="creditsNeededForCheckout" id="creditsNeededForCheckout">
					<input type="hidden" name="creditsNeededToCheckout" id="creditsNeededToCheckout" value="<?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['creditsTotal'];?>
">
					<input type="hidden" name="creditsAvailableAtCheckout" id="creditsAvailableAtCheckout" value="<?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['creditsAvailableAtCheckout'];?>
">
					<input type="hidden" name="lowSubtotalWarning" id="lowSubtotalWarning" value="<?php echo $_smarty_tpl->tpl_vars['lowSubtotalWarning']->value;?>
">
					<input type="hidden" name="accountWorkbox" id="accountWorkbox" value="<?php echo $_smarty_tpl->tpl_vars['accountWorkbox']->value;?>
">
					
					<?php if (count($_smarty_tpl->tpl_vars['cartItems']->value)>0){?>
					
					<?php if ($_smarty_tpl->tpl_vars['mode']->value=='add'){?><div class="cartItemAddedMessage"><?php echo $_smarty_tpl->tpl_vars['lang']->value['cartItemAdded'];?>
 <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['continueShopUpper'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['continueShoppingButton']->value['linkto'];?>
" class="colorButton backLink" style="float: right;"></div><?php }?>
					
					<?php if ($_smarty_tpl->tpl_vars['promotions']->value){?>
						<div class="galleryFeaturedItemsContainer cartPromotions">
							<h3><?php echo $_smarty_tpl->tpl_vars['lang']->value['promotions'];?>
</h3>
							<?php  $_smarty_tpl->tpl_vars['promo'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['promo']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['promotions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['promo']->key => $_smarty_tpl->tpl_vars['promo']->value){
$_smarty_tpl->tpl_vars['promo']->_loop = true;
?>
								<div class="featuredPageItem featuredPromos workboxLinkAttach">
									<h2><a href="<?php echo $_smarty_tpl->tpl_vars['promo']->value['linkto'];?>
" class="workboxLink"><?php echo $_smarty_tpl->tpl_vars['promo']->value['name'];?>
</a></h2>
									<p class="description">
										<?php if ($_smarty_tpl->tpl_vars['promo']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['promo']->value['promo_id'],'itemType'=>'promo','photoID'=>$_smarty_tpl->tpl_vars['promo']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
"><br><br><?php }?>
										<?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['promo']->value['description'],30);?>
<br><br>
										<!--<?php if ($_smarty_tpl->tpl_vars['promo']->value['autoapply']){?><span class="promoUse">*<?php echo $_smarty_tpl->tpl_vars['lang']->value['autoApply'];?>
</span><?php }elseif($_smarty_tpl->tpl_vars['promo']->value['promo_code']){?><span class="promoUse">*<?php echo $_smarty_tpl->tpl_vars['lang']->value['useCoupon'];?>
<strong>: <?php echo $_smarty_tpl->tpl_vars['promo']->value['promo_code'];?>
</strong></span><?php }?>-->
									</p>
								</div>
							<?php } ?>
						</div>
					<?php }?>
					
					
					<div class="container cartContainer" style="clear: both;">
						<?php  $_smarty_tpl->tpl_vars['cartItem'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cartItem']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cartItems']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cartItem']->key => $_smarty_tpl->tpl_vars['cartItem']->value){
$_smarty_tpl->tpl_vars['cartItem']->_loop = true;
?>
						<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['photoPuzzleImg']){?>
							<?php $_smarty_tpl->tpl_vars['bPP'] = new Smarty_variable(1, null, 0);?>
						<?php }?>
						<div class="row">
							<div class="col-md-2 cartThumbColumn">
								<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['numOf'];?>

								<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['media']&&!$_smarty_tpl->tpl_vars['bPP']->value){?>
									<a href="<?php echo linkto(array('page'=>"media.details.php?mediaID=".($_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['media']['useMediaID'])),$_smarty_tpl);?>
"><img src="image.php?mediaID=<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['media']['encryptedID'];?>
=&type=icon&folderID=<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['media']['encryptedFID'];?>
&size=60" class="thumb"></a>
								<?php }elseif($_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['photo']){?>
									<img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['cartItem']->value['item_id'],'itemType'=>$_smarty_tpl->tpl_vars['cartItem']->value['itemTypeShort'],'photoID'=>$_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['photo']['id'],'size'=>60),$_smarty_tpl);?>
" class="thumb">
								<?php }elseif($_smarty_tpl->tpl_vars['bPP']->value){?>
									<img src="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['photoPuzzleImg'];?>
" class="thumb" style="width: 100px;">
								<?php }else{ ?>
									<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/blank.cart.item.png">
								<?php }?>
							</div>
							<div class="col-md-5">
								<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['cart_notes']){?><p class="cartAddNotes" cartItemID="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['oi_id'];?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/note.icon.png" title="<?php echo $_smarty_tpl->tpl_vars['lang']->value['notes'];?>
"></p><?php }?>
								<?php if (!$_smarty_tpl->tpl_vars['bPP']->value){?>
									<h2><a href="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['cartEditLink'];?>
" class="cartItemEditLink"><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['name'];?>
</a><!--<img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/comment.icon.png" title="Add Comment" style="margin-left: 5px;">--></h2>
								<?php }else{ ?>
									<h2><span class="cartItemEditLink"><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['name'];?>
</span></h2>
								<?php }?>
								<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['item_type']=='package'){?>
									<input type="hidden" name="packageItemsLeftToFill[]" value="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['package_media_remaining'];?>
" class="checkPackageFill" packageID="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['item_id'];?>
">
									<div class="packageFilledContainer" id="packageFilledContainer<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['item_id'];?>
">
										<div class="packageFilledBar"><p style="width: <?php echo $_smarty_tpl->tpl_vars['cartItem']->value['package_media_percentage'];?>
%"></p></div>
										<!--<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['package_media_filled'];?>
/<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['package_media_needed'];?>
 = --><strong><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['package_media_remaining'];?>
</strong> <?php echo $_smarty_tpl->tpl_vars['lang']->value['leftToFill'];?>

									</div>
								<?php }?>
								<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['item_type']=='digital'){?>
									<p class="cartItemDescription">
										<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['display_license']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['license'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['licenseLang'];?>
</strong><br><?php }?>
										<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['width']||$_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['height']){?><strong><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['width'];?>
 x <?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['height'];?>
 px</strong> <!--<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['widthIC']||$_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['heightIC']){?><em>( <?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['widthIC'];?>
 x <?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['heightIC'];?>
 @ <?php echo $_smarty_tpl->tpl_vars['config']->value['dpiCalc'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['dpi'];?>
 )</em><?php }?>--><br><?php }?>
										<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['format']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFormat'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['format'];?>
</strong><br><?php }?>
										<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['dsp_type']=='video'){?>
											<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['fps']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelFPS'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['fps'];?>
</strong><br><?php }?>
											<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['running_time']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelRunningTime'];?>
: <strong><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['running_time'];?>
</strong> <?php echo $_smarty_tpl->tpl_vars['lang']->value['seconds'];?>
<br><?php }?>
										<?php }?>
									</p>
								<?php }else{ ?>
									<p class="cartItemDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['description'],200);?>
</p>
								<?php }?>
								<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['has_options']&&!$_smarty_tpl->tpl_vars['bPP']->value){?>
								
									<div class="cartItemDetailsContainer">
										<a href="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['oi_id'];?>
" itemType="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['item_type'];?>
" class="buttonLink cartItemDetailsButton">+</a> <?php if ($_smarty_tpl->tpl_vars['cartItem']->value['item_type']=='package'){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['viewPackOptions'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['lang']->value['viewOptions'];?>
<?php }?>
										<!--<a href="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['cartEditLink'];?>
" class="colorLink cartItemEditLink" style="float: right;">[Edit]</a>-->
										<div style="display: none" id="optionsBox<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['oi_id'];?>
" class="optionsBox"></div>
										<!--ID: <?php echo $_smarty_tpl->tpl_vars['cartItem']->value['oi_id'];?>
 - Type: <?php echo $_smarty_tpl->tpl_vars['cartItem']->value['item_type'];?>
-->
									</div>
								<?php }elseif($_smarty_tpl->tpl_vars['cartItem']->value['has_options']&&$_smarty_tpl->tpl_vars['bPP']->value){?>
									<div>
										<?php  $_smarty_tpl->tpl_vars['gruppoOpzione'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['gruppoOpzione']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cartItem']->value['photoPuzzleGruppoOpzione']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['gruppoOpzione']->key => $_smarty_tpl->tpl_vars['gruppoOpzione']->value){
$_smarty_tpl->tpl_vars['gruppoOpzione']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['gruppoOpzione']->key;
?>
											<span><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['gruppoOpzione']->value];?>
: <?php echo $_smarty_tpl->tpl_vars['cartItem']->value['photoPuzzleOpzione'][$_smarty_tpl->tpl_vars['k']->value];?>
</span>
										<?php } ?>
										
										<!--<span><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['cartItem']->value]['photoPuzzleGruppoOpzione'];?>
</span>-->
										<!--<div style="display: none" id="optionsBox<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['oi_id'];?>
" class="optionsBox"></div>-->
									</div>
								<?php }?>
							</div>
							<div class="col-md-1">
								<input type="text" value="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['quantity'];?>
" name="quantity[<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['oi_id'];?>
]" class="quantity form-control" <?php if ($_smarty_tpl->tpl_vars['cartItem']->value['item_type']=='collection'||$_smarty_tpl->tpl_vars['cartItem']->value['item_type']=='digital'||$_smarty_tpl->tpl_vars['cartItem']->value['item_type']=='subscription'||$_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['multiple']==0){?>disabled="disabled"<?php }?>>
							</div>
							<div class="col-md-2 cartPriceColumn">
								<div class="btn-group">
									
										<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['usePayType']=='cur'){?>
											
											<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['payTypeCount']>1){?>
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
												<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['lineItemPriceTotalLocal']['display'];?>
<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['taxInc']){?>*<?php }?> <span class="caret"></span>
												</button>
											<?php }else{ ?>
												<h2 style="margin-top: 6px;"><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['lineItemPriceTotalLocal']['display'];?>
<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['taxInc']){?>*<?php }?></h2>
											<?php }?>
										<?php }else{ ?>
											
											<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['payTypeCount']>1){?>
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
												<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['lineItemCreditsTotal'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
 <span class="caret"></span>
												</button>
											<?php }else{ ?>
												<h2 style="margin-top: 6px;"><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['lineItemCreditsTotal'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
</h2>
											<?php }?>
										<?php }?>
									<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['payTypeCount']>1){?>
										<ul class="payType dropdown-menu" role="menu">
											<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['usePayType']=='cur'){?>
												<li cartItemID="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['encryptedID'];?>
" payType="cred"><?php echo $_smarty_tpl->tpl_vars['lang']->value['use'];?>
 <strong><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['lineItemCreditsTotal'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
</strong></li>
											<?php }else{ ?>
												<li cartItemID="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['encryptedID'];?>
" payType="cur"><?php echo $_smarty_tpl->tpl_vars['lang']->value['use'];?>
 <strong><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['lineItemPriceTotalLocal']['display'];?>
</strong></li>
											<?php }?>
										</ul>
									<?php }?>
								</div>
								<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['quantity']>1){?><br><span class="cartPriceEach">(<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['usePayType']=='cur'){?><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['lineItemPriceEachLocal']['display'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['lineItemCreditsEach'];?>
<?php }?> <?php echo $_smarty_tpl->tpl_vars['lang']->value['each'];?>
)</span><?php }?>
							</div>
							<div class="col-md-1">
								<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['discountPercentage']){?><span class="cartItemSavings"><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['discountPercentage'];?>
% Savings</span><br><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['freeItems']){?><span class="cartItemSavings"><?php echo $_smarty_tpl->tpl_vars['cartItem']->value['freeItems'];?>
 Free</span><?php }?>
							</div>							
							<div class="col-md-2 cartActionsColumn">
								<?php if ($_smarty_tpl->tpl_vars['cartItem']->value['item_type']=='package'||$_smarty_tpl->tpl_vars['cartItem']->value['item_type']=='print'||$_smarty_tpl->tpl_vars['cartItem']->value['item_type']=='product'){?>
									<?php if (!$_smarty_tpl->tpl_vars['bPP']->value){?>
										<input type="button" href="<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['itemDetails']['cartEditLink'];?>
" class="btn btn-xs btn-success cartItemEditLink" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['edit'];?>
"><br>
									<?php }?>
								<?php }?>
									<input type="button" href="cart.php?mode=remove&cid=<?php echo $_smarty_tpl->tpl_vars['cartItem']->value['encryptedID'];?>
" class="btn btn-xs btn-danger cartItemRemoveLink" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['remove'];?>
">
							</div>
						</div>
						
						<?php if ($_smarty_tpl->tpl_vars['debugMode']->value){?><?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['cartItem']->value,'title'=>'Cart Item'),$_smarty_tpl);?>
<?php }?>
						<?php } ?>
					</div>
					<div style="clear: both; margin-bottom: 20px;">
						<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['taxInPrices']){?><p style="float: left;">* <?php echo $_smarty_tpl->tpl_vars['lang']->value['includesTax'];?>
<!--: <?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['taxA']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['taxAName'];?>
: <?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_a_default'];?>
%<?php }?> <?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['taxB']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['taxBName'];?>
: <?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_b_default'];?>
%<?php }?> <?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['taxC']){?><?php echo $_smarty_tpl->tpl_vars['lang']->value['taxCName'];?>
: <?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_c_default'];?>
%<?php }?>--></p><?php }?><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['update'];?>
" style="float: right; margin-top: 6px;" class="updateQuantitiesButton btn btn-xs btn-primary">
					</div>
					
					</form>
					<?php }else{ ?>
						<?php echo $_smarty_tpl->tpl_vars['lang']->value['cartNoItems'];?>

					<?php }?>
						
				</div>
				
				
				<div class="col-md-4">
				
					<div class="cartTotalColumn">
						<?php if ($_smarty_tpl->tpl_vars['creditSystem']->value){?>
							<div class="cartTotalList yourCredits">
								<div style="padding-top: 10px;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['yourCredits'];?>
</div>
								<div class="myCredits"><?php if ($_smarty_tpl->tpl_vars['member']->value['credits']){?><?php echo $_smarty_tpl->tpl_vars['member']->value['credits'];?>
<?php }else{ ?>0<?php }?></div><div style="float: right; padding-top: 12px; padding-right: 12px;"><input type="button" href="featured.php?mode=credits" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['purchaseCredits'];?>
" class="buyCreditsButton btn btn-xs btn-primary"></div>
							</div>
						<?php }?>
						
						<div class="cartTotalList promotionsBox">
							<p><?php echo $_smarty_tpl->tpl_vars['lang']->value['discountCode'];?>
 <input type="text" name="couponCode" id="couponCode" class="form-control"><div style="padding-top: 1px;"> <input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['apply'];?>
" id="applyCouponButton" style=" float: right;" class="btn btn-xs btn-primary"></div></p>
							<?php if ($_smarty_tpl->tpl_vars['cartCouponsArray']->value){?>
								<ul>
									<?php  $_smarty_tpl->tpl_vars['coupon'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['coupon']->_loop = false;
 $_smarty_tpl->tpl_vars['couponKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cartCouponsArray']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['coupon']->key => $_smarty_tpl->tpl_vars['coupon']->value){
$_smarty_tpl->tpl_vars['coupon']->_loop = true;
 $_smarty_tpl->tpl_vars['couponKey']->value = $_smarty_tpl->tpl_vars['coupon']->key;
?>
										<li><?php echo $_smarty_tpl->tpl_vars['coupon']->value['name'];?>
 <input type="button" href="cart.php?cartMode=removeCoupon&couponID=<?php echo $_smarty_tpl->tpl_vars['coupon']->value['promo_id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['remove'];?>
" class="removeCouponButton btn btn-xs btn-danger"></li><?php if (!$_smarty_tpl->tpl_vars['coupon']->value['autoapply']){?><!-- use for auto apply or not --><?php }?>
									<?php } ?>
								</ul>
							<?php }?>
						</div>
						
						<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['customer_taxid']){?>
							<div class="cartTotalList promotionsBox" style="padding-bottom: 20px;">
								<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['vatIDNumber'];?>
:</h2>
								<div><input type="text" name="taxID" id="taxID" value="<?php echo $_smarty_tpl->tpl_vars['shippingAddress']->value['email'];?>
" class="form-control"> <!--<input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['apply'];?>
" id="applyCouponButton" style=" float: right;" class="colorButton">--></div>
							</div>
						<?php }?>
						
						<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['cart_notes']){?>
							<div class="cartTotalList promotionsBox" style="padding-bottom: 20px;">
								<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['notes'];?>
:</h2>
								<div><textarea name="cartNotes" id="cartNotes" class="form-control"><?php echo $_smarty_tpl->tpl_vars['cartInfo']->value['cartNotes'];?>
</textarea> <!--<input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['apply'];?>
" id="applyCouponButton" style=" float: right;" class="colorButton">--></div>
							</div>
						<?php }?>
						
						<div class="cartTotalList">
							
							<?php if ($_smarty_tpl->tpl_vars['priCurrency']->value['currency_id']!=$_smarty_tpl->tpl_vars['selectedCurrency']->value){?><div class="cartTotalListWarning"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/notice.icon.png"/><?php echo $_smarty_tpl->tpl_vars['lang']->value['cartTotalListWarning'];?>
</div><?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['priceSubTotal']){?>
								<div class="divTable">
									<div class="divTableRow">
										<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['subtotal'];?>
:</div>
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['subTotalLocal']['display'];?>
</span></div>
									</div>
									<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['taxTotal']){?>
										<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['taxA']){?>
											<div class="divTableRow">
												<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['estimated'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['taxAName'];?>
<!-- (<?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_a_default'];?>
%)-->:</div>
												<div class="divTableCell"><span class="<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['clearTax']){?>strike<?php }?>"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['taxALocal']['display'];?>
</span></div>
											</div>
										<?php }?>
										<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['taxB']){?>
											<div class="divTableRow">
												<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['estimated'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['taxBName'];?>
<!-- (<?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_b_default'];?>
%)-->:</div>
												<div class="divTableCell"><span class="<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['clearTax']){?>strike<?php }?>"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['taxBLocal']['display'];?>
</span></div>
											</div>
										<?php }?>
										<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['taxC']){?>
											<div class="divTableRow">
												<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['estimated'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['taxCName'];?>
<!-- (<?php echo $_smarty_tpl->tpl_vars['tax']->value['tax_c_default'];?>
%)-->:</div>
												<div class="divTableCell"><span class="<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['clearTax']){?>strike<?php }?>"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['taxCLocal']['display'];?>
</span></div>
											</div>
										<?php }?>
									<?php }?>
									<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['totalDiscounts']){?>
										<div class="divTableRow">
											<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['discounts'];?>
:</div>
											<div class="divTableCell"><span class="cartTotalDiscounts">-<?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['totalDiscountsLocal']['display'];?>
</span></div>
										</div>
									<?php }?>
									<!--
									<div class="divTableRow">
										<div class="divTableCell">Shipping:</div>
										<div class="divTableCell" style="text-align: right"><span class="price">TBD</span></div>
									</div>
									-->
									<div class="divTableRow">
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['lang']->value['total'];?>
:</span></div>
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['totalLocal']['display'];?>
</span></div>
									</div>
								</div>
							<?php }?>
							
							<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['creditsSubTotal']){?>	
								<div class="divTable">
									<div class="divTableRow">
										<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['creditsSubtotal'];?>
:</div>
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['creditsSubTotal'];?>
</span></div>
									</div>
									<?php if ($_smarty_tpl->tpl_vars['cartTotals']->value['totalCreditsDiscounts']){?>
										<div class="divTableRow">
											<div class="divTableCell"><?php echo $_smarty_tpl->tpl_vars['lang']->value['creditsDiscounts'];?>
:</div>
											<div class="divTableCell"><span class="cartTotalDiscounts">-<?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['totalCreditsDiscounts'];?>
</span></div>
										</div>
									<?php }?>
									<div class="divTableRow">
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['lang']->value['credits'];?>
:</span></div>
										<div class="divTableCell"><span class="price"><?php echo $_smarty_tpl->tpl_vars['cartTotals']->value['creditsTotal'];?>
</span></div>
									</div>
								</div>
							<?php }?>
							<input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['checkout'];?>
" style="float: right" id="cartCheckoutButton" class="btn btn-xs btn-success">
						</div>
					</div>
				</div>
				
			</div>
		</div>
		
		<?php if ($_smarty_tpl->tpl_vars['debugMode']->value){?>
			<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['uniqueOrderID']->value,'title'=>'uniqueOrderID'),$_smarty_tpl);?>

			<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['cartID']->value,'title'=>'cartID'),$_smarty_tpl);?>

			<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['invoiceID']->value,'title'=>'invoiceID'),$_smarty_tpl);?>

			<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['cartItemRows']->value,'title'=>'cartItemRows'),$_smarty_tpl);?>

			<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['cartTotals']->value,'title'=>'Cart Variables'),$_smarty_tpl);?>

			<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['packagesInCartSession']->value,'title'=>'Packages IDs In Cart'),$_smarty_tpl);?>

			<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['cartCouponsArray']->value,'title'=>'Coupons'),$_smarty_tpl);?>

		<?php }?>
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>