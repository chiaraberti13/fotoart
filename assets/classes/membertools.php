<?php
	/******************************************************************
	*  Copyright 2011 Ktools.net LLC - All Rights Reserved
	*  http://www.ktools.net
	*  Created: 4-21-2011
	*  Modified: 4-21-2011
	******************************************************************/
	
	/*
	* Class for working with member data
	*/
	class memberTools
	{
		private $dbinfo;
		private $memID;
		public $state;
		public $country;
		public $zip;
		
		public function __construct($memID='')
		{
			$this->dbinfo = getDBInfo();
			if($memID) $this->memID = $memID;
		}
		
		/*
		* Create a member session and check for the member in the db
		*/
		public function setMemberSession()
		{
			global $config;
			
			unset($_SESSION['member']);
			if($_COOKIE['member']) // Check for cookie
			{
				$_SESSION['member']['umem_id'] = $_COOKIE['member']['umem_id']; // Update the session with the member id from the cookie
			}
			else
			{
				$_SESSION['member']['umem_id'] = create_unique2(); // Create a unique ID for this member/customer
				
				$host = explode(':',$_SERVER['HTTP_HOST']);
				
				if($config['useCookies']) setcookie("member[umem_id]", $_SESSION['member']['umem_id'], time()+60*60*24*30, "/", $host[0]); // Set a member id cookie
			}
			
			if(!$_SESSION['member'] or strlen($_SESSION['member']['umem_id']) < 32) 
				throw new Exception('setMemberSession : No member session exists'); // If the member session still doesn't exist or is less than 32 chars then something went wrong and stop the script

			if(!$_SESSION['member']['mem_id']) // See if this is an existing member - do this check only once to improve performance
			{
				$memberInfoFromDB = $this->getMemberInfoFromDB($_SESSION['member']['umem_id']);
				if($memberInfoFromDB) $_SESSION['member'] = $memberInfoFromDB; // Member exists in DB - Assign the details to the session
			}	
		}
		
		/*
		* Get the members primary address
		*/
		public function getPrimaryAddress()
		{
			global $db;
			if($this->memID)
			{
				$addressResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}members_address WHERE member_id = '{$this->memID}'"); // Select member address details from db
				$addressRows = mysqli_num_rows($addressResult);
				if($addressRows)
				{
					$address = mysqli_fetch_assoc($addressResult);
					
					$stateResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}states WHERE state_id = '{$address[state]}'"); // Select state info
					$state = mysqli_fetch_assoc($stateResult);
					
					$countryResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}countries WHERE country_id = '{$address[country]}'"); // Select country info
					$country = mysqli_fetch_assoc($countryResult);
					
					@$zipResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}zipcodes WHERE zipcode = '{$address[postal_code]}'"); // Select zip code info
					@$zip = mysqli_fetch_assoc($zipResult);
					
					$address['state'] = $state['name']; // xxxxx Select correct language
					$address['country'] = $country['name'];
					
					$address['stateID'] = $state['state_id'];
					$address['countryID'] = $country['country_id'];
					
					$this->state = $state; // Set the state array for use in the class
					$this->country = $country; // Set the country array for use in the class
					$this->zip = $zip; // Set the zip array for use in the class
					
					return $address;	
				}
				else
					return false;
			}
			else
				throw new Exception("getPrimaryAddress: No member id exists");
		}
		
		/*
		* Get member specific taxes
		*/ 
		public function getMemberTaxValues($countryID='',$stateID='',$postalCode='')
		{
			global $config,$db;
			
			if($countryID) $this->country['country_id'] = $countryID; // Check if a new country id is passed				
			if($stateID) $this->state['state_id'] = $stateID; // Check if a new state id is passed				
			if($postalCode)	$this->zip['zipcode'] = $postalCode; // Check if a new zip code has been passed // zipcode_id
			
			$tax['tax_inc'] = 0;
			$tax['tax_a_default'] = 0;
			$tax['tax_b_default'] = 0;
			$tax['tax_c_default'] = 0;
			$tax['tax_prints'] = 0;
			$tax['tax_shipping'] = 0;
			$tax['tax_a_digital'] = 0;
			$tax['tax_b_digital'] = 0;
			$tax['tax_c_digital'] = 0;
			$tax['tax_ms'] = 0;
			$tax['tax_digital'] = 0;
			$tax['tax_credits'] = 0;
			$tax['tax_subs'] = 0;
			
			$countryTaxesResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}taxes WHERE region_type = 1 AND region_id = '{$this->country[country_id]}'"); // Select country tax info
			$countryTaxes = mysqli_fetch_array($countryTaxesResult);
			
			$stateTaxesResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}taxes WHERE region_type = 2 AND region_id = '{$this->state[state_id]}'"); // Select state tax info
			$stateTaxes = mysqli_fetch_array($stateTaxesResult);
			
			if($this->zip['zipcode'])
			{
				// Find zipcode ID
				$zipResult = mysqli_query($db,"SELECT zipcode_id FROM {$this->dbinfo[pre]}zipcodes WHERE zipcode  = '{$this->zip[zipcode]}'"); // Select zip info
				$zipRows = mysqli_num_rows($zipResult);
				$zip = mysqli_fetch_array($zipResult);
				
				if($zipRows)
					$this->zip['zipcode_id'] = $zip['zipcode_id'];
				
				$zipTaxesResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}taxes WHERE region_type = 3 AND region_id = '{$this->zip[zipcode_id]}'"); // Select zip tax info
				$zipTaxesRows = mysqli_num_rows($zipTaxesResult);
				$zipTaxes = mysqli_fetch_array($zipTaxesResult);
				
				//echo $zipTaxesRows;
			}
			
			if($config['settings']['tax_type'] == 0)  // Tax by region
			{
				/*
				* tax_inc
				*/
				if($countryTaxes['tax_inc']) // Check country
					$tax['tax_inc'] = $countryTaxes['tax_inc'];
				
				if($stateTaxes['tax_inc']) // Check state
					$tax['tax_inc'] = $stateTaxes['tax_inc'];
				
				if($zipTaxes['tax_inc']) // Check zip
					$tax['tax_inc'] = $zipTaxes['tax_inc'];
				
				/*
				* tax_a_default
				*/
				if($countryTaxes['tax_a']) // Check country
					$tax['tax_a_default'] = $countryTaxes['tax_a'];
				
				if($stateTaxes['tax_a']) // Check state
					$tax['tax_a_default'] = $stateTaxes['tax_a'];
				
				if($zipTaxes['tax_a']) // Check zip
					$tax['tax_a_default'] = $zipTaxes['tax_a'];
					
				/*
				* tax_b_default
				*/
				if($countryTaxes['tax_b']) // Check country
					$tax['tax_b_default'] = $countryTaxes['tax_b'];
				
				if($stateTaxes['tax_b']) // Check state
					$tax['tax_b_default'] = $stateTaxes['tax_b'];
				
				if($zipTaxes['tax_b']) // Check zip
					$tax['tax_b_default'] = $zipTaxes['tax_b'];
					
				/*
				* tax_c_default
				*/
				if($countryTaxes['tax_c']) // Check country
					$tax['tax_c_default'] = $countryTaxes['tax_c'];
				
				if($stateTaxes['tax_c']) // Check state
					$tax['tax_c_default'] = $stateTaxes['tax_c'];
				
				if($zipTaxes['tax_c']) // Check zip
					$tax['tax_c_default'] = $zipTaxes['tax_c'];
					
				/*
				* tax_prints
				*/
				if($countryTaxes['tax_prints']) // Check country
					$tax['tax_prints'] = $countryTaxes['tax_prints'];
				
				if($stateTaxes['tax_prints']) // Check state
					$tax['tax_prints'] = $stateTaxes['tax_prints'];
				
				if($zipTaxes['tax_prints']) // Check zip
					$tax['tax_prints'] = $zipTaxes['tax_prints'];
					
				/*
				* tax_digital
				*/
				if($countryTaxes['tax_digital']) // Check country
					$tax['tax_digital'] = $countryTaxes['tax_digital'];
				
				if($stateTaxes['tax_digital']) // Check state
					$tax['tax_digital'] = $stateTaxes['tax_digital'];
				
				if($zipTaxes['tax_digital']) // Check zip
					$tax['tax_digital'] = $zipTaxes['tax_digital'];
					
				/*
				* tax_ms
				*/
				if($countryTaxes['tax_ms']) // Check country
					$tax['tax_ms'] = $countryTaxes['tax_ms'];
				
				if($stateTaxes['tax_ms']) // Check state
					$tax['tax_ms'] = $stateTaxes['tax_ms'];
				
				if($zipTaxes['tax_ms']) // Check zip
					$tax['tax_ms'] = $zipTaxes['tax_ms'];
					
				/*
				* tax_subs
				*/
				if($countryTaxes['tax_subs']) // Check country
					$tax['tax_subs'] = $countryTaxes['tax_subs'];
				
				if($stateTaxes['tax_subs']) // Check state
					$tax['tax_subs'] = $stateTaxes['tax_subs'];
				
				if($zipTaxes['tax_subs']) // Check zip
					$tax['tax_subs'] = $zipTaxes['tax_subs'];
					
				/*
				* tax_shipping
				*/
				if($countryTaxes['tax_shipping']) // Check country
					$tax['tax_shipping'] = $countryTaxes['tax_shipping'];
				
				if($stateTaxes['tax_shipping']) // Check state
					$tax['tax_shipping'] = $stateTaxes['tax_shipping'];
				
				if($zipTaxes['tax_shipping']) // Check zip
					$tax['tax_shipping'] = $zipTaxes['tax_shipping'];
					
				/*
				* tax_credits
				*/
				if($countryTaxes['tax_credits']) // Check country
					$tax['tax_credits'] = $countryTaxes['tax_credits'];
				
				if($stateTaxes['tax_credits']) // Check state
					$tax['tax_credits'] = $stateTaxes['tax_credits'];
				
				if($zipTaxes['tax_credits']) // Check zip
					$tax['tax_credits'] = $zipTaxes['tax_credits'];
					
				/*
				* tax_a_digital
				*/
				if($countryTaxes['tax_a_digital']) // Check country
					$tax['tax_a_digital'] = $countryTaxes['tax_a_digital'];
				
				if($stateTaxes['tax_a_digital']) // Check state
					$tax['tax_a_digital'] = $stateTaxes['tax_a_digital'];
				
				if($zipTaxes['tax_a_digital']) // Check zip
					$tax['tax_a_digital'] = $zipTaxes['tax_a_digital'];
					
				/*
				* tax_b_digital
				*/
				if($countryTaxes['tax_b_digital']) // Check country
					$tax['tax_b_digital'] = $countryTaxes['tax_b_digital'];
				
				if($stateTaxes['tax_b_digital']) // Check state
					$tax['tax_b_digital'] = $stateTaxes['tax_b_digital'];
				
				if($zipTaxes['tax_b_digital']) // Check zip
					$tax['tax_b_digital'] = $zipTaxes['tax_b_digital'];
					
				/*
				* tax_c_digital
				*/
				if($countryTaxes['tax_c_digital']) // Check country
					$tax['tax_c_digital'] = $countryTaxes['tax_c_digital'];
				
				if($stateTaxes['tax_c_digital']) // Check state
					$tax['tax_c_digital'] = $stateTaxes['tax_c_digital'];
				
				if($zipTaxes['tax_c_digital']) // Check zip
					$tax['tax_c_digital'] = $zipTaxes['tax_c_digital'];
			}
			return $tax;
		}
		
		/*
		* Grab the members details from the database and return an array with the info
		*/
		public function getMemberInfoFromDB($id)
		{
			global $db;
			
			if($id) // Make sure some sort of id was passed
			{
				if(is_numeric($id)) // Check if it is an id or a unique id
					$memberResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}members WHERE mem_id = '{$id}'"); // Select member details from db
				else
					$memberResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}members WHERE umem_id = '{$id}'"); // Select member details from db
				
				$memberRows = mysqli_num_rows($memberResult);
				if($memberRows)
				{	
					$member = mysqli_fetch_assoc($memberResult);
					
					$this->memID = $member['mem_id']; // Assign the mem id just in case it hasn't been set already
					
					if(!$member['display_name']) // Set display name if none exists
							$member['display_name'] = $member['f_name'].' '.$member['l_name'];
					
					$member['seoName'] = cleanForSEO($member['display_name']);
					
					$member['unencryptedPassword'] = k_decrypt($member['password']);
					$member['trialed_memberships'] = explode(",",$member['trialed_memberships']);
					$member['fee_memberships'] = explode(",",$member['fee_memberships']);
					
					if($member['bio_content']) $member['bio_content'] = nl2br($member['bio_content']); // Update display for bio content
					
					return $member; // Member exists add details to the session
				}
				else
					return false;
			}
			else
				throw new Exception('getMemberInfoFromDB : No member ID was passed'); // If no umemID was passed to the function throw exception	
		}
		
		/*
		* Grab the membership details from the database and return an array with the info
		*/
		public function getMembershipInfoFromDB($msID)
		{
			global $db;
			
			if($msID)
			{
				$membershipResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}memberships WHERE ms_id = '{$msID}'"); // Select membership details from db
				$membershipRows = mysqli_num_rows($membershipResult);
				if($membershipRows)
				{
					$membershipInfo = mysqli_fetch_assoc($membershipResult);
					
					//$membershipInfo['name'] = $membershipInfo['name']; // xxxxx Language
					
					return $membershipInfo; // Membership exists add details to the session
				}
				else
					return false;
			}
			else
			{
				throw new Exception('getMembershipInfoFromDB : No membership ID was passed'); // If no membership id was passed to the function throw exception	
			}
		}
				
		/*
		* Get the groups the member belongs to
		*/
		public function getMemberGroups($memID)
		{
			global $db;
			
			if($memID) $this->$memID = $memID; // If a member ID is passed update
			if($this->$memID)
			{
				$memberGroupResult = mysqli_query($db,"SELECT * FROM {$this->dbinfo[pre]}groupids WHERE mgrarea = 'members' AND item_id = '{$this->memID}'"); // Select member groups from db
				$memberGroupRows = mysqli_num_rows($memberGroupResult);
				while($memberGroup = mysqli_fetch_array($memberGroupResult))
				{
					$memberGroups[] = $memberGroup['group_id']; // Assign the group id to the memberGroups array
				}
				return $memberGroups;
			}
			else
			{
				throw new Exception('getMemberGroups : No member ID exists'); // If at this point there is still no member id throw exception
			}
		}
	}
?>