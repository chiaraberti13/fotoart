<?php
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	
	//sleep(1);
	
	# INCLUDE MANAGER CONFIG FILE
	require_once('mgr.config.php');
	# INCLUDE MANAGER FUNCTIONS FILE
	require_once('mgr.functions.php');
	# INCLUDE THE LANGUAGE FILE	
	include_lang();	
	
	// MAKE SURE THERE IS DATE SENT
	if(!$_GET['lang'] or !$_GET['ldir']){
		echo "$mgrlang[gen_langerr1]!";
		exit;
	}
	
	// TURN TO LOCAL VALUES
	$langs = $_GET['lang'];
	$langdir = ($_GET['ldir'] == 'lang_detailsm') ? "manager" : "public";

	// INCLUDE REGISTER MANAGER LANGUAGE FILES
	include('../assets/includes/reglang.php');
	
	// MAKE SURE THE LANGUAGE SETTING FILE EXISTS
	if(file_exists("../assets/languages/$langs/lang.settings.php")){
		include("../assets/languages/$langs/lang.settings.php");
	} else {
		echo "$mgrlang[gen_langerr2]!";
		exit;
	}
?><img src='images/mgr.lang.arrow.gif' style='float: left; margin-top: 40px' />
<div style="border: 1px solid #d8d7d7; background-color: #f7f7f7;float: left;">
	
	<div style="padding: 4px 4px 4px 8px; color: #ffffff; background-image: url(./images/mgr.table.bar.bg.gif); background-repeat: repeat-x; font-weight: bold;"><?php echo $langset['name']; ?></div>
    <div style="padding: 8px 12px 8px 8px;">
        <?php echo $mgrlang['gen_langfilesXXXX']; ?>Translated Through Version: <strong><?php echo $langset['version']; ?></strong><br /><br />
		
		<?php
			if($langset['translatedBy']) echo "Translated By: <strong>{$langset[translatedBy]}</strong><br /><br />";
		?>
		
		<strong><?php echo $mgrlang['gen_langfiles']; ?>:</strong><br />
    	<?php
			if($langdir == "manager"){
				foreach($regmgrfiles as $value){
					if(file_exists("../assets/languages/" . $langs . "/" . $value)){
						echo "&nbsp;&nbsp;/assets/languages/$langs/$value<br />";
					} else {
						echo "&nbsp;&nbsp;/assets/languages/$langs/$value <span style='color: #b60829; font-weight: bold;'>($mgrlang[gen_missing])</span><br />";
					}			
				}
			} else {
				foreach($regpubfiles as $value){
					if(file_exists("../assets/languages/" . $langs . "/" . $value)){
						echo "&nbsp;&nbsp;/assets/languages/$langs/$value<br />";
					} else {
						echo "&nbsp;&nbsp;/assets/languages/$langs/$value <span style='color: #b60829; font-weight: bold;'>($mgrlang[gen_missing])</span><br />";
					}			
				}
			}
		?>      
    </div>
    <?php
		if($langdir == "manager"){
	?>
    	<div style="padding: 4px 8px 4px 8px; background-color: #fffdcf;"><strong>* <?php echo $mgrlang['languages_defaults']; ?>:</strong><br /> /assets/languages/<?php echo $langs; ?>/lang.settings.php</div>
    <?php
	   	}
	?>
</div>