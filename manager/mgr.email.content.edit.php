<?php	
	###################################################################
	####	EMAIL CONTENT EDIT AREA                                ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 8-11-2010                                     ####
	####	Modified: 8-11-2010                                    #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
	
		$page = "email_content";
		$lnav = "content";
		
		$supportPageID = '361';
	
		require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
		require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
		require_once('../assets/includes/tweak.php');					# INCLUDE TWEAK FILE
		if(file_exists("../assets/includes/db.config.php")){			
			require_once('../assets/includes/db.config.php');			# INCLUDE DATABASE CONFIG FILE
		} else { 											
			@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
		}
		require_once('../assets/includes/shared.functions.php');		# INCLUDE SHARED FUNCTIONS FILE
		require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
		error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
		require_once('../assets/includes/db.conn.php');					# INCLUDE DATABASE CONNECTION FILE
		require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
		include_lang();													# INCLUDE THE LANGUAGE FILE	
		require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$content_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}content WHERE content_id = '$_GET[edit]'");
			$content_rows = mysqli_num_rows($content_result);
			$content = mysqli_fetch_object($content_result);
		}
		
		# ACTIONS
		switch($_REQUEST['action']){
			# SAVE EDIT				
			case "save_edit":							
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# SAVE ANY FILES BEING UPLOADED AND RECORD INTO DATABASE
				if($_FILES['uploadedfile']['name'] != ""){
					if(!is_dir("../assets/files/content_files")){
						mkdir("../assets/files/content_files");
					}
					upload_file($_FILES['uploadedfile'],$_FILES['uploadedfile']['name'],"../assets/files/content_files/");
					$fname = $file_details[1];
					$sql = "INSERT INTO {$dbinfo[pre]}content_files (content_id,file_name,type) VALUES ('$saveid','$fname','email')";
					$result = mysqli_query($db,$sql);
				}
				# DELETE ANY FILES THAT WERE UPLOADED
				if($_POST['deleteupload']){
					foreach($_POST['deleteupload'] as $value){
						if($value > 0){
							$deletefile_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}content_files WHERE id = '$value'");
							$deletefile = mysqli_fetch_object($deletefile_result);
							if(file_exists("../assets/files/content_files/$deletefile->file_name")){
								unlink("../assets/files/content_files/$deletefile->file_name");
							}
							$sql="DELETE FROM {$dbinfo[pre]}content_files WHERE id = '$value'";
							$result = mysqli_query($db,$sql);
						}
					}
				}
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				if(in_array('multilang',$installed_addons) and count($active_langs) > 0)
				{
					foreach($active_langs as $value){ 
						$name_val = ${"name_" . $value};
						$addsql.= "name_$value='$name_val',";
						$content_val = ${"contenttext_" . $value};
						$addsql.= "content_$value='$content_val',";
					}
					# STRIP THE TRAILING COMMA
					$addsql = substr($addsql,0,strlen($addsql)-1);
					$addsql = ",".$addsql;
				}
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}content SET 
							name='$name',
							description='$description',
							content='$contenttext'";
				$sql.= $addsql;				
				$sql.= " WHERE content_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
								
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_email_content'],1,$mgrlang['gen_b_ed'] . " > <strong>$name</strong>");
				
				header("location: mgr.email.content.php?mes=edit"); exit;
			break;
			# SAVE NEW ITEM
			case "save_new":
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				if(in_array('multilang',$installed_addons) and count($active_langs) > 0)
				{
					foreach($active_langs as $value){ 
						$name_val = ${"name_" . $value};
						$addsqla.= ",name_$value";
						$addsqlb.= ",'$name_val'";
						$content_val = ${"contenttext_" . $value};
						$addsqla.= ",content_$value";
						$addsqlb.= ",'$content_val'";
					}
				}
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}content (
						name,
						content,
						description,
						ca_id";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$name',
						'$contenttext',
						'$description',
						'5'";
				$sql.= $addsqlb;
				$sql.= ")";
				$result = mysqli_query($db,$sql);
				$saveid = mysqli_insert_id($db);
						
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_email_content'],1,$mgrlang['gen_b_new'] . " > <strong>$name</strong>");
				
				header("location: mgr.email.content.php?mes=new"); exit;
			break;
		}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_media_types']; ?></title>
	<!-- LOAD THE STYLE SHEET -->
	<link rel="stylesheet" href="mgr.style.css" />
	<!--[if lt IE 7.]><script defer type="text/javascript" src="../assets/javascript/pngfix.js"></script><![endif]-->
    <!-- PHP TO JAVASCRIPT VARS -->
    <?php include('mgr.javascript.vars.php'); ?>
	<!-- LOAD PUBLIC AND MANAGER SHARED JAVASCRIPT -->	
	<script type="text/javascript" src="../assets/javascript/shared.min.js"></script>
	<!-- LOAD PROTOTYPE LIBRARY -->	
	<script type="text/javascript" src="../assets/javascript/prototype/prototype.js"></script>
	<!-- LOAD jQUERY -->
	<script type="text/javascript" src="../assets/javascript/jquery/jquery.min.js"></script>
	<script>var $j = jQuery.noConflict();</script>
    <!-- LOAD SCRIPTACULOUS LIBRARY -->   
    <script type="text/javascript" src="../assets/javascript/scriptaculous/scriptaculous.js"></script>
	<!-- GENERIC MGR JAVASCRIPT -->	
	<script type="text/javascript" src="./mgr.min.js"></script>	
	<!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- INCLUDE THE EDITOR JS -->
	<?php include_editor_js(); ?>
	<script language="javascript">	
		function fixspaces(){
			$('url').value = removeSpaces($('url').value);
		}
		function form_sumbit(){
			// REVERT BACK
			$('name_div').className='fs_row_off';
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					$action_link = ($_GET['edit'] == "new") ? "mgr.email.content.edit.php?action=save_new" : "mgr.email.content.edit.php?action=save_edit";
				
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("name","media_type_f_name",1);			
			?>
				// FIX SPACES
				fixspaces();

				//$('data_form').action = "<?php echo $action_link; ?>";
				//$('data_form').submit();
			<?php
				}
			?>
		}
		Event.observe(window, 'load', function()
		{
			// HELP BUTTON
			if($('abutton_help')!=null)
			{
				$('abutton_help').observe('click', function()
					{
						support_popup('<?php echo $supportPageID; ?>');
					});
				$('abutton_help').observe('mouseover', function()
					{
						$('img_help').src='./images/mgr.button.help.png';
					});
				$('abutton_help').observe('mouseout', function()
					{
						$('img_help').src='./images/mgr.button.help.off.png';
					});
			}
			resizefsdiv('content_div',360);
		});	
		
		Event.observe(window, 'resize', function()
			{
				resizefsdiv('content_div',360);
			});	
	</script>
