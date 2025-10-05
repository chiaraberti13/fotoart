<?php

if (isset($_POST['f'])) {

	define('BASE_PATH',dirname(dirname(dirname(__FILE__))));
	require_once BASE_PATH.'/assets/includes/session.php';
	require_once BASE_PATH.'/assets/includes/initialize.php';
	
	require_once BASE_PATH.'/assets/includes/commands.php';
	require_once BASE_PATH.'/assets/includes/init.member.php';
	//require_once BASE_PATH.'/assets/includes/security.inc.php';
	require_once BASE_PATH.'/assets/includes/language.inc.php';
	
	require_once BASE_PATH.'/assets/includes/config.php';
	require_once BASE_PATH.'/assets/includes/photo.puzzle.db.php'; 
	require_once BASE_PATH.'/assets/includes/photo.puzzle.func.php'; 
	
	require_once BASE_PATH.'/assets/tcpdf/tcpdf.php';
	require_once BASE_PATH.'/assets/fpdi/fpdi.php';
	
	switch ($_POST['f']) {
					
		case 'manageSession':
			echo manageSession();
			break;
			
		case 'getLimits':
			echo getLimits();
			break;
		
		case 'getRestore':
			echo getRestore($_POST['data']);
			break;
			
		case 'getQuality':
			echo getQuality($_POST['data']['pW'], $_POST['data']['pH'], $_POST['data']['sW'], $_POST['data']['sH'], $_POST['data']['oR'], $_POST['data']['c'], FALSE, $_POST['data']['s']);
			break;
			
		case 'getPuzzles':
			echo getPuzzlesList($_POST['type']);
			break;
			
		case 'getBoxesList':
			echo getBoxesList();
			break;
			
		case 'getBoxes':
			echo getBoxes($_POST['boxId'], $_POST['indice'], $_POST['redraw']);
			break;
			
		case 'submitForm':
			submitForm($_POST['form'], $_POST['data']);
			break;
			
		case 'getOrderDesc':
			echo getOrderDesc((bool)$_POST['boxed'], $_POST['optionId'], $_POST['optionIdValue']);
			break;
			
		case 'updateSession':
			updateSession($_POST['data']);
			break;
		
		case 'getOpzioniProdotto':
			echo getOpzioniProdotto();
			break;
		
		case 'gestisciOpzioniBox':
			gestisciOpzioniBox($_POST['data']);
			break;
			
		case 'mgrGetBoxes':
			echo mgrGetBoxes($_POST['groupId']);
			break;
			
		case 'mgrGetBoxesList':
			echo mgrGetBoxesList();
			break;
		
		case 'mgrDrawBox':
			echo mgrDrawBox($_POST['filename'], $_POST['obj'], $_POST['bFoto'], $_POST['pezzi'], $_POST['dimensioni']);
			break;
	}

} elseif (isset($_FILES['file'])) {

	define('BASE_PATH',dirname(dirname(dirname(__FILE__))));
	require_once BASE_PATH.'/assets/includes/session.php';
	require_once BASE_PATH.'/assets/includes/initialize.php';
	require_once BASE_PATH.'/assets/includes/config.php';
	require_once BASE_PATH.'/assets/includes/photo.puzzle.db.php'; 
	require_once BASE_PATH.'/assets/includes/photo.puzzle.func.php'; 
	
	if($_FILES['file']['error'] == 0) {
		try {
			$im = new Imagick($_FILES["file"]["tmp_name"]);
			$format = $im->getImageFormat();
			if($format == 'PDF') {echo json_encode(array('res' => 'ko', 'detail' => 'attenzione: al momento i pdf non sono supportati')); return;};
			if($im->valid()) {
	
				$aPixelSize = $im->getImageGeometry();
				$imageWidth = $aPixelSize['width'];
				$imageHeight = $aPixelSize['height'];
				$printable = FALSE;
				if($imageWidth > $imageHeight || $imageWidth == $imageHeight) $orientamento = 'landscape';
				else $orientamento = 'portrait';
				
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
				if(!$printable) {echo json_encode(array('res' => 'ko', 'detail' => 'attenzione: la qualità della foto inviata non è idonea alla stampa')); return;}
				else {
					// preparo versioni più leggere dell'immagine
					$preview = clone $im;
					$thumb = clone $im;
					if($orientamento == 'landscape') {
						$preview->scaleImage(PREVIEW_WIDTH, 0);
						$thumb->scaleImage(0, THUMB_HEIGHT);
					}
					else {
						$preview->scaleImage(0, PREVIEW_HEIGHT);
						$thumb->scaleImage(0, THUMB_HEIGHT);
					}

					$sesskey = tep_session_id();
					$puzzleId = db_add_puzzle($uploaded = TRUE);
					switch ($format) {
						case 'JPEG':
							$estensione = '.jpg';
							break;
						case 'PNG':
							$estensione = '.png';
							break;
						case 'TIFF':
							$estensione = '.tiff';
							break;
						case 'GIF':
							$estensione = '.gif';
							break;
					}
					
					$file = $puzzleId.$estensione;
					$path = WORK_DIR.$sesskey.'/'.$puzzleId.'/';

					if(!file_exists($path)) {
						mkdir($path, 0775, true);
						mkdir($path.'preview', 0775, true);
						mkdir($path.'thumb', 0775, true);
						mkdir($path.'boxes', 0775, true);
						mkdir($path.'puzzle', 0775, true);
					}
					
					move_uploaded_file($_FILES["file"]["tmp_name"], $path.$file);
					$preview->writeImage($path.'preview/'.getName($file, 'preview'));
					$thumb->writeImage($path.'thumb/'.getName($file, 'thumb'));
					
					$im->destroy();
					$preview->destroy();
					$thumb->destroy();

					tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET img_file = '".$file."' WHERE photo_puzzle_id = ".$puzzleId."");
					//echo json_encode(array('res' => 'ok', 'trueSize' => array($imageWidth, $imageHeight), 'file' => DIR_WS_IMAGES.'temporary/'.$sesskey.'/'.$puzzleId.'/preview/'.getName($file, 'preview').'?cache='.time(), 'orientation' => $orientamento, 'puzzles' => $aPuzzles));
					echo json_encode(array('res' => 'ok', 'trueSize' => array($imageWidth, $imageHeight), 'file' => WEB_DIR.$sesskey.'/'.$puzzleId.'/preview/'.getName($file, 'preview').'?cache='.time(), 'orientation' => $orientamento, 'puzzles' => $aPuzzles));
				}
			}
		} catch(Exception $e) {echo json_encode(array('res' => 'ko', 'detail' => 'attenzione: errore nell\'invio o tipo di file non supportato'));}//try
	} else echo json_encode(array('res' => 'ko', 'detail' => 'attenzione: errore nell\'invio o tipo di file non supportato'));
	
} elseif ( isset($_GET['mediaID']) ) {
	
	require_once BASE_PATH.'/assets/includes/config.php';
	require_once BASE_PATH.'/assets/includes/photo.puzzle.db.php'; 
	require_once BASE_PATH.'/assets/includes/photo.puzzle.func.php'; 
	
	newMedia($_GET['mediaID']);
	
} else {
	
	define('BASE_PATH',dirname(dirname(dirname(__FILE__))));
	require_once BASE_PATH.'/assets/includes/config.php';
	require_once BASE_PATH.'/assets/includes/photo.puzzle.db.php'; 
	require_once BASE_PATH.'/assets/includes/photo.puzzle.func.php';
	
	require_once BASE_PATH.'/assets/tcpdf/tcpdf.php';
	require_once BASE_PATH.'/assets/fpdi/fpdi.php';
}

		

