<?php
# CONTENT NAV SETUP
$comp[] = array(
	"nav_order"		=> "4",
	"nav_name"		=> $mgrlang['nav_content'],
	"nav_id"		=> "content",
	"link"			=> "mgr.content.editor.php",
	"subnav"		=> 
		array(
			array(
				"subnav_name"	=> $mgrlang['subnav_page_content'],
				"nav_id"		=> "page_content",
				"new_win"		=> 0,
				"link"			=> "mgr.page.content.php?ep=1",
				"badge"			=> "mgr.badge.content.png",
				"desc"			=> $mgrlang['subnav_page_content_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_email_content'],
				"nav_id"		=> "email_content",
				"new_win"		=> 0,
				"link"			=> "mgr.email.content.php?ep=1",
				"badge"			=> "mgr.badge.email.png",
				"desc"			=> $mgrlang['subnav_email_content_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_news'],
				"nav_id"		=> "news",
				"new_win"		=> 0,
				"link"			=> "mgr.news.php?ep=1",
				"badge"			=> "mgr.badge.news.png",
				"desc"			=> $mgrlang['subnav_news_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_agreements'],
				"nav_id"		=> "agreements",
				"new_win"		=> 0,
				"link"			=> "mgr.agreements.php?ep=1",
				"badge"			=> "mgr.badge.licenses.png",
				"desc"			=> $mgrlang['subnav_agreements_d']
				)
			)
	);	
?>
