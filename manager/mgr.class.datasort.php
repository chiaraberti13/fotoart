<?php
# DATA SORT CLASS
class data_sorting {
	var $prefix;
	var $db_listby;
	var $db_listtype;
	var $db_perpage;
	var $savedsort_rows;
	
	# CLEAR THE SORTS
	function clear_sorts($ep){	
		global $config;
		# CLEAR THE SORTS IF IT RETAIN SORTING IS OFF AND THIS IS AN ENTRY PAGE
		if($ep == 1 and empty($config['RetainSorting'])){
			unset($_SESSION[$this->prefix.'_listby']);
			unset($_SESSION[$this->prefix.'_listtype']);
		}
	}
	
	# PULLT THE SORT DATA FROM THE DATABASE
	function pull_sort_data(){
		global $dbinfo, $db, $page;
		$savedsort_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}saved_sorting WHERE area_name = '$page' LIMIT 1");
		$savedsort_rows = mysqli_num_rows($savedsort_result);
		$savedsort = mysqli_fetch_object($savedsort_result);
		$this->db_listby = $savedsort->listby;
		$this->db_listtype = $savedsort->listtype;
		$this->db_perpage = $savedsort->perpage;
		
		if($savedsort_rows > 0)	{
			return true;
		} else {
			return false;
		}	
	}
	
	# INSERT THE SORT INTO THE DATABASE
	function db_insert($d){
		global $dbinfo, $page, $db;
		# INSERT INFO INTO THE DATABASE
		$sql = "INSERT INTO {$dbinfo[pre]}saved_sorting (
				area_name,listby,listtype
				) VALUES (
				'$page','$d','desc'
				)";
		$result = mysqli_query($db,$sql);
	}	
	
	function db_update($new_lb,$new_lt,$new_pp){
		global $dbinfo, $page, $db;
		# UPDATE THE DATABASE
		if($new_lb){
			$sql = "UPDATE {$dbinfo[pre]}saved_sorting SET listby = '$new_lb' WHERE area_name = '$page' LIMIT 1";
			$result = mysqli_query($db,$sql);
		}
		if($new_lt){
			$sql = "UPDATE {$dbinfo[pre]}saved_sorting SET listtype = '$new_lt' WHERE area_name = '$page' LIMIT 1";
			$result = mysqli_query($db,$sql);
		}
		if($new_pp){
			$sql = "UPDATE {$dbinfo[pre]}saved_sorting SET perpage = '$new_pp' WHERE area_name = '$page' LIMIT 1";
			$result = mysqli_query($db,$sql);
		}
	}

	# SET THE LIST BY SESSION
	function set_listby($d=''){
		//session_register($this->prefix.'_listby');
		if(!empty($d)){
			$_SESSION[$this->prefix.'_listby'] = $d;
		} else {				
			$_SESSION[$this->prefix.'_listby'] = $this->db_listby;
		}
	}
	
	# SET THE LIST TYPE SESSION
	function set_listtype($d=''){
		//session_register($this->prefix.'_listtype');
		if(!empty($d)){
			$_SESSION[$this->prefix.'_listtype'] = $d;
		} else {				
			$_SESSION[$this->prefix.'_listtype'] = $this->db_listtype;
		}
	}
	
	# SET THE LIST TYPE SESSION
	function set_perpage($d=''){
		//session_register($this->prefix.'_perpage');
		if(!empty($d)){
			$_SESSION[$this->prefix.'_perpage'] = $d;
		} else {				
			$_SESSION[$this->prefix.'_perpage'] = $this->db_perpage;
		}
		$_SESSION['currentpage'] = 1;
	}						
}
?>