<?php
	###################################################################
	####	MANAGER SEARCH BOX                                     ####
	####	Copyright © 2003-2009 Ktools.net. All Rights Reserved  ####
	####	http://www.ktools.net                                  ####
	####	Created: 9-21-2007                                     ####
	####	Modified: 9-21-2007                                    #### 
	###################################################################
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "search";
		
		# KEEP THE PAGE FROM CACHING
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
	
		# INCLUDE DATABASE CONFIG FILE
		if(file_exists("../assets/includes/db.config.php")){	 require_once('../assets/includes/db.config.php'); } else { @$script_error[] = "The db.config.php file is missing."; }
		
		# INCLUDE DATABASE CONNECTION FILE
		require_once('../assets/includes/db.conn.php');
		
		# INCLUDE SHARED FUNCTIONS FOR CHECKING USERNAME
		require_once('../assets/includes/shared.functions.php');
		
		# INCLUDE MANAGER FUNCTIONS FILE
		require_once('mgr.functions.php');
		
		# SELECT THE SETTINGS DATABASE
		require_once('mgr.select.settings.php');
		
		# INCLUDE THE LANGUAGE FILE	
		include_lang();
		
		//echo "test - {$mgrlang}"; // Testing
		
		if(strlen(trim($_GET['searchphrase'])) < 1)
		{
			echo $mgrlang['gen_search_start'];
			exit;	
		}
		
		$search_words = explode(" ",trim($_GET['searchphrase']));
		
		# MEMBER SEARCH
		$search_word_length = 1;
		$snext = 1;
		foreach($search_words as $value){
			if(strlen($value) >= $search_word_length){
				// ADD OR IF YOU ARE ON THE SECOND TERM ON
				if($snext > 1){ $sql_search.= " or "; }
				$sql_search.= " f_name LIKE '%$value%'";
				$sql_search.= " or l_name LIKE '%$value%'";
				$sql_search.= " or mem_id LIKE '%$value%'";
				$sql_search.= " or umem_id LIKE '%$value%'";
				$sql_search.= " or email LIKE '%$value%'";
				$sql_search.= " or website LIKE '%$value%'";
				/*$sql_search.= " or notes LIKE '%$value%'";*/
				$sql_search.= " or comp_name LIKE '%$value%'";
				//$sql_search.= " or address LIKE '%$value%' or address2 LIKE '%$value%' or city LIKE '%$value%'";
				$snext++;
			}
		}
		//echo $sql_search; exit;
		@$mem_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT(mem_id) FROM {$dbinfo[pre]}members WHERE" . $sql_search));
		
		# ORDER SEARCH
		$sql_search = ""; // Clear the previous search string
		$search_word_length = 1;
		$snext = 1;
		foreach($search_words as $value){
			if(strlen($value) >= $search_word_length){
				// ADD OR IF YOU ARE ON THE SECOND TERM ON
				if($snext > 1){ $sql_search.= " or "; }
				$sql_search.= " {$dbinfo[pre]}members.f_name LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}members.l_name LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}members.mem_id LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}members.umem_id LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}members.email LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}orders.uorder_id LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}orders.order_number LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}invoices.invoice_id LIKE '%$value%'";
				$snext++;				
			}
		}
		@$order_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT({$dbinfo[pre]}orders.uorder_id) FROM ({$dbinfo[pre]}orders LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}orders.member_id = {$dbinfo[pre]}members.mem_id) LEFT JOIN {$dbinfo[pre]}invoices ON {$dbinfo[pre]}orders.order_id = {$dbinfo[pre]}invoices.order_id WHERE $sql_search"));
		
		# MEDIA SEARCH - media, members, keywords
		$sql_search = ""; // Clear the previous search string
		$search_word_length = 1;
		$snext = 1;
		foreach($search_words as $value){
			if(strlen($value) >= $search_word_length){
				// ADD OR IF YOU ARE ON THE SECOND TERM ON
				if($snext > 1){ $sql_search.= " or "; }
				$sql_search.= " {$dbinfo[pre]}members.f_name LIKE '%$value%'";				
				$sql_search.= " or {$dbinfo[pre]}members.l_name LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}members.mem_id LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}members.umem_id LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}members.email LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}media.filename LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}media.title LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}media.description LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}media.media_id LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}media.umedia_id LIKE '%$value%'";
				$sql_search.= " or {$dbinfo[pre]}keywords.keyword LIKE '%$value%'";
				$snext++;				
			}
		}
		//$media_rows = mysqli_result_patch(mysqli_query($db,"SELECT COUNT({$dbinfo[pre]}media.umedia_id) FROM {$dbinfo[pre]}media LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}media.owner = {$dbinfo[pre]}members.mem_id LEFT JOIN {$dbinfo[pre]}keywords ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}keywords.media_id WHERE $sql_search"));
		//echo "SELECT COUNT({$dbinfo[pre]}media.umedia_id) FROM {$dbinfo[pre]}media LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}media.owner = {$dbinfo[pre]}members.mem_id WHERE $sql_search";
		
		@$media_result = mysqli_query($db,"SELECT DISTINCT({$dbinfo[pre]}media.media_id) FROM {$dbinfo[pre]}media LEFT JOIN {$dbinfo[pre]}members ON {$dbinfo[pre]}media.owner = {$dbinfo[pre]}members.mem_id LEFT JOIN {$dbinfo[pre]}keywords ON {$dbinfo[pre]}media.media_id = {$dbinfo[pre]}keywords.media_id WHERE $sql_search");
		@$media_rows = mysqli_num_rows($media_result);
		/*
		echo "<div style='color: #333;'>";
		while($media = mysqli_fetch_object($media_result))
		{
			echo "{$media->media_id}<br />";	
		}
		echo "</div>";
		*/
		
		echo "<div style='float: right;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick=\"javascript:Effect.Fade('search_box',{ duration: 0.7 });\" /></div>";

		//$plus = $_SESSION['plus']++;
		echo "<span>{$mgrlang[gen_search_results]} <strong><em>" . $_GET['searchphrase'] . "</em></strong></span>";
		
		echo "<ul>";
        echo "<li><a href='mgr.members.php?dtype=search&ep=1&search=$_GET[searchphrase]'>{$mgrlang[gen_wb_members]}</a> ({$mem_rows})</li>";
		echo "<li><a href='mgr.orders.php?dtype=search&ep=1&search=$_GET[searchphrase]'>{$mgrlang[gen_orders]}</a> ({$order_rows})</li>";
		echo "<li><a href='mgr.media.php?dtype=search&ep=1&search=$_GET[searchphrase]'>{$mgrlang[gen_medianame_media]}</a> ({$media_rows})</li>";
		echo "</ul>";
		//echo $_GET['search'];
?>