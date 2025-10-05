<?php
# USERS NAV SETUP
$comp[] = array(
	"nav_order"		=> "15",
	"nav_name"		=> $mgrlang['nav_help'],
	"nav_id"		=> "help",
	"link"			=> "mgr.help.php",
	"subnav"		=> 
		array(
			array(
				"subnav_name"	=> $mgrlang['subnav_googletrans'],
				"nav_id"		=> "google_translate",
				"new_win"		=> 1,
				"link"			=> "http://translate.google.com/",
				"badge"			=> "mgr.badge.google.png",
				"desc"			=> $mgrlang['subnav_googletrans_d']	
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_passgen'],
				"nav_id"		=> "pass_gen",
				"new_win"		=> 0,
				"link"			=> "#",
				"onclick"		=> "open_password_gen_win();",
				"badge"			=> "mgr.badge.password.png",
				"desc"			=> $mgrlang['subnav_passgen_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_wizard'],
				"nav_id"		=> "setup_wizard",
				"new_win"		=> 0,
				"link"			=> "#",
				"badge"			=> "mgr.badge.wizard.png",
				"desc"			=> $mgrlang['subnav_wizard_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_forum'],
				"nav_id"		=> "support_forum",
				"new_win"		=> 1,
				"link"			=> "http://www.ktools.net/forum/",
				"badge"			=> "mgr.badge.forum.png",
				"desc"			=> $mgrlang['subnav_forum_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_extras'],
				"nav_id"		=> "support_extras",
				"new_win"		=> 1,
				"link"			=> buildSupportLink('http://ktools.net/photostore/extras.php'),
				"badge"			=> "mgr.badge.extras.png",
				"desc"			=> $mgrlang['subnav_extras_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_faqs'],
				"nav_id"		=> "support_faqs",
				"new_win"		=> 1,
				"link"			=> buildSupportLink('http://www.ktools.net/photostore/documentation.php'),
				"badge"			=> "mgr.badge.faq.png",
				"desc"			=> $mgrlang['subnav_faqs_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_software_upgrade'],
				"nav_id"		=> "software_upgrade",
				"new_win"		=> 0,
				"link"			=> "mgr.software.upgrade.php?ep=1",
				"badge"			=> "mgr.badge.softupgrade.png",
				"desc"			=> $mgrlang['subnav_software_upgrade_d']
				),
			array(
				"subnav_name"	=> $mgrlang['setup_tab6'],
				"nav_id"		=> "about_software",
				"new_win"		=> 0,
				"link"			=> "mgr.software.setup.php?ep=1&about=1",
				"badge"			=> "mgr.badge.setup.png",
				"desc"			=> ''
				)
			)
	);
	/*
	array(
				"subnav_name"	=> $mgrlang['subnav_manual'],
				"nav_id"		=> "support_manual",
				"new_win"		=> 1,
				"link"			=> buildSupportLink('http://www.ktools.net/wiki/index.php?action=show&cat=82'),
				"badge"			=> "mgr.badge.manual.png",
				"desc"			=> $mgrlang['subnav_manual_d']
				),
	*/
	
?>
