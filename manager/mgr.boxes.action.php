<?php

if (isset($_POST['function'])) {
	
	switch ($_POST['function']) {
		case 'group_add':
			$trim = trim($_POST['groupName']);
			$nome = empty($trim) ? NULL : $_POST['groupName'];
			if(!is_null($nome)) {
				$foto = is_null($_POST['foto']) ? 1 : 0;
				$aJson = array('desc' => $nome, 'foto' => $foto, 'scatole' => '');
				$json = json_encode($aJson);
				tep_db_query("INSERT INTO photo_puzzle_products_options_values (products_options_group_name, products_options_values_name) VALUES ('".PUZZLES_BOX_OPTION_NAME."', '".$json."')");
			} 
			else $errmsg = "devi specificare un nome per la categoria di scatole da aggiungere";
			break;
			
		case 'group_del':
			(int) $id = $_POST['group_id'];
			//AGGIORNO photo_puzzle_products_options_values
			tep_db_query("UPDATE photo_puzzle_products_options_values SET deleted = 1 WHERE products_options_values_id = ".$id."");
			
			//AGGIORNO photo_puzzle_ps4_options_to_products_options_values
			//tep_db_query("UPDATE photo_puzzle_ps4_options_to_products_options_values SET active = 0, deleted = 1 WHERE products_options_values_id = ".$id."");
			
			//AGGIORNO LE OPZIONI VERE E PROPRIE
			$query = tep_db_query("SELECT op_id FROM photo_puzzle_ps4_options_to_products_options_values WHERE products_options_values_id = ".$id."");
			$aResult = tep_db_fetch_array($query);
			if($aResult) {
				foreach($aResult as $kRow => $op_id) {
					tep_db_query("UPDATE ps4_options SET active = 0, deleted = 1 WHERE op_id = ".(int)$op_id."");
				}
			}
			break;
			
		case 'box_add_menu':
			$id = $_POST['group_id'];
			$newId = getNextId($id);
			$formTable = printEditTable($aBoxStyle, $id, $newId, FALSE);
			break;
			
		case 'box_mod_menu':
			$groupId = $_POST['group_id'];
			//$newId = getNextId($id);
			$boxId = $_POST['box_id'];
			
			$query = tep_db_query("SELECT products_options_values_name FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = ".$groupId."");			
			$aQuery = tep_db_fetch_array($query);
			$products_options_values_name = $aQuery['products_options_values_name'];
			$aJson = json_decode($products_options_values_name, TRUE);
			$aScatole = $aJson['scatole'];
			$aBox = $aScatole[$boxId];
			
			$formTable = printEditTable($aBox, $groupId, $boxId, TRUE);
			break;
		
		case 'box_add':
			$aPost = array();
			foreach ($_POST as $k => $value) {
				if ($k != 'function') $aPost[$k] = $value;
			}

			$query = tep_db_query("SELECT products_options_values_name FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = ".$_POST['optionId']."");			
			$aQuery = tep_db_fetch_array($query);
			$products_options_values_name = $aQuery['products_options_values_name'];
			$aJson = json_decode($products_options_values_name, TRUE);

			$aScatole = $aJson['scatole'];
			$aScatole[] = $aPost;
			$aJson['scatole'] = $aScatole;
			$json = json_encode($aJson);
			tep_db_query("UPDATE ".TABLE_PRODUCTS_OPTIONS_VALUES." SET products_options_values_name = '".$json."' WHERE products_options_values_id = ".$_POST['optionId']."");
			break;
			
		case 'box_edit':
			$aPost = array();
			foreach ($_POST as $k => $value) {
				if ($k != 'function') $aPost[$k] = $value;
			}

			$query = tep_db_query("SELECT products_options_values_name FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = ".$_POST['optionId']."");			
			$aQuery = tep_db_fetch_array($query);
			$products_options_values_name = $aQuery['products_options_values_name'];
			$aJson = json_decode($products_options_values_name, TRUE);

			$aScatole = $aJson['scatole'];
			$aScatole[$aPost['box_id']] = $aPost;
			$aJson['scatole'] = $aScatole;
			$json = json_encode($aJson);
			tep_db_query("UPDATE ".TABLE_PRODUCTS_OPTIONS_VALUES." SET products_options_values_name = '".$json."' WHERE products_options_values_id = ".$_POST['optionId']."");
			break;
			
		case 'box_del':

			$query = tep_db_query("SELECT products_options_values_name FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = ".$_POST['group_id']."");			
			$aQuery = tep_db_fetch_array($query);
			$products_options_values_name = $aQuery['products_options_values_name'];
			$aJson = json_decode($products_options_values_name, TRUE);

			$aScatole = $aJson['scatole'];
			unset($aScatole[$_POST['box_id']]);
			$aJson['scatole'] = $aScatole;
			$json = json_encode($aJson);
			tep_db_query("UPDATE ".TABLE_PRODUCTS_OPTIONS_VALUES." SET products_options_values_name = '".$json."' WHERE products_options_values_id = ".$_POST['group_id']."");
			break;
	}
	
}





	//function drawEditForm($cat_id, $box_id){
	function drawEditForm($group_id){
		
		$query = tep_db_query("SELECT products_options_values_name FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = ".$cat_id."");			
		$aQuery = tep_db_fetch_array($query);

		$oJson = json_decode($aQuery['products_options_values_name']);
		$aJson = (array) $oJson->scatole;
		
		return  printEditTable((array) $aJson[$box_id], $edit = true);
		
	}


	function getNextId($group_id) {
		
		$query = tep_db_query("SELECT products_options_values_name FROM photo_puzzle_products_options_values WHERE products_options_values_id = ".$group_id."");
		$aQuery = tep_db_fetch_array($query);
		
		$aJson = json_decode($aQuery['products_options_values_name'], TRUE);
		$aId = array();
		if(empty($aJson['scatole'])) return 0;
		foreach ($aJson['scatole'] as $array) {
			$aId[] = $array['box_id']; 
		}
		return (max($aId) + 1);
	}



?>