<?php
	require_once('../assets/includes/session.php');					# INCLUDE THE SESSION START FILE
	
	//require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
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

	require_once('../assets/includes/clean.data.php');

	//print_r($items);
?>
<input type="hidden" value="1" id="batchActive" />
<h1><?php echo $mgrlang['batch_edit']; ?> <span id="batchItemCount">(<?php echo count($items); ?>)</span></h1>
<img src="./images/mgr.button.close2.png" id="batchCloseButton" border="0" onclick="closeBatchWindow();">
<h2 onclick="openBatchEditGroup('beGroupDetails');"><?php echo $mgrlang['gen_details']; ?></h2>
<div class="beGroup" id="beGroupDetails" style="display: block;">								
	<p>
		<span class="beTitle"><?php echo $mgrlang['media_f_title']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_title">- <?php echo $mgrlang['saved']; ?>!</span><br />
		<input type="text" name="batchTitle" style="width: 160px;" />
		<?php
			if(in_array('multilang',$installed_addons)){
		?>
			&nbsp;<span class="mtag_dblue" style="cursor: pointer" onclick="displaybool('lang_batchTitle','','','','plusminus-11');"><img src="./images/mgr.plusminus.0.png" id="plusminus11" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
			<div id="lang_batchTitle" style="display: none;">
			<ul>
			<?php
				foreach($active_langs as $value){
			?>
				<li><input type="text" name="batchTitle_<?php echo $value; ?>" id="batchTitle_<?php echo $value; ?>" style="width: 160px;" maxlength="100" value="<?php echo @stripslashes($mediaInfo['title_'.$value]); ?>" />&nbsp;&nbsp;<span class="mtag_dblue"><?php echo ucfirst($value); ?></span></li>
		<?php
				}
				echo "</ul></div>";
			}
		?>
		<input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('title','');" class="save_title" />
	</p>
	<hr />								
	<p>
		<span class="beTitle"><?php echo $mgrlang['media_f_description']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_description">- <?php echo $mgrlang['saved']; ?>!</span><br />
		<textarea name="batchDesc" style="width: 160px; vertical-align: middle"></textarea>
		<?php
			if(in_array('multilang',$installed_addons)){
		?>
			&nbsp;<span class="mtag_dblue" style="cursor: pointer" onclick="displaybool('lang_batchDesc','','','','plusminus-12');"><img src="./images/mgr.plusminus.0.png" id="plusminus12" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span>
			<div id="lang_batchDesc" style="display: none;">
			<ul>
			<?php
				foreach($active_langs as $value){
			?>
				<li><textarea name="batchDesc_<?php echo $value; ?>" style="width: 160px; height: 50px; vertical-align: middle"><?php echo @stripslashes($mediaInfo['description_'.$value]); ?></textarea>&nbsp;&nbsp;<span class="mtag_dblue"><?php echo ucfirst($value); ?></span></li>
		<?php
				}
				echo "</ul></div>";
			}
		?>
		<input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" style="vertical-align:text-top;" onclick="saveBatchOptions('description','');" class="save_description" />
	</p>
	<hr />
	<p>
		<span class="beTitle"><?php echo $mgrlang['mediadet_keywords']; ?></span><br />
		
		<div class="beKeywordList">
			<?php
				if(in_array('multilang',$installed_addons)){
			?>
				<div style="float: right;"><span class="mtag_dblue" style="cursor: pointer" onclick="displaybool('lang_batchKeywords','','','','plusminus-13');"><img src="./images/mgr.plusminus.0.png" id="plusminus13" align="texttop" style="margin: 2px 4px 0 0" border="0" /><?php echo ucfirst($config['settings']['lang_file_mgr']); ?></span></div>
			<?php
				}
			?>		
			<div id="batchKeywordsList" style="clear: both; padding-top: 10px;"></div>
		</div>
				
		<input type="text" name="beNewKeyword_<?php echo $config['settings']['lang_file_mgr']; ?>" id="beNewKeyword_<?php echo $config['settings']['lang_file_mgr']; ?>" style="width: 178px;" /> <input type="button" value="Add" style="vertical-align:text-top;" onclick="beAddKeyword('<?php echo $config['settings']['lang_file_mgr']; ?>');" />
		
		<?php
			if(in_array('multilang',$installed_addons)){
		?>
			<div id="lang_batchKeywords" style="display: none;">
			<?php
				foreach($active_langs as $value){
			?>
				<hr />
				<div class="beKeywordList">
					<div style="float: right;"><span class="mtag_dblue"><?php echo ucfirst($value); ?></span></div>
					<div id="batchKeywordsList_<?php echo $value; ?>" style="clear: both; padding-top: 10px;"></div>
				</div>				
				<input type="text" name="beNewKeyword_<?php echo $value; ?>" id="beNewKeyword_<?php echo $value; ?>" style="width: 178px;" /> <input type="button" value="Add" style="vertical-align:text-top;" onclick="beAddKeyword('<?php echo $value; ?>');" />
			<?php
				}
				echo "</div>";
			}
		?>
		
	</p>
