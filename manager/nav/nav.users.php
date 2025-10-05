<?php
# USERS NAV SETUP
$comp[] = array(
	"nav_order"		=> "2",
	"nav_name"		=> $mgrlang['nav_users'],
	"nav_id"		=> "users",
	"link"			=> "mgr.users.php",
	"subnav"		=> 
		array(
			array(
				"subnav_name"	=> $mgrlang['subnav_administrators'],
				"nav_id"		=> "administrators",
				"new_win"		=> 0,
				"link"			=> "mgr.administrators.php?ep=1",
				"badge"			=> "mgr.badge.admin.png",
				"desc"			=> $mgrlang['subnav_administrators_d']	
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_members'],
				"nav_id"		=> "members",
				"new_win"		=> 0,
				"link"			=> "mgr.members.php?ep=1",
				"badge"			=> "mgr.badge.members.png",
				"desc"			=> $mgrlang['subnav_members_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_memberships'],
				"nav_id"		=> "memberships",
				"new_win"		=> 0,
				"link"			=> "mgr.memberships.php?ep=1",
				"badge"			=> "mgr.badge.membership.png",
				"desc"			=> $mgrlang['subnav_memberships_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_support_tickets'],
				"nav_id"		=> "support_tickets",
				"new_win"		=> 0,
				"link"			=> "mgr.support.tickets.php?ep=1",
				"badge"			=> "mgr.badge.support.tickets.png",
				"desc"			=> $mgrlang['subnav_support_tickets_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_lightboxes'],
				"nav_id"		=> "lightboxes",
				"new_win"		=> 0,
				"link"			=> "mgr.lightboxes.php?ep=1",
				"badge"			=> "mgr.badge.lightbox.png",
				"desc"			=> $mgrlang['subnav_lightboxes_d']
				)
			/*,
			array(
				"subnav_name"	=> $mgrlang['subnav_member_bios'],
				"nav_id"		=> "member_bios",
				"new_win"		=> 0,
				"link"			=> "mgr.member.bios.php?ep=1",
				"badge"			=> "mgr.badge.memberssssss.png",
				"desc"			=> $mgrlang['subnav_member_bios_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_member_avatars'],
				"nav_id"		=> "member_avatars",
				"new_win"		=> 0,
				"link"			=> "mgr.member.avatars.php?ep=1",
				"badge"			=> "mgr.badge.memberssssss.png",
				"desc"			=> $mgrlang['subnav_member_avatars_d']
				)
			*/
			)
	);
	
?>
