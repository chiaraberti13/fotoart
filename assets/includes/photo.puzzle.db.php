<?php

function getPuzzlesList($outputType){
	
	$query = tep_db_query(	"	SELECT DISTINCT b.products_id, b.products_model, REPLACE(FORMAT(b.products_price ,2),'.',',') as prezzo,  
								(SELECT epf_value FROM extra_field_values WHERE value_id = d.extra_value_id1) as pezzi,
								(SELECT epf_value FROM extra_field_values WHERE value_id = d.extra_value_id4) as dimensioni,
								(SELECT epf_value FROM extra_field_values WHERE value_id = d.extra_value_id6) as shape
								FROM 
								products_to_categories as a
								INNER JOIN
								products as b ON (a.products_id = b.products_id)
								INNER JOIN
								categories_description as c ON (a.categories_id = c.categories_id)
								INNER JOIN
								products_description as d ON (d.products_id = a.products_id)
								WHERE c.categories_name = '".PUZZLES_CATEGORY."'
								AND d.extra_value_id4 != 0
								order by LENGTH(pezzi), pezzi
				");
					
	if(!$outputType) return $query;
	else {
			while($row = tep_db_fetch_array($query)){
				$aPuzzles[] = array(	'id' => $row['products_id'],
										'modello' => $row['products_model'],
										'prezzo' => $row['prezzo'],
										'pezzi' => $row['pezzi'],
										'shape' => $row['shape'],
										'dimensioni' => $row['dimensioni'],
										'larg' => $aDimensioni['larg'],
										'alt' => $aDimensioni['alt']
				);
			echo json_encode($aPuzzles);
		}
	}
	
}



function db_ppuzzles_options(){	//verifico quali solo le opzioni disponibili per i puzzles, escluse le scatole
	
	global $lang;				
	$sesskey = tep_session_id();				
	$queryProductId = tep_db_query("SELECT products_id FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1");
	$aProductId = tep_db_fetch_array($queryProductId);
	$pId = $aProductId['products_id'];

	//$queryGruppoOpzioniScatole = tep_db_query("SELECT og_id FROM ps4_option_grp WHERE parent_id = ".$pId." AND name = '".PUZZLES_BOX_OPTION_NAME."'");
	//$aGruppoOpzioniScatole = tep_db_fetch_array($queryGruppoOpzioniScatole);
	$queryGruppoAltreOpzioni = tep_db_query("SELECT og_id FROM ps4_option_grp WHERE parent_id = ".$pId." AND deleted = 0 AND active = 1 AND name = '".PUZZLES_OTHERS_OPTION_NAME."'");
	$aGruppoAltreOpzioni = tep_db_fetch_array($queryGruppoAltreOpzioni);
	/*
	$query = tep_db_query("	SELECT a.parent_id as id_prodotto, a.og_id as id_gruppo_opzioni, a.name as nome_gruppo, b.op_id as id_opzione, b.name as nome_opzione, REPLACE(FORMAT(b.price, 2), '.', ',') as prezzo
							FROM 
							ps4_option_grp as a
							INNER JOIN
                            ps4_options as b
                            ON (a.og_id = b.parent_id)
                            WHERE a.parent_id = ".$pId." AND a.og_id != ".$aGruppoOpzioniScatole['og_id']."");
	*/
	$query = tep_db_query("	SELECT a.parent_id as id_prodotto, a.og_id as id_gruppo_opzioni, a.name as nome_gruppo, b.op_id as id_opzione, b.name as nome_opzione, REPLACE(FORMAT(b.price, 2), '.', ',') as prezzo
							FROM 
							ps4_option_grp as a
							INNER JOIN
                            ps4_options as b
                            ON (a.og_id = b.parent_id)
                            WHERE a.parent_id = ".$pId." AND a.og_id = ".$aGruppoAltreOpzioni['og_id']." AND b.name = '".$lang['TEXT_PREASS_ATTR_NAME']."' AND b.deleted = 0");
							
	if(!$query) return FALSE; 
	//$aRes = array();
	/*
	while ($row = tep_db_fetch_array($query)) {
		$aRes[] = $row;
	}*/
	//return json_encode($aRes);
	//return $aRes;
	return tep_db_fetch_array($query);
	
}



//function db_boxes_option_id($productId){
function db_boxes_option_id($puzzleId, $boxed){
	
	global $dbinfo;		
	//$query = tep_db_query("SELECT products_options_id FROM ".TABLE_PRODUCTS_OPTIONS." WHERE products_options_name = '".BOX_ATTR_NAME."'");
	//$query = tep_db_query("SELECT og_id FROM {$dbinfo['pre']}option_grp WHERE name = '".PUZZLES_BOX_OPTION_NAME."' AND parent_id = ".$productId."");
	if(!$boxed) {

		$query = tep_db_query("	SELECT op_id, parent_id, REPLACE(FORMAT(price, 2), '.', ',') as price FROM
							{$dbinfo['pre']}options
							WHERE parent_id = (SELECT products_id FROM photo_puzzle WHERE photo_puzzle_id = ".$puzzleId.") ");
			
	} else {
			
		$query = tep_db_query("	SELECT a.op_id, a.parent_id, REPLACE(FORMAT(a.price, 2), '.', ',') FROM
							{$dbinfo['pre']}options as a
							INNER JOIN
							photo_puzzle_ps4_options_to_products_options_values as b ON (a.op_id = b.op_id)
							WHERE b.id = (SELECT box_id FROM photo_puzzle WHERE photo_puzzle_id = ".$puzzleId.") ");
	}
	$aRes = tep_db_fetch_array($query);
	//return (int) $aRes['og_id'];
	return array('op_id' => $aRes['op_id'], 'parent_id' => $aRes['parent_id'], 'price' => $aRes['price']);
	
}



function db_boxes_attr_values(){	
					
	$sesskey = tep_session_id();				
	$queryProductId = tep_db_query("SELECT products_id FROM ".TABLE_PHOTO_PUZZLE." WHERE sesskey = '".$sesskey."' AND current = 1");
	$aProductId = tep_db_fetch_array($queryProductId);

	//$queryProductsOptionsId = tep_db_query("SELECT products_options_id FROM products_options WHERE products_options_name = '".BOX_ATTR_NAME."'");
	//$aProductsOptionsId = tep_db_fetch_array($queryProductsOptionsId);
	/*
	$query = tep_db_query("	SELECT a.products_attributes_id as id, a.options_values_id, REPLACE(FORMAT(a.options_values_price, 2), '.', ',') as prezzo, b.products_options_values_name as json FROM 
							products_attributes as a
							INNER JOIN
							products_options_values as b
							ON a.options_values_id = b.products_options_values_id
							WHERE a.products_id = ".$aProductId['products_id']." and a.options_id = ".$aProductsOptionsId['products_options_id']."
							ORDER BY options_values_price asc"
			);
	*/

	/*$query = tep_db_query("	SELECT a.id, c.parent_id as product_id, REPLACE(FORMAT(c.price, 2), '.', ',') as prezzo, b.products_options_values_name as json
							FROM 
							photo_puzzle_ps4_options_to_products_options_values as a
							INNER JOIN
							photo_puzzle_products_options_values as b
							INNER JOIN
							ps4_options as c
							INNER JOIN
							ps4_option_grp AS d ON (c.parent_id = d.og_id)
							ON (a.products_options_values_id = b.products_options_values_id) AND (a.op_id = c.op_id)
							WHERE d.parent_id = ".$aProductId['products_id']." ");*/
	$query = tep_db_query("	SELECT b.id, REPLACE(FORMAT(c.price, 2), '.', ',') as prezzo, a.products_options_values_name as json
							FROM
							photo_puzzle_products_options_values as a
							INNER JOIN 
							photo_puzzle_ps4_options_to_products_options_values as b ON (a.products_options_values_id = b.products_options_values_id)
							INNER JOIN ps4_options as c ON (b.op_id = c.op_id)
							INNER JOIN ps4_option_grp as d ON (c.parent_id = d.og_id) 
							WHERE a.products_options_group_name = '".PUZZLES_BOX_OPTION_NAME."'
							AND d.parent_id = ".$aProductId['products_id']." 
							AND (c.active = 1 AND c.deleted = 0) AND (d.deleted = 0 AND d.active = 1)");

	$aRes = array();
	while ($row = tep_db_fetch_array($query)) {
		$aJson = json_decode($row['json'], TRUE);
		if($aJson['scatole']) $aRes[] = $row;
	}
	return $aRes;

}



function db_box_attr_values($attributes_id){
	
	global $dbinfo;		
	//$mysqli = mysqli_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);
	$mysqli = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['name']);
	/*$query = "	SELECT a.products_attributes_id as id, b.products_options_values_name as json FROM 
				products_attributes as a
				INNER JOIN
				products_options_values as b
				ON a.options_values_id = b.products_options_values_id
				WHERE a.products_attributes_id = ".$attributes_id."
			";*/
	$query = "	SELECT products_options_values_id as id, products_options_values_name as json FROM 
				photo_puzzle_products_options_values
				WHERE products_options_values_id = (SELECT products_options_values_id FROM photo_puzzle_ps4_options_to_products_options_values WHERE id = ".$attributes_id.")
			";
	//var_dump($query);
	$mysqliResult = $mysqli->query($query);
	$aRes = $mysqliResult->fetch_array();
	$mysqli->close();
	return $aRes;
}



function db_boxes_options_values_id($box_id){

	$query = tep_db_query("SELECT options_values_id FROM products_attributes WHERE products_attributes_id = ".$box_id."");
	$aRes = tep_db_fetch_array($query);
	return $aRes['options_values_id'];

}


/*
function db_add_puzzle($uploaded = FALSE){

	$sesskey = tep_session_id();
	tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET current = 0 WHERE sesskey = '".$sesskey."' AND current != 0");	
	$mysqli = mysqli_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);
	$query = "INSERT INTO ".TABLE_PHOTO_PUZZLE." VALUES(DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, '".$sesskey."', ".($uploaded ? 1 : "DEFAULT").", DEFAULT, DEFAULT, DEFAULT)";
	$result = $mysqli->query($query);
	$id = $mysqli->insert_id;
	return $id; 
	
}
*/


function db_get_img($id){
	
	$query = tep_db_query("SELECT img_file FROM ".TABLE_PHOTO_PUZZLE." WHERE photo_puzzle_id = ".$id."");
	$array = tep_db_fetch_array($query);
	return $array['img_file'];
	
}



function db_add_text($array){
		
	global $dbinfo;
	$sesskey = tep_session_id();
	//$mysqli = mysqli_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);
	$mysqli = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['name']);
	$query = "UPDATE ".TABLE_PHOTO_PUZZLE." SET text_type = 'multi', text = '".$mysqli->real_escape_string(json_encode($array))."' WHERE sesskey = \"".$sesskey."\" AND current = 1";
	$mysqli->query($query);
	$mysqli->close();
	
}

// nuove funzioni per photostore
function db_get_original_path($array) {
	
	global $dbinfo;
	$mysqli = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['name']);
	$sql = "SELECT name,encrypted,enc_name FROM {$dbinfo[pre]}folders WHERE folder_id = {$array[folder_id]}";
	if ( ($result = $mysqli->query($sql)) && $result->num_rows == 1 ) {
		$a = $result->fetch_array(MYSQLI_ASSOC);
		$mysqli->close();	
		return $a;
	};
	
}



function db_get_puzzles_list() {
		
	global $dbinfo;
	$mysqli = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['name']);
	$sql = "SELECT prod_id as products_id, item_name as products_model, price as prezzo, notes as dettagli
			FROM {$dbinfo[pre]}products
			WHERE 
			item_code = '".PUZZLES_CATEGORY."'
			AND deleted = 0 AND active = 1
	";
	
	if ( ($result = $mysqli->query($sql)) && $result->num_rows >= 1 ) {
 //["pezzi"]=> int(100) ["dimensioni"]=> string(10) "28;19.5;cm" ["shape"]=> bool(false) 
 //$jprova = json_encode(array('pezzi' => 100, 'dimensioni' => "28;19.5;cm", 'shape' => false));
 //var_dump($jprova);
 //var_dump(json_decode($jprova, TRUE));
 //print "<HR/>";
		$aResult = array();
		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			//var_dump($row['dettagli']);
			
			$aJson = json_decode($row['dettagli'], TRUE);
			$pezzi = $aJson['pezzi'];
			list($larghezza, $altezza, $unita) = explode(';', $aJson['dimensioni']);
			//$aDimensioni = array('larg' => $larghezza, 'alt' => $altezza);
			$strDimensioni = $larghezza." x ".$altezza.' '.$unita; 
			$shape = ($aJson['shape'] == FALSE ? NULL : $aJson['shape']);
			$aResult[] = array(	'products_id' => $row['products_id'],
								'products_model' => $row['products_model'],
								'prezzo' => $row['prezzo'],
								'pezzi' => $pezzi,
								'dimensioni' => $strDimensioni,
								'shape' => $shape,
								'larg' => $larghezza,
								'alt' => $altezza
						); 
			//var_dump(json_decode($row['dettagli']));
		}

/*		
		foreach ($aRows as $k => $aRow) {
			print "<PRE>";
			//print_r($aRow);
			print "</PRE>";
			$aResult[] = array(	'id' => $aRow['products_id'],
								'modello' => $aRow['products_model'],
								'prezzo' => $aRow['prezzo'],
								//'pezzi' => $aRow['pezzi']
						);
			
		}*/
		/*if(!$outputType) return $query;
		else {
			while($row = tep_db_fetch_array($query)){
				$aPuzzles[] = array(	'id' => $row['products_id'],
										'modello' => $row['products_model'],
										'prezzo' => $row['prezzo'],
										'pezzi' => $row['pezzi'],
										'shape' => $row['shape'],
										'dimensioni' => $row['dimensioni'],
										'larg' => $aDimensioni['larg'],
										'alt' => $aDimensioni['alt']
				);
			echo json_encode($aPuzzles);
		}
		}*/
		
		
		$mysqli->close();
		usort($aResult, function($a, $b) {
    		return $a['pezzi'] - $b['pezzi'];
		});
		return $aResult;
	};
	
}



