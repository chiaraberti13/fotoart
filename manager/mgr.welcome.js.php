<?php
		# LOAD PANELS AND JAVASCRIPT FUNCTIONS
		$leftpanels = explode(",",$_SESSION['admin_user']['wp_left']);
		$rightpanels = explode(",",$_SESSION['admin_user']['wp_right']);
		
		# READ IN ALL NAV.* FILES
		$real_dir = realpath("wpanels/");
		$dir = opendir($real_dir);
		# LOOP THROUGH THE DIRECTORY
		$panel_mode = "preload";
		while($file = readdir($dir))
		{
			if($file != ".." && $file != ".")
			{
				if(file_exists('wpanels/' . $file . '/panel.php'))
				{				
				//if(is_dir($file)){
				//if(findexts($file) == "php"){
					include('wpanels/' . $file . "/panel.php");
					if($panel_enable == 1)
					{
						$panelidsarr[] = $panel_id;
						$panelfilearr[] = $file;
						$panelnamearr[] = $panel_name;
						$paneltemparr[] = $panel_template;
					}
				}			
			}
		}
		closedir($dir);
		//exit;
	?>
	<script type="text/javascript">
		//window.onload = function() {
			
		//}
		
		// SET WIZARD TO 0
		function set_wizard_popup_off()
		{
			new Ajax.Request('mgr.welcome.actions.php', {
				parameters: {
					pmode: 'wizardoff'
				}
			});
			close_message();
		}
		
		// SET WIZARD TO 0 TEMPORARILY
		function set_wizard_popup_tempoff()
		{
			new Ajax.Request('mgr.welcome.actions.php', {
				parameters: {
					pmode: 'wizardtempoff'
				}
			});
			close_message();
		}
		
		// LOAD PANEL DATA
		function load_panel(updatecontent,loadpage,pars)
		{
			$(updatecontent).update("<img src=\"images/mgr.loader2.gif\" align=\"absmiddle\" style=\"margin: 6px;\"> <?php echo $mgrlang['gen_loading']; ?>");
			var loadpath = "wpanels/" + loadpage;
			var pars = "panel_language=<?php echo $config['settings']['lang_file_mgr']; ?>&panel_mode=load";
			var myAjax = new Ajax.Updater(updatecontent, loadpath, {method: 'get', parameters: pars, evalScripts: true});
		}
		function update_panels()
		{
			// SET PANEL BUTTON TO ANIMATED
			$('img_panels').src = "images/mgr.loader.gif";
			$('img_panels').setStyle({paddingTop: '3px',paddingBottom: '3px'});
			
			// ONLY SEND VISIBLE PANELS (LEFT)
			var leftlistarr = new Array();
			var lpanelvalue;
			var leftlist = Sortable.serialize('panelsleft');
			leftlist = leftlist.split("&");
			//alert(leftlist);
			var newx = 0;
			if(Sortable.serialize('panelsleft').length > 0)
			{
				//alert(leftlist.length);
				for(var x=0; x < leftlist.length; x++)
				{
					lpanelvalue = leftlist[x].split("=");					
					//alert($('box_' + lpanelvalue[1]));
					if($('box_' + lpanelvalue[1]).style.display != "none")
					{
						leftlistarr[newx] = lpanelvalue[1];
						newx++;
					}
				}
			}
			else
			{
				leftlistarr[0] = "";
			}
			// ONLY SEND VISIBLE PANELS (RIGHT)
			var rightlistarr = new Array();
			var rpanelvalue;
			var rightlist = Sortable.serialize('panelsright');
			rightlist = rightlist.split("&");
			var newy = 0;
			if(Sortable.serialize('panelsright').length > 0)
			{
				for(var y=0; y < rightlist.length; y++)
				{
					rpanelvalue = rightlist[y].split("=");
					if($('box_' + rpanelvalue[1]).style.display != "none")
					{
						rightlistarr[newy] = rpanelvalue[1];
						newy++;
					}
				}
			}
			else
			{
				rightlistarr[0] = "";
			}
			
			//$('wpdisplay').update("<?php echo $mgrlang['gen_saving']; ?>...");
					
			var updatecontent = "wpdisplay";
			var loadpage = "mgr.welcome.actions.php";			
			var pars = "&lpanels=" + leftlistarr + "&rpanels=" + rightlistarr;
			//var pars = "&test";
			//var pars = "&" + Sortable.serialize('panelsright') + "&" + Sortable.serialize('panelsleft');
			var myAjax = new Ajax.Updater(updatecontent, loadpage, {method: 'get', parameters: pars, evalScripts: true});
		}
		function close_panel(box)
		{
			// SET PANEL BUTTON TO ANIMATED
			//$('panel_button').src = "images/mgr.panels.ani.gif";
			Effect.Fade(box,{ duration: 0.5, afterFinish: update_panels });
			//update_panels();
			$('cb_' + box).checked = false;
		}
		function createsort(panel)
		{
			Sortable.create(panel,
				{tag:'div',only: 'welcome_box', handle: 'phandle', containment:["panelsleft","panelsright"], overlap:'horizontal',constraint:false,dropOnEmpty:true, 
					onUpdate:function()
						{
							update_panels();
							//alert(Sortable.serialize(panelsleft));
						}
				});
		}
		function panelbool(box,loadpage,boxname,usetemplate)
		{
			if($('cb_' + box).checked == true)
			{
				create_panel(box,loadpage,boxname,usetemplate);	
			}
			else
			{
				close_panel(box);
			}		
		}		
		function create_panel(box,loadpage,boxname,usetemplate)
		{
			// SET PANEL BUTTON TO ANIMATED
			//$('panel_button').src = "images/mgr.panels.ani.gif";
			
			if($(box) != null)
			{
				Effect.Appear(box,{ duration: 1.0, afterFinish: update_panels });
			}
			else
			{
				newelement = document.createElement("div");
				newelement.id = box;
				newelement.className = 'welcome_box';
				newelement.style.display = 'none';
				if(usetemplate == 1)
				{
					panelhtml = "<div class='welcome_box_inner'><div class='welcome_box_header'><h1 class='phandle'>" + boxname + "</h1><div><a href='javascript:close_panel(\"" + box + "\");'><img src='./images/mgr.button.close.gif' border='0' /></a></div></div><div id='" + box + "_content' class='welcome_box_content'><img src='images/mgr.loader2.gif' align='absmiddle' style='margin: 6px;' /></div></div>";
				}
				else
				{
					//alert('not');
					//newelement.style.background = 'none';
					newelement.style.border = 'none';
					newelement.style.background = 'none';
					panelhtml = "<div class='welcome_box_inner' style='border: none; background: none;'><div id='"+box+"_content'><img src='images/mgr.loader2.gif' align='absmiddle' style='margin: 6px;' /> Loading "+boxname+"</div></div>"; 
					//alert(newelement.id);
					//$(newelement.id).setStyle({
					//	background: 'none',
					//	border: 'none'
					//});
				
				}
				newelement.innerHTML = panelhtml;
				$('panelsleft').appendChild(newelement);
				
				//loadElement('wpanels/' + loadpage,'',box + '_content');
				var page = 'wpanels/' + loadpage + '/panel.php';
				var pars = "?panel_language=<?php echo $config['settings']['lang_file_mgr']; ?>&panel_mode=load";
				var update = box + '_content';
				var myAjax = new Ajax.Updater(
					update, 
					page, 
					{
						method: 'get', 
						parameters: pars,
						evalScripts: true
					});
								
				Effect.Appear(box,{ duration: 1.0, afterFinish: done_appearing });
			}
		}		
		function done_appearing()
		{
			// UPDATE PANELS
			update_panels();				
			// RECREATE SORTABLE
			createsort('panelsleft');
			// LOAD CONTENT
			//loadElement('wpanels/' + loadpage,'',box + '_content');
		}
		
		function hide_qsupport_bubble()
		{
			setTimeout("hide_div_fade('qsupport_bubble')",'10000');
		}
		
		function reset_page()
		{
			hide_div('shortcuts','loader');
			//Effect.Fade('panels',{ duration: 0.5 });
			//$('panel_button').src = 'images/mgr.panels.off.png';
			//pb_status = 'off';
		}
		
		function fix_panels_location()
		{
			//Effect.Fade('panels',{ duration: 0.5 });
			//Effect.Appear('panels',{ duration: 1.0, from: 0.9999, to: 1.0 });
		}
		
		// RELOAD PANEL
		function panel_reload(panel){
			panel_loader(panel);	
			var page = 'wpanels/' + panel.filename + '/panel.php';
			var pars = "?panel_language="+panel.language+"&panel_mode=load";
			var update = 'box_' + panel.pid + '_content';
			var myAjax = new Ajax.Updater(
				update, 
				page, 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true
				});
		}
		// SHOW PANEL LOADER
		function panel_loader(panel){
			$('box_' + panel.pid + '_content').update('<img src="images/mgr.loader2.gif" align="absmiddle" style="margin: 6px;" />');
		}
		// CLOSE THE PANEL
		function panel_close(panel){
			close_panel('box_' + panel.pid);
		}
		// LOAD SPECIFIC PANEL PAGE WITH PARS
		function panel_loadpage(passedpage,passedpars,panel){
			panel_loader(panel);
			if(passedpage == null || passedpage == ''){
				var page = 'wpanels/' + panel.filename + '/panel.php';
			} else {
				var page = 'wpanels/' + panel.filename + '/' + passedpage;
			}	
			var pars = 'panel_language='+ panel.language + passedpars;
			var update = 'box_' + panel.pid + '_content';
			var myAjax = new Ajax.Updater(
				update, 
				page, 
				{
					method: 'get', 
					parameters: pars,
					evalScripts: true
				});
		}
		// CLEAR PANEL COMPLETELY
		panel_clear = function(panel){
			var toupdate = 'box_' + panel.pid; 
			//alert(toupdate);
			$(toupdate).setStyle({
				background: 'none',
				border: 'none'
			});
			$(toupdate).update('');
		}
		
		// ADVANCED PANEL HACK TEST
		panel_hack = function(panel){
			panel_clear(panel);
			var newpanelid = 'box_' + panel.pid;
			$(newpanelid).setStyle({
				border: '1px dashed #d4d4d4'
			});
			$(newpanelid).update('<div id="'+newpanelid+'_content"><h1 class="phandle" style="cursor: pointer">Drag</h1><img src="wpanels/blank_panel/test.png" /><a href="javascript:panel_reload('+panel.pid+')">[Reload Panel]</a></div>');
			createsort('panelsleft');
			createsort('panelsright');
		}
		
		Event.observe(window, 'load', function()
		{			

			// LOAD THE WELCOME PANELS
			<?php
				// OUTPUT LOADERS
				foreach($panelfilearr as $key => $value)
				{
					if(in_array($panelidsarr[$key],$leftpanels) or in_array($panelidsarr[$key],$rightpanels))
					{
						echo "loadElement(\"wpanels/" . $value . "/panel.php?panel_language=" . $config['settings']['lang_file_mgr'] . "&panel_mode=load\",'',\"box_" . $panelidsarr[$key] . "_content\");\n";
						// onclick="loadElement('wpanels/wp.blank.php','','box_blankwp_content');"
					}
				}
			?>			
			// FIX SHORTCUTS HEIGHT
			shortcuts_height();
			
			// SUPPORT BUBBLE
			show_div_fade('qsupport_bubble');
			hide_qsupport_bubble();
			
			<?php
				# IF WIZARD IS SET TO 1 SHOW THE POPUP
				if($_SESSION['admin_user']['wizard'])
				{
			?>
				// FIRST TIME LOGGING IN BOX
				message_box('<?php echo $mgrlang['welcome_ftv_wizard']; ?>','<input type="button" value="Yes" onclick="location.href=\'../assets/wizard/\';" /><input type="button" value="No" onclick="set_wizard_popup_off();" /><input type="button" value="Remind Me Later" onclick="set_wizard_popup_tempoff();" />','');
			<?php
				}
			?>

			// PANELS BUTTON
			if($('abutton_panels')!=null)
			{
				$('abutton_panels').observe('click', function()
					{
						$('panel_selector').toggle();
						hide_div('qsupport_bubble');
					});
				$('abutton_panels').observe('mouseover', function()
					{
						$('img_panels').src='./images/mgr.button.group.png';
					});
				$('abutton_panels').observe('mouseout', function()
					{
						$('img_panels').src='./images/mgr.button.group.off.png';
					});
			}
			
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
		});
	</script>