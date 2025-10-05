<?php
# LIBRARY NAV SETUP
$comp[] = array(
	"nav_order"		=> "1",
	"nav_name"		=> $mgrlang['nav_library']	,
	"nav_id"		=> "library",
	"link"			=> "mgr.library.php",
	"subnav"		=> 
		array(
			array(
				"subnav_name"	=> $mgrlang['subnav_galleries'],
				"nav_id"		=> "galleries",
				"new_win"		=> 0,
				"link"			=> "mgr.galleries.php?ep=1",
				"badge"			=> "mgr.badge.galleries.png",
				"desc"			=> $mgrlang['subnav_galleries_d']
			),
			array(
				"subnav_name"	=> $mgrlang['subnav_media'],
				"nav_id"		=> "media",
				"new_win"		=> 0,
				"link"			=> "mgr.media.php?ep=1",
				"badge"			=> "mgr.badge.media.png",
				"desc"			=> $mgrlang['subnav_media_d']
			),
			array(
				"subnav_name"	=> $mgrlang['subnav_add_media'],
				"nav_id"		=> "add_media",
				"new_win"		=> 0,
				"link"			=> "mgr.add.media.php?ep=1",
				"badge"			=> "mgr.badge.addmedia.png",
				"desc"			=> $mgrlang['subnav_add_files_d']
			),
			array(
				"subnav_name"	=> $mgrlang['subnav_collections'],
				"nav_id"		=> "collections",
				"new_win"		=> 0,
				"link"			=> "mgr.collections.php?ep=1",
				"badge"			=> "mgr.badge.collection.png",
				"desc"			=> $mgrlang['subnav_collections_d']
			),		
			array(
				"subnav_name"	=> $mgrlang['subnav_digital_sp'],
				"nav_id"		=> "digital_sp",
				"new_win"		=> 0,
				"link"			=> "mgr.digital.sp.php?ep=1",
				"badge"			=> "mgr.badge.profiles.png",
				"desc"			=> $mgrlang['subnav_digital_sp_d']
			),
			array(
				"subnav_name"	=> $mgrlang['subnav_prints'],
				"nav_id"		=> "prints",
				"new_win"		=> 0,
				"link"			=> "mgr.prints.php?ep=1",
				"badge"			=> "mgr.badge.prints.png",
				"desc"			=> $mgrlang['subnav_prints_d']
			),			
			array(
				"subnav_name"	=> $mgrlang['subnav_products'],
				"nav_id"		=> "products",
				"new_win"		=> 0,
				"link"			=> "mgr.products.php?ep=1",
				"badge"			=> "mgr.badge.products.png",
				"desc"			=> $mgrlang['subnav_products_d']
			),		
			array(
				"subnav_name"	=> $mgrlang['subnav_packages'],
				"nav_id"		=> "packages",
				"new_win"		=> 0,
				"link"			=> "mgr.packages.php?ep=1",
				"badge"			=> "mgr.badge.packages.png",
				"desc"			=> $mgrlang['subnav_packages_d']
			),
			array(
				"subnav_name"	=> $mgrlang['subnav_licenses'],
				"nav_id"		=> "licenses",
				"new_win"		=> 0,
				"link"			=> "mgr.licenses.php?ep=1",
				"badge"			=> "mgr.badge.licenses.png",
				"desc"			=> $mgrlang['subnav_licenses_d']
			),
			array(
				"subnav_name"	=> $mgrlang['subnav_media_types'],
				"nav_id"		=> "media_types",
				"new_win"		=> 0,
				"link"			=> "mgr.media.types.php?ep=1",
				"badge"			=> "mgr.badge.mediatypes.png",
				"desc"			=> $mgrlang['subnav_media_types_d']
			),
			array(
				"subnav_name"	=> $mgrlang['subnav_media_queue'],
				"nav_id"		=> "media_queue",
				"new_win"		=> 0,
				"link"			=> "mgr.media.php?dtype=pending",
				"badge"			=> "mgr.badge.mediaqueue.png",
				"desc"			=> $mgrlang['subnav_media_queue_d']
			),
			array(
				"subnav_name"	=> $mgrlang['subnav_media_comments'],
				"nav_id"		=> "media_comments",
				"new_win"		=> 0,
				"link"			=> "mgr.media.comments.php?ep=1",
				"badge"			=> "mgr.badge.media.comments.png",
				"desc"			=> $mgrlang['subnav_media_comments_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_media_ratings'],
				"nav_id"		=> "media_ratings",
				"new_win"		=> 0,
				"link"			=> "mgr.media.ratings.php?ep=1",
				"badge"			=> "mgr.badge.media.ratings.png",
				"desc"			=> $mgrlang['subnav_media_ratings_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_media_tags'],
				"nav_id"		=> "media_tags",
				"new_win"		=> 0,
				"link"			=> "mgr.media.tags.php?ep=1",
				"badge"			=> "mgr.badge.media.tags.png",
				"desc"			=> $mgrlang['subnav_media_tags_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_folders'],
				"nav_id"		=> "folders",
				"new_win"		=> 0,
				"link"			=> "mgr.folders.php?ep=1",
				"badge"			=> "mgr.badge.folders.png",
				"desc"			=> $mgrlang['subnav_folders_d']
			),
			array(
				"subnav_name"	=> 'Scatole',
				"nav_id"		=> "boxes",
				"new_win"		=> 0,
				//"link"			=> "mgr.boxes.php?ep=1",
				"link"			=> "mgr.boxes.php",
				//"badge"			=> "mgr.badge.folders.png",
				"badge"			=> "mgr.badge.packages.png",
				"desc"			=> 'Gestisci le scatole per i Puzzles'
			)
		)
	);
	
?>
