<?php
	if($discount)
	{
		foreach($discount as $key => $value)
		{
			mysqli_query($db,"UPDATE {$dbinfo[pre]}discount_ranges SET item_type='{$page}',item_id='{$saveid}',start_discount_number='{$discountNumber[$key]}',discount_percent='{$discountPercentage[$key]}' WHERE dr_id  = '{$value}'");
		}
	}
?>