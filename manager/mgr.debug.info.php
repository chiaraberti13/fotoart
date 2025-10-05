<?php
	# PAGE END TIMER
	$pltime = microtime();
	$pltime = explode(" ", $pltime);
	$plfinish = $pltime[1] + $pltime[0];
	$pltotaltime = ($plfinish - $plstart);
	
	# SHOW ADMIN ARRAY FOR TESTING				
	//echo "<div class='support_bar'><h1 style='margin: 5px; padding: 0px; font-weight: bold; font-size: 11px;'>$mgrlang[gen_debug]</h1></div>";
	echo "<div id='debug_panel'>";
	
	echo "<p>$mgrlang[gen_pageload] <strong>" . round($pltotaltime,3) . "</strong> $mgrlang[gen_seconds].</p>";
	
	echo "<p><strong>$mgrlang[gen_curpage]:</strong> $page</p>";
	
	echo "<p><strong>$mgrlang[gen_server]:</strong> ".strip_tags($_SERVER['SERVER_SIGNATURE'])."</p>";
	
	echo "<p><strong>$mgrlang[gen_fileex]</strong>: ";
	foreach(getAlldTypeExtensions() as $value){
		echo "$value, ";
	}
	echo "</p>";
	
	echo "<p><strong>$mgrlang[gen_cud]</strong>:<br />";
	
	if($_SESSION['admin_user'])
	{
		foreach($_SESSION['admin_user'] as $key => $value){							
			if(!is_numeric($key)){
				echo "<strong>$key:</strong> ";
				if(is_array($value)){
					foreach($value as $key2 => $value2){
						echo "$value2, ";
					}
					echo "<br />";
				} else {
					 echo "$value<br />";
				}
			}
		}
	}
	echo "</p>";
	
	echo "<p><strong>$mgrlang[gen_settings]</strong>:<br />";
	
	foreach($config['settings'] as $key => $value){							
		if($key != 'sn_code' and $key != 'stats_html')
		{
			if(!is_numeric($key)){
				echo "<strong>$key:</strong> ";
				if(is_array($value)){
					foreach($value as $key2 => $value2){
						echo "$value2, ";
					}
					echo "<br />";
				} else {
					 echo "$value<br />";
				}
			}
		}
	}
	echo "</p>";
	
	echo "<p><strong>$mgrlang[gen_addon]</strong>:<br />";
		sort($installed_addons);
		foreach($installed_addons as $value){
			echo $value.", ";
		}	
	echo "</p>";
	
	echo "</div>";
	//echo "<div style='height: 15px; background-color: #333333'></div>";
?>
