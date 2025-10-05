<?php /* Smarty version Smarty-3.1.8, created on 2025-09-13 20:55:13
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:182390422168b36e078fd1a5-15143952%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '507b1bab411c0045655c567a156079e98c3ffc9e' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/index.tpl',
      1 => 1757795997,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '182390422168b36e078fd1a5-15143952',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b36e079b2892_81205123',
  'variables' => 
  array (
    'config' => 0,
    'baseURL' => 0,
    'featuredMedia' => 0,
    'media' => 0,
    'lang' => 0,
    'randomMediaRows' => 0,
    'randomMedia' => 0,
    'newestMediaRows' => 0,
    'newestMedia' => 0,
    'popularMediaRows' => 0,
    'popularMedia' => 0,
    'subGalleriesData' => 0,
    'subGallery' => 0,
    'galleriesData' => 0,
    'imgPath' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b36e079b2892_81205123')) {function content_68b36e079b2892_81205123($_smarty_tpl) {?><!-- 
Based on this tutorial
http://www.sitepoint.com/understanding-twitter-bootstrap-3/
-->
<!DOCTYPE HTML>
<html>
	<head>
		<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		
		<script>
			$(function()
			{
				$('#myCarousel').carousel({
					interval: <?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['hpf_inverval'];?>

				});
			});
		</script>
		<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['baseURL']->value;?>
/assets/javascript/gallery.js"></script>
	</head>
	<body>
		<?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
		
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		
		<?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['hpfeaturedmedia']&&$_smarty_tpl->tpl_vars['featuredMedia']->value){?>
		<!-- Carousel
		================================================== -->
		<div class="container"><!-- Container is centered in page -->
		<div id="myCarousel" class="carousel slide">
			<!-- Indicators -->
			<ol class="carousel-indicators">
				<?php  $_smarty_tpl->tpl_vars['media'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['media']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['featuredMedia']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['media']->iteration=0;
 $_smarty_tpl->tpl_vars['media']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['media']->key => $_smarty_tpl->tpl_vars['media']->value){
$_smarty_tpl->tpl_vars['media']->_loop = true;
 $_smarty_tpl->tpl_vars['media']->iteration++;
 $_smarty_tpl->tpl_vars['media']->index++;
 $_smarty_tpl->tpl_vars['media']->first = $_smarty_tpl->tpl_vars['media']->index === 0;
?>
					<li data-target="#myCarousel" data-slide-to="<?php echo $_smarty_tpl->tpl_vars['media']->iteration-1;?>
" class="<?php if ($_smarty_tpl->tpl_vars['media']->first){?>active<?php }?>"></li>
				<?php } ?>
			</ol>
			<div class="carousel-inner">
				<?php  $_smarty_tpl->tpl_vars['media'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['media']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['featuredMedia']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['media']->iteration=0;
 $_smarty_tpl->tpl_vars['media']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['media']->key => $_smarty_tpl->tpl_vars['media']->value){
$_smarty_tpl->tpl_vars['media']->_loop = true;
 $_smarty_tpl->tpl_vars['media']->iteration++;
 $_smarty_tpl->tpl_vars['media']->index++;
 $_smarty_tpl->tpl_vars['media']->first = $_smarty_tpl->tpl_vars['media']->index === 0;
?>
				<div class="item <?php if ($_smarty_tpl->tpl_vars['media']->first){?>active<?php }?>">
					<img src="image.php?mediaID=<?php echo $_smarty_tpl->tpl_vars['media']->value['encryptedID'];?>
=&type=featured&folderID=<?php echo $_smarty_tpl->tpl_vars['media']->value['encryptedFID'];?>
&size=<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['hpf_width'];?>
&crop=<?php echo $_smarty_tpl->tpl_vars['config']->value['settings']['hpf_crop_to'];?>
" encMediaID="<?php echo $_smarty_tpl->tpl_vars['media']->value['encryptedID'];?>
" href="<?php echo $_smarty_tpl->tpl_vars['media']->value['linkto'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['media']->value['title']['value'];?>
">
					<div class="container">
						<div class="carousel-caption">
							<h1><?php echo $_smarty_tpl->tpl_vars['media']->value['title']['value'];?>
</h1>
							<p class="hidden-xs"><?php echo $_smarty_tpl->tpl_vars['media']->value['description']['value'];?>
</p>
							<p><a class="btn btn-large btn-primary" href="<?php echo $_smarty_tpl->tpl_vars['media']->value['linkto'];?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['details'];?>
</a></p>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
			<a class="left carousel-control" href="#myCarousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
			<a class="right carousel-control" href="#myCarousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
		</div>
		</div><!-- /.carousel -->
		<?php }?>
	

		<?php echo $_smarty_tpl->getSubTemplate ('header2.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	

						<div>
							<?php echo content(array('id'=>'homeWelcome'),$_smarty_tpl);?>

						</div>
		

		<div class="container">

			<div class="row">
					
					
									
					
					<?php if ($_smarty_tpl->tpl_vars['randomMediaRows']->value){?>
					<div class="clearfix">
						<hr>
					<center>	<h3></h3>					
						<div>
							<?php  $_smarty_tpl->tpl_vars['media'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['media']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['randomMedia']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['media']->iteration=0;
 $_smarty_tpl->tpl_vars['media']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['media']->key => $_smarty_tpl->tpl_vars['media']->value){
$_smarty_tpl->tpl_vars['media']->_loop = true;
 $_smarty_tpl->tpl_vars['media']->iteration++;
 $_smarty_tpl->tpl_vars['media']->index++;
 $_smarty_tpl->tpl_vars['media']->first = $_smarty_tpl->tpl_vars['media']->index === 0;
?>
								<?php echo $_smarty_tpl->getSubTemplate ('media.container.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

							<?php } ?>
						</div>
					</div>
					<?php }?>
					
					
					
					
					
					
					<?php if ($_smarty_tpl->tpl_vars['newestMediaRows']->value){?>				
					<div class="clearfix">
						<hr>
				<center>		<h3><?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['newestpage']){?><a href="<?php echo linkto(array('page'=>'gallery.php?mode=newest-media&page=1'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['newestMedia'];?>
</a><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['lang']->value['newestMedia'];?>
<?php }?><?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['rss_newest']){?> <a href="<?php echo linkto(array('page'=>'rss.php?mode=newestMedia'),$_smarty_tpl);?>
" class="btn btn-xxs btn-warning"><?php echo $_smarty_tpl->tpl_vars['lang']->value['rss'];?>
</a><?php }?></h3>
						<div>
							<?php  $_smarty_tpl->tpl_vars['media'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['media']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['newestMedia']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['media']->iteration=0;
 $_smarty_tpl->tpl_vars['media']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['media']->key => $_smarty_tpl->tpl_vars['media']->value){
$_smarty_tpl->tpl_vars['media']->_loop = true;
 $_smarty_tpl->tpl_vars['media']->iteration++;
 $_smarty_tpl->tpl_vars['media']->index++;
 $_smarty_tpl->tpl_vars['media']->first = $_smarty_tpl->tpl_vars['media']->index === 0;
?>
								<?php echo $_smarty_tpl->getSubTemplate ('media.container.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

							<?php } ?>
						</div>
					</div>
					<?php }?>
					
					
					
					<?php if ($_smarty_tpl->tpl_vars['popularMediaRows']->value){?>
					<div class="clearfix">
						<hr>
			<center>			<h3><?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['popularpage']){?><a href="<?php echo linkto(array('page'=>'gallery.php?mode=popular-media&page=1'),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['lang']->value['popularMedia'];?>
</a><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['lang']->value['popularMedia'];?>
<?php }?><?php if ($_smarty_tpl->tpl_vars['config']->value['settings']['rss_popular']){?> <a href="<?php echo linkto(array('page'=>'rss.php?mode=popularMedia'),$_smarty_tpl);?>
" class="btn btn-xxs btn-warning"><?php echo $_smarty_tpl->tpl_vars['lang']->value['rss'];?>
</a><?php }?></h3>
						<div>
							<?php  $_smarty_tpl->tpl_vars['media'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['media']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['popularMedia']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['media']->iteration=0;
 $_smarty_tpl->tpl_vars['media']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['media']->key => $_smarty_tpl->tpl_vars['media']->value){
$_smarty_tpl->tpl_vars['media']->_loop = true;
 $_smarty_tpl->tpl_vars['media']->iteration++;
 $_smarty_tpl->tpl_vars['media']->index++;
 $_smarty_tpl->tpl_vars['media']->first = $_smarty_tpl->tpl_vars['media']->index === 0;
?>
								<?php echo $_smarty_tpl->getSubTemplate ('media.container.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

							<?php } ?>
						</div>
					</div>
					<?php }?>
					
					

					
						<hr>
					<center>	<h3><?php echo $_smarty_tpl->tpl_vars['lang']->value['viewGalleries'];?>
</h3>	</center>					
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
					

		
			<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	</body>
</html>
<?php }} ?>