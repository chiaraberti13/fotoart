<?php
	###################################################################
	####	MANAGER ADD MEDIA PAGE                                 ####
	####	Copyright 2010 Ktools.net LLC - All Rights Reserved	   ####
	####	http://www.ktools.net                                  ####
	####	Created: 6-4-2010                                      ####
	####	Modified: 6-4-2010                                     #### 
	###################################################################
	
		require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE
		
		$page = "add_media";
		$lnav = "library";
	
		$supportPageID = '318';
	
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
		# ADDITIONAL ADD FILES AREA ERROR CHECKS
		require_once('mgr.add.media.ec.php');
			
		require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE		
		error_reporting(E_ALL & ~E_NOTICE);								# TURN ERROR REPORTING BACK ON
		require_once('mgr.defaultcur.php');								# INCLUDE DEFAULT CURRENCY SETTINGS	
		
		$cleanvalues = new number_formatting;
		$cleanvalues->set_num_defaults(); // SET THE DEFAULTS
		$cleanvalues->set_cur_defaults(); // SET THE CURRENCY DEFAULTS
		//$cleanvalues->decimal_places = 4;
		//$cleanvalues->strip_ezeros = 1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $langset['lang_charset']; ?>" />
	<title><?php echo $manager_page_title . " : " . $mgrlang['subnav_add_media']; ?></title>
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
	<script type="text/javascript" src="../assets/javascript/jstree/v3/jstree.min.js"></script>
	<link rel="stylesheet" href="../assets/javascript/jstree/v3/themes/default/style.min.css" />
	
    <!-- LOAD SCRIPTACULOUS LIBRARY -->   
    <script type="text/javascript" src="../assets/javascript/scriptaculous/scriptaculous.js"></script>
	<!-- GENERIC MGR JAVASCRIPT -->	
	<script type="text/javascript" src="./mgr.min.js"></script>	
    <!-- INCLUDE THE FLASH OBJECT JAVASCRIPT -->
    <script type="text/javascript" src="../assets/javascript/FlashObject.js"></script>
	<!-- TIME OUT AFTER 15 MINUTES -->
	<!--<meta http-equiv=refresh content="<?php echo $config['timeout']; ?>; url=mgr.login.php?notice=timed_out" />-->
	
	<link rel="stylesheet" href="../assets/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css" />
	
	
	<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>
	<script type="text/javascript" src="../assets/plupload/plupload.full.js"></script>
	<script type="text/javascript" src="../assets/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
    
    <style>
		#workbox{
			position: absolute;
		}
		
		.filerow{
			border-bottom: 1px solid #d1d1d1;
			clear: both;
			overflow: auto; 
		}
		
		.thumbcontainer{
			float: left;
			border: 1px solid #bdbdbd;
			margin: 10px 10px 10px 30px;
			background-color: #fff;	
		}
		
		.thumbcontainer img{
			width: 30px;
			border: 2px solid #fff;
		}
		
		.filenamecontainer{
			float: left;
			margin-top: 17px;
			width: 220px;
			overflow: hidden;
			white-space:nowrap;
		}
		
		.statuscontainer{
			float: left;
			margin-top: 15px;
			margin-left: 20px;
			width: 40px;
		}
		
		.keyword_list{
			min-height: 50px;
			overflow: auto;
			padding: 7px;
			border: 1px solid #d9d9d9;
		}
		
		.keyword_list input[type="button"]{
			margin: 2px;
			padding-left: 20px;
			background-image: url(images/mgr.actionlink.bg3.gif);
		}
		
		.keyword_list input[type="button"]:hover{
			padding-left: 20px;
			background-image: url(images/mgr.actionlink.bg4.gif);
		}
		
		.keyword_list_header{
			text-align: right;
			background-color: #eee;
			padding: 5px;
			border: 1px solid #d9d9d9;
			white-space: normal;
		}
		
		.detailswinarrow{
			margin: 14px 0px 0px -10px;
		}
		
		/*@import url(../assets/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css);*/
		.plupload_button.plupload_start, .plupload_header{ display:none; } /* Hide start button */
	</style>
    
    <script language="javascript">
		<?php
			if($config['settings']['uploader'] == '4xxx')
			{
		?>
			Event.observe(window, 'focus', function()
			{
				load_import_win();			
			});
		<?php
			}
		?>
		
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
				
				load_import_win();
				
			});
		
		
		function openPluploadWindow()
		{
			window.open('mgr.plupload.php', 'Plupload', 'width=800,height=380,scrollbars=yes,menubar=no,titlebar=no');
		}
		
		// CHECK FOR ENTER KEY ON NEW GALLERY
		function checkkeygallery()
		{
			if (Event.KEY_RETURN == event.keyCode)
			{
				create_gallery();
			}
		}
		
		// ADD KEYWORD TO LIST
		function add_keyword(language)
		{
			// FIND OPEN ID
			
			// GET KEYWORD
			var new_keyword = $F('new_keyword_'+language);
			if(new_keyword != '')
			{
			
				$('new_keyword_'+language).setValue('');
				
				//alert($$('[kwlanguage="'+language+'"]'));
				
				var numofkeywords = $$('[kwlanguage="'+language+'"]').length-1;
				var lastkeywordid = $$('[kwlanguage="'+language+'"]')[numofkeywords].id;
				
				var splitter = ',';
				
				if(new_keyword.indexOf(","))
				{
					splitter = ',';
				}
				else if(new_keyword.indexOf(";"))
				{
					splitter = ';';
				}
					
				var keywords = new_keyword.split(splitter);
				
				$(keywords).each(function(s,index)
				{
					var newkeywordid = new Date();
					newkeywordid = newkeywordid.valueOf();
					
					var templatedata = "<input type=\"button\" onclick=\"remove_keyword('DEFAULT_key_"+newkeywordid+"')\" keyword=\"\" kwlanguage=\""+language+"\" id=\"DEFAULT_key_"+newkeywordid+"\" value=\""+s+"\" class='greyButton' />";
					templatedata += "<input type=\"hidden\" name=\"keyword_"+language+"[]\" id=\"DEFAULT_key_"+newkeywordid+"_input\" value=\""+s+"\" />";
					lastkeywordid = $$('[kwlanguage="'+language+'"]')[numofkeywords].id;
					$(lastkeywordid).insert({'after':templatedata});
				});
				
				//alert(keywords.length);
				
				
				//var rowTemplate = new Template(templatedata);	
				//$(last_name).insert({after: 
				//	rowTemplate.evaluate({
				//		id: '1'
				///	})});
				
				// UPDATE
				//$('keywords_'+language).update($('keywords_'+language).innerHTML + keyword_line);
			}
		}
		
		
		
		// REMOVE KEYWORD FROM LIST
		function remove_keyword(id)
		{
			//alert(id);
			$(id).remove();
			$(id+'_input').remove();
		}
		
		<?php
			switch($config['settings']['uploader'])
			{
				default;
				case "1":
					$uploader = 'java_upload';
				break;
				case "2":
					$uploader = 'flash_upload';
				break;
				case "3":
					$uploader = 'html_upload';
				break;
				case "4":
					$uploader = 'plupload';
				break;
			}	
		//echo $config['settings']['uploader']; exit;
		
		?>	
		
			
		uploadobj = {};
		uploadobj.page = 'mgr.workbox.php';
		uploadobj.pars = 'box=<?php echo $uploader; ?>&page=add_media&maxfiles=<?php echo $config['MaxBatchUpload']; ?>';
		
		//assign_details = {};
		//assign_details.mode = 'assign_details';
		//assign_details.id = 'details';
		
		// Java File Uploader
		function getUploader()
		{
			return document.jumpLoaderApplet.getUploader();
		}
		
		function getUploaderConfig()
		{
			return document.jumpLoaderApplet.getUploaderConfig();
		}
		
		function getUploadView()
		{
			return getMainView().getUploadView();
		}
		
		function getMainView()
		{
			return getApplet().getMainView();
		}
		
		function getApplet()
		{
			return document.jumpLoaderApplet;
		}
		
		function getViewConfig()
		{
			return getApplet().getViewConfig();
		}
		
		function startJavaUpload( index )
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "\$('uploadbox').hide();";
					echo "demo_message2();";
				}
				else
				{
			?>
				var error = getUploader().startUpload();
				if( error != null )
				{
					//alert( error );
				}
			<?php
				}
			?>
		}
		
		function stopJavaUpload( index )
		{
			var error = getUploader().stopUpload();
			if( error != null )
			{
				//alert( error );
			}
		}
		
		function uploaderFileAdded( uploader, file )
		{
			//alert('test');
			$('start_button').enable();
		}
		
		function uploaderFileStatusChanged( uploader, file )
		{ 
			var status = file.getStatus(); 
			if(status == 2)
			{ 
				//window.location = "your_redirect_url.html";
				//alert(file.getIndex());
				if ((file.getIndex()+1) == uploader.getFileCount())
				{
					//alert('test');
					close_uploadbox();
					//step2();
					load_import_win();
					$('vmessage').show();
					setTimeout(vmessage_uploadedfiles,'5500');
				}
			} 
		}
		
		function uploaderFileRemoved( uploader, file )
		{
			if(uploader.getFileCount() == 0)
			{
				$('start_button').disable();
			}
		}
		
		function disable_start_button()
		{
			$('start_button').disable();
		}
		
		// LOAD IMPORT WINDOW
		function load_import_win()
		{
			show_loader('import_list_div');
			//alert($F('permowner'));
			var pars = 'mode=import_list';
			var myAjax = new Ajax.Updater('import_list_div', 'mgr.add.media.actions.php', {method: 'get', parameters: pars, evalScripts: true});
		}
		
		// SHOW UPLOADED FILES VMESSAGE
		function vmessage_uploadedfiles()
		{
			$('vmessage').hide();
		}
		
		function delete_import_files(startat)
		{
			// DELETE IMPORT FILES AREA
			$('remove_selected').disable();
			//alert($('datalist').serialize());
			var passtophp = $('import_list_form').serialize();			
			//alert(passtophp);
			show_loader('import_list_div');
			var myAjax = new Ajax.Updater(
				'import_list_div', 
				'mgr.add.media.actions.php', 
				{
					method: 'post', 
					parameters: 'mode=delete&' + passtophp,
					onComplete: function(){ $('remove_selected').enable() }
				});
		}
		
		// FOLDER DROPDOWN
		function cnf_check()
		{
			var selected_folder = $('folder').options[$('folder').selectedIndex].value;
			//alert(selected_folder);
			$('folder_id').setValue(selected_folder);
			
			if(selected_folder == 0)
			{
				show_div('folder_name_div');				
			}
			else
			{
				hide_div('folder_name_div');
			}
		}
		
		// STORAGE ID DROPDOWN
		function check_storage_id()
		{
			var selected_sid = $('storage_id').options[$('storage_id').selectedIndex].stype;
			if(selected_sid != 'local')
			{
				show_div('storage_warning');
			}
			else
			{
				hide_div('storage_warning');
			}
		}
		
		var assignOpened = 0;
		// CHECK TO MAKE SURE FOLDER NAME IS NOT BLANK
		function check_folder_name()
		{	
			var total_imports = 0;
			
			$$('[is_import_file="1"]').each(function(s)
			{
				if(s.checked==true)
				{
					total_imports++;
				}
			});
			
			if(total_imports > 0)
			{			
				$('folder_name_div').className='fs_row_off';
				var selected_folder = $('folder').options[$('folder').selectedIndex].value;
				//alert(selected_folder);
				
				if(selected_folder == 0)
				{	
					<?php js_validate_field("folder_name","folders_f_name",1); ?>
					
					create_folder();
				}
				else
				{
					if(assignOpened == 0)
					{
						//alert('first open');
						//workbox(assign_details);
						workbox2({page: 'mgr.wb.media.details.php',pars: 'box=assign_details'});
						
						assignOpened = 1;
					}
					else
					{
						//alert('pre opened');
						fade_overlay_in();
						$('workbox').setStyle({ display: 'block'});
					}
				}
			}
			else
			{
				simple_message_box('<?php echo $mgrlang['select_files_import']; ?>','')
			}
		}
		
		// CREATE A NEW GALLERY
		function create_gallery()
		{
			if($F('new_gallery_name'))
			{
				var pars = 'mode=create_gallery&name=' + $F('new_gallery_name');
				var url = 'mgr.add.media.actions.php';
				
				show_loader('gals');
				
				var myAjax = new Ajax.Request( 
				url, 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true,
					onSuccess: function(transport){					
						transport.responseText.evalScripts();					
						//alert(transport.responseText);
						//eval(transport.responseText);
					}
				});
			}
		}
		
		// CHECK AND THEN OPEN NEW WORKBOX OR EXISTING WORKBOX
		function openworkbox()
		{	
			if(assignOpened == 0)
			{
				//alert('first open');
				//workbox(assign_details);
				workbox2({page: 'mgr.wb.media.details.php',pars: 'box=assign_details'});
				assignOpened = 1;
			}
			else
			{
				//alert('pre opened');
				fade_overlay_in();
				$('workbox').setStyle({ display: 'block'});
			}
		}
		
		// CREATE NEW FOLDER
		function create_folder()
		{
			var selected_folder = $F('folder');
			
			var storage = $F('storage_id');
			
			//alert(selected_folder);
			
			if($F('encrypt_folder'))
			{
				var encrypted = 1;
			}
			else
			{
				var encrypted = 0;	
			}
			
			var pars = 'mode=create_directory&storage=' + storage + '&folder=' + $F('folder_name') + "&encrypted=" + encrypted;
			var url = 'mgr.add.media.actions.php';
			
			//var myAjax = new Ajax.Updater('import_list_div', 'mgr.add.media.actions.php', {method: 'get', evalScripts: true, parameters: pars});
			//alert(pars);
			
			var myAjax = new Ajax.Request( 
			url, 
			{
				method: 'get', 
				parameters: pars,
				evalScripts: true,
				onSuccess: function(transport){					
					transport.responseText.evalScripts();					
					//alert(transport.responseText);
					//eval(transport.responseText);
				}
			});
		}
		
		// START THE IMPORTING PROCESS
		function start_importing()
		{
			<?php
				if($_SESSION['admin_user']['admin_id'] == "DEMO")
				{
					echo "demo_message2();";
				}
				else
				{
			?>
				//alert($F('folder_id')); // Testing
				close_workbox();
				
				build_files_array();
				
				$('gc_a').hide();
				$('gc_b').hide();
				//$('gc_c').hide();
				$('importing_window').show();
				$('importing_window').setStyle({
					display: 'block'
				});
				
				setTimeout(import_process,800);
			<?php
				}
			?>
		}
		
		function removeGalleryFromList(galleryID,type)
		{
			//$j('#'+type+'-'+galleryID).remove();
			
			$j('#'+type).find('#'+type+'-'+galleryID).remove();
		}
		
		function addGalleryToList(galleryID,type)
		{
			//alert('#'+type);
			
			$j('#'+type).append("<input type='text' name='mediaGalleries[]' id='mediaGalleries-"+galleryID+"' value='"+galleryID+"'><br>");
			
		}
		
		// LOAD GALLERIES
		function load_gals()
		{
			show_loader('gals');
			$j('#gals').jstree('destroy');
			
			$j('#gals').jstree({
				'core' : {
					'data' : {
						'url' : 'mgr.jstree.data.wb.php',
						'data' : function (node) {
							//alert(node.url);
							return { 'id' : node.id };
						}
					},
					'check_callback' : function(o, n, p, i, m) {
						
					},
					'themes' : {
						'responsive' : false,
						'stripes' : false,
						'dots' : false
					}
				},
				'plugins' : ['checkbox'],
				'checkbox' : {
						'visible' : true,
						'three_state' : false,
						'cascade' : ''
					}
			})
			.on('select_node.jstree', function (e, data) {
				//alert(data.node.id);
				// add to gallery array
				addGalleryToList(data.node.id,'mediaGalleries');
				
			})
			.on('deselect_node.jstree', function (e, data) {
				//alert('test');
				// remove from gallery array
				removeGalleryFromList(data.node.id,'mediaGalleries');
			})
			.on('activate_node.jstree', function (e, data) {
				
			})
			.on('changed.jstree', function (e, data) {
				if(data && data.selected && data.selected.length)
				{
					//alert('test');
				}
			})
			.on('ready.jstree', function (e, data) {
				$j('#checkGalLoaded').val('1');
			});
			
			/*
			
			//alert($F('permowner'));
			var pars = 'mode=galleries';
			var myAjax = new Ajax.Updater('gals', 'mgr.add.media.actions.php', {method: 'get', parameters: pars});
			*/
		}
		
		// SELECT/DESELECT FOLDER CONTENT
		function select_folder_contents(folder_id_num)
		{
			//alert(folder_id_num);
			$$('[folder_id_num="'+folder_id_num+'"]').each(function(s)
			{
				if(s.checked==true)
				{
					s.checked=false;
				}
				else
				{
					s.checked=true;
				}
				
				//s.src = './images/mgr.small.check.0.png';
			});
		}
		
		// IMPORT PROCESS
		var current_file_number = 1;
		var stopped = 0;
		var start;
		var end;
		var f_start_time;
		var f_end_time;				
		var diff_time;
		var total_time;
		var add_time = 0;
		var show_time;
		var min_time = 0;
		var sec_time = 0;
		var est_time = 0;
		var est_calc = 0;
		var warnings = 0;
		//var running_time = 0;
		var row_color = '#eee';
		
		
		var import_files = [];
		var import_files_basename = [];
		var array_id = 0;
		function build_files_array()
		{
			$$('[is_import_file="1"]').each(function(s)
			{
				if(s.checked==true)
				{
					import_files[array_id] = $F(s);
					import_files_basename[array_id] = s.getAttribute('basename');
					array_id++;
				}
			});
			//alert(import_files_basename);
		}
		
		function import_process()
		{
			//alert(import_files_basename[0]);
			
			if(stopped != 1){
				if(current_file_number == 1){
					$('start_message').hide();
					$('import_status_bar').show();
					$('stop_button').enable();
					$('import_status_bar').setStyle({display: "block"});
					$('import_title_bar').setStyle({display: "block"});
				}
				
				//alert("stopped: "+stopped+" | relen: "+import_files.length+" | cur:"+ current_file_number);
				
				var url = "mgr.add.media.actions.php";
				var updatebox = "importing_contents";
				var rf_length = import_files.length;
				
				if(current_file_number <= rf_length){				
					//alert('2');

					start = new Date();
    				f_start_time = start.valueOf();
					
					if(row_color == '#eee')
					{
						row_color = '#fff';
					}
					else
					{
						row_color = '#eee';
					}
					
					var row_prep = "<div id='filerow"+current_file_number+"' style='background-color: "+row_color+"' class='filerow'> <p class='thumbcontainer' id='thumb_container"+current_file_number+"'><img src='images/mgr.no.thumb.tiny.png' id='thumb"+current_file_number+"' /></p> <p id='filename_container"+current_file_number+"' class='filenamecontainer'><img src='images/mgr.loader.gif' align='absmiddle' /> &nbsp; <?php echo $mgrlang['gen_processing']; ?> <strong>"+import_files_basename[current_file_number-1]+"</strong></p><p id='status_container"+current_file_number+"' class='statuscontainer'></p><p id='message_container"+current_file_number+"' style='padding: 18px 6px 6px 6px;'></p></div>";
					//$('importing_contents').update(row_prep + $('importing_contents').innerHTML);
					
					$("filerow"+(current_file_number-1)).insert({'before':row_prep});
					
					var batch_details_vars = $('batch_details_form').serialize();
					var folder_setup_vars = $('folder_setup').serialize();
					
					var pars = "mode=import_file&filename=" + import_files[current_file_number-1] + '&' + batch_details_vars + '&' + folder_setup_vars;				
					
					//alert(batch_details_vars);
					
					var myAjax = new Ajax.Request( 
					url, 
					{
						method: 'post', 
						parameters: pars,
						evalScripts: true,
						onSuccess: function(transport){							
							var json = transport.responseText.evalJSON(true);
							
							//var cur_file = response_info.split("|");	
							//alert(json.myfile.status[0]);
							
							switch(json.myfile.status[0]){
								case "1":						
									var file_status = "<img src='images/mgr.small.check.1.png' />";
									var message = '';
								break
								case "0":
									var file_status = "<img src='images/mgr.notice.icon.small2.png' style='margin-left: -6px; margin-right: 4px; vertical-align: middle' />";
									var message = "<span style='color: #CC0000;'><strong><?php echo $mgrlang['gen_notice']; ?>:</strong> "+json.myfile.errormessage[0]+"</span>";
									
									warnings++;
									
									if(json.myfile.errorcode[0] == '999')
									{
										$('importing_contents').update("<p style='padding: 20px;'>"+file_status+message+"</p>");
										return false;
										break;
									}
								break;
							}
							
							<?php
								if($config['ShowImportIcons'])
								{
							?>
								if(json.myfile.thumbstatus[0] == 1)
								{
									//alert(json.myfile.thumbpath[0]);
									$("thumb"+current_file_number).setAttribute('src',json.myfile.thumbpath[0]);
									//alert(json.myfile.thumbpath[0]);
								}
							<?php
								}
							?>
							
							//var row_builder = 'Replacing ' + json.myfile.filepath[0] + '...' + file_status;
														
							//$('filename_container'+current_file_number).update(json.myfile.filepath[0] + " --- s"+f_start_time);
							
							$('status_container'+current_file_number).update(file_status);
							
							$('message_container'+current_file_number).update(message);
							
							$('files_processed').update('<?php echo $mgrlang['files_processed']; ?> ' + (current_file_number) + '/' + rf_length);
					
							// CALCULATE PERCENTAGE
							var percentage =  Math.round((current_file_number/rf_length)*100);
							//$('progress_bar').style.width = Math.round(percentage*1.5);							
							$('progress_bar').setStyle({width: Math.round(percentage*1.5) + 'px'});							
							$('show_perc').innerHTML = percentage + "%";
							
							//var objDiv = $('importing_contents');
							//objDiv.scrollTop = objDiv.scrollHeight;
														
							// END TIME
							end = new Date();
							f_end_time = end.valueOf();
							
							diff_time = f_end_time - f_start_time;
							add_time+=diff_time;
							show_time = calc_time(add_time);							
							est_calc = (add_time/current_file_number)*(rf_length-current_file_number);
							est_time = calc_time(est_calc);							
							// SHOW PROCESS TIME FOR CURRENT FILE
							//$('fprocesstime_' + i).innerHTML = calc_time(diff_time);							
							// SHOW PROCESS TIME ELAPSED AND REMAINING			
							$('time_calc').innerHTML = "<?php echo $mgrlang['elapsed']; ?>: " + show_time + " - <?php echo $mgrlang['remaining']; ?>: ~" + est_time;
							$('filename_container'+current_file_number).update(json.myfile.filepath[0]);
							//alert('cool');
							
							current_file_number++;
							
							json = "";
							row_prep = '';
							row_builder = '';
							
							// ADD RESTING TIME
							add_time+=<?php echo $config['ImportRest']; ?>;
							
							setTimeout(import_process,<?php echo $config['ImportRest']; ?>);
							
						},
						onFailure: function(){
							alert('<?php echo $mgrlang['gen_error_occ']; ?>');
						}
					});
					
					//var myAjax = new Ajax.Request(
					//	url, 
					//	{
					//		method: 'get', 
					//		parameters: pars
					//	});
					//,onSuccess: status_good,
					//onFailure: status_bad
					
					
					// MOVE TO ON COMPLETE
					//$('file_window').update(current_window + replacement_files[current_file_number]);
					
								
				} else {
					//var objDiv = $('file_window');
					//objDiv.scrollTop = objDiv.scrollHeight;
					//$('current_file_window').update('Complete');
					//$('cont_button').enable();
					//$('stop_button').disable();
					if(warnings > 0)
						simple_message_box('<?php echo $mgrlang['gen_import_done_mes'].' '.$mgrlang['gen_import_done_mes2']; ?>','cont_button');
					else
						simple_message_box('<?php echo $mgrlang['gen_import_done_mes']; ?>','cont_button');
					
					$('stop_button').hide();
					$('import_more').enable();
					
					$('import_new_batch').enable();					
					$('manage_my_media').enable();
					//alert("complete");		
				}
			} else {
				$('file_window').update($('file_window').innerHTML + '<span style="color: #CC0000; font-weight: bold;">STOPPED!</span><br />');
			}	
		}
		
		// CALCULATE TIME
		function calc_time(new_time){
			if(new_time > 1000){
				sec_time = Math.round(new_time/1000);
				if(sec_time > 60){
					min_time = Math.round(sec_time/60);
					return min_time + "min";
				} else {
					return sec_time + "sec";
				}				
			} else {
				//return "< 1sec";				
				return "0sec";
			}
		}	
		
		// STOP UPGRADE FUNCTION
		function stop_import(){
			stopped = 1;
			//$('stop_button').disable();			
			//$('start_button').enable();	
			//$('cont_button').enable();
			//$('start_button').value = 'Resume';		
		}
		
		// IMPORT MORE
		function import_more(){
			stopped = 1;
			
			$('gc_a').show();
			$('gc_b').show();
			//$('gc_c').hide();
			$('importing_window').hide();
			load_import_win();
			
			restart_import();
		}
		
		// START UPGRADE FUNCTION
		function restart_import(){
			$('importing_contents').update("<div id='filerow0'></div><div style='padding: 25px;' id='start_message'><?php echo $mgrlang['starting_import']; ?>...</div>");
			
			// IMPORT PROCESS
			current_file_number = 1;
			stopped = 444444;
			add_time = 0;
			min_time = 0;
			sec_time = 0;
			est_time = 0;
			est_calc = 0;
			warnings = 0;
			row_color = '#eee';
			
			import_files = new Array();
			import_files_basename = new Array();
			array_id = 0;
			
			$('start_message').show();
			$('progress_bar').setStyle({width: '0px'});	
			
			//$('start_button').disable();
			//$('stop_button').enable();
			//$('cont_button').disable();		
		}
		
		
		
		function checkFileSizeRestrictions()
		{
			var overFileSize = 0;
			$$('[is_import_file="1"]').each(function(s)
			{
				if(s.checked==true)
				{
					if(s.getAttribute('filesize') > (<?php echo $config['OffsiteStogageLimit']; ?>*1024))
					{
						overFileSize = 1;
					}
				}
			});
			
			var folder_stype = $('folder').options[$('folder').selectedIndex].getAttribute('stype');
			var storage_stype = $('storage_id').options[$('storage_id').selectedIndex].getAttribute('stype');
			var isLocal = 0;
			
			if(folder_stype == 'cnf')
			{
				if(storage_stype == 'local')
				{
					isLocal = 1;
				}
				else
				{
					isLocal = 0;
				}
			}
			else
			{
				if(folder_stype == 'local')
				{
					isLocal = 1;
				}
				else
				{
					isLocal = 0;
				}
			}
			
			if(overFileSize == 1 && isLocal == 0)
			{
				$('vmessage2').show();	
			}
			else
			{
				$('vmessage2').hide();
			}
		}
		
		// CHECK FOR ENTER KEY ON KEYWORDS
		function checkkey(language)
		{
			if (Event.KEY_RETURN == event.keyCode)
			{
				//alert('tester');
				add_keyword(language);
			}
		}
		
		function displayDSPOptions(dspID)
		{			
			// Blank to keep function running on add media page
		}
		
		// Flash upload is complete. Close workbox and refresh import window.
		function flashUploadComplete()
		{
			close_uploadbox();
			load_import_win();
		}
	</script>
	<?php include('mgr.media.details.js.php'); ?>
