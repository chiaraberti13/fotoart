<?php

function manageSession(){
		
	$aImg = FALSE;
	$aFiles = array();
	$sesskey = tep_session_id();
	if(file_exists(WORK_DIR.$sesskey)) {
		$query = tep_db_query("SELECT photo_puzzle_id, img_file FROM ".TABLE_PHOTO_PUZZLE." where uploaded = TRUE and sesskey = \"".$sesskey."\"");
		while ( $row = tep_db_fetch_array($query) ) {
				$puzzleId = $row['photo_puzzle_id'];
				if(file_exists(WORK_DIR.$sesskey. '/'. $puzzleId)) {
					$img = $row['img_file'];
					//$aImg[] = DIR_WS_IMAGES.'temporary/'.$sesskey.'/'.$puzzleId.'/thumb/'.getName($img, 'thumb').'?cache='.time();
					$aImg[] = WEB_DIR.$sesskey.'/'.$puzzleId.'/thumb/'.getName($img, 'thumb').'?cache='.time();
					$aFiles[] = $puzzleId;
				}
		}
	}

	$aResult = array('limit' => getLimits(), 'restore' => $aImg, 'files' => $aFiles);
	return json_encode($aResult);
	
}



function getLimits(){
	
	return min( unitToByte(ini_get('upload_max_filesize')), unitToByte(ini_get('post_max_size')) );
	
}



function getRestore($uploadedImgId){
	
	$sesskey = tep_session_id();
	$puzzleId = db_add_puzzle();
	
	$uploadedImg = db_get_img($uploadedImgId);
	
	$srcPath = WORK_DIR.$sesskey.'/'.$uploadedImgId.'/';
	$newPath = WORK_DIR.$sesskey.'/'.$puzzleId.'/';
	
	mkdir($newPath, 0775, true);
	mkdir($newPath.'boxes', 0775, true);
	mkdir($newPath.'preview', 0775, true);
	mkdir($newPath.'thumb', 0775, true);
	mkdir($newPath.'puzzle', 0775, true);
	
	$img = $puzzleId.substr($uploadedImg, strpos($uploadedImg, '.'));
	tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET img_file = '".$img."' WHERE photo_puzzle_id = ".$puzzleId."");
	$srcImg = $uploadedImg;

	copy($srcPath.$srcImg, $newPath.$img);
	copy($srcPath.'preview/'.getName($srcImg, 'preview'), $newPath.'preview/'.getName($img, 'preview'));
	copy($srcPath.'thumb/'.getName($srcImg, 'thumb'), $newPath.'thumb/'.getName($img, 'thumb'));
	
	$im = new Imagick($newPath.$img);
	$aPixelSize = $im->getImageGeometry();
	$imageWidth = $aPixelSize['width'];
	$imageHeight = $aPixelSize['height'];

	$orientamento = 'landscape'; //default
	
	$aData = db_get_puzzles_list();
	foreach ($aData as $k => $aPuzzle) {
		$aDimensioni = array('larg' => $aPuzzle['larg'], 'alt' => $aPuzzle['alt']);
		$printQuality = getQuality($aDimensioni['larg'], $aDimensioni['alt'], $imageWidth, $imageHeight, $orientamento, false, false, strtolower($aPuzzle['shape']));
		if($printQuality == TRUE && $printable == FALSE) $printable = TRUE;
		$aPuzzles[] = array(	'id' => $aPuzzle['products_id'],
								'modello' => $aPuzzle['products_model'],
								'prezzo' => $aPuzzle['prezzo'],
								'pezzi' => $aPuzzle['pezzi'],
								'dimensioni' => $aPuzzle['dimensioni'],
								'shape' => $aPuzzle['shape'],
								'larg' => $aDimensioni['larg'],
								'alt' => $aDimensioni['alt'],
								'initSelectArea' => setCoordinates($imageWidth, $imageHeight, $aDimensioni['larg'], $aDimensioni['alt'], $orientamento, strtolower($aPuzzle['shape'])),
								'quality' => $printQuality
		);
		
	}
	
	$im->destroy();
	return json_encode(array('res' => 'ok', 'trueSize' => array($imageWidth, $imageHeight), 'file' => WEB_DIR.$sesskey.'/'.$puzzleId.'/preview/'.getName($img, 'preview').'?cache='.time(), 'orientation' => $orientamento, 'puzzles' => $aPuzzles));
		
}



function setCoordinates($w, $h, $printW, $printH, $orientamento, $shape) {
	
	$aData['landscape'] = getLandscapeSelection($w, $h, $printW, $printH, $orientamento, $shape);
	$aData['portrait'] = getPortraitSelection($w, $h, $printW, $printH, $orientamento, $shape);

	return json_encode($aData);
}



function getLandscapeSelection($w, $h, $printW, $printH, $orientamento, $shape){
	
	$pxLarg = ( ($h * $printW) / $printH ); //pixel di larghezza dell'area di selezione in proporzione all'altezza
	if($pxLarg > $w) {
		$pxLarg = $w;
		$x = 0;
	} else $x = ( ( $w - $pxLarg ) / 2 );
	
	$pxAlt = ( ($w * $printH) /  $printW );
	if($pxAlt > $h) {
		$pxAlt = $h;
		$y = 0;
	} else $y = ( ($h - $pxAlt) / 2);

	$x2 = $pxLarg + $x;
	$y2 = $y+$pxAlt;

	$ratio = ($printW/$printH);
	return array('w' => $w, 'h' => $h, 'x' => $x, 'y' => $y, 'x2' => $x2, 'y2' => $y2, 'ratio' => $ratio, 'printW' => $printW, 'printH' => $printH, 'orientamento' => $orientamento, 'shape' => $shape);

}



function getPortraitSelection($w, $h, $printW, $printH, $orientamento, $shape){
	
	list($printW, $printH) = array($printH, $printW);

	$pxLarg = ( ($h * $printW) / $printH ); //pixel di larghezza dell'area di selezione in proporzione all'altezza

	if($pxLarg > $w) { 
		$pxLarg = $w;
		$x = 0;
	}	else $x = ( ( $w - $pxLarg ) / 2 );

	$pxAlt = ( ($printH * $pxLarg) / $printW );
	if($pxAlt > $h) {
		$pxAlt = $h;
		$y = 0;
	} else $y = ( ($h - $pxAlt) / 2);

	$x2 = $pxLarg + $x;
	$y2 = $y+$pxAlt;

	//$ratio = ($printH/$printW);
	$ratio = ($printW/$printH);
	return array('w' => $w, 'h' => $h, 'x' => $x, 'y' => $y, 'x2' => $x2, 'y2' => $y2, 'ratio' => $ratio, 'printW' => $printW, 'printH' => $printH, 'orientamento' => $orientamento, 'shape' => $shape);
	
}



function getDimensioni($array){
	
	list($larg, $alt) = explode('x', $array);
	return array('larg' => trim($larg), 'alt' => trim(substr($alt, 0, -2))); 
}



function getQuality($printW, $printH, $pixelW, $pixelH, $orientamento, $coords = FALSE, $jcrop = TRUE, $shape = NULL) { //jcrop definisce le chiamate proveninenti dalla selezione via jquery oppure dal ciclo iniziale in php

	if($coords) {
		$aJson = array();
		foreach ($coords as $k => $value) {
			$aJson[$k] = $value;
		}

		$aJson['shape'] = $shape;
		tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." set crop_coords = '".json_encode($aJson)."' WHERE sesskey = '".tep_session_id()."' AND current = 1");
	}

	if ($printW >= $printH) {
		$pixelH = (($pixelW * $printH) / $printW);
		$wDpi = ($pixelW / ($printW/2.54));
		$hDpi = ($pixelH / ($printH/2.54));
	} else {
		$pixelW = (($printW * $pixelH) / $pixelH);
		$wDpi = ($pixelH / ($printW/2.54));
		$hDpi = ($pixelW / ($printH/2.54));
	}
	$avgDpi = ($wDpi+$hDpi)/2;
	if($avgDpi >= EX_QUALITY) return 4; //QUALITA ECCELLENTE
	elseif ($avgDpi >= GREAT_QUALITY) return 3; //QUALITA OTTIMA
	elseif ($avgDpi >= GOOD_QUALITY) return 2; //BUONA QUALITA
	elseif ($avgDpi >= POOR_QUALITY) return 1; //SCARSA QUALITA
	else return 0;

}



function unitToByte($val){

	$val = trim($val);
	$last = strtolower($val[strlen($val)-1]);
	switch($last) {

		case 'g':
            return (substr($val, 0, -1)*pow(1024, 3));
        case 'm':
            return (substr($val, 0, -1)*pow(1024, 2));
        case 'k':
			return (substr($val, 0, -1)*1024);
			
    }

}



