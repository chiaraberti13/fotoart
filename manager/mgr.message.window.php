<?php
	if($_SESSION['wizardStatus'])
	{
?>
	<div class="wizardContainer">
		<div class="wizardContent">
			<img src="images/mgr.button.close2.png" class="wizardClose" onClick="location.href='mgr.welcome.php?wizard=off';" />
			<input type="button" value="<?php if($_SESSION['wizardCurrentStep'] < 6) { echo "Next"; } else { echo "Done"; } ?>" class="wizardNext" onClick="location.href='<?php echo $wizardStep[$_SESSION['wizardCurrentStep']+1]['link']; ?>&wizStep=<?php echo $_SESSION['wizardCurrentStep']+1; ?>'" />
			<?php if($_SESSION['wizardCurrentStep'] < 6){ ?><strong>Step <?php echo $_SESSION['wizardCurrentStep']; ?>:</strong><?php } ?> <?php echo $wizardStep[$_SESSION['wizardCurrentStep']]['text']; ?>
		</div>
	</div>
<?php
	}
?>
<div id="overlay" align="center" style='display: none;'></div>
<div id="messagebox">
	<div id="messageboxheader"><img src="images/mgr.notice.icon.small2.png" align="absmiddle" /> <?php echo $mgrlang['gen_meswin']; ?></div>
	<div id="messagebox_inner">
        <div id="innermessage"></div>
    </div>
</div>
<div id="workbox"></div>