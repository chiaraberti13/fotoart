<?php /* Smarty version Smarty-3.1.8, created on 2025-09-01 15:24:13
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/media.details.tpl" */ ?>
<?php /*%%SmartyHeaderCode:149922668968b5ba9de4ffc0-98947851%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '87335874d6e0f90971171fbee17ccabb15647a42' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/media.details.tpl',
      1 => 1755093989,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '149922668968b5ba9de4ffc0-98947851',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'baseURL' => 0,
    'config' => 0,
    'media' => 0,
    'noAccess' => 0,
    'lang' => 0,
    'galleryMode' => 0,
    'backButton' => 0,
    'nextButtonID' => 0,
    'prevButtonID' => 0,
    'printRows' => 0,
    'productRows' => 0,
    'collectionRows' => 0,
    'packageRows' => 0,
    'prints' => 0,
    'print' => 0,
    'cartStatus' => 0,
    'products' => 0,
    'product' => 0,
    'ppzl' => 0,
    'collections' => 0,
    'collection' => 0,
    'packages' => 0,
    'package' => 0,
    'useMediaID' => 0,
    'imgPath' => 0,
    'detail' => 0,
    'keyword' => 0,
    'color' => 0,
    'stars' => 0,
    'commentSystem' => 0,
    'taggingSystem' => 0,
    'iptcRows' => 0,
    'exifRows' => 0,
    'useGalleryID' => 0,
    'member' => 0,
    'formKey' => 0,
    'iptc' => 0,
    'exif' => 0,
    'fullURL' => 0,
    'subGalleriesData' => 0,
    'subGallery' => 0,
    'galleriesData' => 0,
    'debugMode' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b5ba9e35d8f2_48242871',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b5ba9e35d8f2_48242871')) {function content_68b5ba9e35d8f2_48242871($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_truncate')) include '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/smarty/plugins/modifier.truncate.php';
?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/media.details.js"></script>
	<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['zoomonoff']==1){?>
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/imagelens/jquery.imageLens.js"></script>
		<script type="text/javascript" language="javascript">
			$(function()
			{
				$("#imagetozoom").imageLens({
					lensSize: <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['zoomlenssize'];?>
,
					borderSize: <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['zoombordersize'];?>
,
					imageSrc: "<?php if ($_smarty_tpl->tpl_vars['media']->value['zoomCachedLink']){?><?php echo $_smarty_tpl->tpl_vars['media']->value['zoomCachedLink'];?>
<?php }else{ ?><?php echo mediaImage(array('mediaID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedID'],'type'=>'sample','folderID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedFID'],'seo'=>$_smarty_tpl->tpl_vars['media']->value['seoName'],'size'=>1024),$_smarty_tpl);?>
<?php }?>",
					borderColor: "#<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['zoombordercolor'];?>
"
				});
			});
		</script>
	<?php }?>
</head>
<body>
	<?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<?php echo $_smarty_tpl->getSubTemplate ('header2.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
		<div class="container">
			<?php if ($_smarty_tpl->tpl_vars['noAccess']->value){?>
				<div class="row">
					<div class="col-md-12">
						<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['noAccess'];?>
</p>
					</div>
				</div>
			<?php }else{ ?>				
				
				
				
				<div class="row" style="margin-bottom: 10px;">
					<div class="col-md-12">
						<?php if ($_smarty_tpl->tpl_vars['galleryMode']->value){?>
							<div class="prevNext">
								<?php if ($_smarty_tpl->tpl_vars['backButton']->value){?><input type="button" value="< <?php echo $_smarty_tpl->tpl_vars['lang']->value['backUpper'];?>
" id="backButton" href="<?php echo $_smarty_tpl->tpl_vars['backButton']->value['linkto'];?>
" class="btn btn-xs btn-primary"><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['nextButtonID']->value){?><input type="button" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['nextUpper'];?>
 >" id="nextButton" href="<?php echo linkto(array('page'=>"media.details.php?mediaID=".($_smarty_tpl->tpl_vars['nextButtonID']->value)),$_smarty_tpl);?>
" style="float: right; margin-left: 2px;" class="btn btn-xs btn-primary"><!--&raquo;--><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['prevButtonID']->value){?><input type="button" value="< <?php echo $_smarty_tpl->tpl_vars['lang']->value['prevUpper'];?>
" id="prevButton" href="<?php echo linkto(array('page'=>"media.details.php?mediaID=".($_smarty_tpl->tpl_vars['prevButtonID']->value)),$_smarty_tpl);?>
" style="float: right;" class="btn btn-xs btn-primary"><?php }?>
							</div>
						<?php }?>			
					</div>
				</div>
				<hr>
					<div class="row">
					<div class="col-md-12">
		             
						<h4> <?php echo $_smarty_tpl->tpl_vars['media']->value['details']['description']['value'];?>
 </h4>
					</div>
				</div>
								
				<div class="row">					
					<div class="col-md-8 mediaPreviewCol">					
						<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['media']->value['media_id'];?>
" id="mediaID">						
						<?php if ($_smarty_tpl->tpl_vars['media']->value['videoStatus']){?>									
							<div id="hoverMediaContainer" class="hoverMediaContainer" style="width: <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['video_sample_width'];?>
px; height: <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['video_sample_height'];?>
px; background-image: none;"><p id="vidContainer"></p></div>
							<script type="text/javascript">
								jwplayer("vidContainer").setup(
								{
									'flashplayer': "<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/jwplayer/player.swf",
									'file': "<?php echo $_smarty_tpl->tpl_vars['media']->value['videoInfo']['url'];?>
",
									<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['video_autoplay']){?>'autostart': true,<?php }?>
									'type': 'video',
									'repeat': '<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['video_autorepeat'];?>
',
									'controlbar.position': '<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['video_controls'];?>
',
									'logo.file': '<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/watermarks/<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['vidpreview_wm'];?>
',
									'logo.hide': false,
									'logo.position': '<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['video_wmpos'];?>
',
									'stretching': '<?php echo $_smarty_tpl->tpl_vars['config']->value['featuredVideoStretch'];?>
',
									'width': '100%',
									'height': '100%',
									'skin': '<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/jwplayer/skins/<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['video_skin'];?>
/<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['video_skin'];?>
.zip',
									'screencolor': '<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['video_bg_color'];?>
',
									'volume': 100,
									'modes': [
										{ 'type': <?php if ($_smarty_tpl->tpl_vars['media']->value['videoInfo']['vidsample_extension']=='flv'||$_smarty_tpl->tpl_vars['config']->value['forceFlashVideoPlayer']){?>'flash', src: '<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/jwplayer/player.swf'<?php }else{ ?>'html5'<?php }?> },
										{ 'type': 'download' }
									]
								});
								
								<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['video_autoresize']){?>
									jwplayer("vidContainer").onMeta(function()
									{
										vidWindowResize("vidContainer");
									});
								<?php }?>
							</script>
						<?php }else{ ?>
							
							<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['zoomonoff']==1){?>
								<p class="previewContainer" style="min-width: <?php echo $_smarty_tpl->tpl_vars['media']->value['previewWidth'];?>
px; width: <?php echo $_smarty_tpl->tpl_vars['media']->value['previewWidth'];?>
px; height: <?php echo $_smarty_tpl->tpl_vars['media']->value['previewHeight'];?>
px;"><img <?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['zoomonoff']==1){?>id="imagetozoom"<?php }?> src="<?php if ($_smarty_tpl->tpl_vars['media']->value['sampleCachedLink']){?><?php echo $_smarty_tpl->tpl_vars['media']->value['sampleCachedLink'];?>
<?php }else{ ?><?php echo mediaImage(array('mediaID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedID'],'type'=>'sample','folderID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedFID'],'seo'=>$_smarty_tpl->tpl_vars['media']->value['seoName']),$_smarty_tpl);?>
<?php }?>" title="<?php echo $_smarty_tpl->tpl_vars['media']->value['details']['title']['value'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['media']->value['details']['title']['value'];?>
" style="width: <?php echo $_smarty_tpl->tpl_vars['media']->value['previewWidth'];?>
px; height: <?php echo $_smarty_tpl->tpl_vars['media']->value['previewHeight'];?>
px;"></p>
							<?php }else{ ?>
								<div class="previewContainer"><img src="<?php if ($_smarty_tpl->tpl_vars['media']->value['sampleCachedLink']){?><?php echo $_smarty_tpl->tpl_vars['media']->value['sampleCachedLink'];?>
<?php }else{ ?><?php echo mediaImage(array('mediaID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedID'],'type'=>'sample','folderID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedFID'],'seo'=>$_smarty_tpl->tpl_vars['media']->value['seoName']),$_smarty_tpl);?>
<?php }?>" title="<?php echo $_smarty_tpl->tpl_vars['media']->value['details']['description']['value'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['media']->value['details']['description']['value'];?>
" class="img-responsive"></div>
							<?php }?>
						<?php }?>
						
						
						<div class="row">
					<div class="col-md-12">
		             
						<h4><?php echo $_smarty_tpl->tpl_vars['media']->value['details']['title']['value'];?>
</h4><hr>
					</div>
				</div>
				
				
						<div class="row">
						<div class="col-md-12">					
							<ul id="mediaPurchaseTabsContainer" class="tabs">
						
								<?php if ($_smarty_tpl->tpl_vars['printRows']->value){?><li container="purchasePrints"><?php echo $_smarty_tpl->tpl_vars['lang']->value['prints'];?>
</li><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['productRows']->value){?><li container="purchaseProducts"><?php echo $_smarty_tpl->tpl_vars['lang']->value['products'];?>
</li><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['collectionRows']->value){?><li container="purchaseCollections"><?php echo $_smarty_tpl->tpl_vars['lang']->value['collections'];?>
</li><?php }?>
								<?php if ($_smarty_tpl->tpl_vars['packageRows']->value){?><li container="purchasePackages"><?php echo $_smarty_tpl->tpl_vars['lang']->value['packages'];?>
</li><?php }?>
							</ul>
					
					
							<?php if ($_smarty_tpl->tpl_vars['printRows']->value){?>
								<div class="mediaPurchaseContainers" id="purchasePrints">
									<?php  $_smarty_tpl->tpl_vars['print'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['print']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['prints']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['print']->key => $_smarty_tpl->tpl_vars['print']->value){
$_smarty_tpl->tpl_vars['print']->_loop = true;
?>
										<div class="purchaseRow">
											<?php if ($_smarty_tpl->tpl_vars['print']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['print']->value['print_id'],'itemType'=>'print','photoID'=>$_smarty_tpl->tpl_vars['print']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
"><?php }?>
											<h2><a href="<?php echo $_smarty_tpl->tpl_vars['print']->value['linkto'];?>
" class="workboxLink workboxLinkAttach"><?php echo $_smarty_tpl->tpl_vars['print']->value['name'];?>
</a></h2>
											<p class="purchaseListDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['print']->value['description'],200);?>
</p>
											<p class="purchaseListPrice"><?php if ($_smarty_tpl->tpl_vars['print']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['print']->value['price']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['print']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['print']->value['credits']&&$_smarty_tpl->tpl_vars['print']->value['price']){?> <?php echo $_smarty_tpl->tpl_vars['lang']->value['priceCreditSep'];?>
 <?php }?><?php if ($_smarty_tpl->tpl_vars['print']->value['credits']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['print']->value['credits'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelCredits'];?>
</span><?php }?></p>
											<?php if ($_smarty_tpl->tpl_vars['cartStatus']->value){?><a href="<?php echo $_smarty_tpl->tpl_vars['print']->value['addToCartLink'];?>
" class="btn btn-xs btn-primary <?php if (!$_smarty_tpl->tpl_vars['print']->value['directToCart']){?> workboxLink workboxLinkAttach<?php }elseif($_smarty_tpl->tpl_vars['config']->value['settings']['minicart']){?> miniCartLink<?php }?>" rel="nofollow"><?php echo $_smarty_tpl->tpl_vars['lang']->value['addToCart'];?>
</a><?php }?>
										</div>
									<?php } ?>
								</div>
							<?php }?>
							
							
							<?php if ($_smarty_tpl->tpl_vars['productRows']->value){?>
								<div class="mediaPurchaseContainers" id="purchaseProducts">
									
									<?php $_smarty_tpl->tpl_vars['ppzl'] = new Smarty_variable(false, null, 0);?>						
									<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
										
										<?php if ($_smarty_tpl->tpl_vars['product']->value['item_code']=="PPZL"){?>
											<?php if ($_smarty_tpl->tpl_vars['ppzl']->value==false){?>
												<?php $_smarty_tpl->tpl_vars['ppzl'] = new Smarty_variable(true, null, 0);?>
												<div class="purchaseRow">
												
												<h2><a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['linktopuzzle'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['fotopuzzleName'];?>
</a></h2>
												<p class="purchaseListDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['lang']->value['fotopuzzleDesc'],200);?>
</p>
												
											</div>
											<?php }?>
										<?php }else{ ?>
											<div class="purchaseRow">
												<?php if ($_smarty_tpl->tpl_vars['product']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['product']->value['prod_id'],'itemType'=>'prod','photoID'=>$_smarty_tpl->tpl_vars['product']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
"><?php }?>
												<h2><a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['linkto'];?>
" class="workboxLink workboxLinkAttach"><?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>
</a></h2>
												<p class="purchaseListDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['product']->value['description'],200);?>
</p>
												<p class="purchaseListPrice"><?php if ($_smarty_tpl->tpl_vars['product']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['product']->value['price']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['product']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['product']->value['credits']&&$_smarty_tpl->tpl_vars['product']->value['price']){?> <?php echo $_smarty_tpl->tpl_vars['lang']->value['priceCreditSep'];?>
 <?php }?><?php if ($_smarty_tpl->tpl_vars['product']->value['credits']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['product']->value['credits'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelCredits'];?>
</span><?php }?></p>
												<?php if ($_smarty_tpl->tpl_vars['cartStatus']->value){?><a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['addToCartLink'];?>
" class="btn btn-xs btn-primary <?php if (!$_smarty_tpl->tpl_vars['product']->value['directToCart']){?> workboxLink workboxLinkAttach<?php }elseif($_smarty_tpl->tpl_vars['config']->value['settings']['minicart']){?> miniCartLink<?php }?>" rel="nofollow"><?php echo $_smarty_tpl->tpl_vars['lang']->value['addToCart'];?>
</a><?php }?>
											</div>
										
										<?php }?>
									<?php } ?>					
								</div>
							<?php }?>
							
							
							<?php if ($_smarty_tpl->tpl_vars['collectionRows']->value){?>
								<div class="mediaPurchaseContainers" id="purchaseCollections">						
									<p class="mpcDescription"><?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaIncludedInColl'];?>
</p>
									<?php  $_smarty_tpl->tpl_vars['collection'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['collection']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['collections']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['collection']->key => $_smarty_tpl->tpl_vars['collection']->value){
$_smarty_tpl->tpl_vars['collection']->_loop = true;
?>
										<div class="purchaseRow">
											<?php if ($_smarty_tpl->tpl_vars['collection']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['collection']->value['coll_id'],'itemType'=>'coll','photoID'=>$_smarty_tpl->tpl_vars['collection']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
"><?php }?>
											<h2><a href="<?php echo $_smarty_tpl->tpl_vars['collection']->value['linkto'];?>
" class="workboxLink workboxLinkAttach"><?php echo $_smarty_tpl->tpl_vars['collection']->value['name'];?>
</a></h2>
											<p class="purchaseListDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['collection']->value['description'],200);?>
</p>
											<p class="purchaseListPrice"><?php if ($_smarty_tpl->tpl_vars['collection']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['collection']->value['price']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['collection']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['collection']->value['credits']&&$_smarty_tpl->tpl_vars['collection']->value['price']){?> <?php echo $_smarty_tpl->tpl_vars['lang']->value['priceCreditSep'];?>
 <?php }?><?php if ($_smarty_tpl->tpl_vars['collection']->value['credits']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['collection']->value['credits'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelCredits'];?>
</span><?php }?></p>
											<a href="<?php echo $_smarty_tpl->tpl_vars['collection']->value['viewCollectionLink'];?>
" class="btn btn-xs btn-primary"><?php echo $_smarty_tpl->tpl_vars['lang']->value['viewCollection'];?>
</a><?php if ($_smarty_tpl->tpl_vars['cartStatus']->value){?> <a href="<?php echo $_smarty_tpl->tpl_vars['collection']->value['addToCartLink'];?>
" class="btn btn-xs btn-primary <?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['minicart']){?>miniCartLink<?php }?>" rel="nofollow"><?php echo $_smarty_tpl->tpl_vars['lang']->value['addToCart'];?>
</a><?php }?>
										</div>
									<?php } ?>
								</div>
							<?php }?>
							
							
							<?php if ($_smarty_tpl->tpl_vars['packageRows']->value){?>
								<div class="mediaPurchaseContainers" id="purchasePackages">
									<?php  $_smarty_tpl->tpl_vars['package'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['package']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['packages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['package']->key => $_smarty_tpl->tpl_vars['package']->value){
$_smarty_tpl->tpl_vars['package']->_loop = true;
?>
										<div class="purchaseRow">
											<?php if ($_smarty_tpl->tpl_vars['package']->value['photo']){?><img src="<?php echo productShot(array('itemID'=>$_smarty_tpl->tpl_vars['package']->value['pack_id'],'itemType'=>'pack','photoID'=>$_smarty_tpl->tpl_vars['package']->value['photo']['id'],'size'=>125),$_smarty_tpl);?>
"><?php }?>
											<h2><a href="<?php echo $_smarty_tpl->tpl_vars['package']->value['linkto'];?>
" class="workboxLink workboxLinkAttach"><?php echo $_smarty_tpl->tpl_vars['package']->value['name'];?>
</a></h2>
											<p class="purchaseListDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['package']->value['description'],200);?>
</p>
											<p class="purchaseListPrice"><?php if ($_smarty_tpl->tpl_vars['package']->value['price']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['package']->value['price']['display'];?>
</span><?php if ($_smarty_tpl->tpl_vars['package']->value['price']['taxInc']){?> <span class="taxIncMessage">(<?php echo $_smarty_tpl->tpl_vars['lang']->value['taxIncMessage'];?>
)</span><?php }?><?php }?><?php if ($_smarty_tpl->tpl_vars['package']->value['credits']&&$_smarty_tpl->tpl_vars['package']->value['price']){?> <?php echo $_smarty_tpl->tpl_vars['lang']->value['priceCreditSep'];?>
 <?php }?><?php if ($_smarty_tpl->tpl_vars['package']->value['credits']){?><span class="price"><?php echo $_smarty_tpl->tpl_vars['package']->value['credits'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['mediaLabelCredits'];?>
</span><?php }?></p>
											<?php if ($_smarty_tpl->tpl_vars['cartStatus']->value){?><a href="<?php echo $_smarty_tpl->tpl_vars['package']->value['linkto'];?>
" class="workboxLink workboxLinkAttach btn btn-xs btn-primary" rel="nofollow"><?php echo $_smarty_tpl->tpl_vars['lang']->value['addToCart'];?>
</a><?php }?>
										</div>
									<?php } ?>
								</div>
							<?php }?>
							
						</div>
					</div>
					</div>
					
					
			
									
					<div class="col-md-4">
					<div style="margin-top: 4px; overflow: auto; clear: both; margin-bottom: 4px;">
							<p style="float: left; overflow: auto"><?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['sn_code'];?>
</p>
							<?php if ($_smarty_tpl->tpl_vars['media']->value['showLightbox']){?><p class="btn btn-info btn-primary addToLightboxButton" lightboxItemID="<?php echo $_smarty_tpl->tpl_vars['media']->value['lightboxItemID'];?>
" mediaID="<?php echo $_smarty_tpl->tpl_vars['media']->value['media_id'];?>
"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['lightbox'];?>
</p><?php }?>
							<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['email_friend']){?><p class="btn btn-info btn-primary emailToFriend" mediaID="<?php echo $_smarty_tpl->tpl_vars['useMediaID']->value;?>
"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['email'];?>
</p><?php }?>										
							<?php if ($_smarty_tpl->tpl_vars['packageRows']->value){?><p class="mediaPreviewContainerIcon assignToPackageButton" mediaID="<?php echo $_smarty_tpl->tpl_vars['media']->value['media_id'];?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/package.icon.0.png"> <?php echo $_smarty_tpl->tpl_vars['lang']->value['assignToPackage'];?>
</p><?php }?>
							<!--<p class="mediaPreviewContainerIcon clickToEnlarge" mediaID="<?php echo $_smarty_tpl->tpl_vars['useMediaID']->value;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/enlarge.icon.0.png"> Enlarge</p>-->
						</div>
					
					
					<ul class="mediaDetailsList">
						<?php echo content(array('id'=>99),$_smarty_tpl);?>

						<hr>
					<center>	<p class="btn btn-warning btn-primary"><a href="<?php echo $_smarty_tpl->tpl_vars['product']->value['linktopuzzle'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['fotopuzzleName'];?>
</a></p> </center><br>
									<center>			<p class="purchaseListDescription"><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['lang']->value['fotopuzzleDesc'],200);?>
</p></center>
						<hr>						
						<ul class="mediaDetailsList2">
						<?php  $_smarty_tpl->tpl_vars['detail'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['detail']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['media']->value['details']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['detail']->key => $_smarty_tpl->tpl_vars['detail']->value){
$_smarty_tpl->tpl_vars['detail']->_loop = true;
?>
							<?php if ($_smarty_tpl->tpl_vars['detail']->value['value']!=''){?>
								<li>
									<span class="mediaDetailLabel mediaDetailLabel<?php echo $_smarty_tpl->tpl_vars['detail']->key;?>
" style="float: left;"><?php echo $_smarty_tpl->tpl_vars['detail']->value['lang'];?>
:&nbsp;</span> 
									<?php if ($_smarty_tpl->tpl_vars['detail']->key=='keywords'){?>
										<?php  $_smarty_tpl->tpl_vars['keyword'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['keyword']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['detail']->value['value']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['keyword']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['keyword']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['keyword']->key => $_smarty_tpl->tpl_vars['keyword']->value){
$_smarty_tpl->tpl_vars['keyword']->_loop = true;
 $_smarty_tpl->tpl_vars['keyword']->iteration++;
 $_smarty_tpl->tpl_vars['keyword']->last = $_smarty_tpl->tpl_vars['keyword']->iteration === $_smarty_tpl->tpl_vars['keyword']->total;
?>
											<a href="<?php echo linkto(array('page'=>"search.php?clearSearch=true&searchPhrase=".($_smarty_tpl->tpl_vars['keyword']->value)),$_smarty_tpl);?>
" class="colorLink"><?php echo $_smarty_tpl->tpl_vars['keyword']->value;?>
</a><?php if (!$_smarty_tpl->tpl_vars['keyword']->last){?>,<?php }?> 
										<?php } ?>
									<?php }elseif($_smarty_tpl->tpl_vars['detail']->key=='colorPalette'){?>
										<?php  $_smarty_tpl->tpl_vars['color'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['color']->_loop = false;
 $_smarty_tpl->tpl_vars['colorKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['detail']->value['value']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['color']->key => $_smarty_tpl->tpl_vars['color']->value){
$_smarty_tpl->tpl_vars['color']->_loop = true;
 $_smarty_tpl->tpl_vars['colorKey']->value = $_smarty_tpl->tpl_vars['color']->key;
?>
											<span class="colorSwatch" style="background-color: #<?php echo $_smarty_tpl->tpl_vars['color']->value['hex'];?>
" title='#<?php echo $_smarty_tpl->tpl_vars['color']->value['hex'];?>
'></span>
										<?php } ?>
									<?php }elseif($_smarty_tpl->tpl_vars['detail']->key=='owner'){?>
										<span class="mediaDetailValue mediaDetailValue<?php echo $_smarty_tpl->tpl_vars['detail']->key;?>
">
											<?php if ($_smarty_tpl->tpl_vars['detail']->value['value']['useID']){?>
												<a href="<?php echo linkto(array('page'=>"contributors.php?id=".($_smarty_tpl->tpl_vars['detail']->value['value']['useID'])."&seoName=".($_smarty_tpl->tpl_vars['detail']->value['value']['seoName'])),$_smarty_tpl);?>
" class="colorLink">
												
												<?php echo $_smarty_tpl->tpl_vars['detail']->value['value']['displayName'];?>

												</a>
											<?php }else{ ?>
												<?php echo $_smarty_tpl->tpl_vars['detail']->value['value']['displayName'];?>

											<?php }?>
										</span>
									<?php }else{ ?>
										<span class="mediaDetailValue mediaDetailValue<?php echo $_smarty_tpl->tpl_vars['detail']->key;?>
"><?php echo $_smarty_tpl->tpl_vars['detail']->value['value'];?>
</span>
									<?php }?>
								</li>
							<?php }?>									
						<?php } ?>
						<?php if ($_smarty_tpl->tpl_vars['media']->value['showRating']){?>
							<li style="padding-top: 15px; clear: both;">
								<p class="<?php if ($_smarty_tpl->tpl_vars['media']->value['allowRating']){?>starRating<?php }?>" mediaID="<?php echo $_smarty_tpl->tpl_vars['media']->value['media_id'];?>
">
									<?php  $_smarty_tpl->tpl_vars['stars'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['stars']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['media']->value['rating']['stars']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['stars']->key => $_smarty_tpl->tpl_vars['stars']->value){
$_smarty_tpl->tpl_vars['stars']->_loop = true;
?><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/star.<?php echo $_smarty_tpl->tpl_vars['stars']->value;?>
.png" class="ratingStar" originalStatus="<?php echo $_smarty_tpl->tpl_vars['stars']->value;?>
"><?php } ?>
									&nbsp;<span class="mediaDetailValue"><strong><?php echo $_smarty_tpl->tpl_vars['media']->value['rating']['average'];?>
</strong>/<?php echo $_smarty_tpl->tpl_vars['config']->value['RatingStars'];?>
 (<?php echo $_smarty_tpl->tpl_vars['media']->value['rating']['votes'];?>
 <?php echo $_smarty_tpl->tpl_vars['lang']->value['votes'];?>
)</span><br>
								</p>
							</li>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['media']->value['latitude']&&$_smarty_tpl->tpl_vars['media']->value['longitude']){?>
							<li style="padding-top: 15px; clear: both;">
								<span class="mediaDetailLabel mediaDetailLabelLocation" style="float: left;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['location'];?>
:&nbsp;</span><br>
								<p>
									<a href='http://maps.google.com/maps?q=<?php echo $_smarty_tpl->tpl_vars['media']->value['latitude'];?>
,<?php echo $_smarty_tpl->tpl_vars['media']->value['longitude'];?>
' target='_blank' border='0'>
									<img src='http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $_smarty_tpl->tpl_vars['media']->value['latitude'];?>
,<?php echo $_smarty_tpl->tpl_vars['media']->value['longitude'];?>
&zoom=<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['gpszoom'];?>
&size=<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['gpswidth'];?>
x<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['gpsheight'];?>
&maptype=<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['gpsmaptype'];?>
&sensor=false&markers=color:<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['gpscolor'];?>
%7C<?php echo $_smarty_tpl->tpl_vars['media']->value['latitude'];?>
,<?php echo $_smarty_tpl->tpl_vars['media']->value['longitude'];?>
' class="nofotomoto">
									</a>
								</p>
							</li>
						<?php }?>
						</ul></ul>
					</div>
				</div>
					
					
					
					<div class="row">
						<div class="col-md-12">
							
							<div id="mediaExtraInfoDG" style="margin-bottom: 30px;">
								<ul class="tabs">
									<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['related_media']){?><li container="similarMediaDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['similarMedia'];?>
</li><?php }?>
									<?php if ($_smarty_tpl->tpl_vars['commentSystem']->value){?><li container="commentsDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['comments'];?>
</li><?php }?>
									<?php if ($_smarty_tpl->tpl_vars['taggingSystem']->value){?><li container="taggingDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['memberTags'];?>
</li><?php }?>
									<?php if ($_smarty_tpl->tpl_vars['iptcRows']->value){?><li container="iptcDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['iptc'];?>
</li><?php }?>
									<?php if ($_smarty_tpl->tpl_vars['exifRows']->value){?><li container="exifDGC"><?php echo $_smarty_tpl->tpl_vars['lang']->value['exif'];?>
</li><?php }?>
									<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['share']){?><li container="shareMedia"><?php echo $_smarty_tpl->tpl_vars['lang']->value['share'];?>
</li><?php }?>
								</ul>
								<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['related_media']){?>
									<div class="dataGroupContainer" id="similarMediaDGC">
										<div id="mediaSimilarPhotos" mediaID="<?php echo $_smarty_tpl->tpl_vars['useMediaID']->value;?>
" galleryID="<?php echo $_smarty_tpl->tpl_vars['useGalleryID']->value;?>
" galleryMode="<?php echo $_smarty_tpl->tpl_vars['galleryMode']->value;?>
" style="overflow: auto; min-height: 60px;"></div>
									</div>
								<?php }?>
								<?php if ($_smarty_tpl->tpl_vars['commentSystem']->value){?>
									<div class="dataGroupContainer" id="commentsDGC">
										<p class="notice" id="newCommentMessage"></p>
										<?php if ($_smarty_tpl->tpl_vars['member']->value['allowCommenting']){?>
											<form action="" id="newCommentForm" method="post" class="form-group">
												<input type="hidden" name="action" value="newComment">
												<input type="hidden" name="formKey" value="<?php echo $_smarty_tpl->tpl_vars['formKey']->value;?>
">						
												<div id="newCommentContainer">
													<?php echo $_smarty_tpl->tpl_vars['lang']->value['addNewComment'];?>
 <img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/down.arrow.png"><br>								
													<textarea class="form-control" name="newComment" id="newComment"></textarea><br>
													<input type="submit" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['submit'];?>
" style="float: right;" class="btn btn-xs btn-primary">
												</div>
											</form>
										<?php }?>
										<div id="mediaComments" mediaID="<?php echo $_smarty_tpl->tpl_vars['useMediaID']->value;?>
" style="min-height: 30px;"></div>
									</div>
								<?php }?>
								<?php if ($_smarty_tpl->tpl_vars['taggingSystem']->value){?>
									<div class="dataGroupContainer" id="taggingDGC" style="overflow: auto;">
										<?php if ($_smarty_tpl->tpl_vars['member']->value['allowTagging']){?>
											<div style="float: right; color: #666; font-weight: bold; font-size: 11px; float: right; margin-top: 10px;">
												<?php echo $_smarty_tpl->tpl_vars['lang']->value['addTag'];?>
: <br>
												<form action="" id="newTagForm" method="post" class="form-group">
													<input type="hidden" name="action" value="newTag">
													<input type="hidden" name="formKey" value="<?php echo $_smarty_tpl->tpl_vars['formKey']->value;?>
">
													<input type="text" style="min-width: 150px;" name="newTag" id="newTag" class="form-control"> 
													<input type="submit" value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['submit'];?>
" style="vertical-align: middle; float: right;" class="btn btn-xs btn-primary">
												</form>
											</div>
										<?php }?>
										<p class="notice" id="newTagMessage"></p>
										<div id="mediaTags" mediaID="<?php echo $_smarty_tpl->tpl_vars['useMediaID']->value;?>
" style="min-height: 30px;"></div>
									</div>
								<?php }?>
								
								<?php if ($_smarty_tpl->tpl_vars['iptcRows']->value){?>
									<div class="dataGroupContainer" id="iptcDGC">
										<ul style="color: #999">
											<?php  $_smarty_tpl->tpl_vars['iptc'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['iptc']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['media']->value['iptc']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['iptc']->key => $_smarty_tpl->tpl_vars['iptc']->value){
$_smarty_tpl->tpl_vars['iptc']->_loop = true;
?>
												<?php if ($_smarty_tpl->tpl_vars['iptc']->value){?>
													<li>
														<span class="mediaDetailLabel mediaDetailLabel<?php echo $_smarty_tpl->tpl_vars['iptc']->key;?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['iptc']->key];?>
</span>: 
														<span class="mediaDetailValue mediaDetailValue<?php echo $_smarty_tpl->tpl_vars['iptc']->key;?>
"><?php echo $_smarty_tpl->tpl_vars['iptc']->value;?>
</span>
													</li>
												<?php }?>
											<?php } ?>
										</ul>
									</div>
								<?php }?>
								
								<?php if ($_smarty_tpl->tpl_vars['exifRows']->value){?>
									<div class="dataGroupContainer" id="exifDGC">
										<ul style="color: #999">
											<?php  $_smarty_tpl->tpl_vars['exif'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['exif']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['media']->value['exif']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['exif']->key => $_smarty_tpl->tpl_vars['exif']->value){
$_smarty_tpl->tpl_vars['exif']->_loop = true;
?>
												<?php if ($_smarty_tpl->tpl_vars['exif']->value){?>
													<li>
														<span class="mediaDetailLabel mediaDetailLabel<?php echo $_smarty_tpl->tpl_vars['exif']->key;?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value[$_smarty_tpl->tpl_vars['exif']->key];?>
</span>: 
														<span class="mediaDetailValue mediaDetailValue<?php echo $_smarty_tpl->tpl_vars['exif']->key;?>
"><?php echo $_smarty_tpl->tpl_vars['exif']->value;?>
</span>
													</li>
												<?php }?>
											<?php } ?>
										</ul>
									</div>
								<?php }?>
								
								
								<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['share']){?>
								<div class="dataGroupContainer" id="shareMedia">
									<div class="divTable" style="width: 100%">
										<div class="divTableRow">
												<div class="divTableCell" style="vertical-align: top"></div>
												<div class="divTableCell" style="width: 100%;">
													<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['bbcode'];?>
</h2>
													<p class="purchaseListDescription"><textarea name="bbcode" style="min-width: 609px" class="form-control">[url=<?php echo $_smarty_tpl->tpl_vars['fullURL']->value;?>
][img]<?php echo mediaImage(array('mediaID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedID'],'type'=>'sample','folderID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedFID'],'seo'=>$_smarty_tpl->tpl_vars['media']->value['seoName']),$_smarty_tpl);?>
[/img][/url]</textarea></p>
												</div>
										</div>
										<div class="divTableRow">
												<div class="divTableCell" style="vertical-align: top"></div>
												<div class="divTableCell" style="width: 100%;padding-top:10px">
													<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['html'];?>
</h2>
													<p class="purchaseListDescription"><textarea name="html" style="min-width: 609px" class="form-control"><a href="<?php echo $_smarty_tpl->tpl_vars['fullURL']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['media']->value['title'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['media']->value['title'];?>
"><img src="<?php echo mediaImage(array('mediaID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedID'],'type'=>'sample','folderID'=>$_smarty_tpl->tpl_vars['media']->value['encryptedFID'],'seo'=>$_smarty_tpl->tpl_vars['media']->value['seoName']),$_smarty_tpl);?>
" title="<?php echo $_smarty_tpl->tpl_vars['media']->value['title'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['media']->value['title'];?>
" border="0"></a></textarea></p>
												</div>
										</div>
										<div class="divTableRow">
												<div class="divTableCell" style="vertical-align: top"></div>
												<div class="divTableCell" style="width: 100%;padding-top:10px">
													<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['link'];?>
</h2>
													<p class="purchaseListDescription"><input type="textbox" name="linkto" value="<?php echo $_smarty_tpl->tpl_vars['fullURL']->value;?>
" style="min-width: 609px" class="form-control"></p>
												</div>
							
										</div>
									</div>
								</div>
								<?php }?>
							</div>
						<?php }?>
			
					</div>

												
					<center>	<h3><?php echo $_smarty_tpl->tpl_vars['lang']->value['viewGalleries'];?>
</h3>		</center>				
						<div id="galleryListContainer">
							<?php  $_smarty_tpl->tpl_vars['subGallery'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['subGallery']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['subGalleriesData']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['subGallery']->key => $_smarty_tpl->tpl_vars['subGallery']->value){
$_smarty_tpl->tpl_vars['subGallery']->_loop = true;
?>
								<div class="galleryContainer" style="width: <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['gallery_thumb_size'];?>
px">
									
										<a href="<?php echo $_smarty_tpl->tpl_vars['galleriesData']->value[$_smarty_tpl->tpl_vars['subGallery']->value]['linkto'];?>
"></p>
										<p class="galleryDetails"><?php if ($_smarty_tpl->tpl_vars['galleriesData']->value[$_smarty_tpl->tpl_vars['subGallery']->value]['password']){?><img src="<?php echo $_smarty_tpl->tpl_vars['imgPath']->value;?>
/lock.png" class="lock"><?php }?><a href="<?php echo $_smarty_tpl->tpl_vars['galleriesData']->value[$_smarty_tpl->tpl_vars['subGallery']->value]['linkto'];?>
"><?php echo $_smarty_tpl->tpl_vars['galleriesData']->value[$_smarty_tpl->tpl_vars['subGallery']->value]['name'];?>
</a></p>
									</div>
									<!--gi: <?php echo $_smarty_tpl->tpl_vars['galleriesData']->value[$_smarty_tpl->tpl_vars['subGallery']->value]['galleryIcon']['imgSrc'];?>
-->
								
							<?php } ?>
						</div>	
									</div>
				
			</div>
				
			
		</div>
					
		
		<?php if ($_smarty_tpl->tpl_vars['debugMode']->value){?>
			<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['media']->value,'title'=>'media'),$_smarty_tpl);?>

		<?php }?>
		
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>