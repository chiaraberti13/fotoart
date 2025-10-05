// REMOVE SPACES FROM STRING
	function removeSpaces(string) {
		var tstring = "";
		string = '' + string;
		splitstring = string.split(" ");
		for(i = 0; i < splitstring.length; i++)
		tstring += splitstring[i];
		return tstring;
	}
	

// NUMBER CLEANUP AND DISPLAY FUNCTIONS
	
	// CLEAN THE NUMBER TO USE IT
	function number_clean(inputnum)
	{
		inputnum = strip_number_symbol(inputnum);
		var exploded_value = inputnum.split(numset.decimal_separator);
		if(exploded_value.length > 1)
		{				
			var decimal = strip_nonnumbers(exploded_value[exploded_value.length-1]);
			exploded_value.splice(exploded_value.length-1,1);				
			var fullnum = strip_nonnumbers(exploded_value.join(''));
			inputnum = fullnum + "." + decimal;
		}
		else
		{
			// STRIP NON NUMBERS
			inputnum = strip_nonnumbers(inputnum);
		}
		return inputnum;
		//alert(inputnum);
	}
	
	// CLEAN THE NUMBER TO USE IT
	function currency_clean(inputnum)
	{
		inputnum = strip_number_symbol(inputnum);
		var exploded_value = inputnum.split(numset.cur_decimal_separator);
		if(exploded_value.length > 1)
		{				
			var decimal = strip_nonnumbers(exploded_value[exploded_value.length-1]);
			exploded_value.splice(exploded_value.length-1,1);				
			var fullnum = strip_nonnumbers(exploded_value.join(''));
			inputnum = fullnum + "." + decimal;
		}
		else
		{
			// STRIP NON NUMBERS
			inputnum = strip_nonnumbers(inputnum);
		}
		return inputnum;
		//alert(inputnum);
	}
	
	// DISPLAY THE NUMBER IN THE BROWSER
	function number_display(inputnum,decimal_places,strip_ezeros)
	{
		// USE DEFUALT IF NOTHING ELSE IS PASSED
		if(decimal_places == '' && numset.decimal_places != null)
		{
			decimal_places = numset.decimal_places;
		}
		// USE DEFUALT IF NOTHING ELSE IS PASSED
		if(strip_ezeros == '' && numset.strip_ezeros != null)
		{
			strip_ezeros = numset.strip_ezeros;
		}
		
		// ROUND NUMBER TO CORRECT DECIMALS
		//inputnum.toFixed(2);
		if(inputnum < 0)
		{
			var negative_number = 1;
			// CHANGE TO POSITIVE
			inputnum = inputnum * -1;
		}
		
		//var decimal_places = numset.decimal_places;
		inputnum = parseFloat(inputnum).toFixed(decimal_places);
		
		// CHECK FOR DIFFERENT DECIMAL SEPARATORS
		if(numset.decimal_separator != '.')
		{
			var exploded_value = inputnum.split(".");
			inputnum = exploded_value.join(numset.decimal_separator);
		}
		
		// STRIP ZEROS IF NEEDED
		if(strip_ezeros == 1)
		{
			inputnum = strip_end_zeros(inputnum);
		}
		
		// FORMAT AS CURRENCY
		if(numset.format_as_currency == 1)
		{
			// NEGATIVE NUMBER
			if(negative_number == 1)
			{
				
			}
			// POSITIVE NUMBER
			else
			{
				
			}
		}
		// FORMAT AS REGULAR NUMBER
		else
		{
			// NEGATIVE NUMBER
			if(negative_number == 1)
			{
				
			}				
		}
		return inputnum;
	}
	
	// SET THE VALUE OF THE CURRENCY HIDE DENOTATION VARIALBE
	function set_cur_hide_denotation(value)
	{
		numset.cur_hide_denotation = value;
	}
	
	// DISPLAY THE CURRENCY
	function currency_display(inputnum,show_denotation)
	{
		if(!show_denotation)
		{
			show_denotation = 0;	
		}
		
		// USE DEFUALT IF NOTHING ELSE IS PASSED
		if(numset.cur_decimal_places != null)
		{
			var decimal_places = numset.cur_decimal_places;
		}
		// USE DEFUALT IF NOTHING ELSE IS PASSED
		if(numset.cur_strip_ezeros != null)
		{
			var strip_ezeros = numset.cur_strip_ezeros;
		}
		
		// ROUND NUMBER TO CORRECT DECIMALS
		//inputnum.toFixed(2);
		if(inputnum < 0)
		{
			var negative_number = 1;
			// CHANGE TO POSITIVE
			inputnum = inputnum * -1;
		}
		
		//var decimal_places = numset.decimal_places;
		inputnum = parseFloat(inputnum).toFixed(decimal_places);
		
		// CHECK FOR DIFFERENT DECIMAL SEPARATORS
		if(numset.cur_decimal_separator != '.')
		{
			var exploded_value = inputnum.split(".");
			inputnum = exploded_value.join(numset.cur_decimal_separator);
		}
		
		// STRIP ZEROS IF NEEDED
		if(strip_ezeros == 1)
		{
			inputnum = strip_end_zeros(inputnum);
		}
		
		// DONT INCLUDE CURRENCY SYMBOL OR RESET IT IF NEEDED
		if(show_denotation == 0)
		{
			var cur_denotation = '';
		}
		else
		{
			var cur_denotation = numset.cur_denotation_reset;
		}
		
		var outputnum;
		
		// NEGATIVE CURRENCY
		if(negative_number == 1)
		{
			//alert('neg');
			switch(numset.cur_neg_num_format)
			{
				case "1":
					outputnum = "(" + cur_denotation + inputnum + ")";
				break;
				case "2":
					outputnum = "(" + cur_denotation + " " + inputnum + ")";
				break;
				case "3":
					outputnum = "(" + inputnum + cur_denotation + ")";
				break;
				case "4":
					outputnum = "(" + inputnum + " " + cur_denotation + ")";
				break;
				case "5":
					outputnum = cur_denotation + "-" + inputnum;
				break;
				case "6":
					outputnum = cur_denotation + " -" + inputnum;
				break;
				case "7":
					outputnum = "-" + cur_denotation +  inputnum;
				break;
				case "8":
					outputnum = "- " + cur_denotation +  inputnum;
				break;
				case "9":
					outputnum = "-" + cur_denotation +  " " + inputnum;
				break;
				case "10":
					outputnum = "- " + cur_denotation +  " " + inputnum;
				break;
				case "11":
					outputnum = "-" + inputnum + cur_denotation;
				break;
				case "12":
					outputnum = "- " + inputnum + cur_denotation;
				break;
				case "13":
					outputnum = "-" + inputnum + " " + cur_denotation;
				break;
				case "14":
					outputnum = "- " + inputnum + " " + cur_denotation;
				break;
			}
		}
		// POSITIVE CURRENCY
		else
		{
			//alert('pos');
			switch(numset.cur_pos_num_format)
			{
				case 1:
					outputnum = cur_denotation + inputnum;
				break;
				case 2:
					outputnum = cur_denotation + " " + inputnum;
				break;
				case 3:
					outputnum = inputnum + cur_denotation;
				break;
				case 4:
					outputnum = inputnum + " " + cur_denotation;
				break;
			}
		}
		return outputnum;
	}
	
	// STRIP ZEROS FROM THE END OF A NUMBER
	function strip_end_zeros(inputnum)
	{
		var lastcar = inputnum.substring(inputnum.length-1,inputnum.length);
		if(lastcar == "0")
		{
			inputnum = inputnum.substring(0,inputnum.length-1);
			inputnum = strip_end_zeros(inputnum);
		}
		
		if(lastcar == numset.decimal_separator)
		{
			inputnum = inputnum.substring(0,inputnum.length-1);
		}
		return inputnum;
	}
	
	//alert(lastcar);
	
	// STRIP ALL NON NUMBERS
	function strip_nonnumbers(inputnum)
	{
		//var m_strOut = new String(inputnum); 
		inputnum = inputnum.replace(/[^0-9]/g, '');		
		return inputnum;
	}
	
	// STRIP NUMBER SYMBOLS
	function strip_number_symbol(inputnum)
	{
		["$","£","¥","€","₪"].each(function(s){
			inputnum = inputnum.replace(s,"");										
		});			
		return inputnum;
	}
	
	// FILL A FIELD WITH THE FORMATTED NUMBER ON BLUR
	function update_input_num(id,decimal_places,strip_ezeros)
	{
		// GET CURENT VALUE
		if($F(id))
		{
			var value = $F(id);
			value = number_clean(value);
			value = number_display(value,decimal_places,strip_ezeros);
			//alert(value);
			$(id).setValue(value);
		}
	}
	
	// FILL A FIELD WITH THE FORMATTED NUMBER ON BLUR
	function update_input_cur(id)
	{
		// GET CURENT VALUE
		if($F(id))
		{
			var value = $F(id);
			value = currency_clean(value);
			value = currency_display(value);
			//alert(value);
			$(id).setValue(value);
		}
	}