function getBoxesList(){

	global $dbinfo;
	global $lang;
	$sesskey = tep_session_id();
	tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET text_type = NULL, text = NULL WHERE sesskey = \"".$sesskey."\" AND current = 1");
	//$query = tep_db_query("SELECT products_id, photo_puzzle_id, img_file, crop_coords FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1");
	$query = tep_db_query("SELECT products_id, photo_puzzle_id, img_file, crop_coords, ps4_path, ps4_original_filename FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1");
	$aQueryResult = tep_db_fetch_array($query);
	$puzzleId =  $aQueryResult['photo_puzzle_id'];
	$productId = $aQueryResult['products_id'];
	$jsonCropCoords = $aQueryResult['crop_coords'];
	$aCoords = json_decode($jsonCropCoords, TRUE);

	if($aQueryResult['ps4_original_filename'] != NULL) {
		$opath = $aQueryResult['ps4_path'];
		//$srcFile = WORK_DIR.$sesskey.'/'.$puzzleId.'/'.$aQueryResult['img_file'];
		$srcFile = BASE_PATH.'/assets/library/'.$opath.'/originals/'.$aQueryResult['ps4_original_filename'];
		$puzzleFile = WORK_DIR.$sesskey.'/'.$puzzleId.'/puzzle/'.getName($aQueryResult['img_file'], 'crop');
	} else {
		$srcFile = WORK_DIR.$sesskey.'/'.$puzzleId.'/'.$aQueryResult['img_file'];
		$puzzleFile = WORK_DIR.$sesskey.'/'.$puzzleId.'/puzzle/'.getName(basename($srcFile), 'crop');
	}
	//$boxDir = WORK_DIR.$sesskey.'/'.$puzzleId.'/boxes/';
	$boxDir = 'assets/puzzles/temporary/'.$sesskey.'/'.$puzzleId.'/boxes/';
	
	
	/*$queryPezzi = tep_db_query("SELECT (SELECT epf_value FROM extra_field_values WHERE value_id = d.extra_value_id1) as pezzi
								FROM 
								".TABLE_PRODUCTS." as b
								INNER JOIN
								".TABLE_PRODUCTS_DESCRIPTION." as d ON (d.products_id = b.products_id)
								WHERE b.products_id = (SELECT products_id FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1)
							");
	*/ 
	$queryPezzi = tep_db_query("SELECT notes
								FROM 
								".$dbinfo['pre']."products
								WHERE prod_id = (SELECT products_id FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1)
							");
	$aqueryPezziResult = tep_db_fetch_array($queryPezzi);
	$aJson = json_decode($aqueryPezziResult['notes'], TRUE);
	$pezzi = $aJson['pezzi'];

	//$pezzi = $aqueryPezziResult['pezzi'];

	if(file_exists($puzzleFile))unlink($puzzleFile);
	$aFiles = glob($boxDir."*");
	if ($aFiles) {
		foreach ($aFiles as $f) {
	    	unlink($f);
		}
	}

	$im = new Imagick();
	$im->readImage($srcFile);
	$im->profileImage( '*' , NULL );
	if (!empty($aCoords['shape'])) {

		$mask = new Imagick();
		if($aCoords['w'] >= $aCoords['h']) $mask->readImage(BASE_PATH.'/assets/images/heart_mask_negate.png');
		else $mask->readImage(BASE_PATH.'/assets/images/heart_mask_negate_portrait.png');

		$mask->scaleImage($aCoords['w'], 0);
		
		if($im->getImageFormat() != 'PNG') {
			$im->setImageFormat('PNG');
			$puzzleFile = changeExt($puzzleFile, 'png');
		}
		
	 	$im->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
        $im->setImageMatte(true);

		$im->cropImage($aCoords['w'], $aCoords['h'], $aCoords['x'], $aCoords['y']);
		$im->setImagePage($aCoords['w'], $aCoords['h'], 0, 0);

		$im->compositeImage($mask, Imagick::COMPOSITE_DSTIN, 0, 0, Imagick::CHANNEL_ALPHA);
		$im->writeImage($puzzleFile);
		$mask->destroy();
		$im->destroy();
		
	} else {
		$im->cropImage($aCoords['w'], $aCoords['h'], $aCoords['x'], $aCoords['y']);
		$im->setImagePage($aCoords['w'], $aCoords['h'], 0, 0);
		$im->writeImage($puzzleFile);
		$im->destroy();
	}

	$htmlTabs = '<form id="singleText" style="display: none;">';
	$htmlTabs .= '<div class="input-group" style="margin-bottom: 20px;"><input class="form-control" type="text" name="text" placeholder=" '. $lang['TEXT_PP_INSERISCI_TESTO'] .' ">';
	$htmlTabs .= '<div class="input-group-addon yellow write"><span class="glyphicon glyphicon-pencil" title="applica il testo" style="cursor: pointer;"></span></div>';
	$htmlTabs .= '<div class="input-group-addon limit">0/'.CHARS_LIMIT.'</div>';
	$htmlTabs .= '</div>'; //input-group
	$htmlTabs .= '</form>';
	
	$htmlTabs .= '<form id="extendedText" style="display: none;">';
	for($i=1; $i<= TXT_ROWS; $i++) {		
		if($i != TXT_ROWS) $marginb = 5;
		else $marginb = 20;  
		$htmlTabs .= '<div class="input-group" style="margin-bottom: '.$marginb.'px;">';
		$htmlTabs .= '<input class="form-control" type="text" name="text" placeholder="riga '.$i.'...">';
		$htmlTabs .= '<div class="input-group-addon limit">0/'.CHARS_LIMIT.'</div>';
		if($i == TXT_ROWS) $htmlTabs .= '<div class="input-group-addon yellow write multirows"><span class="glyphicon glyphicon-pencil" title="applica il testo" style="cursor: pointer;"></span></div>';
		$htmlTabs .= '</div>'; //input-group
	}
	$htmlTabs .= '</form>';
		
	$htmlTabs .= '<div class="tabbable tabs-left"><ul class="nav nav-tabs nav-custom" role="tablist">';
	$c = 0;

	$boxesPanel = '';
	$aBoxes = db_boxes_attr_values();

	foreach ($aBoxes as $k => $row) {
		
		$oStyle = json_decode($row['json']);
		if ($oStyle->foto == FALSE) $txtAttr = "extendedText";
		else $txtAttr = "singleText";

		//$nomeTab = $oStyle->desc;
		if($row['prezzo'] == '0,00') $nomeTab = $oStyle->desc;
		else $nomeTab = $oStyle->desc.'<BR/><small><strong>€'.$row['prezzo'].'</strong></small>'; 
		
		if($c == 0) {
			//$htmlTabs .= '<li class="active"><a href="#'.$row['id'].'" role="tab" data-toggle="tab" text-toggle="'.$txtAttr.'"  scatole="'.count($oStyle->scatole).'">'.$nomeTab.'</a></li>';
			//$boxesPanel .= '<div id="'.$row['id'].'" class="tab-pane boxes active">';
			$htmlTabs .= '<li class="active"><a href="#tab'.$row['id'].'" role="tab" data-toggle="tab" text-toggle="'.$txtAttr.'" scatole="'.count($oStyle->scatole).'">'.$nomeTab.'</a></li>';
			$boxesPanel .= '<div id="tab'.$row['id'].'" class="tab-pane boxes active" role="tabpanel">';
		}
		else {
			$htmlTabs .= '<li><a href="#tab'.$row['id'].'" role="tab" data-toggle="tab" text-toggle="'.$txtAttr.'" scatole="'.count($oStyle->scatole).'">'.$nomeTab.'</a></li>';
			//$boxesPanel .= '<div id="'.$row['id'].'" class="tab-pane boxes">';
			$boxesPanel .= '<div id="tab'.$row['id'].'" class="tab-pane boxes" role="tabpanel">';
		}
	
		foreach ($oStyle->scatole as $k => $aScatola) {
			$filename = $pezzi.'_'.str_replace(' ', '_',$oStyle->desc).($oStyle->foto ? '_foto_' : '_').$aScatola->modelFile;
			$filename = strtolower(str_replace('.pdf', '.png', $filename));
			//$src =  'images/scatole/png/'.$filename;
			$src =  'assets/puzzles/scatole/png/'.$filename;
			$elaboratedSrc =  $boxDir.strtolower(str_replace('.pdf', '.png', $aScatola->modelFile));//.'?cache='.time();
			$elaboratedSrc = ($oStyle->foto ? str_replace('.png', '_foto.png', $elaboratedSrc) : $elaboratedSrc);
			$boxesPanel .= '<img class="box" boxId="'.$row['id'].'" indice="'.$k.'" data-file="" data-elaborated="'.$elaboratedSrc.'"src='.$src.'?cache='.time().'">';
		}
		$boxesPanel .= '</div>';
		$c++;
		
	}
	$htmlTabs .= '</ul></div><div class="tab-content lista-box">';
	$htmlTabs .= '<div id="boxesWait" class="tab-pane"><img style="margin-top: 50px;" src="assets/images/preloader.gif"></div>'.$boxesPanel.'</div>';

	return $htmlTabs;
}