</div>							
<h2 onclick="openBatchEditGroup('beGroupGalleries');"><?php echo $mgrlang['gen_tab_galleries']; ?> &nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_galleries">- <?php echo $mgrlang['saved']; ?>!</span></h2>
<div class="beGroup" id="beGroupGalleries" style="display: none;">
	<div style="clear: both;">
	<?php
		if($_GET['gal_mem'])
		{
			$gal_mem = $_GET['gal_mem'];
		}
		else
		{
			$gal_mem = 0;	
		}

		// CREATE ARRAY TO WORK WITH							
		$folders = array();
		$folders['name'] = array();
		$folders['folder_id'] = array();
		$folders['parent_id'] = array();
		$folders['folder_rows'] = array();
		$folders['pass_protected'] = array();
		$folder_array_id = 1;
		
		// READ STRUCTURE FUNCTION															
		read_gal_structure(0,'name','',$gal_mem);
		
		//$gallery_parent = $gallery->parent_gal;
		$gallery_current = 0;
		
		# BUILD THE GALLERIES AREA
		$mygalleries = new build_galleries;
		$mygalleries->scroll_offset_id = "gals";
		//$mygalleries->alt_colorA = "efefef";
		$mygalleries->alt_colorA = "e1e1e1";
		$mygalleries->alt_colorB = "e1e1e1";
		$mygalleries->scroll_offset = 1;
		$mygalleries->selected_gals = $inGalleries;
		$mygalleries->options_name = 'beMediaGalleries[]';
		$mygalleries->options = "checkbox";
		$mygalleries->output_struc_array(0);
	?>
	</div>
	<?php
		echo "<div style='clear: both; margin-top: 15px;'>";
		echo "<input type='button' value='{$mgrlang[add_selected]}' onclick=\"saveBatchOptions('galleries','add');\" class='save_galleries' />";
		echo "<input type='button' value='{$mgrlang[remove_selected]}' onclick=\"saveBatchOptions('galleries','remove');\" class='save_galleries' />";
		echo "</div>";
	?>
</div>

