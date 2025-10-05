<?php
	/*
		# CHECK FOR AFFILIATE ID
	if(!empty($_GET['aff'])){		
		# INSERT REFERRAL DETAILS
		$sql = "INSERT INTO k_referrals (
					affiliate_id,
					vdate
				) VALUES (
					'$_GET[aff]',
					now()	
				)";
		$result = mysqli_query($db,$sql);
		
		# SET AFFILIATE DETAILS
		session_register("affiliate_id");
		$_SESSION['affiliate_id'] = $_GET['aff'];
		setcookie("affiliate_id", strtolower($_GET['aff']) . "|" . "blank", time()+60*60*24*30);
	} else if(isset($_COOKIE['affiliate_id']) && empty($_SESSION['affiliate_id'])){
		# SET AFFILIATE DETAILS FROM COOKIE
		session_register("affiliate_id");
		$affiliate_cookie =  $_COOKIE['affiliate_id'];
		$affiliate_array = explode("|", $affiliate_cookie);
		$_SESSION['affiliate_id'] = strtolower($affiliate_array[0]);
	}
	
	# CHECK FOR REFERRAL
	
	
	if(!isset($_SESSION['referral_url'])){		
		session_register("referral_url");
		# CHECK FOR COOKIE
		if(isset($_COOKIE['referral_url'])){
			# LOAD COOKIE
			$referral_cookie =  $_COOKIE['referral_url'];
			$referral_array = explode("|", $referral_cookie);
			$_SESSION['referral_url'] = strtolower($referral_array[0]);
			
			//$emt = 1;
		} else {
			# NO COOKIE SET. SET ONE AND SET SESSION VARIABLE
			$_SESSION['referral_url'] = strtolower($_SERVER['HTTP_REFERER']);
			if(isset($verify_google_ad)){
				$_SESSION['referral_url'] = $_SESSION['referral_url'] . "*" . $verify_google_ad;
			}			
			setcookie("referral_url", strtolower($_SESSION['referral_url']) . "|" . "blank", time()+60*60*24*30, "/", "ktools.net");
			//$emt = 0;
		}	
	} else {
		//$emt = 99;
	}
*/
?>