</head>
<body>
	<?php demo_message($_SESSION['admin_user']['admin_id']); ?>
	<?php include("mgr.message.window.php"); ?>
    <div id="uploadbox">UploadBox<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></div>
	<div id="container">
		<?php include('mgr.header.php'); ?>
		<?php include('mgr.support.bar.php'); ?>
		<?php include('mgr.shortcuts.cont.php'); ?>
        
        <!-- START CONTENT CONTAINER -->
        <div id="content_container">
            <!-- TITLE BAR AREA -->
            <div id="title_bar">
                <img src="./images/mgr.badge.addmedia.png" class="badge" />
                <p><strong><?php echo $mgrlang['subnav_add_media']; ?></strong><br /><span><?php echo $mgrlang['subnav_add_files_d']; ?></span></p>
                <div style="float: right; margin-right: 20px;" class="abuttons" id="abutton_help"><img src="./images/mgr.button.help.off.png" align="absmiddle" border="0" id="img_help" /><br /><?php echo $mgrlang['gen_b_help']; ?></div>
            </div>
            
            <?php
                # CHECK FOR ERRORS
                $minor_errors = array();
                
                @$minor_errors[] = "Mod Security check";
                
                @$minor_errors[] = "Upload Limits / Max time / Memory Limits(mem really not needed at this point?)";
                
                @$minor_errors[] = "Check incoming dir";
                
                @$minor_errors[] = "Maybe do a gallery check again";
                
                //is_writable($config['settings']['incoming_path'] . "/web"
                
                # OUTPUT ERRORS IF NOT IN DEMO MODE
                if(!$minor_errors and $_SESSION['admin_user']['admin_id'] != "DEMO"){
                    echo "<div id='minor_errors' class='minor_errors'>";
                    foreach($minor_errors as $value){
                        echo "<img src='images/mgr.notice.icon.small.gif' align='absmiddle' /> $value ";
                        echo create_info_button();
                        echo "<br /><br />";
                    }
                    echo "</div>";
                }
            ?>
            
            <!-- START CONTENT -->
            <div id="content" style="padding: 0 10px 0 10px;">                        	
            	<div id="spacer_bar"></div>
                
                <div id="importing_window" style="display: none; padding: 0; margin-top: 0;" class="group">
                    <div id="import_status_bar" class="perpage_bar" style="display: none; background-color: #4a4a4a; color: #fff; border-bottom: 1px solid #333; border-top: 1px solid #707070;">
                        <div id="files_processed" style="float: left; border-right: 1px solid #4a4949; padding-right: 10px;"></div>
                        <div id="time_calc" style="float: left; border-left: 1px solid #7d7c7c; border-right: 1px solid #4a4949; padding-left: 10px; padding-right: 10px;"></div>
                        <div style="float: left; border-left: 1px solid #7d7c7c; padding-right: 10px;">&nbsp;</div>
                        <div style="width: 150px; border: 1px solid #5a5a5a; background-color: #5a5a5a; height: 10px; float: left; margin-top: 2px;" id='progbar'><div id="progress_bar" style="width: 0%; height: 10px; background-image: url(images/mgr.loader3.gif); background-repeat: repeat-x"></div></div>
                        <div id="show_perc" style="float: left; padding: 0px 4px 0px 4px;"></div>
                    </div>
                    
                    <div id="import_title_bar" style="display: none; background-color: #eee; color: #fff; border-bottom: 2px solid #cfcece; border-top: 1px solid #fff; overflow: auto; color: #333; font-weight: bold; padding: 10px;">
                        <p style="float: left; width: 275px; padding-left: 20px;">Media</p>
                        <p style="float: left; width: 40px;">Status</p>
                        <p style="float: left; width: 220px;"><!-- Message --></p>
                    </div> 
                                    
                    <div id='importing_contents' style='height: 394px; overflow: auto; clear: both;'>
                    	<div id='filerow0'></div>
                    	<div style="padding: 25px;" id="start_message"><?php echo $mgrlang['starting_import']; ?>...</div>
                    </div>
                    
                    <div style="padding: 10px; text-align: right; background-color: #EEE"><input type="button" value="<?php echo $mgrlang['gen_stop']; ?>" onclick="stop_import();" id="stop_button" disabled="disabled" /><input type="button" value="<?php echo $mgrlang['add_more_to_batch']; ?>" onclick="import_more();" id="import_more" disabled="disabled" /><input type="button" value="<?php echo $mgrlang['import_new_batch']; ?>" onclick="window.location='mgr.add.media.php?ep=1'" id="import_new_batch" disabled="disabled" /><input type="button" value="<?php echo $mgrlang['gen_manage_media']; ?> &raquo;" onclick="window.location='mgr.media.php?ep=1'" id="manage_my_media" disabled="disabled" /></div>                  
                </div>
                                
                <div class="group_container" id="gc_a" style="width: <?php if($config['settings']['auto_folders']){ echo '98%'; } else { echo '48%'; } ?>;">
                    <?php $row_color = 0; ?>
                    <div id="tab_a2_group" class="group" style="display: block; margin: 0; padding: 0; min-height: 480px;">
                        <div class="steps"><!--<img src="images/mgr.cir2.png" align="middle" /> --><span><?php echo $mgrlang['gen_imp_media']; ?> </span><input type="button" value="<?php echo $mgrlang['upload_new_media']; ?>" <?php if($config['settings']['uploader'] == '4xxx'){ echo "onclick=\"openPluploadWindow();\""; } else { echo "onclick='uploadbox(uploadobj);'"; } ?> style="margin-top: -4px; margin-left: 6px;" /></div>
                        <div id="vmessage" style="display: none; border-bottom: 1px solid #6a0a09; background-color: #a91513; color: #FFFFFF; padding: 5px 0px 5px 20px; font-weight: bold; background-image: url(images/mgr.warning.bg.gif); background-repeat: repeat-x;"><img src='images/mgr.notice.icon.small.gif' align='absmiddle' />&nbsp; <?php echo $mgrlang['files_uploaded']; ?></div>
                        <div style="padding: 15px 25px 5px 25px; height: 360px;" id="import_list_div"></div>
                        <?php if($config['settings']['auto_folders']){ ?><div style="text-align: right; padding: 10px 25px 25px 25px;"><p id="step_1_buttons">&nbsp;<input type="button" value="<?php echo $mgrlang['gen_continue']; ?>" onclick="check_folder_name();" id="step2_continue" /></p></div><?php } ?>
                    </div>
                </div>
                
                <form id="folder_setup">
                <input type="hidden" id="folder_id" name="folder_id" value="0" />
                <div class="group_container" id="gc_b" style="display: <?php if($config['settings']['auto_folders']){ echo 'none'; } else { echo 'block'; } ?>; width: 47%;">       
                    <?php $row_color = 0; ?>
                    <div id="tab_b1_group" class="group" style="display: block; margin: 0; padding: 0; height: 480px;">
                        <div class="steps"><!--<img src="images/mgr.cir3.png" align="middle" /> --><span><?php echo $mgrlang['choose_save_loc']; ?></span></div>
                        <div style="height: 350px;">
                            <div style="padding: 25px 25px 10px 25px;">
                                <?php echo $mgrlang['import_folder']; ?>:<br />                              
                                <select style="width: 240px" onchange='cnf_check();' id="folder">
                                    <option value="0" stype="cnf"><?php echo $mgrlang['create_folder']; ?>...</option>
                                    <?php
										$folder_result = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}folders");
										$folder_rows = mysqli_num_rows($folder_result);
										while($folder = mysqli_fetch_object($folder_result))
										{
											if($folder->storage_id == 0)
											{
												$storage_type = 'local';
											}
											else
											{
												$storage_result = mysqli_query($db,"SELECT storage_id,name,storage_type FROM {$dbinfo[pre]}storage WHERE storage_id = '$folder->storage_id'");
                                            	$storage = mysqli_fetch_object($storage_result);
												$storage_type = $storage->storage_type;
											}
											echo "<option value='$folder->folder_id' stype='$storage_type'>$folder->name</option>";
										}
									?>                                    
                                </select>
                            </div>
                            <?php
								$ndate = new kdate;
								$ndate->distime = 0;
								$new_date = $ndate->date_to_form(gmt_date());
								
								//$ndate->setDateFormat('INT');
								//$ndate->setDateDisplayType('NUM');
								//$ndate->setDateSeperator('-');
							?>
                            <div style="padding: 15px 25px 5px 25px;" id="folder_name_div">
                                <label><?php echo $mgrlang['folders_f_name']; ?>:</label><br />                                
                                <input type="text" value="<?php echo $new_date['year'] . "-" . $new_date['month'] . "-" . $new_date['day']; ?>" id="folder_name" style="width: 228px" class=""> 
								<br /><br />
                                <p style="<?php if(!in_array("pro",$installed_addons)){ echo "display: none;"; } ?>"><input type="checkbox" id="encrypt_folder" value="1"  <?php if($config['settings']['enc_folders']){ echo "checked"; } ?> /> <label for="encrypt_folder"><?php echo $mgrlang['enc_folder']; ?> <img src="images/mgr.med.lock.1.png" style="vertical-align: middle; margin-left: 4px; margin-top: -3px;"></label></p>
								<?php
									if(in_array("storage",$installed_addons))
									{
								?>
                                    <br /><br />
                                    <label><?php echo $mgrlang['folders_f_storage']; ?>:</label><br />
                                    <select name="storage_id" id="storage_id" style="width: 240px;" onchange="check_storage_id();">
                                        <option value="0" stype="local"><?php echo $mgrlang['local_lib']; ?></option>
                                        <?php
                                            $storage_result = mysqli_query($db,"SELECT storage_id,name,storage_type FROM {$dbinfo[pre]}storage WHERE active = '1'");
                                            while($storage = mysqli_fetch_object($storage_result))
                                            {
                                                echo "<option value='$storage->storage_id' stype='$storage->storage_type'>$storage->name</option>";
                                            }
                                        ?>
                                    </select>
                                <?php
									}
									else
									{
										echo "<input type='hidden' name='storage_id' id='storage_id' value='0' stype='local' />";
									}
								?>
                                <div id="storage_warning" style="font-size: 11px; color: #666; padding: 6px 0 0 0; display: none;"><img src="images/mgr.notice.icon.small2.png" align="absmiddle" /> <?php echo $mgrlang['ext_store_limit']; ?> <strong><?php echo $config['OffsiteStogageLimit']; ?>MB</strong>. <?php echo $mgrlang['large_files_skipped']; ?></div>
                                <br /><br />
                            </div>
                        </div>                        
                        <div style="text-align: right; padding: 25px;"><input type="button" value="<?php echo $mgrlang['gen_continue']; ?>" onclick="check_folder_name();" /></div>
                    </div>
                </div>
                </form>
                
            </div>
            <!-- END CONTENT -->
            <div class="footer_spacer"></div>
        </div>        
		<!-- END CONTENT CONTAINER -->
		<?php include("mgr.footer.php"); ?>		
	</div>
</body>
</html>
<?php mysqli_close($db); ?>