function newMedia($mediaID) {
			
	global $smarty;
	//global $mediaID;
	global $config;
	global $dbinfo;

	$useMediaID = $mediaID; // Original untouched media ID
	if(!$mediaID) // Make sure a media ID was passed
	{
		$smarty->assign('noAccess',1); //messaggio di errore sul media non valido
		return; 
	}
	else
	{

		$smarty->assign('upload', 0);
		if($config['EncryptIDs']) // Decrypt IDs
		{
			$mediaID = k_decrypt($mediaID);
			//$useGalleryID = k_encrypt($_SESSION['id']);
		}
		else //$useGalleryID = $_SESSION['id'];

		idCheck($mediaID); // Make sure ID is numeric

		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$dbinfo[pre]}media WHERE media_id = '{$mediaID}'";
		$mediaInfo = new mediaList($sql);

		if($mediaInfo->getRows())
		{
			//$media = $mediaInfo->getSingleMediaDetails('preview');
			$media = $mediaInfo->getSingleMediaDetails();
			$aOriginalPath = db_get_original_path($media);
			if($aOriginalPath['encrypted'] == 0) $opath = $aOriginalPath['name'];
			else $opath = $aOriginalPath['enc_name'];

			$originalFile = BASE_PATH.'/assets/library/'.$opath.'/originals/'.$media['filename'];
			$im = new Imagick($originalFile);
		
			$format = $im->getImageFormat();
			//if($format == 'PDF') {echo json_encode(array('res' => 'ko', 'detail' => 'attenzione: al momento i pdf non sono supportati')); return;};
			if($im->valid()) {
	
				$aPixelSize = $im->getImageGeometry();
				$imageWidth = $aPixelSize['width'];
				$imageHeight = $aPixelSize['height'];
				$printable = FALSE;
				if($imageWidth > $imageHeight || $imageWidth == $imageHeight) $orientamento = 'landscape';
				else $orientamento = 'portrait';
				
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

				if(!$printable) {echo json_encode(array('res' => 'ko', 'detail' => 'attenzione: la qualità della foto inviata non è idonea alla stampa')); return;}
				else {

					// preparo versioni più leggere dell'immagine
					$preview = clone $im;
					$thumb = clone $im;
					if($orientamento == 'landscape') {
						$preview->scaleImage(PREVIEW_WIDTH, 0);
						$thumb->scaleImage(0, THUMB_HEIGHT);
					}
					else {
						$preview->scaleImage(0, PREVIEW_HEIGHT);
						$thumb->scaleImage(0, THUMB_HEIGHT);
					}

					$sesskey = tep_session_id();
					$puzzleId = db_add_puzzle(FALSE, $opath, $media['filename'], $mediaID);
					switch ($format) {
						case 'JPEG':
							$estensione = '.jpg';
							break;
						case 'PNG':
							$estensione = '.png';
							break;
						case 'TIFF':
							$estensione = '.tiff';
							break;
						case 'GIF':
							$estensione = '.gif';
							break;
					}
					
					$filePreview = $puzzleId.$estensione;
					$file = substr($media['filename'], 0, strlen($media['filename'])/2).$estensione;
					$path = WORK_DIR.$sesskey.'/'.$puzzleId.'/';

					if(!file_exists($path)) {
						mkdir($path, 0775, true);
						mkdir($path.'preview', 0775, true);
						mkdir($path.'thumb', 0775, true);
						mkdir($path.'boxes', 0775, true);
						mkdir($path.'puzzle', 0775, true);
					}
					
					//move_uploaded_file($_FILES["file"]["tmp_name"], $path.$file); //non posso spostare il file originale per evitarne il download
					$preview->writeImage($path.'preview/'.getName($filePreview, 'preview'));
					$thumb->writeImage($path.'thumb/'.getName($filePreview, 'thumb'));
					
					$im->destroy();
					$preview->destroy();
					$thumb->destroy();

					tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET img_file = '".$file."' WHERE photo_puzzle_id = ".$puzzleId."");
					//echo json_encode(array('res' => 'ok', 'trueSize' => array($imageWidth, $imageHeight), 'file' => DIR_WS_IMAGES.'temporary/'.$sesskey.'/'.$puzzleId.'/preview/'.getName($file, 'preview').'?cache='.time(), 'orientation' => $orientamento, 'puzzles' => $aPuzzles));
					$smarty->assign('file', WEB_DIR.$sesskey.'/'.$puzzleId.'/preview/'.getName($filePreview, 'preview').'?cache='.time());
					$smarty->assign('aPuzzles', $aPuzzles);
				}
			}
		}
		else
			$smarty->assign('noAccess',1);	
		
	}
}

?>