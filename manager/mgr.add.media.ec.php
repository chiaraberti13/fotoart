<?php
// NEED TO MAKE THIS CHECK FOR ALL STORAGE PATHS
	# CHECK FOR THE GALLERIES PATH
	/*
	if(!file_exists($config['settings']['galleries_path']))
		@$script_error[] = $mgrlang['gen_error_01'] . " " . $config['settings']['galleries_path'];
	# CHECK TO MAKE SURE GALLERIES IS WRITABLE
	if(!is_writable($config['settings']['galleries_path']))
		@$script_error[] = $mgrlang['gen_error_02'] . " " . $config['settings']['galleries_path'];
	*/
	# CHECK FOR THE GALLERIES PATH
	if(!file_exists($config['settings']['incoming_path']))
		@$script_error[] = $mgrlang['gen_error_18'] . " " . $config['settings']['incoming_path'];
	# CHECK TO MAKE SURE GALLERIES IS WRITABLE
	if(!is_writable($config['settings']['incoming_path']))
		@$script_error[] = $mgrlang['gen_error_19'] . " " . $config['settings']['incoming_path'];
			
	# CHECK TO MAKE SURE FUNCTIONS EXIST
	if(!function_exists('rename'))
		@$script_error[] = $mgrlang['gen_error_12'];
	if(!function_exists('mkdir'))
		@$script_error[] = $mgrlang['gen_error_13'];
	if(!function_exists('rmdir'))
		@$script_error[] = $mgrlang['gen_error_14'];
		
?>