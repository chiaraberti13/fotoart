<?php
	# IF THEY AREN'T ALREADY SET AND RETAIN SORTING IS ON GET THEM FROM THE DATABASE
	if((!isset($_SESSION[$sortprefix.'_listby']) or !isset($_SESSION[$sortprefix.'_listtype'])) and !empty($config['RetainSorting'])){
		# GET FROM DATABASE
		if($datasorts->pull_sort_data()){
			$datasorts->set_listby();
			$datasorts->set_listtype();
			$datasorts->set_perpage();
			
			//echo "records"; exit;
		} else {
			# NO DATABASE - INSERT THE RECORD AND CREATE THE LIST SESSIONS
			$datasorts->db_insert($id_field_name);
			$datasorts->set_listby($id_field_name);
			$datasorts->set_listtype('desc');
		}
	}			
	# CREATE THE SESSION IF IT DOESN'T EXISTS
	if(!isset($_SESSION[$sortprefix.'_listby'])){
		$datasorts->set_listby($id_field_name);
	}		
	# IF LISTBY IS SET IN THE URL ADD IT TO THE SESSION
	if(!empty($_GET['listby'])){
		$datasorts->set_listby($_GET['listby']);
		# UPDATE DATABASE WITH NEW INFO
		$datasorts->db_update($_GET[listby],'','');
	}       
	# CREATE THE SESSION IF IT DOESN'T EXISTS
	if(!isset($_SESSION[$sortprefix.'_listtype'])){
		$datasorts->set_listtype('desc');
	}
	# IF ORDERBY IS SET IN THE URL ADD IT TO THE SESSION
	if(!empty($_GET['listtype'])){
		$datasorts->set_listtype($_GET['listtype']);
		# UPDATE DATABASE WITH NEW INFO
		$datasorts->db_update('',$_GET['listtype'],'');				
	}
	
	# CREATE THE SESSION IF IT DOESN'T EXISTS
	if(!isset($_SESSION[$sortprefix.'_perpage'])){
		$datasorts->set_perpage('25');
	}
	# IF PERPAGE IS SET IN THE URL ADD IT TO THE SESSION
	if(!empty($_GET['perpage'])){
		$datasorts->set_perpage($_GET['perpage']);
		# UPDATE DATABASE WITH NEW INFO
		$datasorts->db_update('','',$_GET['perpage']);				
	}	
	
	# FOR EASE MAKE THE VARIABLE LOCAL
	$perpage = $_SESSION[$sortprefix.'_perpage'];
	# FOR EASE MAKE THE VARIABLE LOCAL
	$listby = $_SESSION[$sortprefix.'_listby'];			
	# FOR EASE MAKE THE VARIABLE LOCAL
	$listtype = $_SESSION[$sortprefix.'_listtype'];
?>