function getBoxes($boxid, $indice, $redraw = FALSE){
	
	global $dbinfo;
	$sesskey = tep_session_id();
	$query = tep_db_query("SELECT text_type, photo_puzzle_id, crop_coords FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1");
	$aQueryResult = tep_db_fetch_array($query);
	$textType = $aQueryResult['text_type'];
	$puzzleId =  $aQueryResult['photo_puzzle_id'];

	/*$query = tep_db_query("	SELECT (SELECT epf_value FROM extra_field_values WHERE value_id = d.extra_value_id1) as pezzi
							FROM 
							".TABLE_PRODUCTS." as b
							INNER JOIN
							".TABLE_PRODUCTS_DESCRIPTION." as d ON (d.products_id = b.products_id)
							WHERE b.products_id = (SELECT products_id FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1)
			");
	*/
	$query = tep_db_query("	SELECT notes as json
							FROM 
							".$dbinfo['pre']."products
							WHERE prod_id = (SELECT products_id FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1)
							");

	$aQueryResult = tep_db_fetch_array($query);
	$aJson = json_decode($aQueryResult['json'], TRUE);
	$pezzi = $aJson['pezzi'];

	$aBox = db_box_attr_values($boxid);
	if($aBox) {
		$oStyle = json_decode($aBox['json']);
		if($oStyle){
			$tabContent = '';
			$aStyle = (array) $oStyle->scatole[$indice];

			if ($oStyle->foto) {
				$filename = str_replace('.pdf', '_foto.png', $aStyle['modelFile']);
				$boxName = str_replace('.pdf', '_foto', $aStyle['modelFile']);
			}
			else {
				$filename = str_replace('.pdf', '.png', $aStyle['modelFile']);
				$boxName = str_replace('.pdf', '', $aStyle['modelFile']);
			}
			
			tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET box_file = '".$boxName."', box_id = ".$boxid.", box_option_value_index = ".$indice." WHERE sesskey = '".$sesskey."' AND current = 1");

			if( ($oStyle->foto && $textType == 'multi') || (!$oStyle->foto && $textType == 'single') )  tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET text_type = NULL, text = NULL WHERE sesskey = \"".$sesskey."\" AND current = 1");  					

			drawBox($boxName, $pezzi, $aStyle, $puzzleId, $oStyle->foto);

			return '<img class="box" data-file="'.$filename.'" src="images/temporary/'.$sesskey.'/'.$puzzleId.'/boxes/'.$filename.'?cache='.time().'">';

		}
	}

}



function submitForm($form, $aData){

	$sesskey = tep_session_id();
	
	if($form == 'singleText')  {
		
		if(empty($aData[0]['value'])) {
			tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET text_type = NULL, text = NULL WHERE sesskey = \"".$sesskey."\" AND current = 1");
		} else {
			tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET text_type = 'single', text = '".$aData[0]['value']."' WHERE sesskey = \"".$sesskey."\" AND current = 1");
		}

	}  else {
		$array = array();
		$empty = TRUE;
		foreach ($aData as $k => $aInput ){
			if(!empty($aInput['value'])) $empty = FALSE; 
			$array[] = stripslashes($aInput['value']);
		}
		if($empty) tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET text_type = NULL, text = NULL WHERE sesskey = \"".$sesskey."\" AND current = 1");
		else db_add_text($array); 

	}

}



