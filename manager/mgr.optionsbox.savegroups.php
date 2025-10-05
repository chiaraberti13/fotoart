<?php
	
	# SAVE OPTIONGROUPS
	if($optiongroup)
	{
		foreach($optiongroup as $key2 => $value2)
		{
			//echo $value2." - ".${'optiongrpactive_'.$value2}; exit;
			
			//echo $optiongrpname[$key]."<br />";
			
			# ADD SUPPORT FOR ADDITIONAL LANGUAGES
			foreach($active_langs as $value3){ 
				$name_val = ${"langname_" . $value3}[$key2];
				$addsql2.= "name_$value3='$name_val',";
			}
			
			# UPDATE THE DATABASE
			$sql = "UPDATE {$dbinfo[pre]}option_grp SET 
						name='".$optiongrpname[$key2]."',
						ltype='".$optiongrpltype[$key2]."',";
			$sql.= $addsql2;
			$sql.= "	active='".${'optiongrpactive_'.$value2}."',
						required='".${'optiongrprequired_'.$value2}."',
						parent_id='$saveid',
						parent_type='$page'
						where og_id  = '$value2'";
			$result = mysqli_query($db,$sql);
		}
	}
?>