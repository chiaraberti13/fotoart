<div style="position: absolute; right: 0; top: -2px;"><a href="../actions.php?action=memberSessionDestroy" target="_blank"><img src="images/mgr.pubwebsite.png" style="border: 0;" /></a></div>
<div id="header_a">
	<div style="float: left; color: #828282; margin: 12px 0 0 0;">
        <!--<img src="images/mgr.ps.logo.png" align="absmiddle" style="margin: -4px 3px 0 -2px;" />--><?php  echo $manager_header_title; ?>
    </div>
	
	<div style="float: right; margin: 10px 20px 0 4px" class="header_login">
    	<img src="images/mgr.icon.loggedin.png" align="texttop" /> <?php echo $mgrlang['gen_logged_as']; ?> <span style="font-weight: bold; color: #51c4ed;"><?php echo $_SESSION['admin_user']['username']; ?></span> <a href="mgr.login.php?notice=logged_out"><?php echo $mgrlang['gen_logout']; ?></a> | <a href="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "javascript:demo_message();"; } else { echo "mgr.administrators.edit.php?edit=".$_SESSION['admin_user']['uadmin_id']; }  ?>"><?php echo $mgrlang['edit_prof']; ?></a>
    </div>
	
	<div style="float: right; border-right: 1px solid #666; margin-top: 12px;">&nbsp;</div>
	
	<div style="float: right; margin: 18px 10px 0 20px;">
    	<div id="alerts" style="display: none;"></div>
        <img src="images/mgr.alert.icon.gray.png" onclick="load_alerts_b();" id="alerticon" border="0" style="margin: 0; cursor: pointer;" align="absmiddle" alt="<?php echo $mgrlang['gen_alerts']; ?>">
    </div>
	
	<div style="float: right; border-right: 1px solid #666; margin-top: 12px;">&nbsp;</div>
	
	<?php
		if(count($_SESSION['sesalerts']) > 0){
			echo "<script language='javascript' type='text/javascript'>$('alerticon').src='images/mgr.alert.icon.png';</script>\n";
		}
	?>
	
	<div style="float: right; margin: 14px 10px 0 0; font-size: 11px;">
    	<form method="get" style="margin: 0px;"><input type="text" name="search_phrase" id="search_phrase" value="Search..." /></form>
    	<div id="search_box" style="display: none;">
        	<img src="images/mgr.alert.point.png" style="position: absolute; margin: -18px 0 0 67px;" />
            <!--<p><a href="#" onclick="javascript:Effect.Fade('search_box',{ duration: 0.7 });"><img src="images/mgr.search.box.close.gif" align="right" border="0" /></a><span style="color: #8d9093; font-weight: bold; font-size: 14px;"><?php echo $mgrlang['gen_search_quick']; ?></span></p>-->
            <div id="search_box_results">
                <div style='float: right;'><img src="images/mgr.button.close2.png" style="border: 0; cursor: pointer;" onclick="javascript:Effect.Fade('search_box',{ duration: 0.7 });" /></div>
                <span><?php echo $mgrlang['gen_search_start']; ?></span>
            </div>
        </div>
    </div>
	
</div>
<div id="header_b">
	<!--<div style="float: left;"><img src="images/mgr.header.left.gif" /></div>-->
	<div style="padding-left: 8px;">
		<?php
			if($lnav == "welcome"){
				echo "<div class=\"mnb_on\"><a href=\"mgr.welcome.php\">$mgrlang[nav_home]</a></div>";
			} else {
				echo "<div class=\"mnb_off\" id='nav_home'><a href=\"mgr.welcome.php\" class='mnb_link'>$mgrlang[nav_home]</a></div>";
				//echo "<div style=\"float: left;\"><img src=\"images/mgr.header.div.short.gif\" style=\"margin-top: 7px;\" /></div>";
			}
			# READ NAV FUNCTION
			read_nav();
			# SORT THE ARRAY
			sort($comp);
			
			foreach($comp as $key => $value){
				if(in_array($value['nav_id'],$_SESSION['admin_user']['permissions']) and !in_array($value['nav_id'], $hide_nav)){
					$nclass = ($value['nav_id'] == $lnav) ? "mnb_on" : "mnb_off";
						echo "<div class=\"$nclass\" id='nav_$value[nav_id]'>";
							if(is_array($value['subnav'])){
								echo "<div id='dd_$value[nav_id]' align='left' class='subnav_dd'>";								
									foreach($value['subnav'] as $key2 => $value2){
										# ONLY SHOW THE NAV ITEM IF USER HAS ACCESS
										if(in_array($value2['nav_id'],$_SESSION['admin_user']['permissions']) and !in_array($value2['nav_id'], $hide_nav)){
											$subnavlink_window = ($value2['new_win']) ? "target='_blank'" : "";
											$subnavlink_onclick = ($value2['onclick']) ? "onclick=\"$value2[onclick]\"" : "";
											echo "<a href=\"$value2[link]\" class='toolslinks' $subnavlink_window $subnavlink_onclick>$value2[subnav_name]";
											
											echo "<span class='pending_number' id='hnp_$value2[nav_id]'";
											if($_SESSION['pending_'.$value2['nav_id']] == 0){ echo " style='display: none;'"; }
											echo ">".$_SESSION['pending_'.$value2['nav_id']]."</span>";
											
											echo "</a>";
										}						
									}								
								echo "</div>";
							}
						echo "<a href=\"$value[link]?ep=1\" class='mnb_link'>$value[nav_name]</a>";
						echo "</div>";
				}
			}
			
			/*
			# MY LINKS AREA
			$tldd_result = mysqli_query($db,"SELECT tl_name,tl_link,tl_target FROM {$dbinfo[pre]}toolslinks WHERE tl_owner = '0' OR tl_owner = '".$_SESSION['admin_user']['admin_id']."'");
			$nclass = ($lnav == 'mylinks') ? "mnb_on" : "mnb_off";
			echo "<div class='{$nclass}' id='nav_mylinks'>";
				echo "<div id='dd_mylinks' align='left' class='subnav_dd'>";		
					while($tldd = mysqli_fetch_object($tldd_result)){
						echo "<a href='{$tldd->tl_link}' class='toolslinks' target='{$tldd->tl_target}'>" . substr($tldd->tl_name,0,35) . "</a>";
					}
					echo "<a href='mgr.toolslinks.php' class='toolslinks' target='_self'>{$mgrlang[gen_editlinks]}</a>";
				echo "</div>";
			echo "<a href='mgr.toolslinks.php?ep=1' class='mnb_link'>{$mgrlang[subnav_toolslinks]}</a>";
			echo "</div>";
			*/
		?>
	</div>  
</div>