function getOrderDesc($boxed, $optionId = NULL, $optionIdValue = NULL){

	global $lang;
	
	if($boxed) {
		
		$sesskey = tep_session_id();
		$arrayCurrent = getCurrent($sesskey);
	
		$file = $arrayCurrent['img_file'];
		$mediaId = $arrayCurrent['media_id'];
		$puzzleId = $arrayCurrent['photo_puzzle_id'];
		//$imgBox = DIR_WS_IMAGES.'temporary/'.$sesskey.'/'.$puzzleId.'/boxes/'.$arrayCurrent['box_file'].'.png';
		$imgBox = WEB_DIR.$sesskey.'/'.$puzzleId.'/boxes/'.$arrayCurrent['box_file'].'.png';
		$productId = $arrayCurrent['products_id'];
		$boxId = $arrayCurrent['box_id'];
		$jsonCropCoords = $arrayCurrent['crop_coords'];
		$aCoords = json_decode($jsonCropCoords, TRUE);
		
		if (!empty($aCoords['shape'])) $img = WEB_DIR.$sesskey.'/'.$puzzleId.'/boxes/'.getName(changeExt($file, 'png'), 'boximage');
		else $img = WEB_DIR.$sesskey.'/'.$puzzleId.'/boxes/'.getName($file, 'boximage');
		
		chdir(dirname(dirname(__DIR__)));
		$im = new Imagick($img);
		$aGeo = $im->getImageGeometry();
		$im->destroy();
		$img = $img.'?cache='.time();
	
		$body = '<div class="row">';
		//$body .= '<div class="col-xs-6" style="text-align: center;"><h4>Box</h4><img style="margin-top:10px;" height="300px" src="'.$imgBox.'?cache='.time().'"></div>';
		$body .= '<div class="col-md-6" style="text-align: center;"><h4>Box</h4><img style="margin-top:10px; display: block; margin-left: auto; margin-right: auto;" class="img-responsive" src="'.$imgBox.'?cache='.time().'"></div>';
		
		//if($aGeo['width'] > $aGeo['height']) $body .= '<div class="col-xs-6" style="text-align: center;"><h4>Puzzle</h4><img style="margin-top:10px;" width="350px;" src="'.$img.'?cache='.time().'"></div>';
		//else $body .= '<div class="col-xs-6" style="text-align: center;"><h4>Puzzle</h4><img style="margin-top:10px;" height="300px;" src="'.$img.'?cache='.time().'"></div>';
		if($aGeo['width'] > $aGeo['height']) $body .= '<div class="col-md-6" style="text-align: center;"><h4>Puzzle</h4><img style="margin-top:10px; display: block; margin-left: auto; margin-right: auto;" class="img-responsive" src="'.$img.'?cache='.time().'"></div>';
		else $body .= '<div class="col-md-6" style="text-align: center;"><h4>Puzzle</h4><img style="margin-top:10px; display: block; margin-left: auto; margin-right: auto;" class="img-responsive" src="'.$img.'?cache='.time().'"></div>';
		
		$body .= '</div>';
		//http://www.foto-art-puzzle.it/cart.php?mode=add&type=product&id=5&mediaID=873
		/*$footer = '<form method="post" action="'.tep_href_link(FILENAME_PHOTO_PUZZLE).'?products_id='.$productId.'&action=add_product" name="cart_quantity">';
		$footer .= '<div style="display: none;"><select name="id['.db_boxes_option_id().']"><option selected="selected" value="'.db_boxes_options_values_id($boxId).'"></option></select></div>';
		$footer .= '<input type="hidden" value="'.$productId.'" name="products_id">';
		$footer .= '<input type="hidden" value="'.$puzzleId.'" name="pp">';
	    $footer .= '<button id="addtosc" type="submit" class="btn btn-primary"> '. TEXT_PP_AGGIUNGI_CARR .' <span class="glyphicon glyphicon-shopping-cart"></span></button>';
		$footer .= '</form>';
		*/
		$aOptions = db_boxes_option_id($puzzleId, TRUE);
		if ($mediaId) {
			
			$footer = '<form method="post" action="cart.php">';
			$footer .= '<input id="rawPrice" type="hidden" value="'.$aOptions['price'].'">';
			$footer .= '<input id="taxInc" type="hidden" value="">';
			$footer .= '<input id="startingCredits" type="hidden" value="">';
			$footer .= '<input id="mode" type="hidden" value="add" name="mode">';
			$footer .= '<input id="type" type="hidden" value="product" name="type">';
			$footer .= '<input type="hidden" value="'.$productId.'" name="id">';
			$footer .= '<input type="hidden" value="'.$mediaId.'" name="mediaID">';
			$footer .= '<input type="hidden" value="1" name="hasOptions">';
			$footer .= '<input type="hidden" value="'.$aOptions['op_id'].'" name="option['.$aOptions['parent_id'].']">';
			$footer .= '<button type="submit" class="btn btn-primary"> '. $lang['TEXT_PP_AGGIUNGI_CARR'] .' <span class="glyphicon glyphicon-shopping-cart"></span></button>';
			$footer .= '</form>';
		} else {

			$footer = '<form method="post" action="cart.php">';
			//$footer .= '<input id="rawPrice" type="hidden" value="'.$aOptions['price'].'">';
			//$footer .= '<input id="taxInc" type="hidden" value="">';
			//$footer .= '<input id="startingCredits" type="hidden" value="">';
			$footer .= '<input id="mode" type="hidden" value="add" name="mode">';
			$footer .= '<input id="type" type="hidden" value="product" name="type">';
			$footer .= '<input type="hidden" value="'.$productId.'" name="id">';
			$footer .= '<input type="hidden" value="" name="mediaID">';
			$footer .= '<input type="hidden" value="1" name="hasOptions">';
			$footer .= '<input type="hidden" value="'.$aOptions['op_id'].'" name="option['.$aOptions['parent_id'].']">';
			$footer .= '<button type="submit" class="btn btn-primary"> '. $lang['TEXT_PP_AGGIUNGI_CARR'] .' <span class="glyphicon glyphicon-shopping-cart"></span></button>';
			$footer .= '</form>';
			
		}
		return json_encode(array('body' => $body, 'footer' => $footer));
		
	} else { //ordine senza scatola

		$sesskey = tep_session_id();
		tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET text_type = NULL, text = NULL WHERE sesskey = \"".$sesskey."\" AND current = 1");
		$query = tep_db_query("SELECT products_id, photo_puzzle_id, img_file, crop_coords, ps4_path, ps4_original_filename, media_id FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1");
		$aQueryResult = tep_db_fetch_array($query);
		$mediaId = $aQueryResult['media_id'];
		$puzzleId =  $aQueryResult['photo_puzzle_id'];
		$productId = $aQueryResult['products_id'];
		$jsonCropCoords = $aQueryResult['crop_coords'];
		$aCoords = json_decode($jsonCropCoords, TRUE);
		
		if(is_null($mediaId)) { //puzzle da upload utente
			$srcFile = WORK_DIR.$sesskey.'/'.$puzzleId.'/'.$aQueryResult['img_file']; //file inviato da utente
			$puzzleFile = WORK_DIR.$sesskey.'/'.$puzzleId.'/puzzle/'.getName(basename($srcFile), 'crop'); //il puzzle vero e proprio
		} else {
			//$srcFile = BASE_PATH.'/assets/library/'.$aQueryResult['ps4_path'].'/originals/'.$aQueryResult['ps4_original_filename'];
			$srcFile = BASE_PATH.'/assets/library/'.$aQueryResult['ps4_path'].'/originals/'.$aQueryResult['ps4_original_filename'];
			$puzzleFile = WORK_DIR.$sesskey.'/'.$puzzleId.'/puzzle/'.getName(basename($aQueryResult['img_file']), 'crop'); //il puzzle vero e proprio
		}
		//$srcFile = WORK_DIR.$sesskey.'/'.$puzzleId.'/'.$aQueryResult['img_file']; //file inviato da utente
		//$puzzleFile = WORK_DIR.$sesskey.'/'.$puzzleId.'/puzzle/'.getName(basename($srcFile), 'crop'); //il puzzle vero e proprio

		/*immagine a 96dpi per la preview ordine*/
		if (!empty($aCoords['shape'])) $img = WEB_DIR . $sesskey . '/' . $puzzleId . '/boxes/' . getName(changeExt($aQueryResult['img_file'], 'png'), 'boximage');
		else $img = WEB_DIR . $sesskey . '/' . $puzzleId . '/boxes/' . getName($aQueryResult['img_file'], 'boximage');  
		
		$box_file = basename($img);
		tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET box_file = '".$box_file."' WHERE photo_puzzle_id = ".$puzzleId."");

		if(file_exists($puzzleFile))unlink($puzzleFile);
		
		$im = new Imagick();
		$im->readImage($srcFile);
		$im->profileImage( '*' , NULL );
		if (!empty($aCoords['shape'])) {
			
			$mask = new Imagick();
			//if($aCoords['w'] >= $aCoords['h']) $mask->readImage(DIR_WS_IMAGES.'heart_mask_negate.png');
			//else $mask->readImage(DIR_WS_IMAGES.'heart_mask_negate_portrait.png');
			if($aCoords['w'] >= $aCoords['h']) $mask->readImage(BASE_PATH.'/assets/images/heart_mask_negate.png');
			else $mask->readImage(BASE_PATH.'/assets/images/heart_mask_negate_portrait.png');
	
			$mask->scaleImage($aCoords['w'], 0);
			
			if($im->getImageFormat() != 'PNG') {
				$im->setImageFormat('PNG');
				$puzzleFile = changeExt($puzzleFile, 'png');
			}
			
		 	$im->setImageVirtualPixelMethod(Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
	        $im->setImageMatte(true);
	
			$im->cropImage($aCoords['w'], $aCoords['h'], $aCoords['x'], $aCoords['y']);
			$im->setImagePage($aCoords['w'], $aCoords['h'], 0, 0);
	
			$im->compositeImage($mask, Imagick::COMPOSITE_DSTIN, 0, 0, Imagick::CHANNEL_ALPHA);
			$im->writeImage($puzzleFile);
			$mask->destroy();
			
		} else {

			$im->cropImage($aCoords['w'], $aCoords['h'], $aCoords['x'], $aCoords['y']);
			$im->setImagePage($aCoords['w'], $aCoords['h'], 0, 0);
			$im->writeImage($puzzleFile);

		}
		
		$aGeo = $im->getImageGeometry();
		$aRisoluzione = array('x' => 96, 'y' => 96);
		$im = new Imagick($puzzleFile);
		$scaleWidth = mm2px(450, $aRisoluzione['x']);
		if($scaleWidth > $aGeo['width']) $scaleWidth = $aGeo['width'];

		$im->scaleImage($scaleWidth, 0);
		$im->writeImage(BASE_PATH.'/'.$img);
		$im->destroy();
		
		$img = $img.'?cache='.time();
		$body = '<div class="row">';
		//$body .= '<div class="col-xs-6" style="text-align: center;"><h4>Box</h4><img style="margin-top:10px;" height="300px" src="'.$imgBox.'?cache='.time().'"></div>';
		
		if($aGeo['width'] > $aGeo['height']) $body .= '<div style="text-align: center;"><h4>Puzzle</h4><img style="margin-top:10px;" width="350px;" src="'.$img.'?cache='.time().'"></div>';
		else $body .= '<div class="col-xs-6" style="text-align: center;"><h4>Puzzle</h4><img style="margin-top:10px;" height="300px;" src="'.$img.'?cache='.time().'"></div>';
		
		$body .= '</div>';
		$footer = '';
		/*
		$footer = '<form method="post" action="'.tep_href_link(FILENAME_PHOTO_PUZZLE).'?products_id='.$productId.'&action=add_product" name="cart_quantity">';
		$footer .= '<div style="display: none;"><select name="id['.$optionId.']"><option selected="selected" value="'.$optionIdValue.'"></option></select></div>';
		$footer .= '<input type="hidden" value="'.$productId.'" name="products_id">';
		$footer .= '<input type="hidden" value="'.$puzzleId.'" name="pp">';
	    $footer .= '<button id="addtosc" type="submit" class="btn btn-primary"> '. $lang['TEXT_PP_AGGIUNGI_CARR'] .' <span class="glyphicon glyphicon-shopping-cart"></span></button>';
		$footer .= '</form>';
		*/
		$aOptions = db_boxes_option_id($puzzleId, FALSE);

		if ($mediaId) {

			$footer = '<form method="post" action="cart.php">';
			$footer .= '<input id="rawPrice" type="hidden" value="'.$aOptions['price'].'">';
			$footer .= '<input id="taxInc" type="hidden" value="">';
			$footer .= '<input id="startingCredits" type="hidden" value="">';
			$footer .= '<input id="mode" type="hidden" value="add" name="mode">';
			$footer .= '<input id="type" type="hidden" value="product" name="type">';
			$footer .= '<input type="hidden" value="'.$productId.'" name="id">';
			$footer .= '<input type="hidden" value="'.$aQueryResult['media_id'].'" name="mediaID">';
			$footer .= '<input type="hidden" value="1" name="hasOptions">';
			$footer .= '<input type="hidden" value="'.$aOptions['op_id'].'" name="option['.$aOptions['parent_id'].']">';
			$footer .= '<button type="submit" class="btn btn-primary"> '. $lang['TEXT_PP_AGGIUNGI_CARR'] .' <span class="glyphicon glyphicon-shopping-cart"></span></button>';
			$footer .= '</form>';
			
		} else {

			$footer = '<form method="post" action="cart.php">';
			//$footer .= '<input id="rawPrice" type="hidden" value="'.$aOptions['price'].'">';
			//$footer .= '<input id="taxInc" type="hidden" value="">';
			//$footer .= '<input id="startingCredits" type="hidden" value="">';
			$footer .= '<input id="mode" type="hidden" value="add" name="mode">';
			$footer .= '<input id="type" type="hidden" value="product" name="type">';
			$footer .= '<input type="hidden" value="'.$productId.'" name="id">';
			$footer .= '<input type="hidden" value="" name="mediaID">';
			$footer .= '<input type="hidden" value="1" name="hasOptions">';
			$footer .= '<input type="hidden" value="'.$aOptions['op_id'].'" name="option['.$aOptions['parent_id'].']">';
			$footer .= '<button type="submit" class="btn btn-primary"> '. $lang['TEXT_PP_AGGIUNGI_CARR'] .' <span class="glyphicon glyphicon-shopping-cart"></span></button>';
			$footer .= '</form>';
			
		}
		
		return json_encode(array('body' => $body, 'footer' => $footer));
		
	}
	
}



