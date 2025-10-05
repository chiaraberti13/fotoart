<?php
# SETTINGS NAV SETUP
$comp[] = array(
	"nav_order"		=> "9",
	"nav_name"		=> $mgrlang['nav_settings'],
	"nav_id"		=> "settings",
	"link"			=> "mgr.settings.php",
	"subnav"		=> 
		array(
			array(
				"subnav_name"	=> $mgrlang['subnav_website_settings'],
				"nav_id"		=> "website_settings",
				"new_win"		=> 0,
				"link"			=> "mgr.website.settings.php?ep=1",
				"badge"			=> "mgr.badge.websettings.png",
				"desc"			=> $mgrlang['subnav_website_settings_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_look'],
				"nav_id"		=> "look_feel",
				"new_win"		=> 0,
				"link"			=> "mgr.look.feel.php?ep=1",
				"badge"			=> "mgr.badge.look.png",
				"desc"			=> $mgrlang['subnav_look_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_software_setup'],
				"nav_id"		=> "software_setup",
				"new_win"		=> 0,
				"link"			=> "mgr.software.setup.php?ep=1",
				"badge"			=> "mgr.badge.setup.png",
				"desc"			=> $mgrlang['subnav_software_setup_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_languages'],
				"nav_id"		=> "languages",
				"new_win"		=> 0,
				"link"			=> "mgr.languages.php?ep=1",
				"badge"			=> "mgr.badge.languages.png",
				"desc"			=> $mgrlang['subnav_languages_d']
				),				
			array(
				"subnav_name"	=> $mgrlang['subnav_countries'],
				"nav_id"		=> "countries",
				"new_win"		=> 0,
				"link"			=> "mgr.countries.php?ep=1",
				"badge"			=> "mgr.badge.countries.png",
				"desc"			=> $mgrlang['subnav_countries_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_states'],
				"nav_id"		=> "states",
				"new_win"		=> 0,
				"link"			=> "mgr.states.php?ep=1",
				"badge"			=> "mgr.badge.states.png",
				"desc"			=> $mgrlang['subnav_states_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_zipcodes'],
				"nav_id"		=> "zipcodes",
				"new_win"		=> 0,
				"link"			=> "mgr.zipcodes.php?ep=1",
				"badge"			=> "mgr.badge.zip.png",
				"desc"			=> $mgrlang['subnav_zipcodes_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_shipping'],
				"nav_id"		=> "shipping",
				"new_win"		=> 0,
				"link"			=> "mgr.shipping.php?ep=1",
				"badge"			=> "mgr.badge.shipping.png",
				"desc"			=> $mgrlang['subnav_shipping_d']
				),			
			array(
				"subnav_name"	=> $mgrlang['subnav_taxes'],
				"nav_id"		=> "taxes",
				"new_win"		=> 0,
				"link"			=> "mgr.taxes.php?ep=1",
				"badge"			=> "mgr.badge.taxes.png",
				"desc"			=> $mgrlang['subnav_taxes_d']
				),			
			/*
			array(
				"subnav_name"	=> $mgrlang['subnav_rightsmanaged'],
				"nav_id"		=> "rightsmanaged",
				"new_win"		=> 0,
				"link"			=> "#",
				"badge"			=> "mgr.badge.rm.png",
				"desc"			=> $mgrlang['subnav_rightsmanaged_d']
				),
			*/			
			array(
				"subnav_name"	=> $mgrlang['subnav_credits'],
				"nav_id"		=> "credits",
				"new_win"		=> 0,
				"link"			=> "mgr.credits.php?ep=1",
				"badge"			=> "mgr.badge.credits.png",
				"desc"			=> $mgrlang['subnav_credits_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_payment_options'],
				"nav_id"		=> "payment_options",
				"new_win"		=> 0,
				"link"			=> "mgr.payment.gateways.php?ep=1",
				"badge"			=> "mgr.badge.payment.png",
				"desc"			=> $mgrlang['subnav_payment_options_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_currencies'],
				"nav_id"		=> "currencies",
				"new_win"		=> 0,
				"link"			=> "mgr.currencies.php?ep=1",
				"badge"			=> "mgr.badge.currencies.png",
				"desc"			=> $mgrlang['subnav_currencies_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_subscriptions'],
				"nav_id"		=> "subscriptions",
				"new_win"		=> 0,
				"link"			=> "mgr.subscriptions.php?ep=1",
				"badge"			=> "mgr.badge.subscriptions.png",
				"desc"			=> $mgrlang['subnav_currencies_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_promotions'],
				"nav_id"		=> "promotions",
				"new_win"		=> 0,
				"link"			=> "mgr.promotions.php?ep=1",
				"badge"			=> "mgr.badge.promo.png",
				"desc"			=> $mgrlang['subnav_promotions']
				),
			/*
			array(
				"subnav_name"	=> $mgrlang['subnav_components'],
				"nav_id"		=> "components",
				"link"			=> "mgr.components.php",
				"badge"			=> "mgr.badge.xx.gif",
				"desc"			=> $mgrlang['subnav_components_d']
				),
			
			array(
				"subnav_name"	=> $mgrlang['subnav_services'],
				"nav_id"		=> "services",
				"new_win"		=> 0,
				"link"			=> "mgr.services.php?ep=1",
				"badge"			=> "mgr.badge..gif",
				"desc"			=> $mgrlang['subnav_services_d']
				),
			*/
			array(
				"subnav_name"	=> $mgrlang['subnav_utilities'],
				"nav_id"		=> "utilities",
				"new_win"		=> 0,
				"link"			=> "mgr.utilities.php?ep=1",
				"badge"			=> "mgr.badge.utilities.png",
				"desc"			=> $mgrlang['subnav_utilities_d']
				),
			array(
				"subnav_name"	=> $mgrlang['subnav_storage'],
				"nav_id"		=> "storage",
				"new_win"		=> 0,
				"link"			=> "mgr.storage.php?ep=1",
				"badge"			=> "mgr.badge.storage.png",
				"desc"			=> $mgrlang['subnav_storage_d']
				)
			/*,
			array(
				"subnav_name"	=> $mgrlang['subnav_toolslinks'],
				"nav_id"		=> "toolslinks",
				"new_win"		=> 0,
				"link"			=> "mgr.toolslinks.php?ep=1",
				"badge"			=> "mgr.badgexx..gif",
				"desc"			=> $mgrlang['subnav_toolslinks_d']
				)
			*/
			)
	);	
?>