<h2 onclick="openBatchEditGroup('beGroupDigitalVersions');"><?php echo $mgrlang['gen_dig_ver']; ?></h2>
<div class="beGroup" id="beGroupDigitalVersions" style="display: none;">
	<span class="beTitle"><?php echo $mgrlang['media_f_orgcopy']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_originalCopy">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<select id="beOriginalCopy" name="beOriginalCopy" style="width: 238px;">
		<option value="nfs" <?php if($mediaInfo['license'] == 'nfs'){ echo "selected='selected'"; } ?>><?php echo $mgrlang['media_f_hidden']; ?></option>                            
		<?php
			$licenseResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}licenses");
			while($license = mysqli_fetch_assoc($licenseResult))
			{
				echo "<option ' value='{$license['lic_purchase_type']}-{$license['license_id']}' ";
				//if($mediaInfo['license'] == $license['license_id']) echo "selected='selected'";
				echo ">{$license[lic_name]}</option>";
			}
		?>                           
	</select><br />
	<input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('originalCopy','');" class="save_originalCopy" />
	<hr />
	<span class="beFiletype"><?php echo $mgrlang['media_f_format']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_filetype">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<input type="text" value="" name="beFiletype" style="width: 150px" /><input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('filetype','');" class="save_filetype" />
	<hr />
	<span class="beTitle"><?php echo $mgrlang['media_f_price']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_price">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<input type="text" value="" name="bePrice" style="width: 150px" /><input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('price','');" class="save_price" />
	<hr />
	<span class="beTitle"><?php echo $mgrlang['media_f_credits']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_credits">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<input type="text" value="" name="beCredits" style="width: 150px" /><input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('credits','');" class="save_credits" />
	<hr />	
	<span class="beTitle"><?php echo $mgrlang['media_f_digsizes']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_digitalSizes">- <?php echo $mgrlang['saved']; ?>!</span><br /><br />
	<ul>
	<?php
		$digitalSizesResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}digital_sizes WHERE active = '1' AND deleted = '0' ORDER BY sortorder");
		if($digitalSizesRows = mysqli_num_rows($digitalSizesResult))
		{
			while($digitalSize = mysqli_fetch_array($digitalSizesResult))
			{	
				echo "<li><input type='checkbox' name='beDigitalSize[]' id='beDigitalSize{$digitalSize['ds_id']}' value='{$digitalSize['ds_id']}' /> <label for='beDigitalSize{$digitalSize['ds_id']}'>{$digitalSize['name']}</label></li>";	
			}
		}
	?>
	</ul>	
	<input type="button" value="<?php echo $mgrlang['add_selected']; ?>" onclick="saveBatchOptions('digitalSizes','add');" class="save_digitalSizes" />
	<input type="button" value="<?php echo $mgrlang['remove_selected']; ?>" onclick="saveBatchOptions('digitalSizes','remove');" class="save_digitalSizes" />
</div>
<h2 onclick="openBatchEditGroup('beGroupProducts');"><?php echo $mgrlang['gen_prods']; ?> &nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_products">- <?php echo $mgrlang['saved']; ?>!</span></h2>
<div class="beGroup" id="beGroupProducts" style="display: none;">
	<?php
		$productGroupResult = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'products' ORDER BY name");
		$productGroupRows = mysqli_num_rows($productGroupResult);
		
		if($productGroupRows)
		{
			echo "<div class='groupContainer'>";
			echo "<h3>{$mgrlang[gen_b_grps]}</h3>";
			echo "<ul class='pad'>";
				while($productGroup = mysqli_fetch_assoc($productGroupResult))
				{
					echo "<li><input type='checkbox' name='beProductGroups[]' value='{$productGroup[gr_id]}' id='buProductGroup{$productGroup[gr_id]}'> <label for='buProductGroup{$productGroup[gr_id]}'>{$productGroup[name]}</label></li>";
				}
			echo "</ul>";
			echo "</div>";
		}
		
		$productResult = mysqli_query($db,"SELECT prod_id,item_name,price,credits,product_type FROM {$dbinfo[pre]}products WHERE deleted='0'");
		$productRows = mysqli_num_rows($productResult);
		
		if($productRows)
		{
			echo "<ul class='pad'>";
				while($product = mysqli_fetch_assoc($productResult))
				{
					echo "<li><input type='checkbox' name='beProducts[]' value='{$product[prod_id]}' id='beProduct{$product[prod_id]}'> <label for='beProduct{$product[prod_id]}'>{$product[item_name]}</label></li>";
				}
			echo "</ul>";
		}
		
		echo "<br>";
		echo "<input type='button' value='{$mgrlang[add_selected]}' onclick=\"saveBatchOptions('products','add');\" class='save_products' />";
		echo "<input type='button' value='{$mgrlang[remove_selected]}' onclick=\"saveBatchOptions('products','remove');\" class='save_products' />";
	?>
</div>

