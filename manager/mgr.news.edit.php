<?php
	###################################################################
	####	MANAGER NEWS EDIT AREA                                 ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 9-7-2006                                      ####
	####	Modified: 12-8-2009                                    #### 
	###################################################################
	
		$page = "news";
		$lnav = "content";		
		$supportPageID = '363';
	
		require_once('../assets/includes/session.php');					# INCLUDE THE SESSION START FILE
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
		require_once('../assets/includes/addons.php');					# INCLUDE MANAGER ADDONS FILE		
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON	
		
		$ndate = new kdate;
		$ndate->distime = 1;
		
		# IF EDITING GET THE INFO FROM THE DATABASE		
		if($_GET['edit'] != "new" and !$_REQUEST['action']){
			$news_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}news WHERE news_id = '$_GET[edit]'");
			$news_rows = mysqli_num_rows($news_result);
			$news = mysqli_fetch_object($news_result);
		}
		
		# ACTIONS
		if($_REQUEST['action'] == "save_edit" or $_REQUEST['action'] == "save_new" ){
			# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA
			require_once('../assets/includes/clean.data.php');
			
			# SAVE ANY FILES BEING UPLOADED AND RECORD INTO DATABASE
			if($_FILES['uploadedfile']['name'] != ""){
				if(!is_dir("../assets/files/content_files")){
					mkdir("../assets/files/content_files");
				}
				upload_file($_FILES['uploadedfile'],$_FILES['uploadedfile']['name'],"../assets/files/content_files/");
				$fname = $file_details[1];
				$sql = "INSERT INTO {$dbinfo[pre]}content_files (content_id,file_name,type) VALUES ('$saveid','$fname','news')";
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
				
			$save_date = $_POST['created_year']."-".$_POST['created_month']."-".$_POST['created_day']." " .$_POST['created_hour']. ":" .$_POST['created_minute']. ":00";
			
			$expire_date = $_POST['expire_year']."-".$_POST['expire_month']."-".$_POST['expire_day']." 00:00:00";
			$save_date = $ndate->formdate_to_gmt($save_date);
			//echo $save_date; exit; // Testing
			$expire_date = $ndate->formdate_to_gmt($expire_date);
			
			# SAVE EDIT				
			if($_REQUEST['action'] == "save_edit"){				
				//$save_date = $_POST['created_year']."-".$_POST['created_month']."-".$_POST['created_day']." " .$_POST['created_hour']. ":" .$_POST['created_minute']. ":00";								
				
				//$save_date = $ndate->formdate_to_gmt($save_date);
				
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				
				
				//$plangroups = ($setgroups) ? "," . implode(",",$setgroups) . "," : "";
				
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value){ 
					$title_val = ${"title_" . $value};
					$short_val = ${"short_" . $value};
					$article_val = ${"article_" . $value};
					$addsql.= "title_$value='$title_val',";
					$addsql.= "short_$value='$short_val',";
					$addsql.= "article_$value='$article_val',";
				}
				
				# UPDATE THE DATABASE
				$sql = "UPDATE {$dbinfo[pre]}news SET 
							title='$title',
							short='$short',
							article='$article',
							expire_type='$expire_type',
							expire_date='$expire_date',
							newsgroups='$plangroups',";
				$sql.= $addsql;				
				$sql.= "	homepage='$homepage',
							active='$active',
							add_date='$save_date'
							where news_id  = '$saveid'";
				$result = mysqli_query($db,$sql);
				
				# DELETE GROUPS FIRST
				mysqli_query($db,"DELETE FROM {$dbinfo[pre]}groupids WHERE item_id = '$saveid' AND mgrarea = '$page'");
				# ADD GROUPS
				if($setgroups){
					foreach($setgroups as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_news'],1,$mgrlang['gen_b_ed'] . " > <strong>$title</strong>");
				
				header("location: mgr.news.php?mes=edit"); exit;
			}
			# SAVE NEW ITEM
			if($_REQUEST['action'] == "save_new"){			
				$save_date = $_POST['created_year']."-".$_POST['created_month']."-".$_POST['created_day']." " .$_POST['created_hour']. ":" .$_POST['created_minute']. ":00";
				$save_date = $ndate->formdate_to_gmt($save_date);
				
				$expire_date = $_POST['expire_year']."-".$_POST['expire_month']."-".$_POST['expire_day']." 00:00:00";
				$expire_date = $ndate->formdate_to_gmt($expire_date);
				
				//echo $save_date; exit; // testing
				
				# CONVERT POST & GET ARRAYS TO LOCAL VALUES AND CLEAN DATA				
				require_once('../assets/includes/clean.data.php');
								
				# ADD SUPPORT FOR ADDITIONAL LANGUAGES
				foreach($active_langs as $value){ 
					$title_val = ${"title_" . $value};
					$short_val = ${"short_" . $value};
					$article_val = ${"article_" . $value};
					$addsqla.= ",title_$value";
					$addsqlb.= ",'$title_val'";
					$addsqla.= ",short_$value";
					$addsqlb.= ",'$short_val'";
					$addsqla.= ",article_$value";
					$addsqlb.= ",'$article_val'";
				}
				
				# INSERT INFO INTO THE DATABASE
				$sql = "INSERT INTO {$dbinfo[pre]}news (
						title,
						short,
						article,
						homepage,
						active,
						add_date,
						expire_type,
						expire_date,
						newsgroups";
				$sql.= $addsqla;
				$sql.= ") VALUES (
						'$title',
						'$short',
						'$article',
						'$homepage',
						'$active',
						'$save_date',
						'$expire_type',
						'$expire_date',
						'$plangroups'";
				$sql.= $addsqlb;
				$sql.= ")";
				$result = mysqli_query($db,$sql);		
				$saveid = mysqli_insert_id($db);		
				
				# ADD GROUPS
				if($setgroups){
					foreach($setgroups as $value){
						mysqli_query($db,"INSERT INTO {$dbinfo[pre]}groupids (mgrarea,item_id,group_id) VALUES ('$page','$saveid','$value')");
					}
				}
				
				# UPDATE ACTIVITY LOG
				save_activity($_SESSION['admin_user']['admin_id'],$mgrlang['subnav_news'],1,$mgrlang['gen_b_new'] . " > <strong>$title</strong>");
				
				header("location: mgr.news.php?mes=new"); exit;
			}
		}
		
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_news']; ?></title>
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
	<!-- INCLUDE THE FLASH OBJECT JAVASCRIPT -->
    <script type="text/javascript" src="../assets/javascript/FlashObject.js"></script>    
    <!-- TIME OUT AFTER 15 MINUTES -->
	<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />
	<!-- INCLUDE THE EDITOR JS -->
	<?php include_editor_js(); ?>
	<script language="javascript">
		function form_submitter(){
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO"){
					echo "demo_message();";
					echo "return false;";
				} else {
					# CONSTRUCT THE ACTION LINK
					$action_link = ($_GET['edit'] == "new") ? "mgr.news.edit.php?action=save_new" : "mgr.news.edit.php?action=save_edit";
					# CHECK FIELD AND OUTPUT MESSAGE - ID, LANGUAGE, BRING TO FRONT, CUSTOM MESSAGE
					js_validate_field("title","news_title",1);
					# OUTPUT JS FOR THE EDITOR
					js_editor("article");
				}
			?>
		}
		
		// RUN ON PAGE LOAD
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
			load_parent_gals();
		});
		
		function expire_type_status()
		{
			var expire_selected = $('expire_type').options[$('expire_type').selectedIndex].value;
			if(expire_selected == 1)
			{
				show_div('expire_date_div');
			}
			else
			{
				hide_div('expire_date_div');
			}
		}

	</script>
	