function getOpzioniProdotto() {
	
	global $lang;
	
	$aRes = db_ppuzzles_options();

	if($aRes) {
		$body = '<div class="row" style="margin-left: 0px !important; margin-right: 0px !important;">';
		$body .= '<table class="table table-hover">';
		$body .= '<thead><tr><th>Opzione</th><th>Prezzo</th></tr></thead>'; //localizzare
		//$body .= '<tr opzione="scatola" style="cursor: pointer;"><td>'.$lang['TEXT_PP_OPZIONE_SCATOLA_PREZZO'].'</td><td>'.$lang['TEXT_PP_OPZIONE_SCATOLA'].'</td></tr>'; //localizzare
		$body .= '<tr opzione="'.strtolower(str_replace(' ', '_', $lang['TEXT_PUZZLES_BOX_OPTION_NAME'])).'" style="cursor: pointer;"><td>'.$lang['TEXT_PUZZLES_BOX_OPTION_NAME'].'</td><td>'.$lang['TEXT_PP_OPZIONE_SCATOLA_PREZZO'].'</td></tr>';
		$body .= '<tr opzione="'.strtolower($aRes['nome_opzione']).'" style="cursor: pointer;" idOpzione="'.$aRes['id_gruppo_opzioni'].'" idOptionValue="'.$aRes['id_opzione'].'"><td>'.$lang['TEXT_PREASS_ATTR_NAME'].'</td><td>'.$aRes['prezzo'].'</td></tr>';
		//foreach ($aRes as $k => $row) {
			//$body .= '<tr opzione="'.strtolower($row['nome_opzione']).'" style="cursor: pointer;" idOpzione="'.$row['id_opzione'].'" idOptionValue="'.$row['options_values_id'].'"><td>'.$row['nome_opzione'].'</td><td>'.$row['prezzo'].'</td></tr>';
			//$body .= '<tr opzione="'.strtolower($row['nome_opzione']).'" style="cursor: pointer;" idOpzione="'.$row['id_gruppo_opzioni'].'" idOptionValue="'.$row['id_opzione'].'"><td>'.$row['nome_opzione'].'</td><td>'.$row['prezzo'].'</td></tr>';
			//$body .= '<tr opzione="'.strtolower($row['nome_gruppo']).'" style="cursor: pointer;" idOpzione="'.$row['id_gruppo_opzioni'].'" idOptionValue="'.$row['id_opzione'].'"><td>'.$lang['TEXT_PREASS_ATTR_NAME'].'</td><td>'.$row['prezzo'].'</td></tr>';
		//}
		$body .= '</table>';
		$body .= '</div>';
		return $body;
	
	} else return false;

}



function updateSession($array){

	if (is_numeric($array['value'])) tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET ".$array['campo']." = ".$array['value']." WHERE sesskey = '".tep_session_id()."' AND current = 1");
	else tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET ".$array['campo']." = '".$array['value']."' WHERE sesskey = '".tep_session_id()."' AND current = 1");

}


// foto store
 //--- PhotoPuzzle ---
function is_photo_puzzle($id){

	global $dbinfo;	
	//$query = tep_db_query("SELECT a.products_id FROM products_to_categories as a INNER JOIN categories_description as c ON (a.categories_id = c.categories_id) WHERE c.categories_name = '".PUZZLES_CATEGORY."' and products_id = ".(int)$id."");
	$query = tep_db_query("SELECT item_code from ".$dbinfo['pre']."products WHERE prod_id = ".(int)$id."");
	$aResult = tep_db_fetch_array($query);
		if($aResult['item_code'] == PUZZLES_CATEGORY) return true;
        else return false;
}



function getCurrent($sesskey){
        $query = tep_db_query("SELECT * FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1");
        return tep_db_fetch_array($query);
}

function changeExt($file, $newExt){
    
	/*$oldExt = substr($file, strpos($file, '.'));
    $puzzleFile = str_replace($oldExt, '.'.$newExt, $file);*/
    $oldExt = substr(basename($file), strpos(basename($file), '.'));
    $puzzleFile = str_replace($oldExt, '.'.$newExt, $file);
    return $puzzleFile;
    
}

function getName($file, $type, $format = FALSE){

    if($format) $ext = '.'.$format;
	else $ext = substr($file, strpos($file, '.'));
    $name = substr($file, 0, strpos($file, '.'));
    return $name.'_'.$type.$ext;

}

        function tep_get_ppid($string) {

        list($idProdotto, $idCat, $optValueId, $photo_puzzle_id) = sscanf($string, "%d{%d}%d{pp}%d");
                return array($idProdotto, $photo_puzzle_id);
                //return array('idProdotto' => $idProdotto, 'photo_puzzle_id' => $photo_puzzle_id);

        }

function gestisciOpzioniBox($array){

	global $dbinfo;
	if($array['action'] == 0) {
		//tep_db_query("DELETE FROM photo_puzzle_ps4_options_to_products_options_values WHERE op_id = ".$array['opId']." and products_options_values_id = ".$array['optValId']."");
		//tep_db_query("DELETE FROM ps4_options WHERE op_id = ".$array['opId']."");
		
		//tep_db_query("UPDATE photo_puzzle_ps4_options_to_products_options_values SET active = 0 WHERE (op_id = ".$array['opId']." and products_options_values_id = ".$array['optValId'].")");
		//tep_db_query("UPDATE ps4_options SET deleted = 1 WHERE op_id = ".$array['opId']."");
		tep_db_query("UPDATE ps4_options SET active = 0 WHERE op_id = ".$array['opId']."");
	}
	else {
			
			tep_db_query("UPDATE ps4_options SET active = 1 WHERE op_id = ".$array['opId']."");
			// opId diventa option Group Id
			/*
			$mysqli = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['name']);
			$mysqli->query("INSERT INTO ps4_options (parent_id, uop_id, name, price, price_mod, credits, credits_mod, my_cost, add_weight, sortorder, active, deleted, name_english, name_russian, name_spanish, name_dutch, name_french, name_german, name_, name_polish, name_romanian, name_slovenian, name_italian) 
						VALUES (".$array['optGrpId'].", '', '".$array['optName']."', 0.0000, 'add', 0, '', '', 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '')");
			$id = $mysqli->insert_id;
			tep_db_query("INSERT INTO photo_puzzle_ps4_options_to_products_options_values (op_id, products_options_values_id) VALUES(".$id.",".$array['optValId'].")");
			*/
	}	
	
}


function mgrGetBoxes($optionId) {
			
	global $dbinfo;
	$query = tep_db_query("SELECT products_options_values_name FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = ".$optionId."");
	$aQuery = tep_db_fetch_array($query);
	$products_options_values_name = $aQuery['products_options_values_name'];
	$aJson = json_decode($products_options_values_name, TRUE);
	$aScatole = $aJson['scatole'];
	$aRes = array();
	foreach ($aScatole as $k => $aBox) {
		$aRes[] = array('boxId' => $aBox['box_id'], 'nome' => $aBox['modelFile']);
	}
	return json_encode($aRes);
	
}



function mgrGetBoxesList() {
		
	$pngDir = DIR_BOX_MODELS.'png/';
	$aJobs = array();
	//$queryProductsOptionsId = tep_db_query("SELECT products_options_id FROM products_options WHERE products_options_name = '".BOX_ATTR_NAME."'");
	//$aProductsOptionsId = tep_db_fetch_array($queryProductsOptionsId);

	/*$query = tep_db_query("	SELECT a.products_attributes_id as id, a.options_values_id, REPLACE(FORMAT(a.options_values_price, 2), '.', ',') as prezzo, b.products_options_values_name as json FROM 
							products_attributes as a
							INNER JOIN
							products_options_values as b
							ON a.options_values_id = b.products_options_values_id
							WHERE a.options_id = ".$aProductsOptionsId['products_options_id']."
							GROUP BY options_values_id"
			);
	*/
	$query = tep_db_query("SELECT products_options_values_name as json FROM fotoartphfpuzzle.photo_puzzle_products_options_values WHERE deleted = 0 AND products_options_group_name = '".PUZZLES_BOX_OPTION_NAME."'");
	while ($row = tep_db_fetch_array($query)) {
		$json = json_decode($row['json']);
		$categoria = strtolower(str_replace(' ', '_', $json->desc));
		$bFoto = $json->foto;
		//foreach (getPuzzlesList() as $aPuzzles) {
		foreach (db_get_puzzles_list() as $aPuzzles) {
			
			$pezzi = $aPuzzles['pezzi'];
			$dimensioni = $aPuzzles['dimensioni'];
			if(!empty($json->scatole)) {
				foreach ($json->scatole as $obj) {
					$modelfile = $obj->modelFile;
					$filename = $pezzi.'_'.$categoria.($bFoto ? '_foto_' : '_').str_replace('.pdf', '.png', $modelfile);
					$aJobs[] = array('filename' => $filename, 'obj' => $obj, 'bFoto' => $bFoto, 'pezzi' => $pezzi, 'dimensioni' => $dimensioni);
				}
			}
		}
	}
	foreach(glob($pngDir."*") as $file) unlink($file);  
	return json_encode(array('tot' => count($aJobs), 'jobs' => $aJobs));

}



