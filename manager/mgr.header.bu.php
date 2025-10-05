<div style="position: absolute; right: 0; top: -1px;"><a href=""><img src="images/mgr.pubwebsite.png" style="border: 0;" /></a></div>
<div id="header_a">
	<div style="float: left; color: #acacac; margin: 28px 0 0 0;">
        <img src="images/mgr.ps.logo.png" align="absmiddle" style="margin: -2px 3px 0 -2px;" /><?php  echo $manager_header_title; ?>
    </div>
	<div style="float: right;" class="header_login">
    	<img src="images/mgr.icon.loggedin.png" align="texttop" /> <?php echo $mgrlang['gen_logged_as']; ?> <span style="font-weight: bold; color: #86f9fd;"><?php echo $_SESSION['admin_user']['username']; ?></span><br /><a href="mgr.login.php?notice=logged_out"><?php echo $mgrlang['gen_logout']; ?></a> | <a href="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ echo "javascript:demo_message();"; } else { echo "mgr.administrators.edit.php?edit=".$_SESSION['admin_user']['uadmin_id']; }  ?>"><?php echo $mgrlang['edit_prof']; ?></a>
    </div>
</div>
<div id="header_b">
	<!--<div style="float: left;"><img src="images/mgr.header.left.gif" /></div>-->
	<div style="padding-left: 8px;">
		<?php
			if($lnav == "welcome"){
				echo "<div class=\"mnb_on\"><a href=\"mgr.welcome.php\">$mgrlang[nav_home]</a></div>";
			} else {
				echo "<div class=\"mnb_off\" id='nav_home' onmouseover=\"$('nav_home').className='mnb_on'\" onmouseout=\"$('nav_home').className='mnb_off'\"><a href=\"mgr.welcome.php\">$mgrlang[nav_home]</a></div>";
				//echo "<div style=\"float: left;\"><img src=\"images/mgr.header.div.short.gif\" style=\"margin-top: 7px;\" /></div>";
			}
			# READ NAV FUNCTION
			read_nav();
			# SORT THE ARRAY
			sort($comp);
			
			foreach($comp as $key => $value){
				if(in_array($value['nav_id'],$_SESSION['admin_user']['permissions']) and !in_array($value['nav_id'], $hide_nav)){
					$nclass = ($value['nav_id'] == $lnav) ? "mnb_on" : "mnb_off";
						echo "<div class=\"$nclass\" id='nav_$value[nav_id]' onmouseover=\"";
						# CHECK TO SEE IF THERE IS SUBNAV
						if(!empty($value['subnav'])){
							echo "show_div_delay('dd_$value[nav_id]');";
						}
						echo "\$('nav_$value[nav_id]').className='mnb_on'\" onmouseout=\"";
						# CHECK TO SEE IF THERE IS SUBNAV
						if(!empty($value['subnav'])){
							echo "hide_div_delay('dd_$value[nav_id]','250');";
						}
						echo "\$('nav_$value[nav_id]').className='$nclass'\">";					
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
						echo "<a href=\"$value[link]?ep=1\">$value[nav_name]</a>";
						echo "</div>";
						
					if($value['nav_id'] != $lnav){
					//echo "<div style=\"float: left;\"><img src=\"images/mgr.header.div.short.gif\" style=\"margin-top: 7px;\" /></div>";
					}
				}
			}
			
			# MY LINKS AREA
			$tldd_result = mysqli_query($db,"SELECT tl_name,tl_link,tl_target FROM {$dbinfo[pre]}toolslinks WHERE tl_owner = '0' OR tl_owner = '".$_SESSION['admin_user']['admin_id']."'");
			$nclass = ($lnav == 'mylinks') ? "mnb_on" : "mnb_off";
			echo "<div class=\"$nclass\" id='nav_mylinks' onmouseover=\"show_div_delay('dd_mylinks'); \$('nav_mylinks').className='mnb_on'\" onmouseout=\"hide_div_delay('dd_mylinks','250'); \$('nav_mylinks').className='$nclass'\">";
				echo "<div id='dd_mylinks' align='left' class='subnav_dd'>";		
					while($tldd = mysqli_fetch_object($tldd_result)){
						echo "<a href=\"$tldd->tl_link\" class=\"toolslinks\" target=\"$tldd->tl_target\">" . substr($tldd->tl_name,0,35) . "</a>";
					}
					echo "<a href=\"mgr.toolslinks.php\" class=\"toolslinks\" target=\"_self\">$mgrlang[gen_editlinks]</a>";
				echo "</div>";
			echo "<a href=\"mgr.toolslinks.php?ep=1\">$mgrlang[subnav_toolslinks]</a>";
			echo "</div>";
		?>
	</div>  
</div>