</head>
<body>
	<?php echo $browser; ?>
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
            <form enctype="multipart/form-data" name="data_form" method="post" id="data_form" action="<?php echo $action_link; ?>" onsubmit="return form_submitter();">
            <input type="hidden" name="saveid" value="<?php echo $_GET['edit']; ?>" />
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.news.png" class="badge" />
                <p><strong><?php echo ($ptitle = ($_GET['edit'] == "new") ? $mgrlang['news_new_header'] : $mgrlang['news_edit_header']); ?></strong><br /><span><?php echo ($pdescr = ($_GET['edit'] == "new") ? $mgrlang['news_new_message'] : $mgrlang['news_edit_message']); ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" alt="<?php echo $mgrlang['gen_b_grps_alt-']; ?>" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            <!-- START CONTENT -->
            <div id="content">
            <div id="spacer_bar"></div>    
            <?php
				# PULL GROUPS
				$news_group_result = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = '$page' ORDER BY name");
				$news_group_rows = mysqli_num_rows($news_group_result);
			?>            
            <div id="button_bar">
                <div class="subsubon" onclick="bringtofront('1');" id="tab1"><?php echo $mgrlang['news_tab1']; ?></div>
                <?php if($news_group_rows){ ?><div class="subsuboff" onclick="bringtofront('2');" id="tab2"><?php echo $mgrlang['news_tab2']; ?></div><?php } ?>
                <div class="subsuboff" onclick="bringtofront('3');" id="tab3" style="border-right: 1px solid #d8d7d7;"><?php echo $mgrlang['gen_tab_advanced']; ?></div>
            </div>
            
            <?php $row_color = 0; ?>
            <div id="tab1_group" class="group">
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="created_month" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['news_f_date']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['news_f_date_d']; ?></span>
                    </p>
                    <?php 
						if($_GET['edit'] == 'new')
							$form_news_date = $ndate->date_to_form(gmt_date(),1);
						else
							$form_news_date = $ndate->date_to_form($news->add_date);
					?>
                    <select style="width: 75px;" name="created_year">
                        <?php
                            for($i=2000; $i<(date("Y")+6); $i++){
                                if(strlen($i) < 2){
                                    $dis_i_as = "0$i";
                                } else {
                                    $dis_i_as = $i;
                                }
                                echo "<option ";
                                if($form_news_date['year'] == $dis_i_as){
                                    echo "selected";
                                }
                                echo ">$dis_i_as</option>";
                            }
                        ?>
                    </select>
					<select style="width: 55px;" name="created_month">
                        <?php
                            for($i=1; $i<13; $i++){
                                if(strlen($i) < 2){
                                    $dis_i_as = "0$i";
                                } else {
                                    $dis_i_as = $i;
                                }
                                echo "<option ";
                                if($form_news_date['month'] == $dis_i_as){
                                    echo "selected";
                                }
                                echo ">$dis_i_as</option>";
                            }
                        ?>
                    </select>
                    <select style="width: 55px;" name="created_day">
                        <?php
                            for($i=1; $i<=31; $i++){
                                if(strlen($i) < 2){
                                    $dis_i_as = "0$i";
                                } else {
                                    $dis_i_as = $i;
                                }
                                echo "<option ";
                                if($form_news_date['day'] == $dis_i_as){
                                    echo "selected";
                                }
                                echo ">$dis_i_as</option>";
                            }
                        ?>
                    </select>
                    &nbsp;
                    <select style="width: 55px;" name="created_hour">
                            <?php
                                for($i=0; $i<24; $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_news_date['hour'] == $dis_i_as){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        <select style="width: 55px;" name="created_minute">
                            <?php
                                for($i=1; $i<60; $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_news_date['minute'] == $dis_i_as){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                </div>
                
                <div class="<?php fs_row_color(); ?>" id="title_div">
                    <img src="images/mgr.ast.gif" class="ast" />
                    <p for="title" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['news_f_title']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['news_f_title_d']; ?></span>
                    </p>
                    <div class="additional_langs">
                        <input type="text" name="title" id="title" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($news->title); ?>" />
                        <?php							
							if(in_array('multilang',$installed_addons)){
                        ?>
                            &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_title','','','','plusminus-01');"><img src="images/mgr.plusminus.0.png" id="plusminus01" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                            <div id="lang_title" style="display: none;">
                            <ul>
                            <?php
                                foreach($active_langs as $value){
                            ?>
                                <li><input type="text" name="title_<?php echo $value; ?>" id="title_<?php echo $value; ?>" style="width: 300px;" maxlength="100" value="<?php echo @stripslashes($news->{"title" . "_" . $value}); ?>" />&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                        <?php
                                }
                                echo "</ul></div>";
                            }
                        ?>
                    </div>
                </div>
                
                <div class="<?php fs_row_color(); ?>" style="clear: both;">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="short" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['news_f_short']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['news_f_short_d']; ?></span>
                    </p>
                    
                    <div class="additional_langs">
                        <textarea name="short" id="short" style="width: 300px; height: 75px; vertical-align: middle"><?php echo @stripslashes($news->short); ?></textarea>
                        <?php
                            if(in_array('multilang',$installed_addons)){
                        ?>
                            &nbsp;<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_short','','','','plusminus-02');"><img src="images/mgr.plusminus.0.png" id="plusminus02" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
                            <div id="lang_short" style="display: none;">
                            <ul>
                            <?php
                                foreach($active_langs as $value){
                            ?>
                                <li><textarea name="short_<?php echo $value; ?>" style="width: 300px; height: 75px;"><?php echo @stripslashes($news->{"short" . "_" . $value}); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue" style="color: #FFF"><?php echo ucfirst($value); ?></span></li>
                        <?php
                                }
                                echo "</ul></div>";
                            }
                        ?>
                    </div>
                </div>
                
                <!-- FIX FOR THE CONTENT EDITOR HEADER BECOMING OUT OF PLACE -->
                <div style="clear: both;">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <td></td>
                        </tr>
                    </table>
                </div>
                
                <div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['news_f_article']; ?>: <br />
                        <span class="input_label_subtext"><?php echo $mgrlang['news_f_acticle_d']; ?></span>
                   	</p>
					<div style="float: left;">
					<?php
                        show_editor("650px","300px",stripslashes($news->article),"article","editor");
                        if(in_array('multilang',$installed_addons)){
						
						echo "<div align='right' style='background-color: #e4e3ed; padding: 8px;'>";
                    ?>
                    	<span class="mtag_dblue" style="color: #FFF; cursor: pointer" onclick="displaybool('lang_article','','','','plusminus-03');"><img src="images/mgr.plusminus.0.png" id="plusminus03" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span></div>
                    <?php
                            echo "<div id='lang_article' style='display: none;'>";
							foreach($active_langs as $value){ 
                                show_editor("650px","200px",stripslashes($news->{"article_".$value}),"article_".$value,"editor_".$value);
                                //echo "<br clear='both'/>";
                                echo "<div align='right' style='background-color: #e4e3ed; padding: 8px;'><span class='mtag_dblue' style='color: #FFF'>".strtoupper($value)."</span></div>";
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
						$file_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}content_files WHERE content_id = '$_GET[edit]' AND type = 'news' ORDER BY file_name ASC");
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
                    <p for="active" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['news_f_active']; ?>: <br />
                    	<span class="input_label_subtext"><?php echo $mgrlang['news_f_active_d']; ?></span>
                    </p>
                    <input type="checkbox" name="active" id="active" value="1" <?php if(@!empty($news->active) or $_GET['edit'] == "new"){ echo "checked"; } ?> />
                </div>                
                <div class="<?php fs_row_color(); ?>">
                    <?php
							if($config['settings']['hpnews'] == 0){
						?>
                            <div style="position: absolute; margin: -8px 0 0 300px; vertical-align: middle">
                                <img src='images/mgr.note.arrow.png' style='position: absolute; z-index: 30; margin: 17px 0px 0px -8px;' /><div class='notes' style="width: 250px"><?php echo $mgrlang['gen_hpfeaturearea']; ?></div>
                            </div>
                        <?php
							}
						?>
					
					<img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="homepage" onclick="support_popup('<?php echo $supportPageID; ?>');">
						<?php echo $mgrlang['news_f_homepage']; ?>: <br />
                     	<span class="input_label_subtext"><?php echo $mgrlang['news_f_homepage_d']; ?></span>
                    </p>
                    <input type="checkbox" name="homepage" id="homepage" value="1" <?php if(@!empty($news->homepage)){ echo "checked"; } ?> />
                </div>
            </div>
            
            <?php
            	if($news_group_rows){
					$row_color = 0;
			?>
                <div id="tab2_group" class="group"> 
                    <div class="<?php fs_row_color(); ?>" id="name_div">
                        <img src="images/mgr.ast.off.gif" class="ast" />
                        <p for="name" onclick="support_popup('<?php echo $supportPageID; ?>');">
							<?php echo $mgrlang['news_f_groups']; ?>:<br />
                        	<span><?php echo $mgrlang['news_f_groups_d']; ?></span>
                        </p>
						<?php
							$plangroups = array();
							# FIND THE GROUPS THAT THIS ITEM IS IN
							$news_groupids_result = mysqli_query($db,"SELECT group_id FROM {$dbinfo[pre]}groupids WHERE mgrarea = '$page' AND item_id = '$news->news_id' AND item_id != 0");
							while($news_groupids = mysqli_fetch_object($news_groupids_result)){
								$plangroups[] = $news_groupids->group_id;
							}
							echo "<ul style='float: left; margin: 0px; list-style-type:none; padding: 0;'>";
							while($news_group = mysqli_fetch_object($news_group_result)){
								echo "<li><input type='checkbox' id='$news_group->gr_id' class='permcheckbox' name='setgroups[]' value='$news_group->gr_id' "; if(in_array($news_group->gr_id,$plangroups)){ echo "checked "; } echo "/> "; if($news_group->flagtype == 'icon.none.gif'){ echo "<img src='./images/mini_icons/$news_group->flagtype' align='absmiddle' width='1' height='16' /> "; } else { echo "<img src='./images/mini_icons/$news_group->flagtype' align='absmiddle' /> "; } echo substr($news_group->name,0,30)."</li>";
							}
							echo "</ul>";
                        ?>
                    </div>
            	</div>
			<?php
                }
            ?>
            
            <?php $row_color = 0; ?>
            <div id="tab3_group" class="group">            
            	<div class="<?php fs_row_color(); ?>">
                    <img src="images/mgr.ast.off.gif" class="ast" />
                    <p for="x" onclick="support_popup('<?php echo $supportPageID; ?>');">
                        <?php echo $mgrlang['gen_expire_date']; ?>: <br />
                        <span><?php echo $mgrlang['news_f_expire_d']; ?></span>
                    </p>
                    <select style="float: left;" name="expire_type" id="expire_type" onchange="expire_type_status()">
                        <option value="0" <?php if($news->expire_type == 0){ echo "selected='selected'"; } ?>><?php echo $mgrlang['get_never']; ?></option>
                        <option value="1" <?php if($news->expire_type == 1){ echo "selected='selected'"; } ?>><?php echo $mgrlang['get_set_date']; ?>...</option>
                    </select>
                    <div style="float: left; padding-left: 15px; <?php if($news->expire_type == 1){ echo "display: block;"; } else { echo "display: none;"; } ?>" id="expire_date_div">
                        <?php 
                            $form_expire_date = $ndate->date_to_form($news->expire_date);
                        ?>
                        <select style="width: 75px;" name="expire_month">
                            <?php
                                for($i=1; $i<13; $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_expire_date['month'] == $dis_i_as or ($_GET['edit'] == "new" and date("m") == $dis_i_as)){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        <select style="width: 75px;" name="expire_day">
                            <?php
                                for($i=1; $i<=31; $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_expire_date['day'] == $dis_i_as or ($_GET['edit'] == "new" and date("d") == $dis_i_as)){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>
                        <select style="width: 132px;" name="expire_year">
                            <?php
                                for($i=2000; $i<(date("Y")+6); $i++){
                                    if(strlen($i) < 2){
                                        $dis_i_as = "0$i";
                                    } else {
                                        $dis_i_as = $i;
                                    }
                                    echo "<option ";
                                    if($form_expire_date['year'] == $dis_i_as or ($_GET['edit'] == "new" and date("Y") == $dis_i_as)){
                                        echo "selected";
                                    }
                                    echo ">$dis_i_as</option>";
                                }
                            ?>
                        </select>                        
                    </div>
                </div>
            </div>
            
            </div>
            <div id="save_bar">
                <input type="button" value="<?php echo $mgrlang['gen_b_cancel']; ?>" onclick="cancel_edit('mgr.news.php');" /><input type="submit" value="<?php echo $mgrlang['gen_b_save']; ?>" />
            </div>
            </form>
            <div class="footer_spacer"></div>
        </div>
        <?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>