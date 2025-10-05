<?php
	###################################################################
	####	WP: KTOOLS NEWS : VERSION 1.0                          ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 2-21-2008                                     ####
	####	Modified: 1-26-2010                                    #### 
	###################################################################

	error_reporting(E_ALL ^ E_NOTICE); // All but notices

	define('BASE_PATH',dirname(dirname(dirname(dirname(__FILE__))))); // Define the base path
	
	# GRAB THE PANEL MODE
	$panel_mode = ($_GET['panel_mode']) ? $_GET['panel_mode']: $panel_mode;

	# INCLUDE THE SESSION FILE IF THE MODE IS OTHER THAN PRELOAD
	if($panel_mode != "preload"){
		require_once('../../../assets/includes/tweak.php');	
		# INCLUDE SESSION FILE
		require_once('../../../assets/includes/session.php');
		$panel_language = ($_SESSION['sess_mgr_lang']) ? $_SESSION['sess_mgr_lang']: 'english';
		# GRAB THE NAME OF THE CURRENT LANGUAGE THAT IS BEING USED
		if(file_exists('../../../assets/languages/'.$panel_language.'/lang.widgets.php'))
		{
			require_once('../../../assets/languages/'.$panel_language.'/lang.widgets.php');
		}
		else
		{
			require_once('../../../assets/languages/english/lang.widgets.php');
		}
	}
	
	# PANEL DETAILS FOR PRELOADING
	$panel_name = $wplang['knews_title'];
	$panel_id = basename(dirname(__FILE__)); // ID OF THE PANEL. NOW USING THE DIRECTORY NAME
	$panel_id = preg_replace("[^A-Za-z0-9]", "", $panel_id); // CLEAN THE PANEL ID JUST IN CASE
	$panel_enable = ($config['BrandAdWidgets']) ? 1 : 0; // ENABLE PANEL
	$panel_version = 1; // THIS SHOULD ALWAYS BE 1 UNLESS OTHERWISE NOTED BY KTOOLS
	$panel_filename = basename(dirname(__FILE__));
	$panel_template = 1; // USE THE PANEL TEMPLATE - CURRENTLY NOT USED BUT MAY BE IN THE FUTURE
	
	switch($panel_mode){
		case "preload";
			# PRELOAD SOME JAVASCRIPT BELOW
			# THIS IS THE OLD WAY OF DOING IT. PREFERABLY THE JAVASCRIPT SHOULD BE IN THE LOAD CASE TO INCREASE INITIAL LOAD TIMES ON THE WELCOME PAGE	
		?>
        	<script language="javascript"></script>
			<!--<script type="text/javascript" src="wpanels/test.js"></script>-->  
           	<style>
				.wp_news_header{
					font-size: 14px;
					margin: 0;
					color: #666;
				}
				.wp_news_row{
					padding: 20px 10px 10px 20px;
					border-bottom: 1px dotted #CCC;
				}
				.wp_news_header a:link,.wp_news_header a:visited{
					color: #666;
					text-decoration: none;
				}
				.wp_news_header a:hover{
					text-decoration: underline;
					color: #06baeb;
				}
				.wp_news_header img{
					margin-right: 4px;
					margin-top: 1px;
				}
				.wp_news_article{
					color: #666;					
					padding: 3px 0 0 14px;
					margin-bottom: 10px;
				}
			</style>
        <?php			
		break;
		case "install":
			# INSTALL THE ADD-ON IF NEEDED
		break;
		case "load":		
			# KEEPS THE PAGE FROM CACHING
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
			# INCLUDE DATABASE CONFIG FILE
			require_once('../../../assets/includes/db.config.php');
			# INCLUDE DATABASE CONNECTION FILE
			require_once('../../../assets/includes/db.conn.php');
			# INCLUDE SHARED FUNCTIONS FILE
			require_once('../../../assets/includes/shared.functions.php');
			# SELECT THE SETTINGS DATABASE
			require_once('../../mgr.select.settings.php');
			
			# OPTIONAL SECURITY - RECOMMENDED
			if((@$_SESSION['access_code'] != @$_SESSION['admin_user']['access_status']) or !isset($_SESSION['admin_user']['access_status']) or !isset($_SESSION['access_code'])){
				echo "<div style='margin: 6px; font-weight: bold; color: #980202;'>$wplang[load_failed]</div>"; exit;
			}
			
			# HERE YOU CAN ALSO CHECK TO SEE IF THE ADD-ON IS INSTALLED			
		?>
        	<script language="javascript">
				// NEEDED TO USE ANY OF THE BUILD IN PANEL FUNCTIONS
				<?php echo $panel_id; ?> = {
					pid:		'<?php echo $panel_id; ?>',
					name:		'<?php echo $wplang['knews_title']; ?>',
					filename:	'<?php echo $panel_filename; ?>',
					language:	'<?php echo $panel_language; ?>'
				}
			</script>         
            <div style="margin: 0;">
                <?php
					if(ini_get("allow_url_fopen")){
						$timeout = 3;
						$old = ini_set('default_socket_timeout', $timeout);
						
						//echo 'http://www.ktools.net/webmgr/push.news.php?product='.$config['productCode'].'&version='.$config['productVersion'].'&builddate='.$config['productBuildDate']; exit;
						
						$dataFile = fopen('http://www.ktools.net/webmgr/push.news.php?product='.$config['productCode'].'&version='.$config['productVersion'].'&builddate='.$config['productBuildDate'], 'r');
						ini_set('default_socket_timeout', $old);
						stream_set_timeout($dataFile, $timeout);
						stream_set_blocking($dataFile, 0);
						
						if ($dataFile){
							while (!feof($dataFile)){
								$buffer = fgets($dataFile, 4096);
								echo $buffer;
							}		
							fclose($dataFile);
						} else {
							die( "fopen failed" ) ;
						}		
					} else {
						echo $mgrlang['gen_error_28'];
					}
				?>
                <!--
                <a href="javascript:panel_reload(<?php echo $panel_id; ?>)">[Reload Panel]</a> - Built-in reload function<br /><br />
                <a href="javascript:panel_loader(<?php echo $panel_id; ?>);">[Panel Loader]</a> - Built-in show loader function<br /><br />                
                <a href="javascript:panel_close(<?php echo $panel_id; ?>);">[Close Panel]</a> - Built-in close panel function<br /><br />
                <a href="javascript:panel_loadpage('','&panel_mode=test&var=1&var2=2',<?php echo $panel_id; ?>);">[Load Page]</a> - Built-in panel load page (page(leave blank to use panel.php),parameters,panel object)<br /><br />
                <a href="javascript:panel_clear(<?php echo $panel_id; ?>);">[Clear Panel]</a><br /><br />
                <a href="javascript:panel_hack(<?php echo $panel_id; ?>);">[Panel Hack Test]</a>
                -->
            </div>
        <?php
		break;
		default:
			# DO NOTHING - NOT LOADED
		break;
		case "test":
			echo "Works!!";
		break;
	}	