function mgrDrawBox($filename, $obj, $bFoto, $pezzi, $dimensioni){
		
	$aStyle = (array) $obj;

	$font = $aStyle['font'];
	$model = DIR_BOX_MODELS.$aStyle['modelFile'];
	$pngDir = DIR_BOX_MODELS.'png/';
	$tmpFile = $pngDir.'tmp.pdf';
	
	chdir($pngDir);
	/*$locale='it_IT.UTF-8';
	setlocale(LC_ALL,$locale);
	putenv('LC_ALL='.$locale);
	echo exec('locale charmap');*/
	

	$im = new Imagick($model);
	$aResolution = $im->getImageResolution();
	$aGeo = $im->getImageGeometry();
	$mmWidth = px2mm($aGeo['width'], $aResolution['x']);
	$mmHeight = px2mm($aGeo['height'], $aResolution['y']);
	$im->destroy();
	
	$margineX = (($mmWidth-$aStyle['width'])/2);
	$margineY = (($mmHeight-$aStyle['height'])/2);

	$pdf = new FPDI('L', 'mm', array($mmWidth, $mmHeight)); //FPDI extends TCPDF
	$pdf->SetAutoPageBreak(FALSE, 0);
	$pdf->SetPrintHeader(FALSE);
	$pdf->SetPrintFooter(FALSE);	
	$pdf->AddPage();
	$pdf->setSourceFile($model);
	$tplIdx = $pdf->ImportPage(1);
	$pdf->useTemplate($tplIdx, 0, 0, $mmWidth, $mmHeight, TRUE);

	// box scritta bottom
	$aBottom = array('xy' => $aStyle['pezziBottomxy'], 'boxSize' => $aStyle['pezziBottomsize'], 'font' => $font, 'fontSize' => $aStyle['pezziBottomFontSize'], 'fontColor' => $aStyle['pezziBottomFontColor'], 'fontBorderColor' => $aStyle['pezziBottomFontBorderColor'], 'testo' => 'Foto Puzzle '.$pezzi);
	printText($pdf, $aBottom, $margineX, $margineY);
	
	// numero pezzi sx
	$aPezziSx = array('xy' => $aStyle['pezziSxxy'], 'boxSize' => $aStyle['pezziSxsize'], 'font' => $font, 'fontSize' => $aStyle['pezziSxFontSize'], 'fontColor' => $aStyle['pezziSxFontColor'], 'fontBorderColor' => $aStyle['pezziSxFontBorderColor'], 'testo' => $pezzi);
	list($xCentro, $yCentro) = explode(';', $aStyle['pezziSxxy']);
	printText($pdf, $aPezziSx, $margineX, $margineY, $mmWidth, $mmHeight);
	
	// dimensioni bottom
	$aDimensioniBottom = array('xy' => $aStyle['dimBottomxy'], 'boxSize' => $aStyle['dimBottomsize'], 'font' => $font, 'fontSize' => $aStyle['dimBottomFontSize'], 'fontColor' => $aStyle['dimBottomFontColor'], 'fontBorderColor' => $aStyle['dimBottomFontBorderColor'], 'testo' => $dimensioni);
	printText($pdf, $aDimensioniBottom, $margineX, $margineY);
	unset($aResolution);

	//$pdf->Output($_SERVER['DOCUMENT_ROOT'].'/'.$tmpFile, 'F');
	$pdf->Output($tmpFile, 'F');

	/* creo la scatola */	
	$mpc = new Imagick();
	$mpc->readImage($tmpFile);
	list($dpiX, $dpiY) = array_values($mpc->getImageResolution());
	$mpc->setImageFormat('MPC');
	$mpc->setImageMatte(false);
	$mpc->cropImage(mm2px($aStyle['width'], $dpiX), mm2px($aStyle['height'], $dpiY), mm2px($margineX, $dpiX), mm2px($margineY, $dpiY));

	$mpc->setImagePage(0, 0, 0, 0);
	
	$mpc->writeImage($pngDir.'fullbox.mpc');

	$boxBorder_mm = $aStyle['boxBorder'];
	$borderPx = mm2px($boxBorder_mm, $dpiX);
	
	list($modelPxWidth, $modelPxHeigh) = array_values($mpc->getImageGeometry());
	$border = $borderPx;
	$mpc->destroy();

	$cmd = "convert -depth 8 -format png ".$pngDir."fullbox.mpc
			( +clone -crop ".($modelPxWidth - ($border*2))."x".($modelPxHeigh - ($border*2))."+".$border."+".$border." +repage
			-alpha set -virtual-pixel transparent -mattecolor none +distort Perspective '0,0 -498.427,-204.296  1043,0 466.7,-389.817  1043,674 422.231,188.464  0,674 -441.695,465.366'
			-write ".$pngDir."tmp1.mpc +delete )
			( +clone -crop ".$border."x".($modelPxHeigh-($border*2))."+0+".$border." +repage
			-alpha set -virtual-pixel transparent -mattecolor none +distort Perspective '0,0 -547.114,-244.206  115,0 -498.427,-204.296  115,674 -441.695,465.366  0,674 -486.987,406.761'
			-modulate 90 -write ".$pngDir."tmp2.mpc +delete ) 
			( +clone -crop ".($modelPxWidth-($border*2))."x".$border."+".$border."+0 +repage
			-alpha set -virtual-pixel transparent -mattecolor none +distort Perspective '0,0 -547.114,-244.206  1043,0 395.634,-416.837  1043,115 466.7,-389.817  0,115 -498.427,-204.296'
			-modulate 90 -write ".$pngDir."tmp3.mpc +delete ) null:;
			
			convert ".$pngDir."tmp1.mpc ".$pngDir."tmp2.mpc ".$pngDir."tmp3.mpc -background none -layers merge +repage ".$pngDir.$filename.".mpc; 
			convert ".$pngDir.$filename.".mpc ( +clone -background black -shadow 80x3+5+5 ) +swap -background none -layers merge  +repage ".$pngDir.getName($filename, 'full');

    # remove newlines and convert single quotes to double to prevent errors
	$command = str_replace(array("\n", "'"), array('', '"'), $cmd);
    # replace multiple spaces with one
    $command = preg_replace('#(\s){2,}#is', ' ', $command);
    # escape shell metacharacters
    $command = escapeshellcmd($command);
	$command = str_replace('\;', ';', $command);
	exec($command);

	$im = new Imagick();
	$im->readImage($pngDir.getName($filename,"full"));
	$im->scaleImage(BOX_IMG_WIDTH, 0);
	$im->writeImage($pngDir.$filename);
	$im->destroy();
	
	foreach(glob($pngDir."*.mpc") as $file) unlink($file);
	foreach(glob($pngDir."*.pdf") as $file) unlink($file);
	foreach(glob($pngDir."*.cache") as $file) unlink($file);

}



function px2mm($px, $resolution) {
	
	return ( ($px/$resolution) * 2.54 * 10);
	
}



function mm2px($mm, $resolution) {
	
	return ($mm*$resolution) / 25.4;
}



function pt2mm($pt) {
		
	return ($pt*0.35278);
}



function getCoordinates($string, $resolution) {
	
	list ($x, $y) = explode(';', $string);
	$x = mm2px(str_replace(',', '.', $x), $resolution);
	$y = mm2px(str_replace(',', '.', $y), $resolution);
	
	return array('x' => $x, 'y' => $y);
}



function printText($obj, $array, $margineX, $margineY, $mmWidth = NULL, $mmHeight = NULL) {

	$pdf = $obj;
	list($xCentro, $yCentro) = explode(';', $array['xy']);
	$xCentro = $xCentro+$margineX;
	$yCentro = $mmHeight-$yCentro-$margineY;

	$pdf->SetFont($array['font'], '', $array['fontSize']);
	$pdf->SetFillColor(255,255,0);
	list($c, $m, $y, $k) = explode(',', $array['fontBorderColor']);
	$pdf->SetDrawColor($c, $m, $y, $k);
	$pdf->setTextRenderingMode($stroke=0.2, $fill=true, $clip=false);
	list($c, $m, $y, $k) = explode(',', $array['fontColor']);
	$pdf->SetTextColor($c, $m, $y, $k);
	
	$textWidth = $pdf->GetStringWidth($array['testo']);

	$xCell = ($xCentro-($textWidth/2));
	$pdf->setXY($xCell, $yCentro);
	$pdf->Cell($textWidth, 0, $array['testo'], $border = 0, $ln = 0, $align = 'C', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'C', $valign = 'C');
	
}