<h2 onclick="openBatchEditGroup('beGroupPrints');"><?php echo $mgrlang['gen_prints']; ?> &nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_prints">- <?php echo $mgrlang['saved']; ?>!</span></h2>
<div class="beGroup" id="beGroupPrints" style="display: none;">
	<?php
		$printGroupResult = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'prints' ORDER BY name");
		$printGroupRows = mysqli_num_rows($printGroupResult);
		
		if($printGroupRows)
		{
			echo "<div class='groupContainer'>";
			echo "<h3>{$mgrlang[gen_b_grps]}</h3>";
			echo "<ul class='pad'>";
				while($printGroup = mysqli_fetch_assoc($printGroupResult))
				{
					echo "<li><input type='checkbox' name='bePrintGroups[]' value='{$printGroup[gr_id]}' id='bePrintGroup{$printGroup[gr_id]}'> <label for='bePrintGroup{$printGroup[gr_id]}'>{$printGroup[name]}</label></li>";
				}
			echo "</ul>";
			echo "</div>";
		}
		
		$printResult = mysqli_query($db,"SELECT print_id,item_name,price,credits FROM {$dbinfo[pre]}prints WHERE deleted='0'");
		$printRows = mysqli_num_rows($printResult);
		
		if($printRows)
		{
			echo "<ul class='pad'>";
				while($print = mysqli_fetch_assoc($printResult))
				{
					echo "<li><input type='checkbox' name='bePrints[]' value='{$print[print_id]}' id='bePrint{$print[print_id]}'> <label for='bePrint{$print[print_id]}'>{$print[item_name]}</label></li>";
				}
			echo "</ul>";
		}
		
		echo "<br>";
		echo "<input type='button' value='{$mgrlang[add_selected]}' onclick=\"saveBatchOptions('prints','add');\" class='save_prints' />";
		echo "<input type='button' value='{$mgrlang[remove_selected]}' onclick=\"saveBatchOptions('prints','remove');\" class='save_prints' />";
	?>
</div>

<h2 onclick="openBatchEditGroup('beGroupPackages');"><?php echo $mgrlang['gen_packs']; ?> &nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_packages">- <?php echo $mgrlang['saved']; ?>!</span></h2>
<div class="beGroup" id="beGroupPackages" style="display: none;">
	<?php
		$packGroupResult = mysqli_query($db,"SELECT gr_id,flagtype,name FROM {$dbinfo[pre]}groups WHERE mgrarea = 'packages' ORDER BY name");
		$packGroupRows = mysqli_num_rows($packGroupResult);
		
		if($packGroupRows)
		{
			echo "<div class='groupContainer'>";
			echo "<h3>{$mgrlang[gen_b_grps]}</h3>";
			echo "<ul class='pad'>";
				while($packGroup = mysqli_fetch_assoc($packGroupResult))
				{
					echo "<li><input type='checkbox' name='bePackGroups[]' value='{$packGroup[gr_id]}' id='bePackGroup{$packGroup[gr_id]}'> <label for='bePackGroup{$packGroup[gr_id]}'>{$packGroup[name]}</label></li>";
				}
			echo "</ul>";
			echo "</div>";
		}
		
		$packResult = mysqli_query($db,"SELECT pack_id,item_name,price,credits FROM {$dbinfo[pre]}packages WHERE deleted='0'");
		$packRows = mysqli_num_rows($packResult);
		
		if($packRows)
		{
			echo "<ul class='pad'>";
				while($pack = mysqli_fetch_assoc($packResult))
				{
					echo "<li><input type='checkbox' name='bePack[]' value='{$pack[pack_id]}' id='bePack{$pack[pack_id]}'> <label for='bePack{$pack[pack_id]}'>{$pack[item_name]}</label></li>";
				}
			echo "</ul>";
		}
		
		echo "<br>";
		echo "<input type='button' value='{$mgrlang[add_selected]}' onclick=\"saveBatchOptions('packages','add');\" class='save_packages' />";
		echo "<input type='button' value='{$mgrlang[remove_selected]}' onclick=\"saveBatchOptions('packages','remove');\" class='save_packages' />";
	?>
</div>