?><?php	
		/*
		if(ini_get("allow_url_fopen")){
			$timeout = 3;
			$old = ini_set('default_socket_timeout', $timeout);
			$dataFile = fopen('http://www.ktools.net/wpanels/ps.ktools.news.php', 'r');
			ini_set('default_socket_timeout', $old);
			stream_set_timeout($dataFile, $timeout);
			stream_set_blocking($dataFile, 0);
			
			if ($dataFile){
				while (!feof($dataFile)){
					$buffer = fgets($dataFile, 4096);
					echo $buffer;
				}		
				fclose($dataFile);
			} else {
				die( "fopen failed for $filename" ) ;
			}		
		} else {
			echo "Sorry. Could not load panel because allow_url_fopen is off in PHP.";
		}
		*/
	
	/*
	function getRemoteFile ($host, $method, $path, $data) {
	
		$method = strtoupper($method);        
		
		if ($method == "GET") {
			$path.= '?'.$data;
		}    
		
		$filePointer = @fsockopen($host, 80, $errorNumber, $errorString, 5);
		
		if (!$filePointer) {
			//logEvent('debug', 'Failed opening http socket connection: '.$errorString.' ('.$errorNumber.')<br/>\n');
			return false;
		}
		
		$requestHeader = $method." ".$path."  HTTP/1.1\r\n";
		$requestHeader.= "Host: ".$host."\r\n";
		$requestHeader.= "User-Agent:      Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1) Gecko/20061010 Firefox/2.0\r\n";
		$requestHeader.= "Content-Type: application/x-www-form-urlencoded\r\n";
		
		if ($method == "POST")
		{
		$requestHeader.= "Content-Length: ".strlen($data)."\r\n";
		}
		
		$requestHeader.= "Connection: close\r\n\r\n";
		
		if ($method == "POST")
		{
		$requestHeader.= $data;
		}            
		
		fwrite($filePointer, $requestHeader);
		
		$responseHeader = '';
		$responseContent = '';
		
		do 
		{
		$responseHeader.= fread($filePointer, 1); 
		}
		while (!preg_match('/\\r\\n\\r\\n$/', $responseHeader));
		
		
		if (!strstr($responseHeader, "Transfer-Encoding: chunked"))
		{
		while (!feof($filePointer))
		{
		   $responseContent.= fgets($filePointer, 128);
		}
		}
		else 
		{
		
		while ($chunk_length = hexdec(fgets($filePointer))) 
		{
		   $responseContentChunk = '';
		
		  
		   $read_length = 0;
		   
		   while ($read_length < $chunk_length) 
		   {
			   $responseContentChunk .= fread($filePointer, $chunk_length - $read_length);
			   $read_length = strlen($responseContentChunk);
		   }
		
		   $responseContent.= $responseContentChunk;
		   
		   fgets($filePointer);
		   
		}
		
		}
		
		return chop($responseContent);
		
	}
	*/
?>