function drawBox($filename, $pezzi, $array, $puzzleId, $foto){

	global $dbinfo;
	$sesskey = tep_session_id();
	$query = tep_db_query("SELECT photo_puzzle_id, products_id, text_type, text, img_file, crop_coords FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1");
	$aQueryResult = tep_db_fetch_array($query);
	$puzzleId =  $aQueryResult['photo_puzzle_id'];
	$productId = $aQueryResult['products_id'];
	$jsonCropCoords = $aQueryResult['crop_coords'];

	$aStyle = (array) $array;
	$font = $aStyle['font'];
	$model = DIR_BOX_MODELS.$aStyle['modelFile'];

	$pdfDir = WORK_DIR.$sesskey.'/'.$puzzleId.'/boxes/';
	$printBox = $pdfDir.$filename.'.pdf';

	$im = new Imagick($model);
	$aResolution = $im->getImageResolution();
	$aGeo = $im->getImageGeometry();
	$mmWidth = px2mm($aGeo['width'], $aResolution['x']);
	$mmHeight = px2mm($aGeo['height'], $aResolution['y']);
	$im->destroy();
	
	$margineX = (($mmWidth-$aStyle['width'])/2);
	$margineY = (($mmHeight-$aStyle['height'])/2);

	$pdf = new FPDI('L', 'mm', array($mmWidth, $mmHeight)); //FPDI extends TCPDF
	$pdf->SetAutoPageBreak(FALSE, 0);
	$pdf->SetPrintHeader(FALSE);
	$pdf->SetPrintFooter(FALSE);	
	$pdf->AddPage();
	$pdf->setSourceFile($model);
	$tplIdx = $pdf->ImportPage(1);
	$pdf->useTemplate($tplIdx, 0, 0, $mmWidth, $mmHeight, TRUE);

	//$aJson = array($mmWidth, $mmHeight, $margineX, $margineY, $aStyle['orderxy'], $aStyle['ordersize'], $aStyle['orderFontSize'], $aStyle['orderFontColor'], $aStyle['orderFontBorderColor']);
	//file_put_contents(WORK_DIR.$sesskey. '/' .$puzzleId. '/boxes/' .$filename.'.txt', json_encode($aJson));

	// box scritta bottom
	$aBottom = array('xy' => $aStyle['pezziBottomxy'], 'boxSize' => $aStyle['pezziBottomsize'], 'font' => $font, 'fontSize' => $aStyle['pezziBottomFontSize'], 'fontColor' => $aStyle['pezziBottomFontColor'], 'fontBorderColor' => $aStyle['pezziBottomFontBorderColor'], 'testo' => 'Foto Puzzle '.$pezzi);
	printText($pdf, $aBottom, $margineX, $margineY);
	
	// numero pezzi sx
	$aPezziSx = array('xy' => $aStyle['pezziSxxy'], 'boxSize' => $aStyle['pezziSxsize'], 'font' => $font, 'fontSize' => $aStyle['pezziSxFontSize'], 'fontColor' => $aStyle['pezziSxFontColor'], 'fontBorderColor' => $aStyle['pezziSxFontBorderColor'], 'testo' => $pezzi);
	list($xCentro, $yCentro) = explode(';', $aStyle['pezziSxxy']);
	printText($pdf, $aPezziSx, $margineX, $margineY, $mmWidth, $mmHeight);
	
	// dimensioni bottom
	/*$query = tep_db_query("SELECT (SELECT epf_value FROM extra_field_values WHERE value_id = d.extra_value_id4) as dimensioni 
				FROM 
				products_to_categories as a
				INNER JOIN
				products as b ON (a.products_id = b.products_id)
				INNER JOIN
				categories_description as c ON (a.categories_id = c.categories_id)
				INNER JOIN
				products_description as d ON (d.products_id = a.products_id)
				WHERE b.products_id = ".$productId."");*/

	$query = tep_db_query("	SELECT notes
							FROM 
							".$dbinfo['pre']."products
							WHERE prod_id = ".$productId."
							");

	$aFetch = tep_db_fetch_array($query);
	$aJson = json_decode($aFetch['notes'], TRUE);
	list($larghezza, $altezza, $unita) = explode(';', $aJson['dimensioni']);
	$strDimensioni = $larghezza."x".$altezza.$unita; 

	//$aDimensioniBottom = array('xy' => $aStyle['dimBottomxy'], 'boxSize' => $aStyle['dimBottomsize'], 'font' => $font, 'fontSize' => $aStyle['dimBottomFontSize'], 'fontColor' => $aStyle['dimBottomFontColor'], 'fontBorderColor' => $aStyle['dimBottomFontBorderColor'], 'testo' => $aFetch['dimensioni']);
	$aDimensioniBottom = array('xy' => $aStyle['dimBottomxy'], 'boxSize' => $aStyle['dimBottomsize'], 'font' => $font, 'fontSize' => $aStyle['dimBottomFontSize'], 'fontColor' => $aStyle['dimBottomFontColor'], 'fontBorderColor' => $aStyle['dimBottomFontBorderColor'], 'testo' => $strDimensioni);
	printText($pdf, $aDimensioniBottom, $margineX, $margineY);
	unset($aResolution);
	
	$aCoords = (array) json_decode($jsonCropCoords);
	if (!empty($aCoords['shape'])) {
		$srcImg = WORK_DIR . $sesskey . '/' . $puzzleId . '/puzzle/' . getName(changeExt($aQueryResult['img_file'], 'png'), 'crop');
		$img = WORK_DIR . $sesskey . '/' . $puzzleId . '/boxes/' . getName(changeExt($aQueryResult['img_file'], 'png'), 'boximage');
	} else {
		$srcImg = WORK_DIR . $sesskey . '/' . $puzzleId . '/puzzle/' . getName($aQueryResult['img_file'], 'crop');
		$img = WORK_DIR . $sesskey . '/' . $puzzleId . '/boxes/' . getName($aQueryResult['img_file'], 'boximage');
	}

	list($boxWidth, $boxHeight) = explode(';', $aStyle['imgSize']);
	$aRisoluzione = array('x' => 96, 'y' => 96);
	if(file_exists($img)) {
		$im = new Imagick($img);
		$aPixelSize = $im->getImageGeometry();
		$imageWidth = px2mm($aPixelSize['width'], $aRisoluzione['x']);
		$imageHeight = px2mm($aPixelSize['height'], $aRisoluzione['x']);
		$im->destroy();
	} else {
		$im = new Imagick($srcImg);
		$im->scaleImage(mm2px($boxWidth, $aRisoluzione['x']), 0);
		$aPixelSize = $im->getImageGeometry();
		$imageWidth = px2mm($aPixelSize['width'], $aRisoluzione['x']);
		$imageHeight = px2mm($aPixelSize['height'], $aRisoluzione['x']);
		$im->writeImage($img);
		$im->destroy();
	}
	
	if($foto) {

		if($imageWidth > $imageHeight) { //landscape

			list($xCentro, $yCentro) = explode(';', $aStyle['coordinateImg']);
			$verticeX = (($xCentro - ($boxWidth/2)))+$margineX;
			$verticeY = $mmHeight-($yCentro + ($boxHeight/2))-$margineY;

			$border = array('LTRB' => array('width' => $aStyle['imgBorderSize'], 'color' => explode(',', $aStyle['imgBorderColor'])));

			$imgRateo = $imageWidth/$imageHeight;
			if($boxWidth/$imgRateo > $boxHeight) {
				$imgWidth = $boxHeight*$imgRateo;
				$newX = $verticeX + (($boxWidth-$imgWidth)/2);
				$pdf->Image($img, $newX, $verticeY, $w = '', $boxHeight, $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border);
			}		
			else $pdf->Image($img, $verticeX, $verticeY, $boxWidth, $h = '', $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border);


			/* miniatura superiore */
			list($xCentro, $yCentro) = explode(';', $aStyle['coordinateImgTop']);
			list($boxWidth, $boxHeight) = explode(';', $aStyle['miniSize']);

			$verticeX = ($xCentro - ($boxWidth/2))+$margineX;
			$verticeX2 = $verticeX + $boxWidth;
			$verticeY = ($mmHeight-($yCentro - ($boxHeight/2)))-$margineY;

			$pdf->StartTransform();
			$pdf->Rotate(-180, $verticeX2, $verticeY);
			//$pdf->Image	($img, $xBox, $yBox+$boxHeight, $boxWidth, $h = (($boxWidth*$imageHeight)/$imageWidth), $type = '', $link = '', $align = '', $resize = true, $dpi = 72, $palign = '', $ismask = false, $imgmask = false, $border = 0, $fitbox = false, $hidden = false, $fitonpage = false, $alt = false, $altimgs = array());
			$pdf->Image($img, $verticeX2, $verticeY, $boxWidth, $h = '', $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border, $fitbox = false, $hidden = false, $fitonpage = false, $alt = false, $altimgs = array());
			$pdf->StopTransform();
			
			/* miniatura laterale */
			list($xCentro, $yCentro) = explode(';', $aStyle['coordinateImgSide']);
			list($boxHeight, $boxWidth) = explode(';', $aStyle['miniSize']); //inverto perché sarà ruotata

			//$verticeX = ($xCentro - ($boxWidth/2)); //del box, non dell' immagine
			$verticeX2 = ($xCentro + ($boxWidth/2))+$margineX; //del box, non dell' immagine
			$verticeY = ($mmHeight-($yCentro + ($boxHeight/2)))-$margineY; //del box, non dell' immagine
			
			$pdf->StartTransform();
			$pdf->Rotate(-90, $verticeX2, $verticeY); //x&y del punto "perno" di rotazione
			$pdf->Image($img, $verticeX2, $verticeY, $boxHeight, $h = '', $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border);
			$pdf->StopTransform();
		}
		else { //portrait

			list($xCentro, $yCentro) = explode(';', $aStyle['coordinateImg']);
			list($boxWidth, $boxHeight) = explode(';', $aStyle['imgSize']);

			$verticeX = ($xCentro+($boxWidth/2))+$margineX;
			$verticeY = $mmHeight-($yCentro + ($boxHeight/2))-$margineY;

			$border = array('LTRB' => array('width' => $aStyle['imgBorderSize'], 'color' => explode(',', $aStyle['imgBorderColor'])));

			$imgRateo =	$imageHeight/$imageWidth;
			if($boxWidth/$imgRateo > $boxHeight) {

				$imgHeightRotated = $boxHeight;
				$imgWidthRotated = $boxHeight*$imgRateo;
				$newX = $verticeX - (($boxWidth-$imgWidthRotated)/2);
				$pdf->StartTransform();
				$pdf->Rotate(-90, $newX, $verticeY); //la rotazione viene applicata
				$pdf->Image($img, $newX, $verticeY, $boxHeight, $h = '', $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border);
				$pdf->StopTransform();
				
			} else {
				$pdf->StartTransform();
				$pdf->Rotate(-90, $verticeX, $verticeY); //la rotazione viene applicata
				$pdf->Image($img, $verticeX, $verticeY, 0, $boxWidth, $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border);
				$pdf->StopTransform();
			}

			/* miniatura superiore */
			list($xCentro, $yCentro) = explode(';', $aStyle['coordinateImgTop']);
			list($boxWidth, $boxHeight) = explode(';', $aStyle['miniSize']);
			$verticeX = ($xCentro + ($boxWidth/2))+$margineX;
			$verticeY = $mmHeight-($yCentro + ($boxHeight/2))-$margineY;
			//$pdf->Line($xCentro, 0, $xCentro, $mmHeight);
			$imgRateo =	$imageHeight/$imageWidth;
			if($boxWidth/$imgRateo > $boxHeight) {

				$imgHeightRotated = $boxHeight;
				$imgWidthRotated = $boxHeight*$imgRateo;
				
				$newX = $verticeX - (($boxWidth-$imgWidthRotated)/2);
				$pdf->StartTransform();
				$pdf->Rotate(-90, $newX, $verticeY); //la rotazione viene applicata
				$pdf->Image($img, $newX, $verticeY, $boxHeight, $h = '', $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border);
				$pdf->StopTransform();
					
			} else {

				$pdf->StartTransform();
				$pdf->Rotate(-90, $verticeX, $verticeY);
				$pdf->Image($img, $verticeX, $verticeY, 0, $boxWidth, $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border, $fitbox = 'LT', $hidden = false, $fitonpage = false, $alt = false, $altimgs = array());
				$pdf->StopTransform();
			}
			
			/* miniatura laterale */
			list($xCentro, $yCentro) = explode(';', $aStyle['coordinateImgSide']);
			list($boxHeight, $boxWidth ) = explode(';', $aStyle['miniSize']); //inverto perché ruotata
			
			$verticeX = ($xCentro - ($boxWidth/2))+$margineX;
			$verticeY = $mmHeight-($yCentro + ($boxHeight/2))-$margineY;
			$imgRateo =	$imageHeight/$imageWidth;

			if($boxHeight/$imgRateo > $boxWidth) {

				$imgWidth = $boxWidth;
				$imgHeight = $imgWidth*$imgRateo;
				$newY = $verticeY + (($boxHeight-$imgHeight)/2);
				$pdf->Image($img, $verticeX, $newY, $imgWidth, 0, $type = '', $link = '', $align = '', $resize = false, $dpi = 72, $palign = '', $ismask = false, $imgmask = false, $border);				

			} else $pdf->Image($img, $verticeX, $verticeY, 0, $boxHeight, $type = '', $link = '', $align = '', $resize = false, $dpi = 72, $palign = '', $ismask = false, $imgmask = false, $border);

		}

		
	}

	//aggiungo il testo
	$textType = $aQueryResult['text_type'];
	if ($textType == 'single' && !empty($aQueryResult['text'])) {
		$text = substr($aQueryResult['text'], 0, CHARS_LIMIT);
		$aText = array('xy' => $aStyle['textxy'], 'boxSize' => $aStyle['textsize'], 'font' => $font, 'fontSize' => $aStyle['fontSize'], 'fontColor' => $aStyle['textColor'], 'fontBorderColor' => $aStyle['textBorderColor'], 'testo' => $text);
		printText($pdf, $aText, $margineX, $margineY);
	} elseif ($textType == 'multi') {
		$aText = json_decode($aQueryResult['text']);
		list($x, $y) = explode(';', $aStyle['textxy']);
		$mmText = pt2mm($aStyle['fontSize']);
		$interlinea = ( ( $layoutHeight - ($layoutHeight - $y) ) - ($mmText * TXT_ROWS) ) / 4;
		foreach ($aText as $k => $row) {

			if( $k != 0) $y -= $interlinea;
			if(!empty($row)) {
				$aText = array('xy' => implode(";", array($x, $y)), 'boxSize' => $aStyle['textsize'], 'font' => $font, 'fontSize' => $aStyle['fontSize'], 'fontColor' => $aStyle['textColor'], 'fontBorderColor' => $aStyle['textBorderColor'], 'testo' => $row);
				printText($pdf, $aText, $margineX, $margineY);
			}			
		}
		
	}
	//$pdf->Output($_SERVER['DOCUMENT_ROOT'].'/'.$printBox, 'F');
	$pdf->Output($printBox, 'F');

	$mpc = new Imagick();
	$mpc->readImage($printBox);
	list($dpiX, $dpiY) = array_values($mpc->getImageResolution());
	$mpc->setImageFormat('MPC');
	$mpc->setImageMatte(false);
	$mpc->cropImage(mm2px($aStyle['width'], $dpiX), mm2px($aStyle['height'], $dpiY), mm2px($margineX, $dpiX), mm2px($margineY, $dpiY));

	$mpc->setImagePage(0, 0, 0, 0);
	$mpc->writeImage(WORK_DIR.$sesskey .'/'. $puzzleId .'/boxes/fullbox.mpc');

	$boxBorder_mm = $aStyle['boxBorder'];
	$borderPx = mm2px($boxBorder_mm, $dpiX);
	
	list($modelPxWidth, $modelPxHeigh) = array_values($mpc->getImageGeometry());
	$border = $borderPx;
	$mpc->destroy();

	//$dir = WORK_DIR.$sesskey .'/'. $puzzleId .'/boxes/';
	$dir = dirname($printBox).DIRECTORY_SEPARATOR;
	chdir($dir);

	//($dir.$filename."_full.png");
	/*$cmd = "convert -depth 8 -format png ".$dir."fullbox.mpc \
			\( +clone -crop ".($modelPxWidth - ($border*2))."x".($modelPxHeigh - ($border*2))."+".$border."+".$border." +repage  \
			-alpha set -virtual-pixel transparent -mattecolor none +distort Perspective '0,0 -498.427,-204.296  1043,0 466.7,-389.817  1043,674 422.231,188.464  0,674 -441.695,465.366' \
			-write ".$dir."tmp1.mpc +delete \) \
			\( +clone -crop ".$border."x".($modelPxHeigh-($border*2))."+0+".$border." +repage \
			-alpha set -virtual-pixel transparent -mattecolor none +distort Perspective '0,0 -547.114,-244.206  115,0 -498.427,-204.296  115,674 -441.695,465.366  0,674 -486.987,406.761' \
			-modulate 90 -write ".$dir."tmp2.mpc +delete \) \
			\( +clone -crop ".($modelPxWidth-($border*2))."x".$border."+".$border."+0 +repage \
			-alpha set -virtual-pixel transparent -mattecolor none +distort Perspective '0,0 -547.114,-244.206  1043,0 395.634,-416.837  1043,115 466.7,-389.817  0,115 -498.427,-204.296' \
			-modulate 90 -write ".$dir."tmp3.mpc +delete \) null:;
			
			convert ".$dir."tmp1.mpc ".$dir."tmp2.mpc ".$dir."tmp3.mpc -background none -layers merge +repage ".$dir.$filename.".mpc; 
			convert ".$dir.$filename.".mpc 	\( +clone -background black -shadow 80x3+5+5 \) +swap -background none -layers merge  +repage ".$dir.$filename."_full.png";*/

	$cmd = "convert -depth 8 -format png ".$dir."fullbox.mpc
			( +clone -crop ".($modelPxWidth - ($border*2))."x".($modelPxHeigh - ($border*2))."+".$border."+".$border." +repage
			-alpha set -virtual-pixel transparent -mattecolor none +distort Perspective '0,0 -498.427,-204.296  1043,0 466.7,-389.817  1043,674 422.231,188.464  0,674 -441.695,465.366'
			-write ".$dir."tmp1.mpc +delete )
			( +clone -crop ".$border."x".($modelPxHeigh-($border*2))."+0+".$border." +repage
			-alpha set -virtual-pixel transparent -mattecolor none +distort Perspective '0,0 -547.114,-244.206  115,0 -498.427,-204.296  115,674 -441.695,465.366  0,674 -486.987,406.761'
			-modulate 90 -write ".$dir."tmp2.mpc +delete )
			( +clone -crop ".($modelPxWidth-($border*2))."x".$border."+".$border."+0 +repage
			-alpha set -virtual-pixel transparent -mattecolor none +distort Perspective '0,0 -547.114,-244.206  1043,0 395.634,-416.837  1043,115 466.7,-389.817  0,115 -498.427,-204.296'
			-modulate 90 -write ".$dir."tmp3.mpc +delete ) null:;
			
			convert ".$dir."tmp1.mpc ".$dir."tmp2.mpc ".$dir."tmp3.mpc -background none -layers merge +repage ".$dir.$filename.".mpc; 
			convert ".$dir.$filename.".mpc ( +clone -background black -shadow 80x3+5+5 ) +swap -background none -layers merge  +repage ".$dir.$filename."_full.png";
	
    # remove newlines and convert single quotes to double to prevent errors
	$command = str_replace(array("\n", "'"), array('', '"'), $cmd);
    # replace multiple spaces with one
    $command = preg_replace('#(\s){2,}#is', ' ', $command);
    # escape shell metacharacters
    $command = escapeshellcmd($command);
	$command = str_replace('\;', ';', $command);
	exec($command);

	$im = new Imagick();
	$im->readImage($dir.$filename."_full.png");
	//$im->setImageFormat('MPC');
	$im->scaleImage(BOX_IMG_WIDTH, 0);
	$im->writeImage($dir.$filename.'.png');
	$im->destroy();
	
}
?>