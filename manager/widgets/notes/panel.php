<?php
	###################################################################
	####	WP: NOTES : VERSION 1.0                                ####
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
		# GRAB THE CALENDAR LANGUAGE FILE
		if(file_exists('../../../assets/languages/'.$panel_language.'/lang.calendar.php'))
		{
			require_once('../../../assets/languages/'.$panel_language.'/lang.calendar.php');
		}
		else
		{
			require_once('../../../assets/languages/english/lang.calendar.php');
		}
	}
	
	# PANEL DETAILS FOR PRELOADING
	$panel_name = $wplang['notes_title'];
	$panel_id = basename(dirname(__FILE__)); // ID OF THE PANEL. NOW USING THE DIRECTORY NAME
	$panel_id = preg_replace("[^A-Za-z0-9]", "", $panel_id); // CLEAN THE PANEL ID JUST IN CASE
	$panel_enable = 1; // ENABLE PANEL
	$panel_version = 1; // THIS SHOULD ALWAYS BE 1 UNLESS OTHERWISE NOTED BY KTOOLS
	$panel_filename = basename(dirname(__FILE__));
	$panel_template = 1; // USE THE PANEL TEMPLATE - CURRENTLY NOT USED BUT MAY BE IN THE FUTURE
	
	switch($panel_mode){
		case "preload";
			# PRELOAD SOME JAVASCRIPT BELOW
			# THIS IS THE OLD WAY OF DOING IT. PREFERABLY THE JAVASCRIPT SHOULD BE IN THE LOAD CASE TO INCREASE INITIAL LOAD TIMES ON THE WELCOME PAGE	
		?>
        	<script language="javascript">
            	function wp_notes_loadlist()
				{
					show_loader('wp_notes_list');
					var loadpage = "widgets/notes/panel.php?panel_mode=notes_list";
					var updatecontent = 'wp_notes_list';
					var myAjax = new Ajax.Updater(updatecontent, loadpage, {evalScripts: true, method: 'get', parameters: ''});
				} 
				function wp_deletenote(note_id)
				{
					if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
					{
						demo_message();
					}
					else
					{
						show_loader('wp_notes_list');
						var url = "widgets/notes/panel.php?panel_mode=delete_note&id="+note_id;
						var myAjax = new Ajax.Request( 
						url, 
						{
							method: 'get', 
							parameters: '',
							evalScripts: true,
							onSuccess: wp_notes_loadlist
						});
					}
				}
				function wp_savenote()
				{
					if("<?php echo $_SESSION['admin_user']['admin_id']; ?>" == "DEMO")
					{
						demo_message2();
					}
					else
					{
						$('wp_notes').request({
							onFailure: function() { alert('failed'); }, 
							onSuccess: function(){ wp_notes_loadlist(); close_workbox(); }
						});
					}
				}
            </script>
        <?php			
		break;
		case "install":
			# INSTALL THE ADD-ON IF NEEDED
		break;
		case "delete_note":
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
			
			mysqli_query($db,"DELETE FROM {$dbinfo[pre]}wp_notes WHERE note_id = '$_GET[id]'");
		break;
		case "savenote":
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
			
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
			require_once('../../../assets/includes/clean.data.php');
			
			# CREATE THE EDIT DATE
			$savedate = gmt_date();
			
			if($_POST['saveid'] == 'new')
			{
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}wp_notes (title,content,date_added,owner,viewable) VALUES ('$title','$content','$savedate','".$_SESSION['admin_user']['admin_id']."','$viewable')";
				$result = mysqli_query($db,$sql);
			}
			else
			{
				$sql = "UPDATE {$dbinfo[pre]}wp_notes SET title='$title',content='$content',date_added='$savedate',owner='".$_SESSION['admin_user']['admin_id']."',viewable='$viewable' WHERE note_id = '$_POST[saveid]'";
				$result = mysqli_query($db,$sql);
			}			
		break;
			
		case "wp_notes_workbox":
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
		
			echo "<form id='wp_notes' action='widgets/notes/panel.php?panel_mode=savenote' method='post'>";
			echo "<div id='wbheader'><p style='float: left;'>{$wplang[notes_newnote]}:</p><p style='float: right; color: #CCC; font-size: 10px; margin-top: 1px;'><img src='images/mgr.button.close2.png' style='border: 0; cursor: pointer;' onclick='close_workbox();'></p></div>";
			echo "<div id='wbbody'>";
				echo "<div class='more_options' style='background: none; width: 708px; background-color: #FFF'>";	
				switch($_GET['mode'])
				{
					case "new":							
						echo "<input type='hidden' value='new' name='saveid' />";						
						echo "<strong>$wplang[widget_for]:</strong><br />";
						echo "<select name='viewable' style='width: 512px;'>";
							echo "<option value='0'>{$mgrlang[gen_wb_everyone]}</option>";
							$admin_result = mysqli_query($db,"SELECT username,admin_id FROM {$dbinfo[pre]}admins");
							while($admin = mysqli_fetch_object($admin_result))
							{
								echo "<option value='$admin->admin_id'";
								//if($admin->admin_id == $_SESSION['admin_user']['admin_id']){ echo "selected='selected'"; }
								echo ">$admin->username</option>";
							}
						echo "</select>";
						echo "<br /><br />";
						echo "<strong>$wplang[widget_title]:</strong><br /><input type='text' name='title' style='width: 500px;' /><br /><br />";
						echo "<strong>$wplang[widget_note]:</strong><br /><textarea name='content' style='width: 500px; height: 200px'></textarea><br /><br />";
					break;
					case "edit":							
						echo "<input type='hidden' value='$_GET[id]' name='saveid' />";
						$note_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}wp_notes WHERE note_id = '$_GET[id]'");
						$note = mysqli_fetch_object($note_result);
						echo "<strong>$wplang[widget_for]:</strong><br />";
						echo "<select name='viewable' style='width: 512px;'>";
							echo "<option value='0'>{$mgrlang[gen_wb_everyone]}</option>";
							$admin_result = mysqli_query($db,"SELECT username,admin_id FROM {$dbinfo[pre]}admins");
							while($admin = mysqli_fetch_object($admin_result))
							{
								echo "<option value='$admin->admin_id'";
								if($admin->admin_id == $note->viewable){ echo "selected='selected'"; }
								echo ">$admin->username</option>";
							}
						echo "</select>";
						echo "<br /><br />";
						echo "<strong>$wplang[widget_title]:</strong><br /><input type='text' name='title' value='$note->title' style='width: 500px;' /><br /><br />";
						echo "<strong>$wplang[widget_note]:</strong><br /><textarea name='content' style='width: 500px; height: 200px'>$note->content</textarea><br /><br />";					
					break;
					case "view":
						# CREATE A DATE OBJECT
						$notedate = new kdate;
						$notedate->distime = 1;
						
						$note_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}wp_notes WHERE note_id = '$_GET[id]'");
						$note = mysqli_fetch_object($note_result);
						
						$admin_result = mysqli_query($db,"SELECT username,admin_id FROM {$dbinfo[pre]}admins");
						$admin = mysqli_fetch_object($admin_result);
						
						echo "<p style='text-align: right; font-size: 11px; color: #666'>$wplang[notes_postedby]: <strong>$admin->username</strong><br />$wplang[notes_lastupdate]: <strong>".$notedate->showdate($note->date_added)."</strong></p>";
						echo "<strong>$note->title</strong><br />$note->content";					
					break;
				}
				echo "</div>";
			echo "</div>";
			echo "<div id='wbfooter' style='padding: 0 8px 20px 20px; margin: 0;'>";
				switch($_GET['mode'])
				{
					case "new":							
						echo "<p style='float: right;'><input type='button' value='$wplang[widget_save]' class='small_button' onclick='wp_savenote();' /><input type='button' value='$wplang[widget_close]' class='small_button' onclick='close_workbox();' /></p>";					
					break;
					case "edit":							
						echo "<p style='float: right;'><input type='button' value='$wplang[widget_save]' class='small_button' onclick='wp_savenote();' /><input type='button' value='$wplang[widget_close]' class='small_button' onclick='close_workbox();' /></p>";					
					break;
					case "view":							
						echo "<p style='float: right;'><input type='button' value='$wplang[widget_close]' class='small_button' onclick='close_workbox();' /></p>";					
					break;

				}
			echo "</div>";
			echo "</form>";
		break;
		case "notes_list":
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
			
			# CREATE A DATE OBJECT
			$notedate = new kdate;
			$notedate->distime = 1;
		?>
        	
				<?php
                    $note_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}wp_notes WHERE viewable='0' OR viewable='".$_SESSION['admin_user']['admin_id']."' ORDER BY date_added DESC");
                    $note_rows = mysqli_num_rows($note_result);
					
					if($note_rows > 0)
					{
						
						echo "<table style='width: 100%' cellspacing='0' cellpadding='10'>";
					
						while($note = mysqli_fetch_object($note_result)){
							
							# SET THE ROW COLOR
							@$row_color++;
							if ($row_color%2 == 0) {
								$color = "FFF";
							} else {
								$color = "EEE";
							}
                ?>
                        <tr style="background-color: #<?php echo $color; ?>; cursor: pointer" id="wp_notes_<?php echo $note->note_id; ?>">
                            <td width="100%" onclick="javascript:workbox2({page: 'widgets/notes/panel.php',pars: 'panel_mode=wp_notes_workbox&mode=view&id=<?php echo $note->note_id; ?>'});"><?php echo $note->title; ?></td>
                            <td nowrap="nowrap" onclick="javascript:workbox2({page: 'widgets/notes/panel.php',pars: 'panel_mode=wp_notes_workbox&mode=view&id=<?php echo $note->note_id; ?>'});"><?php echo $notedate->showdate($note->date_added); ?></td>
                            <td nowrap="nowrap">
                                <a href="javascript:workbox2({page: 'widgets/notes/panel.php',pars: 'panel_mode=wp_notes_workbox&mode=edit&id=<?php echo $note->note_id; ?>'});" class='actionlink'><img src="images/mgr.icon.edit.png" align="absmiddle" alt="<?php echo $mgrlang['gen_edit']; ?>" border="0" /></a>
                                <a href="javascript:wp_deletenote(<?php echo $note->note_id; ?>);" class='actionlink'><img src="images/mgr.icon.delete.png" align="absmiddle" alt="<?php echo $mgrlang['gen_delete']; ?>" border="0" /></a>
                            </td>
                        </tr>
                <?php
						}
						
						echo "</table>";
                    }
					else
					{
						echo "<p style='padding: 10px; background-color: #EEE'>{$wplang[notes_nonotes]}</p>";
					}
                ?>
        <?php
		
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
					name:		'<?php echo $wplang['notes_title']; ?>',
					filename:	'<?php echo $panel_filename; ?>',
					language:	'<?php echo $panel_language; ?>'
				}
				wp_notes_loadlist();
			</script>            
            <div style="overflow: auto;">
                <p style="text-align: right; padding: 10px 10px 10px 6px; border-bottom: 1px solid #d1d0d0"><a href="javascript:workbox2({page: 'widgets/notes/panel.php',pars: 'panel_mode=wp_notes_workbox&mode=new'});" class="actionlink"><?php echo $wplang['notes_newnote']; ?></a></p>
                <div id="wp_notes_list" style="clear: both; overflow: auto"></div>
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
?>