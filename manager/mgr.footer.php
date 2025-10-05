<div id="debugger" style="display: none;"><?php if($config['settings']['debugpanel']){ include('mgr.debug.info.php'); } ?></div>
<div id="footer">
	<?php
		# HIDES BRANDING IF UNBRANDING FILE EXISTS AND uba_unbrand = 1
		if(!in_array("unbrand",$installed_addons) or $_GET['branding']){
	?>
		<div style="float: left;">
			<a href="http://www.ktools.net/photostore/" target="_blank"><img src="./images/mgr.ktools.logo.png" align="left" border="0" /></a>
			<div id="footer_copyright"><?php echo $mgrlang['powered_by']; ?> <?php echo "<strong>{$config[productName]}"; if(in_array('pro',$installed_addons)) echo " Pro"; echo " {$config[productVersion]}</strong>"; if(!empty($config['productType'])) echo " <span class='mtag_footer roundme'>$config[productType]</span>"; ?><br /><?php echo $mgrlang['copyright']; ?></div>
		</div>
	<?php
		}
	?>
</div>
<div id="hidden_box"></div>