</head>
<body>
	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<?php include("mgr.message.window.php"); ?>
	<div id="container">
		<?php include('mgr.header.php'); ?>
		<?php include('mgr.support.bar.php'); ?>
		<?php include('mgr.shortcuts.cont.php'); ?>
        <!-- START CONTENT CONTAINER -->
        <div id="content_container">
        <?php
            # CHECK FOR DEMO MODE
            //demo_message($_SESSION['admin_user']['admin_id']);					
        ?>
            <form enctype="multipart/form-data" name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_sumbit();">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.email.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['ec_new_header'] : $mgrlang['ec_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['ec_new_message'] : $mgrlang['ec_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content" style="padding: 0px;">                
                <?php $row_color = 0; ?>
                <div id="tab1_group" class="group" style="display: block;">
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.gif" class="ast" />
                        <p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['ec_f_emailsub']; ?>:<br />
                        <span class="input_label_subtext"><?php echo $mgrlang['ec_f_emailsub_d']; ?></span></p>
                        
                        <div class="additional_langs">
                            <input type="text" name="name" id="name" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($content->name); ?>" />
                            <?php
                                if(in_array('multilang',$installed_addons)){
                            ?>
                                &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_name','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                                <div id="lang_name" style="display: none;">
                                <ul>
                                <?php
                                    foreach($active_langs as $value){
                                ?>
                                    <li><input type="text" name="name_<?php echo $value; ?>" id="name_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($content->{"name" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                            <?php
                                    }
                                    echo "</ul></div>";
                                }
                            ?>
                        </div>
                    </div>
                    
					<div class="<?php fs_row_color(); ?>">
						<img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['f_clips']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['f_clips_d']; ?></span>
                        </p>
						<div>
						
