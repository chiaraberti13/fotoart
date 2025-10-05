<?php
	# CHECK STORAGE PATHS
	$sp_result = mysqli_query($db,"SELECT full_path FROM {$dbinfo[pre]}storage_paths");
	while($sp = mysqli_fetch_object($sp_result)){
		# CHECK FOR THE STORAGE PATH
		if(!file_exists($sp->full_path))
			@$script_error[] = $mgrlang['gen_error_01'] . " " . $sp->full_path;
		# CHECK TO MAKE SURE GALLERIES IS WRITABLE
		if(!is_writable($sp->full_path))
			@$script_error[] = $mgrlang['gen_error_02'] . " " . $sp->full_path;
	}
	
	# CHECK TO MAKE SURE FUNCTIONS EXIST
	if(!function_exists('rename'))
		@$script_error[] = $mgrlang['gen_error_12'];
	if(!function_exists('mkdir'))
		@$script_error[] = $mgrlang['gen_error_13'];
	if(!function_exists('rmdir'))
		@$script_error[] = $mgrlang['gen_error_14'];
?>