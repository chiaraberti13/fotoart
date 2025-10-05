<?php
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
					
		$page = NULL;
		//$lnav = "library";
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/config.php');					#FP
		require_once('../assets/includes/tweak.php');									# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php")){			
			require_once('../assets/includes/db.config.php');					# INCLUDE DATABASE CONFIG FILE
		} else { 											
			@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
		}
		require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
		require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
		error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
		require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
		require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
		include_lang();													# INCLUDE THE LANGUAGE FILE	
		require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE

		require_once('../assets/includes/photo.puzzle.db.php');  		#FP
		require_once('mgr.boxes.action.php');  		#FP

		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : Scatole"; ?></title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
	<!-- LOAD THE STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.css" />

	<!--[if lt IE 7.]><script defer type="text/javascript" src="../assets/javascript/pngfix.js"></script><![endif]-->
    <!-- PHP TO JAVASCRIPT VARS -->
    <?php //include('mgr.javascript.vars.php'); ?>
	<!-- LOAD PUBLIC AND MANAGER SHARED JAVASCRIPT -->	
	<!--<script type="text/javascript" src="../assets/javascript/shared.min.js"></script>-->
	<!-- LOAD PROTOTYPE LIBRARY -->	
	<!--<script type="text/javascript" src="../assets/javascript/prototype/prototype.js"></script>-->
	<!-- LOAD jQUERY -->
	<script type="text/javascript" src="../assets/javascript/jquery/jquery.min.js"></script>
	<!--<script>var $j = jQuery.noConflict();</script>-->
	<!-- LOAD SCRIPTACULOUS LIBRARY -->   
    <!--<script type="text/javascript" src="../assets/javascript/scriptaculous/scriptaculous.js"></script>-->
	<!-- GENERIC MGR JAVASCRIPT -->	
	<!--<script type="text/javascript" src="./mgr.min.js"></script>-->	
	<!-- TIME OUT AFTER 15 MINUTES -->
	<!--<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />-->
	
