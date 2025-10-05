<div id="nav">
	<ul>
		<?php
			# LOOP THRU THE NAV AND SUBNAV ARRAY
			foreach($comp as $key => $value){
				if($value['nav_id'] == $lnav){
					# MAKE SURE THE SUBNAV IS AN ARRAY FIRST
					if(is_array($value['subnav'])){
						foreach($value['subnav'] as $key2 => $value2){
							# ONLY SHOW THE NAV ITEM IF USER HAS ACCESS
							if(in_array($value2['nav_id'],$_SESSION['admin_user']['permissions']) and !in_array($value2['nav_id'], $hide_nav)){
								echo "<li><a href=\"$value2[link]?ep=1\" class=\"subnav\">$value2[subnav_name]</a>";
							}						
						}
					}
				}
			}			
		?>	
	</ul>
</div>