<h2 onclick="openBatchEditGroup('beGroupCollections');"><?php echo $mgrlang['gen_colls']; ?> &nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_collections">- <?php echo $mgrlang['saved']; ?>!</span></h2>
<div class="beGroup" id="beGroupCollections" style="display: none;">
	<?php
		$collectionsResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}collections WHERE colltype = '2' AND deleted = 0");
		$collectionsRows = mysqli_num_rows($collectionsResult);
		
		if($collectionsRows)
		{
			echo "<ul class='pad'>";
				while($collection = mysqli_fetch_assoc($collectionsResult))
				{
					echo "<li><input type='checkbox' name='beCollections[]' value='{$collection[coll_id]}' id='beCollection{$collection[coll_id]}'> <label for='beCollection{$collection[coll_id]}'>{$collection[item_name]}</label></li>";
				}
			echo "</ul>";
		}
		
		echo "<br>";
		echo "<input type='button' value='{$mgrlang[add_selected]}' onclick=\"saveBatchOptions('collections','add');\" class='save_collections' />";
		echo "<input type='button' value='{$mgrlang[remove_selected]}' onclick=\"saveBatchOptions('collections','remove');\" class='save_collections' />";
	?>
</div>

<h2 onclick="openBatchEditGroup('beGroupMediaTypes');"><?php echo $mgrlang['gen_media_types']; ?> &nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_mediaTypes">- <?php echo $mgrlang['saved']; ?>!</span></h2>
<div class="beGroup" id="beGroupMediaTypes" style="display: none;">
	<?php
		$mediaTypesResult = mysqli_query($db,"SELECT * FROM {$dbinfo[pre]}media_types");
		$mediaTypesRows = mysqli_num_rows($mediaTypesResult);
		
		if($mediaTypesRows)
		{
			echo "<ul class='pad'>";
				while($mediaType = mysqli_fetch_assoc($mediaTypesResult))
				{
					echo "<li><input type='checkbox' name='beMediaTypes[]' value='{$mediaType[mt_id]}' id='beMediaType{$mediaType[mt_id]}'> <label for='beMediaType{$mediaType[mt_id]}'>{$mediaType[name]}</label></li>";
				}
			echo "</ul>";
		}
		
		echo "<br>";
		echo "<input type='button' value='{$mgrlang[add_selected]}' onclick=\"saveBatchOptions('mediaTypes','add');\" class='save_mediaTypes' />";
		echo "<input type='button' value='{$mgrlang[remove_selected]}' onclick=\"saveBatchOptions('mediaTypes','remove');\" class='save_mediaTypes' />";
	?>