</head>
<body>

	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<!--<?php include("mgr.message.window.php"); ?>-->
	<div id="container">
		<?php include('mgr.header.php'); ?>
		<?php include('mgr.support.bar.php'); ?>
		<?php include('mgr.shortcuts.cont.php'); ?>
					
        <!-- START CONTENT CONTAINER -->
        <div id="content_container" style="background-color: #EEE">

            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <p><strong><!--<?php echo $mgrlang['nav_library']; ?></strong><br /><span><?php echo $mgrlang['nav_library_d']; ?>--></span></p>
            </div>
            <!-- START CONTENT -->
            <div id="content" style="padding: 20px 10px 10px 10px;">
				
	        	<?php
	        	if($errmsg) {
	        		echo '<p class="text-danger">'.$errmsg.'</p>';
	        	}
				
				if (!$formTable) { 

	        		$qResult = tep_db_query("SELECT * FROM fotoartphfpuzzle.photo_puzzle_products_options_values WHERE deleted = 0 AND products_options_group_name = '".PUZZLES_BOX_OPTION_NAME."'");
					if($qResult->num_rows >= 1) {
							
						if (!$formTable) echo "<table class='table table-striped'>";
						else echo "<table class='table table-striped' style='display: none;'>";
						echo "<thead><tr><td>ID</td><td>Gruppo</td><td>Azione</td></tr></thead>";
						while ( $row = tep_db_fetch_array($qResult) ) {
							
							$id = $row['products_options_values_id'];
							$aJson = json_decode($row['products_options_values_name'], TRUE);
							$totBox = (empty($aJson['scatole'])) ? 0 : count($aJson['scatole']);
							$foto = ($aJson['foto'] == 0) ? 'senza foto' : 'con foto';
							echo "<tr>";
							echo "<td>".$id."</td><td>".$aJson['desc']." - ".$foto." - numero scatole: ".$totBox."</td>";
							echo '<td>';
							
								echo '<form method="POST" action="" style="display: inline-block !important;">';
								echo '<input type="hidden" name="function" value="box_add_menu" >';
								echo '<input type="hidden" name="group_id" value="'.$id.'">';
								echo '<button type="submit" class="btn btn-default" style="display: inline-block !important; margin-right: 15px;">Aggiungi Scatola</button>';
								echo '</form>';
								
								
								if($totBox > 0) echo '<button class="btn btn-default" data-toggle="modal" data-target="#modalScatole" data-group-id="'.$id.'">Modifica / Cancella Scatola</button>';
								//echo '<a href="'.$PHP_SELF.'?function=box_add_menu&group_id='.$id.'" class="btn btn-default btn-sm" role="button" style="margin-left: 5px; margin-right: 5px;">Aggiungi Scatola</a>';
								//echo '<input class="btn btn-default" type="button" value="Aggiungi Scatola">';
								//echo '<input class="btn btn-default" type="button" value="Modifica o Cancella Scatola">';
								//echo '<input class="btn btn-default" type="button" value="Modifica Scatola">';
								//echo '<a href="" class="btn btn-default btn-sm" role="button" style="margin-left: 5px; margin-right: 5px;">Modifica o Cancella Scatola</a>';
								//echo '<input class="btn btn-default" type="button" value="Cancella Gruppo">';
								echo '<form method="POST" action="" style="display: inline-block !important; margin-left: 15px; margin-right: 15px;">';
								echo '<input type="hidden" name="function" value="group_del" >';
								echo '<input type="hidden" name="group_id" value="'.$id.'">';
								echo '<button type="submit" class="btn btn-default" onclick="return confirm(\'sicuro ?\')">Cancella Gruppo</button>';
								echo '</form>';
								//echo '<a href="'.$PHP_SELF.'?function=group_del&group_id='.$id.'" class="btn btn-default btn-sm" role="button" style="margin-left: 5px; margin-right: 5px;" onclick="return confirm(\'sicuro ?\')">Cancella Gruppo</a>';
							echo '</td>';
							echo "</tr>";
							
						}
						echo "</table>";
					}
				
				} else echo $formTable;
				//
				?>
                <!--<?php include("mgr.subnav.body.php"); ?>-->
                <?php
	                if (!$formTable) echo '<div>';
					else echo '<div style="display: none;">';
				?>
	                <h4>Aggiungi un nuovo gruppo di scatole</h4>
					<form class="form-inline" method="post" action="#">
						<input type="hidden" name="function" value="group_add">
						<div class="form-group">
							<label for="groupName">Nome Gruppo</label>
							<input type="text" class="form-control" name="groupName" placeholder="nome categoria">
						</div>
						<div class="checkbox">
							<label>
	      						<input type="checkbox" name="foto"> senza foto
							</label>
						  </div>
						<button type="submit" class="btn btn-default">aggiungi</button>
					</form>
				
                <div style="clear: both; height: 5px;"></div>
                <HR style="border-color: grey; !important"/>
                <h4 style="display: inline-block !important; margin-right: 15px;">Genera tutte le scatole inserite per tutti i prodotti PPZL inseriti</h4>
                <button class="btn btn-default" data-toggle="modal" data-target="#modalGenerazionePNG">Genera PNG</button>
                </div>
	            <div id="modalScatole" class="modal fade" tabindex="-1" role="dialog">
	  				<div class="modal-dialog">
	    				<div class="modal-content">
	      					<div class="modal-header">
	        					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        					<h4 class="modal-title">Seleziona una scatola</h4>
	      					</div>
	      					<div class="modal-body">
	        					<!--<p>One fine body&hellip;</p>-->
	      					</div>
	      					<div class="modal-footer">
	        					<button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
	        					<!--<button type="button" class="btn btn-primary">Save changes</button>-->
				      		</div>
	    				</div><!-- /.modal-content -->
	  				</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
				
				<div id="modalGenerazionePNG" class="modal fade" tabindex="-1" role="dialog">
	  				<div class="modal-dialog">
	    				<div class="modal-content">
	      					<div class="modal-header">
	        					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        					<h4 class="modal-title">Attendi il completamento del processo (non eseguire altre azioni!)</h4>
	      					</div>
	      					<div class="modal-body">
	      						<!--<span>Progresso: </span></span=><p id="pngProgresso">0</p>
	      						<span>Totale: </span><p id="pngTotale">0</p>-->
	      						<p id="progresso"></p>
	        					<!--<p>One fine body&hellip;</p>-->
	      					</div>
	      					<div class="modal-footer">
	        					<!--<button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>-->
	        					<!--<button type="button" class="btn btn-primary">Save changes</button>-->
				      		</div>
	    				</div><!-- /.modal-content -->
	  				</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->		
										
            </div>
            <!-- END CONTENT -->
        </div>
        <div class="footer_spacer"></div>
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>
	</div>
	<!-- Latest compiled and minified JavaScript -->
	<script type="text/javascript" src="../assets/javascript/bootstrap.min.js"></script>
	<script type="text/javascript" src="../assets/javascript/mgr.photo.puzzle.boxes.js"></script>