<textarea style="width: 600px; height: 135px; background-color: #fdf9d0; border: 0; padding: 10px; border: 1px solid #CCC"><?php include 'mgr.clips.php'; ?></textarea>

						</div>
					</div>
					
                    <div class="<?php fs_row_color(); ?>">
                        
                        
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                            <?php echo $mgrlang['body']; ?>: <br />
                            <span class="input_label_subtext"><?php echo $mgrlang['body_email_d']; ?></span>
                        </p>
                        <div style="float: left;" id="content_div">
                        <?php
                            show_editor("100%","450px",stripslashes($content->content),"contenttext","editor");
                            if(in_array('multilang',$installed_addons)){
                            
                            echo "<div align='right' style='background-color: #e4e3ed; padding: 8px;'>";
                        ?>
                            <span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_article','','','','plusminus-03');"><img src="images/mgr.plusminus.0.png" id="plusminus03" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span></div>
                        <?php
                                echo "<div id='lang_article' style='display: none;'>";
                                foreach($active_langs as $value){ 
                                    show_editor("100%","200px",stripslashes($content->{"content_".$value}),"contenttext_".$value,"editor_".$value);
                                    //echo "<br clear='both'/>";
                                    echo "<div align='right' style='background-color: #e4e3ed; padding: 8px;'><span class='mtag_dblue' style='color: #FFF'>".ucfirst($value)."</span></div>";
                                }
                                echo "</div>";
                            }
                        ?>
                        </div>
                    </div>
					
					<div class="<?php fs_row_color(); ?>">
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['fileupload_title']; ?>:<br />
						<span class="input_label_subtext"><?php echo $mgrlang['fileupload_details']; ?></span></p>
						<input name="uploadedfile" type="file" style="font-size: 11; width: 287">
						<?php
						#GET FILES LIST
						$file_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}content_files WHERE content_id = '$_GET[edit]' AND type = 'email' ORDER BY file_name ASC");
						$file_rows = mysqli_num_rows($file_result);
						if($file_rows > 0){
							?>
							<br><br>
							<p>
								<span style="white-space: nowrap">
									<?php
										echo $mgrlang['fileupload_source']."<br>";
										while($files = mysqli_fetch_object($file_result)){
											echo "<input type='checkbox' name='deleteupload[]' value='$files->id'>&nbsp;<a href='"
											.$config['settings']['site_url'].
											"/assets/files/content_files/"
											.$files->file_name.
											"' target='_blank'>"
											.$config['settings']['site_url'].
											"/assets/files/content_files/"
											.$files->file_name.
											"</a><br>";
										}
									}
							?>
						</span>
					</p>
					</div>
					
					<div class="<?php fs_row_color(); ?>">
						<img src="images/mgr.ast.off.gif" class="ast" />
						<p class="input_label" onclick="support_popup('<?php echo $supportPageID; ?>');"><?php echo $mgrlang['int_notes']; ?>:<br />
						<span class="input_label_subtext"><?php echo $mgrlang['int_notes_d']; ?></span></p>
						<textarea name="description" id="description" style="width: 290px; height: 50px; vertical-align: middle"><?php echo @stripslashes($content->description); ?></textarea>
					</div>
                </div>
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.email.content.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
            </div>
            </form>
            <div class="footer_spacer"></div>
        </div>
        <!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>