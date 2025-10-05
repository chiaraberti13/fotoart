<?php
	// Photo Puzzle
	//DEFINE('WORK_DIR', DIR_WS_IMAGES.'temporary/');
	DEFINE('WORK_DIR', BASE_PATH.'/assets/puzzles/temporary/');
	DEFINE('WEB_DIR', 'assets/puzzles/temporary/');
	
	DEFINE('DIR_BOX_MODELS', BASE_PATH.'/assets/puzzles/scatole/');

	//DEFINE('CUSTOMERS_ORDERS_IMG', DIR_WS_IMAGES . 'orders/');
	//DEFINE('DIR_FONT', DIR_FS_CATALOG.'fonts/');
	//DEFINE('DIR_BOX_MODELS', DIR_WS_IMAGES . 'scatole/');
	
	DEFINE('EX_QUALITY', 96);
	DEFINE('GREAT_QUALITY', 72);
	DEFINE('GOOD_QUALITY', 48);
	DEFINE('POOR_QUALITY', 36);
	
	//DEFINE('PREVIEW_WIDTH', 485); //ORIGINALE
    //DEFINE('PREVIEW_HEIGHT', 400); //ORIGINALE
	DEFINE('PREVIEW_WIDTH', 600);
	DEFINE('PREVIEW_HEIGHT', 600);
	//DEFINE('PREVIEW_WIDTH', 800);
	//DEFINE('PREVIEW_HEIGHT', 800);
	DEFINE('THUMB_WIDTH', 160);
	DEFINE('THUMB_HEIGHT', 130);

	//DEFINE('PUZZLES_CATEGORY', 'Foto Puzzle');
	DEFINE('PUZZLES_CATEGORY', "PPZL");
	DEFINE('PUZZLES_BOX_OPTION_NAME', "PPZL_BOX");
	DEFINE('PUZZLES_OTHERS_OPTION_NAME', "PPZL_ALTRE_OPZIONI");
	DEFINE('PUZZLES_PREASSEMBLED_OPTION_NAME', "PPZL_PREASSEMBLATO");
	
	DEFINE('BOX_ATTR_NAME', 'Scatola');
	DEFINE('BOX_IMG_WIDTH', 400);
	
	DEFINE('CHARS_LIMIT', 30);
	DEFINE('TXT_ROWS', 3);
	
	DEFINE('PREASS_ATTR_NAME', 'Preassemblato');
	
	DEFINE('TABLE_PHOTO_PUZZLE', 'photo_puzzle');
	DEFINE('TABLE_PRODUCTS_OPTIONS_VALUES', 'photo_puzzle_products_options_values');
	
	$aBoxStyle = array(	//tutte le unita che non si riferiscono a punti (tipo font size) sono in mm
						'layout' => array(	'width' => 450,
											'height' => 320,	
											'modelFile' => 'nome.pdf',
											'boxBorder' => 41, //dimensione del quadrato angolare 
											'wide' => true,
											'font' => 'monotypecorsiva' //generato con  php tcpdf_addfont.php -i ../../../../fonts/monotype-corsiva.ttf
									),		
						'boxes' => array(	'textxy' => '258;245', 'textsize' => '0;0', 'fontSize' => 55, 'textColor' => '0,0,0,0', 'textBorderColor' => '0,0,0,0', //testo utente

											'pezziSxxy' => '83,918;87,212', 'pezziSxsize' => '0;0', 'pezziSxFontSize' => 60, 'pezziSxFontColor' => '0,0,0,0', 'pezziSxFontBorderColor' => '0,0,0,0',
											
											'pezziBottomxy' => '226;30', 'pezziBottomsize' => '0;0', 'pezziBottomFontSize' => 60, 'pezziBottomFontColor' => '0,0,0,0', 'pezziBottomFontBorderColor' => '0,0,0,0',

											'dimBottomxy' => '105;30', 'dimBottomsize' => '0;0', 'dimBottomFontSize' => 24, 'dimBottomFontColor' => '0,0,0,0', 'dimBottomFontBorderColor' => '0,0,0,0',
											
											'orderxy' => '30;110', 'ordersize' => '0;0', 'orderFontSize' => 14, 'orderFontColor' => '0,0,0,0', 'orderFontBorderColor' => '0,0,0,0'
											
									),
						'immagini' => array(	'imgSize' => '230;162', 'miniSize' => '31,75;22,225',
												'coordinateImg' => '260,669;146,786', 'coordinateImgTop' => '360;292', 'coordinateImgSide' => '30;83',
												'imgBorderSize' => '1', 'imgBorderColor' => '0,0,0,0'
									)
				);
	// Photo Puzzle End
?>