</body>
</html>
<?php 

mysqli_close($db);

function printEditTable($array, $groupId, $boxId, $edit = false){

	$table = '<form method="post" action="">';
	if(!$edit) $table .= '<input type="hidden" name="function" value="box_add">';
	else $table .= '<input type="hidden" name="function" value="box_edit">';
	
	$table .= '<input type="hidden" name="optionId" value="'.$groupId.'">';
	$table .= '<input type="hidden" name="box_id" value="'.$boxId.'">';
	 
	$table .= '<table class="table"><thead></thead><tbody>';
	
	$table .= '<tr><td colspan="8" class="">Layout</td></tr>';
	$table .= '<tr><td class="smallText"><span>larghezza:</span></td><td><input type="text" name="width" value="'.( !$edit ? $array['layout']['width'] : $array['width'] ).'"></td><td class="smallText"><span>altezza:</span></td><td><input type="text" name="height" value="'.( !$edit ? $array['layout']['height'] : $array['height']) .'"></td><td class="smallText"><span>mm bordo:</span></td><td colspan="2"><input type="text" name="boxBorder" value="'.( !$edit ? $array['layout']['boxBorder'] : $array['boxBorder']) .'"></td></tr>';
	$table .= '<tr><td class="smallText"><span>modello pdf:</span></td><td><input type="text" name="modelFile" value="'.( !$edit ? $array['layout']['modelFile'] : $array['modelFile'] ).'"></td><td class="smallText"><span>font file:</span></td><td colspan="4"><input type="text" name="font" value="'. ( !$edit ? $array['layout']['font'] : $array['font'] ).'"></td></tr>';
	//$table .= '<tr><td colspan="8">'.tep_black_line().'</td></tr>';

	$table .= '<tr><td colspan="8">Testo cliente</td></tr>';
	$table .= '<tr><td class="smallText"><span>box xy:</span></td><td><input type="text" name="textxy" value="'.( !$edit ? $array['boxes']['textxy'] : $array['textxy'] ).'"></td><td class="smallText"><span>box size:</span></td><td colspan="6"><input type="text" name="textsize" value="'.( !$edit ? $array['boxes']['textsize'] : $array['textsize'] ).'"></td></tr>';
	$table .= '<tr><td class="smallText"><span>font size:</span></td><td><input type="text" name="fontSize" value="'.( !$edit ? $array['boxes']['fontSize'] : $array['fontSize'] ).'"></td><td class="smallText"><span>font color:</span></td><td><input type="text" name="textColor" value="'.( !$edit ? $array['boxes']['textColor'] : $array['textColor'] ).'"></td><td class="smallText"><span>font bord. col.:</span></td><td colspan="3"><input type="text" name="textBorderColor" value="'.( !$edit ? $array['boxes']['textBorderColor'] : $array['textBorderColor'] ).'"></td></tr>';
	
	$table .= '<tr><td colspan="8">Pezzi sx</td></tr>';
	$table .= '<tr><td class="smallText"><span>box xy:</span></td><td><input type="text" name="pezziSxxy" value="'.( !$edit ? $array['boxes']['pezziSxxy'] : $array['pezziSxxy'] ).'"></td><td class="smallText"><span>box size:</span></td><td colspan="6"><input type="text" name="pezziSxsize" value="'.( !$edit ? $array['boxes']['pezziSxsize'] : $array['pezziSxsize'] ).'"></td></tr>';
	$table .= '<tr><td class="smallText"><span>font size:</span></td><td><input type="text" name="pezziSxFontSize" value="'.( !$edit ? $array['boxes']['pezziSxFontSize'] : $array['pezziSxFontSize'] ).'"></td><td class="smallText"><span>font color:</span></td><td><input type="text" name="pezziSxFontColor" value="'.( !$edit ? $array['boxes']['pezziSxFontColor'] : $array['pezziSxFontColor'] ).'"></td><td class="smallText"><span>font bord. col.:</span></td><td colspan="3"><input type="text" name="pezziSxFontBorderColor" value="'.( !$edit ? $array['boxes']['pezziSxFontBorderColor'] : $array['pezziSxFontBorderColor'] ).'"></td></tr>';

	$table .= '<tr><td colspan="8">Scritta bottom</td></tr>';
	$table .= '<tr><td class="smallText"><span>box xy:</span></td><td><input type="text" name="pezziBottomxy" value="'.( !$edit ? $array['boxes']['pezziBottomxy'] : $array['pezziBottomxy'] ).'"></td><td class="smallText"><span>box size:</span></td><td colspan="6"><input type="text" name="pezziBottomsize" value="'.( !$edit ? $array['boxes']['pezziBottomsize'] : $array['pezziBottomsize'] ).'"></td></tr>';
	$table .= '<tr><td class="smallText"><span>font size:</span></td><td><input type="text" name="pezziBottomFontSize" value="'.( !$edit ? $array['boxes']['pezziBottomFontSize'] : $array['pezziBottomFontSize'] ).'"></td><td class="smallText"><span>font color:</span></td><td><input type="text" name="pezziBottomFontColor" value="'.( !$edit ? $array['boxes']['pezziBottomFontColor'] : $array['pezziBottomFontColor'] ).'"></td><td class="smallText"><span>font bord. col.:</span></td><td colspan="3"><input type="text" name="pezziBottomFontBorderColor" value="'.( !$edit ? $array['boxes']['pezziBottomFontBorderColor'] : $array['pezziBottomFontBorderColor'] ).'"></td></tr>';
	
	$table .= '<tr><td colspan="8">Dimensioni bottom</td></tr>';
	$table .= '<tr><td class="smallText"><span>box xy:</span></td><td><input type="text" name="dimBottomxy" value="'.( !$edit ? $array['boxes']['dimBottomxy'] : $array['dimBottomxy'] ).'"></td><td class="smallText"><span>box size:</span></td><td colspan="6"><input type="text" name="dimBottomsize" value="'.( !$edit ? $array['boxes']['dimBottomsize'] : $array['dimBottomsize'] ).'"></td></tr>';
	$table .= '<tr><td class="smallText"><span>font size:</span></td><td><input type="text" name="dimBottomFontSize" value="'.( !$edit ? $array['boxes']['dimBottomFontSize'] : $array['dimBottomFontSize'] ).'"></td><td class="smallText"><span>font color:</span></td><td><input type="text" name="dimBottomFontColor" value="'.( !$edit ? $array['boxes']['dimBottomFontColor'] : $array['dimBottomFontColor'] ).'"></td><td class="smallText"><span>font bord. col.:</span></td><td colspan="3"><input type="text" name="dimBottomFontBorderColor" value="'.( !$edit ? $array['boxes']['dimBottomFontBorderColor'] : $array['dimBottomFontBorderColor'] ).'"></td></tr>';
	
	$table .= '<tr><td colspan="8">Numero Ordine</td></tr>';
	$table .= '<tr><td class="smallText"><span>box xy:</span></td><td><input type="text" name="orderxy" value="'.( !$edit ? $array['boxes']['orderxy'] : $array['orderxy'] ).'"></td><td class="smallText"><span>box size:</span></td><td colspan="6"><input type="text" name="ordersize" value="'.( !$edit ? $array['boxes']['ordersize'] : $array['ordersize'] ).'"></td></tr>';
	$table .= '<tr><td class="smallText"><span>font size:</span></td><td><input type="text" name="orderFontSize" value="'.( !$edit ? $array['boxes']['orderFontSize'] : $array['orderFontSize'] ).'"></td><td class="smallText"><span>font color:</span></td><td><input type="text" name="orderFontColor" value="'.( !$edit ? $array['boxes']['orderFontColor'] : $array['orderFontColor'] ).'"></td><td class="smallText"><span>font bord. col.:</span></td><td colspan="3"><input type="text" name="orderFontBorderColor" value="'.( !$edit ? $array['boxes']['orderFontBorderColor'] : $array['orderFontBorderColor'] ).'"></td></tr>';
	
	//$table .= '<tr><td colspan="8">'.tep_black_line().'</td></tr>';
	$table .= '<tr><td colspan="8" class="">Immagini</td></tr>';
	$table .= '<tr><td class="smallText"><span>dim. img front:</span></td><td><input type="text" name="imgSize" value="'.( !$edit ? $array['immagini']['imgSize'] : $array['imgSize'] ).'"></td><td class="smallText"><span>dim. img. side&top:</span></td><td colspan="6"><input type="text" name="miniSize" value="'.( !$edit ? $array['immagini']['miniSize'] : $array['miniSize'] ).'"></td></tr>';
	$table .= '<tr><td class="smallText"><span>coor. img front:</span></td><td><input type="text" name="coordinateImg" color" value="'.( !$edit ? $array['immagini']['coordinateImg'] : $array['coordinateImg'] ).'"></td><td class="smallText"><span>coor. img top:</span></td><td><input type="text" name="coordinateImgTop" value="'.( !$edit ? $array['immagini']['coordinateImgTop'] : $array['coordinateImgTop'] ).'"></td><td class="smallText"><span>coor. img side:</span></td><td colspan="3"><input type="text" name="coordinateImgSide" value="'.( !$edit ? $array['immagini']['coordinateImgSide'] : $array['coordinateImgSide'] ).'"></td></tr>';
	$table .= '<tr><td class="smallText"><span>img border size:</span></td><td><input type="text" name="imgBorderSize" value="'.( !$edit ? $array['immagini']['imgBorderSize'] : $array['imgBorderSize'] ).'"></td><td class="smallText"><span>img border col.:</span></td><td colspan="6"><input type="text" name="imgBorderColor" value="'.( !$edit ? $array['immagini']['imgBorderColor'] : $array['imgBorderColor'] ).'"></td></tr>';
	//$table .= '<tr><td colspan="8">'.tep_black_line().'</td></tr>';
	
	$table .= '</tbody></table>';
	$table .= '<input type="submit" value="Salva">';
	//$table .= '<a href="'.$PHP_SELF.'" class="btn btn-default btn-sm" role="button" style="margin-left: 5px; margin-right: 5px;">Salva</a>';
	$table .= '<a href="'.$_SERVER["PHP_SELF"].'" class="btn btn-default btn-sm" role="button" style="margin-left: 5px; margin-right: 5px;">Annulla</a>';
	
	$table .= '</form>';

	return $table;
	//print $table;
	
} 
?>
