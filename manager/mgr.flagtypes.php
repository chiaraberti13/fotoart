<?php
	//sleep(2);
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	
	$start = ($_GET['start'])? $_GET['start']: 0;
	
	echo "<div style='height: 165px; padding: 3px'>";
	
	$dir = opendir("images/mini_icons/");
	# LOOP THROUGH THE FLAG DIRECTORY
	$records=2;
	$go27 = 0;
	while($file = readdir($dir)){
		if($file != ".." and $file != "." and $records >= $start and $go27 < 78 and is_file("images/mini_icons/" . $file)){
			echo "<div style='float: left;' class='flagbutton'><a href='#' onclick=\"$('flagswap').src='images/mini_icons/" . $file . "';$('icon').value='" . $file . "';$('icon').checked='checked'\" ><img src='images/mini_icons/$file' border='0' alt='$file' style='margin: 1px;' width='16' height='16'></a></div>";
			$go27++;
		}
		$records++;
	}
	closedir($dir);
	
	$records-=5;
	
	//$pages = round($records/78)+1;
	$pages = round($records/78);
	
	echo "</div>";
?>
<div style="text-align: right; padding: 4px; border-top: 1px solid #eee; border-bottom: 1px solid #ffffff; margin-top: 4px; font-size: 10px; background-color: #eeeeee; background-image: url(images/mgr.tabarea.fade.gif); background-repeat: repeat-x">
    <div style="float: left; padding-top: 0px;">
    	<?php
			$numstart=0;		
        	for($x=1;$x<=$pages;$x++){
				echo "<a href=\"javascript:load_r_box('" . ($numstart) . "','flagbox','mgr.flagtypes.php');\" style='float: left; padding: 2px 4px 2px 4px; margin: 1px;' ";
				if($numstart == $start){
					echo "class='actionlink_off' ";
				} else {
					echo "class='actionlink' ";
				}
				echo "style='margin: 1px;'>$x</a>";
				$numstart+=78;
			}
		?>
    </div>
    <input type="button" value=" < " <?php if($start > 0){ echo "onclick=\"load_r_box('" . ($start - 78) . "','flagbox','mgr.flagtypes.php');\""; } else { echo "disabled"; } ?> /><input type="button" value=" > " <?php if(($start+78) < $records){ echo "onclick=\"load_r_box('" . ($start+78) . "','flagbox','mgr.flagtypes.php');\""; } else { echo "disabled"; } ?> />
</div>