</div>
<h2 onclick="openBatchEditGroup('beGroupAdvanced');"><?php echo $mgrlang['gen_tab_advanced']; ?></h2>
<div class="beGroup" id="beGroupAdvanced" style="display: none;">
	<span class="beTitle"><?php echo $mgrlang['mediadet_dateadded']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_dateAdded">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<div>
		<select style="width: 70px;" name="beAddedYear">
			<option value="0000"></option>
			<?php
				$todayYear = date("Y");
				for($i=1950; $i<(date("Y")+6); $i++){
					if(strlen($i) < 2){
						$dis_i_as = "0$i";
					} else {
						$dis_i_as = $i;
					}
					echo "<option ";
					if($todayYear == $dis_i_as){
						echo "selected";
					}
					echo ">$dis_i_as</option>";
				}
			?>
		</select>
		<select style="width: 55px;" name="beAddedMonth">
			<option value="00"></option>
			<?php
				$todayMonth = date("m");
				for($i=1; $i<13; $i++){
					if(strlen($i) < 2){
						$dis_i_as = "0$i";
					} else {
						$dis_i_as = $i;
					}
					echo "<option ";
					if($todayMonth == $dis_i_as){
						echo "selected";
					}
					echo ">$dis_i_as</option>";
				}
			?>
		</select>
		<select style="width: 55px;" name="beAddedDay">
			<option value="00"></option>
			<?php
				$todayDay = date("d");
				for($i=1; $i<=31; $i++){
					if(strlen($i) < 2){
						$dis_i_as = "0$i";
					} else {
						$dis_i_as = $i;
					}
					echo "<option ";
					if($todayDay == $dis_i_as){
						echo "selected";
					}
					echo ">$dis_i_as</option>";
				}
			?>
		</select>
		<input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('dateAdded','');" class="save_dateAdded" />
	</div>
	<hr />
	<span class="beTitle"><?php echo $mgrlang['mediadet_datecreated']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_dateCreated">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<div>
		<select style="width: 70px;" name="beCreatedYear">
			<option value="0000"></option>
			<?php
				$todayYear = date("Y");
				for($i=1950; $i<(date("Y")+6); $i++){
					if(strlen($i) < 2){
						$dis_i_as = "0$i";
					} else {
						$dis_i_as = $i;
					}
					echo "<option ";
					if($todayYear == $dis_i_as){
						echo "selected";
					}
					echo ">$dis_i_as</option>";
				}
			?>
		</select>
		<select style="width: 55px;" name="beCreatedMonth">
			<option value="00"></option>
			<?php
				$todayMonth = date("m");
				for($i=1; $i<13; $i++){
					if(strlen($i) < 2){
						$dis_i_as = "0$i";
					} else {
						$dis_i_as = $i;
					}
					echo "<option ";
					if($todayMonth == $dis_i_as){
						echo "selected";
					}
					echo ">$dis_i_as</option>";
				}
			?>
		</select>
		<select style="width: 55px;" name="beCreatedDay">
			<option value="00"></option>
			<?php
				$todayDay = date("d");
				for($i=1; $i<=31; $i++){
					if(strlen($i) < 2){
						$dis_i_as = "0$i";
					} else {
						$dis_i_as = $i;
					}
					echo "<option ";
					if($todayDay == $dis_i_as){
						echo "selected";
					}
					echo ">$dis_i_as</option>";
				}
			?>
		</select>
		<input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('dateCreated','');" class="save_dateCreated" />
	</div>
	<hr />
	<span class="beTitle"><?php echo $mgrlang['mediadet_mod_release']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_modelRelease">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<div><input type="checkbox" value="1" name="beModelRelease" style="margin-top: 10px;" /> <input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('modelRelease','');" class="save_modelRelease" /></div>
	<hr />
	<span class="beTitle"><?php echo $mgrlang['media_f_pr']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_propRelease">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<div><input type="checkbox" value="1" name="bePropRelease" style="margin-top: 10px;" /> <input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('propRelease','');" class="save_propRelease" /></div>
	<hr />
	<!-- Removed for now because it needs to change the icons
	<span class="beTitle"><?php echo $mgrlang['gen_active']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_active">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<div><input type="checkbox" value="1" name="beActive" style="margin-top: 10px;" /> <input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('active','');" class="save_active" /></div>
	<hr />
	<span class="beTitle"><?php echo $mgrlang['gen_featured']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_featured">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<div><input type="checkbox" value="1" name="beFeatured" style="margin-top: 10px;" /> <input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('featured','');" class="save_featured" /></div>
	<hr />
	-->	
	<span class="beTitle"><?php echo $mgrlang['media_f_copyright']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_copyright">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<textarea name="beCopyright" style="width: 226px; height: 100px; vertical-align: middle"></textarea>
	<input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('copyright','');" class="save_copyright" />
	<hr />
	<span class="beTitle"><?php echo $mgrlang['media_f_usageres']; ?></span>&nbsp;&nbsp;<span class="saveMessage" style="display: none;" id="sm_usageRestrictions">- <?php echo $mgrlang['saved']; ?>!</span><br />
	<textarea name="beUsageRestrictions" style="width: 226px; height: 100px; vertical-align: middle"></textarea>
	<input type="button" value="<?php echo $mgrlang['gen_b_save']; ?>" onclick="saveBatchOptions('usageRestrictions','');" class="save_usageRestrictions" />
</div>
<?php
	if(count($items) > 0)
		echo "<script>loadBatchKeywords();</script>";
?>