function db_add_puzzle($uploaded = FALSE, $ps4_path = NULL, $ps4_original_filename = NULL, $media_id = NULL){

	global $dbinfo;
	$sesskey = tep_session_id();
	//tep_db_query("UPDATE ".TABLE_PHOTO_PUZZLE." SET current = 0 WHERE sesskey = '".$sesskey."' AND current != 0");	
	$mysqli = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['name']);
	//$mysqli->query("UPDATE ".TABLE_PHOTO_PUZZLE." SET current = 0 WHERE sesskey = '".$sesskey."' AND current != 0");
	$mysqli->query("UPDATE ".TABLE_PHOTO_PUZZLE." SET current = 0 WHERE sesskey = '".$sesskey."' AND current = 1");
	if (is_null($media_id)) $query = "INSERT INTO ".TABLE_PHOTO_PUZZLE." (sesskey, uploaded) VALUES('".$sesskey."', ".($uploaded ? 1 : "DEFAULT").")";
	else $query = "INSERT INTO ".TABLE_PHOTO_PUZZLE." VALUES(DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT, '".$sesskey."', DEFAULT, DEFAULT, DEFAULT, DEFAULT, '".$ps4_path."', '".$ps4_original_filename."', ".$media_id.")";
	$result = $mysqli->query($query);
	$id = $mysqli->insert_id;
	$mysqli->close();
	return $id; 
	
}



function tep_db_query($query){
	
	global $dbinfo;
	$mysqli = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['name']);
	$result = $mysqli->query($query);
	$mysqli->close();
	return $result;
	
}



function tep_db_fetch_array($db_query) {

	return mysqli_fetch_array($db_query, MYSQLI_ASSOC);
    //return mysql_fetch_array($db_query, MYSQL_ASSOC);

}
?>