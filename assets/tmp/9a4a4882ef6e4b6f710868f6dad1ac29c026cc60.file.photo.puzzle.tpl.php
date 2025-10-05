<?php /* Smarty version Smarty-3.1.8, created on 2025-09-14 00:38:44
         compiled from "/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/photo.puzzle.tpl" */ ?>
<?php /*%%SmartyHeaderCode:40783499368b36e0eafa916-07674279%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9a4a4882ef6e4b6f710868f6dad1ac29c026cc60' => 
    array (
      0 => '/var/www/vhosts/fotoartpuzzle.it/httpdocs/assets/themes/modern/photo.puzzle.tpl',
      1 => 1757796006,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '40783499368b36e0eafa916-07674279',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_68b36e0ebc8ca3_12176076',
  'variables' => 
  array (
    'noAccess' => 0,
    'lang' => 0,
    'file' => 0,
    'aPuzzles' => 0,
    'aPuzzle' => 0,
    'debugMode' => 0,
    'media' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68b36e0ebc8ca3_12176076')) {function content_68b36e0ebc8ca3_12176076($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
	<?php echo $_smarty_tpl->getSubTemplate ('head.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<link href="assets/css/jquery.Jcrop.min.css" type="text/css" rel="stylesheet">
</head>
<body>
	<?php echo $_smarty_tpl->getSubTemplate ('overlays.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="container">
		<?php echo $_smarty_tpl->getSubTemplate ('header.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

				
		<!--<div class="container_24">-->
		<div class="container">
		<?php if ($_smarty_tpl->tpl_vars['noAccess']->value){?>
				<div class="row">
					<div class="col-md-12">
						<p class="notice"><?php echo $_smarty_tpl->tpl_vars['lang']->value['fotopuzzleErr'];?>
</p>
					</div>
				</div>
		<?php }else{ ?>				
			<!-- FP -->
				
			<div class="row">
				<div id="left-box" class="col-md-8">
					<div id="menu-left" class="ppmenu">
						<?php if ($_smarty_tpl->tpl_vars['file']->value){?>
							<div id="labels" style="cursor: pointer;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_SCEGLI'];?>

								<span id="landscape" class="label label-info"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_TAGLIA_O'];?>
</span>
								<span id="portrait" "label label-default" style="margin-right: 15px;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_TAGLIA_V'];?>
</span>
								<span id="quality"></span></center>
							</div>

							<div id="img-container">
								<img id="img" class="img-responsive"; " src="<?php echo $_smarty_tpl->tpl_vars['file']->value;?>
">
							</div>
						<?php }else{ ?>
							<div id="labels" style="display: none; cursor: pointer;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_SCEGLI'];?>

								<span id="landscape" class="label label-info"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_TAGLIA_O'];?>
</span>
								<span id="portrait" "label label-default" style="margin-right: 15px;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_TAGLIA_V'];?>
</span>
								<span id="quality"></span></center>
							</div>
							
							<div id="steps" style="font-size: 15px;">
								<dl class="dl-horizontal">
									<?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_4PUNTI'];?>

								</dl>
							</div>
							
							<div id="progress-bar-container" style="display: none;">
							<h4><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_INVIO_IMM'];?>
</h4>
								<div class="progress" >
									<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style=""></div>
								</div>
							</div>
							<div id="img-container" style="display: none;"></div>
						<?php }?>
												
						<div id="img-box-container" style="text-align: center; display: none;"></div>
						
						<div id="banner-container" style="margin-top: 10px; display: none;"></div>
						<?php if ($_smarty_tpl->tpl_vars['file']->value){?>
							<ul id="navButtons" class="pager foot">
								<li id="navNext" class="next" style="display: none;"><a href="#"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_AVANTI'];?>
</a></li>
							</ul>
						<?php }else{ ?>
							<ul id="navButtons" class="pager foot" style="display: none;">
								<li id="navPrev" class="previous"><a href="#"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_CAMBIA_FOTO'];?>
</a></li>
								<li id="navNext" class="next" style="display: none;"><a href="#"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_AVANTI'];?>
</a></li>
								<!--<li id="navNextBox" class="next" style="display: none;"><a href="#"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_RIEPILOGO'];?>
 <span class="glyphicon glyphicon-shopping-cart"></span></a></li>-->
							</ul>
							<!--
							<ul id="navButtonsBox" class="pager foot">
								<li id="navPrevBox" class="previous"><a href="#"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_CAMBIA_FORM'];?>
</a></li>
								<li id="navNextBox" class="next" style="display: none;"><a href="#"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_RIEPILOGO'];?>
 <span class="glyphicon glyphicon-shopping-cart"></span></a></li>
							</ul>
							-->
						<?php }?>
						
							<ul id="navButtonsBox" class="pager foot" style="display: none;">
								<li id="navPrevBox" class="previous"><a href="#"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_CAMBIA_FORM'];?>
</a></li>
								<li id="navNextBox" class="next" style="display: none;"><a href="#"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_RIEPILOGO'];?>
 <span class="glyphicon glyphicon-shopping-cart"></span></a></li>
							</ul>
					</div> <!-- /menu-left -->
				</div> <!-- /col-md -->
		
				<div id="right-box" class="col-md-4">
					<div id="menu-right" class="ppmenu">
						<?php if ($_smarty_tpl->tpl_vars['file']->value){?>
							<div id="puzzlesList">	
								<table class="table table-hover">
									<thead><tr><th><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_PEZZI'];?>
</th><th><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_FORMATO'];?>
</th><th><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_PREZZO'];?>
</th></tr></thead>
									<tbody>
									<?php  $_smarty_tpl->tpl_vars['aPuzzle'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aPuzzle']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aPuzzles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aPuzzle']->key => $_smarty_tpl->tpl_vars['aPuzzle']->value){
$_smarty_tpl->tpl_vars['aPuzzle']->_loop = true;
?>
										<?php if ($_smarty_tpl->tpl_vars['aPuzzle']->value['shape']==false){?> <tr style="cursor: pointer" id="<?php echo $_smarty_tpl->tpl_vars['aPuzzle']->value['id'];?>
" data="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['aPuzzle']->value['initSelectArea'], ENT_QUOTES, 'UTF-8', true);?>
" shape="null"><td><?php echo $_smarty_tpl->tpl_vars['aPuzzle']->value['pezzi'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['aPuzzle']->value['dimensioni'];?>
</td><td><strong>€<?php echo $_smarty_tpl->tpl_vars['aPuzzle']->value['prezzo'];?>
</strong></td> 
										<?php }else{ ?> <tr style="cursor: pointer" id="<?php echo $_smarty_tpl->tpl_vars['aPuzzle']->value['id'];?>
" data="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['aPuzzle']->value['initSelectArea'], ENT_QUOTES, 'UTF-8', true);?>
" shape="$aPuzzle.shape|lower"><td><?php echo $_smarty_tpl->tpl_vars['aPuzzle']->value['pezzi'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['aPuzzle']->value['dimensioni'];?>
 - <?php echo $_smarty_tpl->tpl_vars['aPuzzle']->value['shape'];?>
</td><td><strong>€<?php echo $_smarty_tpl->tpl_vars['aPuzzle']->value['prezzo'];?>
</strong></td>
										<?php }?>
										<!--<?php echo var_dump($_smarty_tpl->tpl_vars['aPuzzle']->value['initSelectArea']);?>
-->
									<?php } ?>
									</tbody>
									</table>
							</div>
							<div id="boxsList" style="display: none;"></div>
						<?php }else{ ?>
							<div id="upload">
								<h2><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_CARICA'];?>
</h2>
								<h6><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_DESC_CARICA'];?>
<strong><span id="maxFileSize"></span><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_MEGABYTE'];?>
</strong></h6>

								<div id="drop-area-div" style="text-align: center;">
									<button type="button" class="btn btn-default" id="buttonFile"><span class="glyphicon glyphicon-upload"></span><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_SELEZIONA'];?>
</button>
									<input type="file" name="file" id="inputFile" style="display: none;"> 
								</div>
							</div>
							
							<div id="uploaded" style="display: none; margin-top: 20px; text-align: center; background-color: #eee; padding: 15px;"></div>
							<div id="puzzlesList" style="display: none;"></div>
							<div id="boxsList" style="display: none;"></div>
							<div id="boxProgressContainer" style="display: none; position: absolute; bottom: 15px; left: 50%">
								<div id="boxProgressBanner" style="position: relative; left: -50%; width: 500px;"></div>
							</div>
							
						<?php }?>
						
					</div><!-- /menu-right -->
				</div><!-- /col-md -->
					
			</div> <!-- /row -->
			<!-- /FP -->
		<?php }?> <!-- fine esle -->
		</div><!-- /container-->
		
		<!-- modal opzione scatola o preassemblato -->
		<div class="modal fade" id="modalOpzione">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_SCELTA_OPZIONE'];?>
</h4>
					</div>
					<div class="modal-body"></div>
		    		<div class="modal-footer" style="text-align: center;">
		    			<span class="text-info" id="modalOpzioneFootNote"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_SCELTA_OPZIONE_FOOT'];?>
</span>
		    			<span class="text-warning" id="modalOpzioneFootWaitMsg" style="display: none;"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_SCELTA_OPZIONE_FOOT_WAIT'];?>
</span>
					</div>
		    	</div><!-- /.modal-content -->
		  	</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
		<div class="modal fade" id="orderConf">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_RIEPILOGO_ORDINE'];?>
</h4>
						<span id="orderConfBoxedHeader">
							<br/>
							<?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_CONTROLLO'];?>

							<br/>
							<?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_VERIFICA'];?>

							
						</span>
					</div>
					<div class="modal-body"></div>
			    	<div style="padding: 15px; border-top: 1px solid #e5e5e5;">
		    			<input id="consenso" type="checkbox">  
		      				<small><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_CONSENSO'];?>
</small><p><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_CONSENSO_CONFERMA'];?>
</p>
					</div>	        
		    		<div class="modal-footer">
		    			<span id="modal-warning" style="display: none;"><p class="text-danger"><?php echo $_smarty_tpl->tpl_vars['lang']->value['TEXT_PP_ACCETTA_COND'];?>
</p></span>
		    			<div id="modalForm"></div>
					</div>
		    	</div><!-- /.modal-content -->
		  	</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		
		
		
		<?php if ($_smarty_tpl->tpl_vars['debugMode']->value){?>
			<?php echo debugOutput(array('value'=>$_smarty_tpl->tpl_vars['media']->value,'title'=>'media'),$_smarty_tpl);?>

		<?php }?>
		
		<?php echo $_smarty_tpl->getSubTemplate ('footer.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

    </div>
</body>
</html><?php }} ?>