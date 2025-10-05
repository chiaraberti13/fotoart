<?php
	# LOOP THRU THE NAV AND SUBNAV ARRAY
	foreach($comp as $key => $value){

		if($value['nav_id'] == $lnav){
			# MAKE SURE THE SUBNAV IS AN ARRAY FIRST
			if(is_array($value['subnav'])){
				foreach($value['subnav'] as $key2 => $value2){
					# ONLY SHOW THE NAV ITEM IF USER HAS ACCESS
					//if( in_array($value2['nav_id'],$_SESSION['admin_user']['permissions']) and !in_array($value2['nav_id'], $hide_nav) ){
					if( (in_array($value2['nav_id'],$_SESSION['admin_user']['permissions']) && !in_array($value2['nav_id'], $hide_nav)) || $value2['nav_id'] == 'boxes' ){
						
						# ADDED FOR POPUP WINDOWS AND JAVASCRIPT LINKS
						$subnavlink_window = ($value2['new_win']) ? "target='_blank'" : "";
						$subnavlink_onclick = ($value2['onclick']) ? "onclick=\"$value2[onclick]\"" : "";						
						$div_link = ($value2['new_win']) ? "window.open('$value2[link]','mywindow','');" : "window.location='$value2[link]'";
						$div_onclick = ($value2['onclick']) ? "{$value2[onclick]}" : $div_link;
						
?>
						
						<div class="subnavlist" onclick="<?php echo $div_onclick; ?>">
							<div class="subnavlist_inner">
                                <!--<img src="images/<?php echo $value2['badge']; ?>" width="40" />-->
                                <div style='width: 40px; height: 60px; background-image:url(images/<?php echo $value2['badge']; ?>); background-repeat: no-repeat; background-position: center; float: left; margin-right: 17px; margin-top: -5px'>&nbsp;</div>
                                <p><a href="<?php echo $value2['link']; ?>" <?php echo $subnavlink_window; ?> <?php echo $subnavlink_onclick; ?>><?php echo $value2['subnav_name']; ?></a><br /><span><?php echo $value2['desc']; ?></span></p>
                            </div>
						</div>
<?php
					}						
				}
			}
		}
	}
?>	
