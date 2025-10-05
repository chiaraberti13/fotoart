<?php
	//sleep(1);
	
	require_once('../assets/includes/session.php');					# INCLUDE THE SESSION START FILE
	
	//$_SESSION['testing'][] = 'testC';
	
	if(!$_GET['pass']){
		echo "failure";
		exit;
	}
	
	//require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
	if(file_exists("../assets/includes/db.config.php")){			
		require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
	} else { 											
		@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
	}
	require_once('../assets/includes/shared.functions.php');		# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE	
	require_once('../assets/includes/addons.php');					# INCLUDE MANAGER ADDONS FILE		
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
	
	# INCLUDE IMAGETOOLS FILE
	require_once('../assets/classes/imagetools.php');
	
	error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	
	
	//test(md5($config['settings']['access_code']));
	
	if(md5($config['settings']['access_code']) != $_GET['pass']){
		echo "failure";
		exit;
	}
	
	switch($_GET['handler_type'])
	{
		default:
		case "add_media":
			//path to storage
			
			//$upload_dir = $config['settings']['incoming_path'] . "web/";
			$upload_dir = $config['settings']['incoming_path'] . DIRECTORY_SEPARATOR;
			//$storage = $upload_dir;	
			//$filename = strtolower(clean_filename($_FILES['file']['name']));
			$filename = clean_filename($_FILES['file']['name']); // took out string to lower
			$uploadfile = $upload_dir.$filename;
			
			$basefilename = basefilename($filename);
			
			$icon_uploadfile = $upload_dir.basename("icon_".$basefilename.".jpg");
			$thumb_uploadfile = $upload_dir.basename("thumb_".$basefilename.".jpg");
			$sample_uploadfile = $upload_dir.basename("sample_".$basefilename.".jpg");
			
			//test($_FILES['file']['tmp_name']);
			
			//	move original upload
			if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile))
			{
				echo "success";
			}
			else
			{
				echo "failure";
			}
			
			//move icon size
			if(move_uploaded_file($_FILES['icon']['tmp_name'], $icon_uploadfile)){
				echo "success";
			} else{
				echo "failure";
			}
			
			//move thumb size
			if(move_uploaded_file($_FILES['thumb']['tmp_name'], $thumb_uploadfile)){
				echo "success";
			} else{
				echo "failure";
			}
			
			//move sample size
			if(move_uploaded_file($_FILES['sample']['tmp_name'], $sample_uploadfile)){
				echo "success";
			} else{
				echo "failure";
			}
		break;
		case "add_media_flash":
			$upload_dir = $config['settings']['incoming_path'] . DIRECTORY_SEPARATOR;
			$filename = clean_filename($_FILES['Filedata']['name']); // took out string to lower
			$uploadfile = $upload_dir.$filename;			
			$basefilename = basefilename($filename);
			
			//	move original upload
			if(move_uploaded_file($_FILES['Filedata']['tmp_name'], $uploadfile))
			{
				echo "success";
			}
			else
			{
				echo "failure";
			}
						
		break;
		case "add_media_plupload":

			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");

			$targetDir = $config['settings']['incoming_path'] . DIRECTORY_SEPARATOR;
			
			$cleanupTargetDir = true; // Remove old files
			$maxFileAge = 5 * 3600; // Temp file age in seconds
			
			// 5 minutes execution time
			@set_time_limit(5 * 60);
			
			// Uncomment this one to fake upload time
			// usleep(5000);
			
			// Get parameters
			$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
			$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
			$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
			
			// Clean the fileName for security reasons
			$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
							
			// Make sure the fileName is unique but only if chunking is disabled
			if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
				$ext = strrpos($fileName, '.');
				$fileName_a = substr($fileName, 0, $ext);
				$fileName_b = substr($fileName, $ext);
			
				$count = 1;
				while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
					$count++;
			
				$fileName = $fileName_a . '_' . $count . $fileName_b;
			}
			
			$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
			
			// Create target dir
			if (!file_exists($targetDir))
				@mkdir($targetDir);
			
			// Remove old temp files	
			if ($cleanupTargetDir) {
				if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
					while (($file = readdir($dir)) !== false) {
						$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
			
						// Remove temp file if it is older than the max age and is not the current file
						if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
							@unlink($tmpfilePath);
						}
					}
					closedir($dir);
				} else {
					die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
				}
			}	
			
			// Look for the content type header
			if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
				$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
			
			if (isset($_SERVER["CONTENT_TYPE"]))
				$contentType = $_SERVER["CONTENT_TYPE"];
			
			// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
			if (strpos($contentType, "multipart") !== false) {
				if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
					// Open temp file
					$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
					if ($out) {
						// Read binary input stream and append it to temp file
						$in = @fopen($_FILES['file']['tmp_name'], "rb");
			
						if ($in) {
							while ($buff = fread($in, 4096))
								fwrite($out, $buff);
						} else
							die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
						@fclose($in);
						@fclose($out);
						@unlink($_FILES['file']['tmp_name']);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			} else {
				// Open temp file
				$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = @fopen("php://input", "rb");
			
					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			
					@fclose($in);
					@fclose($out);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			}
			
			// Check if file has been uploaded
			if (!$chunks || $chunk == $chunks - 1) {
				// Strip the temp .part suffix off 
				rename("{$filePath}.part", $filePath);

				$fileNameParts = explode(".",$fileName);
				$creatableFiletypes = getCreatableFormats();				
				$iconImage = $targetDir . "icon_" . basefilename($fileName) . ".jpg";
				$thumbImage = $targetDir . "thumb_" . basefilename($fileName) . ".jpg";
				$sampleImage = $targetDir . "sample_" . basefilename($fileName) . ".jpg";
				$filenameExt = strtolower(array_pop($fileNameParts));
				
				# CALCULATE THE MEMORY NEEDED ONLY IF IT IS A CREATABLE FORMAT
				if(in_array(strtolower($filenameExt),$creatableFiletypes))
				{
					# FIGURE MEMORY NEEDED
					$mem_needed = figure_memory_needed($targetDir.$fileName);
					if(ini_get("memory_limit")){
						$memory_limit = ini_get("memory_limit");
					} else {
						$memory_limit = $config['DefaultMemory'];
					}
					# IF IMAGEMAGICK ALLOW TWEAKED MEMORY LIMIT
					if(class_exists('Imagick') and $config['settings']['imageproc'] == 2)
					{
						$memory_limit = $config['DefaultMemory'];
					}
					
					$autoCreateAvailable = 1;
				}	
				
				//test($filenameExt);			
				
				# CHECK TO SEE IF ONE CAN BE CREATED
				if(in_array(strtolower($filenameExt),$creatableFiletypes))
				{
					# CHECK THE MEMORY NEEDED TO CREATE IT
					if($memory_limit > $mem_needed)
					{
						// Create Icon
						$image = new imagetools($targetDir.$fileName);
						$image->setSize($config['IconDefaultSize']);
						$image->setQuality($config['SaveThumbQuality']);
						$image->createImage(0,$iconImage);
						
						// Create Thumb
						$image->setSize($config['ThumbDefaultSize']);
						$image->setQuality($config['SaveThumbQuality']);
						$image->createImage(0,$thumbImage);
						
						// Create Sample
						$image->setSize($config['SampleDefaultSize']);
						$image->setQuality($config['SaveSampleQuality']);
						$image->createImage(0,$sampleImage);
					}
					else
					{
						$errormessage[] = $mgrlang['not_enough_mem'];
					}
				}					
				
				$_SESSION['contrImportFiles'][] = base64_encode($filePath);
			}
			
			die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
			
			
		break;
		case "item_photos_plupload":
			# SWITCH TO LOCAL
			$item_id = $_GET['item_id'];
			$mgrarea = $_GET['mgrarea'];
			
			$item_id_zf = zerofill($item_id,4);
			
			# check for unknown mgrarea
			# DIRECTORY TO MOVE THE PRODUCT SHOTS TO
			$move_to_dir = "../assets/item_photos/";

			
			if($mgrarea == 'gallery') // Reset the gallery icon back to 0 just in case
			{
				updateGalleryVersion(); // Something has changed - update the gallery version
				
				$sql = "UPDATE {$dbinfo[pre]}galleries SET icon=0 WHERE gallery_id = '{$item_id}'";
				$result = mysqli_query($db,$sql);
			}
			
			# INSERT INFO INTO THE DATABASE
			$item_photos_db_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}item_photos WHERE item_id = '$item_id' AND mgrarea = '$mgrarea'");
			$item_photos_db = mysqli_fetch_object($item_photos_db_result);
			$item_photos_db_rows = mysqli_num_rows($item_photos_db_result);
			
			if(!$item_photos_db_rows)
			{
				$sql = "INSERT INTO {$dbinfo[pre]}item_photos (
						item_id,
						mgrarea
						) VALUES (
						'$item_id',
						'$mgrarea'
						)";				
				$result = mysqli_query($db,$sql);			
				$saveid = mysqli_insert_id($db);
				$saveid = zerofill($saveid,4);		
				
			}
			else
			{
				$saveid = zerofill($item_photos_db->ip_id,4);
			}	
			
			$uploadfile = $move_to_dir.$mgrarea.$item_id_zf."_ip".$saveid."_org.jpg"; // Locked to only JPG at this time
			$small_uploadfile = $move_to_dir.$mgrarea.$item_id_zf."_ip".$saveid."_small.jpg"; // 200px
			$medium_uploadfile = $move_to_dir.$mgrarea.$item_id_zf."_ip".$saveid."_med.jpg"; // 500px
			
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");

			$targetDir = $move_to_dir;
			
			$cleanupTargetDir = true; // Remove old files
			$maxFileAge = 5 * 3600; // Temp file age in seconds
			
			// 5 minutes execution time
			@set_time_limit(5 * 60);
			
			// Uncomment this one to fake upload time
			// usleep(5000);
			
			//test(basename($uploadfile));
			//exit;
			
			// Get parameters
			$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
			$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
			$fileName = basename($uploadfile);
			
			// Clean the fileName for security reasons
			$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
							
			// Make sure the fileName is unique but only if chunking is disabled
			if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
				$ext = strrpos($fileName, '.');
				$fileName_a = substr($fileName, 0, $ext);
				$fileName_b = substr($fileName, $ext);
			
				$count = 1;
				while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
					$count++;
			
				$fileName = $fileName_a . '_' . $count . $fileName_b;
			}
			
			$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
			
			// Create target dir
			if (!file_exists($targetDir))
				@mkdir($targetDir);
			
			// Remove old temp files	
			if ($cleanupTargetDir) {
				if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
					while (($file = readdir($dir)) !== false) {
						$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
			
						// Remove temp file if it is older than the max age and is not the current file
						if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
							@unlink($tmpfilePath);
						}
					}
					closedir($dir);
				} else {
					die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
				}
			}	
			
			// Look for the content type header
			if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
				$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
			
			if (isset($_SERVER["CONTENT_TYPE"]))
				$contentType = $_SERVER["CONTENT_TYPE"];
			
			// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
			if (strpos($contentType, "multipart") !== false) {
				if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
					// Open temp file
					$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
					if ($out) {
						// Read binary input stream and append it to temp file
						$in = @fopen($_FILES['file']['tmp_name'], "rb");
			
						if ($in) {
							while ($buff = fread($in, 4096))
								fwrite($out, $buff);
						} else
							die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
						@fclose($in);
						@fclose($out);
						@unlink($_FILES['file']['tmp_name']);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			} else {
				// Open temp file
				$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = @fopen("php://input", "rb");
			
					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			
					@fclose($in);
					@fclose($out);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			}
			
			// Check if file has been uploaded
			if (!$chunks || $chunk == $chunks - 1) {
				// Strip the temp .part suffix off 
				rename("{$filePath}.part", $filePath);
				
				
				
				$fileNameParts = explode(".",$fileName);
				$creatableFiletypes = getCreatableFormats();				
				//$iconImage = $targetDir . "icon_" . basefilename($fileName) . ".jpg";
				//$thumbImage = $targetDir . "thumb_" . basefilename($fileName) . ".jpg";
				//$sampleImage = $targetDir . "sample_" . basefilename($fileName) . ".jpg";
				$filenameExt = strtolower(array_pop($fileNameParts));
				
				# CALCULATE THE MEMORY NEEDED ONLY IF IT IS A CREATABLE FORMAT
				if(in_array(strtolower($filenameExt),$creatableFiletypes))
				{
					# FIGURE MEMORY NEEDED
					$mem_needed = figure_memory_needed($targetDir.$fileName);
					if(ini_get("memory_limit")){
						$memory_limit = ini_get("memory_limit");
					} else {
						$memory_limit = $config['DefaultMemory'];
					}
					# IF IMAGEMAGICK ALLOW TWEAKED MEMORY LIMIT
					if(class_exists('Imagick') and $config['settings']['imageproc'] == 2)
					{
						$memory_limit = $config['DefaultMemory'];
					}
					
					$autoCreateAvailable = 1;
				}	
				
				//test($filenameExt);			
				
				# CHECK TO SEE IF ONE CAN BE CREATED
				if(in_array(strtolower($filenameExt),$creatableFiletypes))
				{
					# CHECK THE MEMORY NEEDED TO CREATE IT
					if($memory_limit > $mem_needed)
					{
						$src = realpath($targetDir.$fileName);
						$image = new imagetools($src);
						$image->size = 200;
						$image->createImage(0,$small_uploadfile);
		
						$image = new imagetools($src);
						$image->size = 500;
						$image->createImage(0,$medium_uploadfile);
						
					}
					else
					{
						$errormessage[] = $mgrlang['not_enough_mem'];
					}
				}					
			}
			
			
			
			die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
			
			/*
			
			// move original upload
			if(move_uploaded_file($_FILES['Filedata']['tmp_name'], $uploadfile))
			{
				$src = realpath($uploadfile);
				$image = new imagetools($src);
				$image->size = 200;
				$image->createImage(0,$small_uploadfile);

				$image = new imagetools($src);
				$image->size = 500;
				$image->createImage(0,$medium_uploadfile);
			}
			else
			{
				echo "failure";
			}
			
			*/
		break;
		
		case "item_photos_flash":
			//sleep(2);
			
			# SWITCH TO LOCAL
			$item_id = $_GET['item_id'];
			$mgrarea = $_GET['mgrarea'];
			
			$item_id_zf = zerofill($item_id,4);
			
			# check for unknown mgrarea
			# DIRECTORY TO MOVE THE PRODUCT SHOTS TO
			$move_to_dir = "../assets/item_photos/";

			//$filename = clean_filename($_FILES['file']['name']); // took out string to lower
			//$new_filename = $mgrarea.$item_id_zf."_org_".$filename;
			//$uploadfile = $move_to_dir.$new_filename ;
			//$filename = $_SESSION['item_photos_page']."_"."item"; 
			
			$basefilename = basefilename($filename);
			
			if($mgrarea == 'gallery') // Reset the gallery icon back to 0 just in case
			{
				updateGalleryVersion(); // Something has changed - update the gallery version
				
				$sql = "UPDATE {$dbinfo[pre]}galleries SET icon=0 WHERE gallery_id = '{$item_id}'";
				$result = mysqli_query($db,$sql);
			}
			
			# INSERT INFO INTO THE DATABASE
			$sql = "INSERT INTO {$dbinfo[pre]}item_photos (
					item_id,
					mgrarea
					) VALUES (
					'$item_id',
					'$mgrarea'
					)";				
			$result = mysqli_query($db,$sql);			
			$saveid = mysqli_insert_id($db);
			$saveid = zerofill($saveid,4);
			
			$uploadfile = $move_to_dir.$mgrarea.$item_id_zf."_ip".$saveid."_org.jpg"; // Locked to only JPG at this time
			$small_uploadfile = $move_to_dir.$mgrarea.$item_id_zf."_ip".$saveid."_small.jpg"; // 200px
			$medium_uploadfile = $move_to_dir.$mgrarea.$item_id_zf."_ip".$saveid."_med.jpg"; // 500px
			
			// move original upload
			if(move_uploaded_file($_FILES['Filedata']['tmp_name'], $uploadfile))
			{
				$src = realpath($uploadfile);
				$image = new imagetools($src);
				$image->size = 200;
				$image->createImage(0,$small_uploadfile);

				$image = new imagetools($src);
				$image->size = 500;
				$image->createImage(0,$medium_uploadfile);
			}
			else
			{
				echo "failure";
			}
			
		break;
		case "item_photos":
			
			//sleep(2);
			
			# SWITCH TO LOCAL
			$item_id = $_SESSION['item_id'];
			$mgrarea = $_SESSION['mgrarea'];
			
			$item_id_zf = zerofill($item_id,4);
			
			# check for unknown mgrarea
			# DIRECTORY TO MOVE THE PRODUCT SHOTS TO
			$move_to_dir = "../assets/item_photos/";

			//$filename = clean_filename($_FILES['file']['name']); // took out string to lower
			//$new_filename = $mgrarea.$item_id_zf."_org_".$filename;
			//$uploadfile = $move_to_dir.$new_filename ;
			//$filename = $_SESSION['item_photos_page']."_"."item"; 
			
			$basefilename = basefilename($filename);
			
			if($mgrarea == 'gallery') // Reset the gallery icon back to 0 just in case
			{
				$sql = "UPDATE {$dbinfo[pre]}galleries SET icon=0 WHERE gallery_id = '{$item_id}'";
				$result = mysqli_query($db,$sql);
			}
			
			# INSERT INFO INTO THE DATABASE
			$sql = "INSERT INTO {$dbinfo[pre]}item_photos (
					item_id,
					mgrarea
					) VALUES (
					'$item_id',
					'$mgrarea'
					)";				
			$result = mysqli_query($db,$sql);			
			$saveid = mysqli_insert_id($db);
			$saveid = zerofill($saveid,4);
			
			$uploadfile = $move_to_dir.$mgrarea.$item_id_zf."_ip".$saveid."_org.jpg"; // Locked to only JPG at this time
			$small_uploadfile = $move_to_dir.$mgrarea.$item_id_zf."_ip".$saveid."_small.jpg"; // 200px
			$medium_uploadfile = $move_to_dir.$mgrarea.$item_id_zf."_ip".$saveid."_med.jpg"; // 500px
			
			// move original upload
			if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile))
			{
				echo "success";
			}
			else
			{
				echo "failure";
			}
			
			// move small size
			if(move_uploaded_file($_FILES['small']['tmp_name'], $small_uploadfile)){
				echo "success";
			} else{
				echo "failure";
			}
			
			// move meduim size
			if(move_uploaded_file($_FILES['medium']['tmp_name'], $medium_uploadfile)){
				echo "success";
			} else{
				echo "failure";
			}
		break;
	}
	
?>