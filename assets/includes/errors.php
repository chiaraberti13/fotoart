<?php
	# SET ERROR REPORTING
	//error_reporting(E_ERROR | E_WARNING | E_PARSE);
	
	if($_GET['cmd'] == 'debug')
	{
		echo "<div style='height: 300px; overflow: auto; font-size: 11px;'>";
			echo "<h1>Config Settings:</h1>";
			foreach($config['settings'] as $key => $value)
			{
				echo "{$key}: <strong>{$value}</strong><br />\n";	
			}
			echo "<hr />\n";
			echo "<h1>Galleries Member Has Permissions To:</h1>";
			foreach($_SESSION['member']['memberPermGalleries'] as $key => $value)
			{
				echo "ID {$value} : <strong>{$_SESSION[galleriesData][$value][name]}</strong><br />\n";	
			}
			echo "<hr />\n";
			echo "<h1>Galleries Member Owns:</h1>";
			foreach($_SESSION['member']['memberOwnedGalleries'] as $key => $value)
			{
				echo "ID {$value} : <strong>{$_SESSION[galleriesData][$value][name]}</strong><br />\n";	
			}
			echo "<hr />\n";
			echo "<h1>Member Permissions Array:</h1>";
			foreach($_SESSION['member']['permmissions'] as $key => $value)
			{
				echo "{$value}<br />\n";	
			}
		echo "</div>";
	}
	
	# OUTPUT ERRORS
	if(!empty($script_error)){
		echo "<span style=\"font-family: verdana; font-size: 12px; color: #ff0000;\">$script_error[0]</span>";
		exit;
	}
?>