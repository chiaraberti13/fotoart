<?php
	if($r_rows){
	@$perpageinc++;
?>
    <!--<div style="height: 1px; background-color: #CCC"></div> <?php if($mediaPerPageFooter){ echo "position: absolute; bottom: 0;"; } ?>-->
    <div class="perpage_bar" style="clear: both;" id="perpage_bar_<?php echo $perpageinc; ?>">
        <p class="perpage_bar_a"><?php echo $mgrlang['gen_page']; ?> 
            <select id="page<?php echo $perpageinc; ?>" onChange="location.href=$('page<?php echo $perpageinc; ?>').options[$('page<?php echo $perpageinc; ?>').selectedIndex].value">
                <?php
                    for($x=1;$x<=$pages;$x++){
                        $selected = ($x == $_SESSION['currentpage']) ? "selected" : "";
                        echo "<option value=\"$_SERVER[PHP_SELF]?updatepage=$x\" $selected>$x</option>";
                    }
                ?>										
            </select> <?php echo $mgrlang['gen_of']; ?> <?php echo $pages; ?> (<strong><?php echo $r_rows; ?></strong> <?php echo $mgrlang['gen_items']; ?>)
        </p>
        <p class="perpage_bar_b"><?php echo $mgrlang['gen_showing']; ?> 
            <select name="perpage<?php echo $perpageinc; ?>" id="perpage<?php echo $perpageinc; ?>" onChange="<?php if($_SESSION['admin_user']['admin_id'] == "DEMO"){ ?>demo_message();<?php } else { ?>location.href=$('perpage<?php echo $perpageinc; ?>').options[$('perpage<?php echo $perpageinc; ?>').selectedIndex].value<?php } ?>">
                <?php
                    if($page == 'media') // Limit to 50 when on media page
						$perpage_array = array(3,10,25,50,100,500,1000);
					else
						$perpage_array = array(3,10,25,50,100);
						
                    foreach($perpage_array as $value){
                        $selected = ($perpage == $value) ? "selected" : "";
                        echo "<option value=\"$_SERVER[PHP_SELF]?perpage=$value\" $selected>$value</option>";
                    }
                ?>
            </select> <?php echo $mgrlang['gen_perpage']; ?>
        </p>
        <p class="perpage_bar_c">
        	<input type="button" value="<?php echo $mgrlang['gen_b_previous']; ?>" <?php if($_SESSION['currentpage'] <= $pages && $_SESSION['currentpage'] != 1){ echo "onclick=\"location.href='$_SERVER[PHP_SELF]?updatepage=" . ($_SESSION['currentpage'] - 1) . "'\""; } else { echo "disabled"; } ?> /><input type="button" value="<?php echo $mgrlang['gen_b_next']; ?>" <?php if($_SESSION['currentpage'] < $pages){ echo "onclick=\"location.href='$_SERVER[PHP_SELF]?updatepage=" . ($_SESSION['currentpage'] + 1) . "'\""; } else { echo "disabled"; } ?> />
        </p>
    </div>
<?php
	}
?>
