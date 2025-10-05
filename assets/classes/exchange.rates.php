<?php

/************************************************************************************************************************

	Name:		CurrencyConvert
	Version:	2.0.4
	Author:		Stephen Smith
				steve.smith@virtual.uk.net


	Copyright © 2007-2009 Stephen Smith
	email: steve.smith@virtual.uk.net

	This file is part of CurrencyConvert.

	CurrencyConvert is free software; you can redistribute it and/or modify
	it under the terms of the GNU Lesser General Public License as published by
	the Free Software Foundation; either version 2.1 of the License, or
	(at your option) any later version.

	CurrencyConvert is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public License
	along with CurrencyConvert; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*************************************************************************************************************************

    CurrencyConvert is a simple PHP4+ Class that performs online currency conversion
    lookup, providing exchange rates between more than 150 currencies.

    This Class scrapes currency converter pages; currently the data sources supported are:

    Yahoo (default) - Yahoo! quotation file download at http://finance.yahoo.com/d/
    Google          - Google Currency Converter at http://finance.google.com/finance/converter
    Yahoo2          - Yahoo! Currency Converter at http://finance.yahoo.com/currency-converter

    Please ensure that you acknowledge the source if you use this conversion Class.

    Requirements:
    PHP 4.2.0 or higher
    PHP cURL extension (for Google and Yahoo2 data sources)


*************************************************************************************************************************/
class CurrencyConvert {

	var $from, $to, $bid, $ask, $currencies, $localised_currencies, $source ;



	/** Constructor sets the currency symbols/names list depending on the information source being used (defaults to Yahoo)*/
	function CurrencyConvert( $source = 'Yahoo' ) {
		$this->from = NULL ;
		$this->to = NULL ;
		$this->price = NULL ;
        $this->source = $source ;
        
        switch ( $source ) {
            
            case 'Google':
                // Currency symbols recognised by Google - will have to be edited if Google change the symbols they use
                $this->currencies = array( 'AED' => 'United Arab Emirates Dirham', 'ANG' => 'Netherlands Antillean Gulden', 'ARS' => 'Argentine Peso', 'AUD' => 'Australian Dollar', 'BGN' => 'Bulgarian Lev', 'BHD' => 'Bahraini Dinar', 'BND' => 'Brunei Dollar', 'BOB' => 'Bolivian Boliviano', 'BRL' => 'Brazilian Real', 'BWP' => 'Botswana Pula', 'CAD' => 'Canadian Dollar', 'CHF' => 'Swiss Franc', 'CLP' => 'Chilean Peso', 'CNY' => 'Chinese Yuan (renminbi)', 'COP' => 'Colombian Peso', 'CSD' => 'Serbian Dinar', 'CZK' => 'Czech Koruna', 'DKK' => 'Danish Krone', 'EEK' => 'Estonian Kroon', 'EGP' => 'Egyptian Pound', 'EUR' => 'Euro', 'FJD' => 'Fijian Dollar', 'GBP' => 'British Pound', 'HKD' => 'Hong Kong Dollar', 'HNL' => 'Honduran Lempira', 'HRK' => 'Croatian Kuna', 'HUF' => 'Hungarian Forint', 'IDR' => 'Indonesian Rupiah', 'ILS' => 'New Israeli Sheqel', 'INR' => 'Indian Rupee', 'ISK' => 'Icelandic Króna', 'JPY' => 'Japanese Yen', 'KRW' => 'South Korean Won', 'KWD' => 'Kuwaiti Dinar', 'KZT' => 'Kazakhstani Tenge', 'LKR' => 'Sri Lankan Rupee', 'LTL' => 'Lithuanian Litas', 'MAD' => 'Moroccan Dirham', 'MUR' => 'Mauritian Rupee', 'MXN' => 'Mexican Peso', 'MYR' => 'Malaysian Ringgit', 'NOK' => 'Norwegian Krone', 'NPR' => 'Nepalese Rupee', 'NZD' => 'New Zealand Dollar', 'OMR' => 'Omani Rial', 'PEN' => 'Peruvian Nuevo Sol', 'PHP' => 'Philippine Peso', 'PKR' => 'Pakistani Rupee', 'PLN' => 'Polish Złoty', 'QAR' => 'Qatari Riyal', 'RON' => 'New Romanian Leu', 'RUB' => 'Russian Ruble', 'SAR' => 'Saudi Riyal', 'SEK' => 'Swedish Krona', 'SGD' => 'Singapore Dollar', 'SIT' => 'Slovenian Tolar', 'SKK' => 'Slovak Koruna', 'THB' => 'Thai Baht', 'TRY' => 'New Turkish Lira', 'TTD' => 'Trinidad and Tobago Dollar', 'TWD' => 'New Taiwan Dollar', 'UAH' => 'Ukrainian Hryvnia', 'USD' => 'United States Dollar', 'VEB' => 'Venezuelan Bolívar', 'ZAR' => 'South African Rand' ) ;
                // Localised currency name for each currency symbol recognised by Google - will have to be edited if Google change the symbols they use
                $this->localised_currencies = array( 'AED' => 'United Arab Emirates Dirham', 'ANG' => 'Netherlands Antillean Gulden', 'ARS' => 'Argentine Peso', 'AUD' => 'Australian Dollar', 'BGN' => 'Bulgarian Lev', 'BHD' => 'Bahraini Dinar', 'BND' => 'Brunei Dollar', 'BOB' => 'Bolivian Boliviano', 'BRL' => 'Brazilian Real', 'BWP' => 'Botswana Pula', 'CAD' => 'Canadian Dollar', 'CHF' => 'Swiss Franc', 'CLP' => 'Chilean Peso', 'CNY' => 'Chinese Yuan (renminbi)', 'COP' => 'Colombian Peso', 'CSD' => 'Serbian Dinar', 'CZK' => 'Czech Koruna', 'DKK' => 'Danish Krone', 'EEK' => 'Estonian Kroon', 'EGP' => 'Egyptian Pound', 'EUR' => 'Euro', 'FJD' => 'Fijian Dollar', 'GBP' => 'British Pound', 'HKD' => 'Hong Kong Dollar', 'HNL' => 'Honduran Lempira', 'HRK' => 'Croatian Kuna', 'HUF' => 'Hungarian Forint', 'IDR' => 'Indonesian Rupiah', 'ILS' => 'New Israeli Sheqel', 'INR' => 'Indian Rupee', 'ISK' => 'Icelandic Króna', 'JPY' => 'Japanese Yen', 'KRW' => 'South Korean Won', 'KWD' => 'Kuwaiti Dinar', 'KZT' => 'Kazakhstani Tenge', 'LKR' => 'Sri Lankan Rupee', 'LTL' => 'Lithuanian Litas', 'MAD' => 'Moroccan Dirham', 'MUR' => 'Mauritian Rupee', 'MXN' => 'Mexican Peso', 'MYR' => 'Malaysian Ringgit', 'NOK' => 'Norwegian Krone', 'NPR' => 'Nepalese Rupee', 'NZD' => 'New Zealand Dollar', 'OMR' => 'Omani Rial', 'PEN' => 'Peruvian Nuevo Sol', 'PHP' => 'Philippine Peso', 'PKR' => 'Pakistani Rupee', 'PLN' => 'Polish Złoty', 'QAR' => 'Qatari Riyal', 'RON' => 'New Romanian Leu', 'RUB' => 'Russian Ruble', 'SAR' => 'Saudi Riyal', 'SEK' => 'Swedish Krona', 'SGD' => 'Singapore Dollar', 'SIT' => 'Slovenian Tolar', 'SKK' => 'Slovak Koruna', 'THB' => 'Thai Baht', 'TRY' => 'New Turkish Lira', 'TTD' => 'Trinidad and Tobago Dollar', 'TWD' => 'New Taiwan Dollar', 'UAH' => 'Ukrainian Hryvnia', 'USD' => 'United States Dollar', 'VEB' => 'Venezuelan Bolívar', 'ZAR' => 'South African Rand' ) ;
                break ;
                
            case 'Yahoo':
            default:
                // Currency symbols recognised by Yahoo! - will have to be edited if Yahoo! change the symbols they use
                $this->currencies = array( 'ALL' => 'Albanian Lek', 'DZD' => 'Algerian Dinar', 'XAL' => 'Aluminium Ounces', 'ARS' => 'Argentine Peso', 'AWG' => 'Aruba Florin', 'AUD' => 'Australian Dollar', 'BSD' => 'Bahamian Dollar', 'BHD' => 'Bahraini Dinar', 'BDT' => 'Bangladesh Taka', 'BBD' => 'Barbados Dollar', 'BYR' => 'Belarus Ruble', 'BZD' => 'Belize Dollar', 'BMD' => 'Bermuda Dollar', 'BTN' => 'Bhutan Ngultrum', 'BOB' => 'Bolivian Boliviano', 'BWP' => 'Botswana Pula', 'BRL' => 'Brazilian Real', 'GBP' => 'British Pound', 'BND' => 'Brunei Dollar', 'BGN' => 'Bulgarian Lev', 'BIF' => 'Burundi Franc', 'KHR' => 'Cambodia Riel', 'CAD' => 'Canadian Dollar', 'CVE' => 'Cape Verde Escudo', 'KYD' => 'Cayman Islands Dollar', 'XOF' => 'CFA Franc (BCEAO)', 'XAF' => 'CFA Franc (BEAC)', 'CLP' => 'Chilean Peso', 'CNY' => 'Chinese Yuan', 'COP' => 'Colombian Peso', 'KMF' => 'Comoros Franc', 'XCP' => 'Copper Ounces', 'CRC' => 'Costa Rica Colon', 'HRK' => 'Croatian Kuna', 'CUP' => 'Cuban Peso', 'CYP' => 'Cyprus Pound', 'CZK' => 'Czech Koruna', 'DKK' => 'Danish Krone', 'DJF' => 'Dijibouti Franc', 'DOP' => 'Dominican Peso', 'XCD' => 'East Caribbean Dollar', 'ECS' => 'Ecuador Sucre', 'EGP' => 'Egyptian Pound', 'SVC' => 'El Salvador Colon', 'ERN' => 'Eritrea Nakfa', 'EEK' => 'Estonian Kroon', 'ETB' => 'Ethiopian Birr', 'EUR' => 'Euro', 'FKP' => 'Falkland Islands Pound', 'FJD' => 'Fiji Dollar', 'GMD' => 'Gambian Dalasi', 'GHC' => 'Ghanian Cedi', 'GIP' => 'Gibraltar Pound', 'XAU' => 'Gold Ounces', 'GTQ' => 'Guatemala Quetzal', 'GNF' => 'Guinea Franc', 'GYD' => 'Guyana Dollar', 'HTG' => 'Haiti Gourde', 'HNL' => 'Honduras Lempira', 'HKD' => 'Hong Kong Dollar', 'HUF' => 'Hungarian Forint', 'ISK' => 'Iceland Krona', 'INR' => 'Indian Rupee', 'IDR' => 'Indonesian Rupiah', 'IRR' => 'Iran Rial', 'IQD' => 'Iraqi Dinar', 'ILS' => 'Israeli Shekel', 'JMD' => 'Jamaican Dollar', 'JPY' => 'Japanese Yen', 'JOD' => 'Jordanian Dinar', 'KZT' => 'Kazakhstan Tenge', 'KES' => 'Kenyan Shilling', 'KRW' => 'Korean Won', 'KWD' => 'Kuwaiti Dinar', 'LAK' => 'Lao Kip', 'LVL' => 'Latvian Lat', 'LBP' => 'Lebanese Pound', 'LSL' => 'Lesotho Loti', 'LRD' => 'Liberian Dollar', 'LYD' => 'Libyan Dinar', 'LTL' => 'Lithuanian Lita', 'MOP' => 'Macau Pataca', 'MKD' => 'Macedonian Denar', 'MWK' => 'Malawi Kwacha', 'MYR' => 'Malaysian Ringgit', 'MVR' => 'Maldives Rufiyaa', 'MTL' => 'Maltese Lira', 'MRO' => 'Mauritania Ougulya', 'MUR' => 'Mauritius Rupee', 'MXN' => 'Mexican Peso', 'MDL' => 'Moldovan Leu', 'MNT' => 'Mongolian Tugrik', 'MAD' => 'Moroccan Dirham', 'MMK' => 'Myanmar Kyat', 'NAD' => 'Namibian Dollar', 'NPR' => 'Nepalese Rupee', 'ANG' => 'Neth Antilles Guilder', 'TRY' => 'New Turkish Lira', 'NZD' => 'New Zealand Dollar', 'ZWN' => 'New Zimbabwe Dollar', 'NIO' => 'Nicaragua Cordoba', 'NGN' => 'Nigerian Naira', 'KPW' => 'North Korean Won', 'NOK' => 'Norwegian Krone', 'OMR' => 'Omani Rial', 'XPF' => 'Pacific Franc', 'PKR' => 'Pakistani Rupee', 'XPD' => 'Palladium Ounces', 'PAB' => 'Panama Balboa', 'PGK' => 'Papua New Guinea Kina', 'PYG' => 'Paraguayan Guarani', 'PEN' => 'Peruvian Nuevo Sol', 'PHP' => 'Philippine Peso', 'XPT' => 'Platinum Ounces', 'PLN' => 'Polish Zloty', 'QAR' => 'Qatar Rial', 'RON' => 'Romanian New Leu', 'RUB' => 'Russian Rouble', 'RWF' => 'Rwanda Franc', 'WST' => 'Samoa Tala', 'STD' => 'Sao Tome Dobra', 'SAR' => 'Saudi Arabian Riyal', 'SCR' => 'Seychelles Rupee', 'SLL' => 'Sierra Leone Leone', 'XAG' => 'Silver Ounces', 'SGD' => 'Singapore Dollar', 'SKK' => 'Slovak Koruna', 'SIT' => 'Slovenian Tolar', 'SBD' => 'Solomon Islands Dollar', 'SOS' => 'Somali Shilling', 'ZAR' => 'South African Rand', 'LKR' => 'Sri Lanka Rupee', 'SHP' => 'St Helena Pound', 'SDD' => 'Sudanese Dinar', 'SZL' => 'Swaziland Lilageni', 'SEK' => 'Swedish Krona', 'CHF' => 'Swiss Franc', 'SYP' => 'Syrian Pound', 'TWD' => 'Taiwan Dollar', 'TZS' => 'Tanzanian Shilling', 'THB' => 'Thai Baht', 'TOP' => 'Tonga Pa\'anga', 'TTD' => 'Trinidad&Tobago Dollar', 'TND' => 'Tunisian Dinar', 'USD' => 'U.S. Dollar', 'AED' => 'UAE Dirham', 'UGX' => 'Ugandan Shilling', 'UAH' => 'Ukraine Hryvnia', 'UYU' => 'Uruguayan New Peso', 'VUV' => 'Vanuatu Vatu', 'VEB' => 'Venezuelan Bolivar', 'VND' => 'Vietnam Dong', 'YER' => 'Yemen Riyal', 'ZMK' => 'Zambian Kwacha', 'ZWD' => 'Zimbabwe Dollar' ) ;
                // Localised currency name for each currency symbol recognised by Yahoo! - will have to be edited if Yahoo! change the symbols they use
                $this->localised_currencies = array( 'ALL' => 'Albanian Lek', 'DZD' => 'Algerian Dinar', 'XAL' => 'Aluminium Ounces', 'ARS' => 'Argentine Peso', 'AWG' => 'Aruba Florin', 'AUD' => 'Australian Dollar', 'BSD' => 'Bahamian Dollar', 'BHD' => 'Bahraini Dinar', 'BDT' => 'Bangladesh Taka', 'BBD' => 'Barbados Dollar', 'BYR' => 'Belarus Ruble', 'BZD' => 'Belize Dollar', 'BMD' => 'Bermuda Dollar', 'BTN' => 'Bhutan Ngultrum', 'BOB' => 'Bolivian Boliviano', 'BWP' => 'Botswana Pula', 'BRL' => 'Brazilian Real', 'GBP' => 'British Pound', 'BND' => 'Brunei Dollar', 'BGN' => 'Bulgarian Lev', 'BIF' => 'Burundi Franc', 'KHR' => 'Cambodia Riel', 'CAD' => 'Canadian Dollar', 'CVE' => 'Cape Verde Escudo', 'KYD' => 'Cayman Islands Dollar', 'XOF' => 'CFA Franc (BCEAO)', 'XAF' => 'CFA Franc (BEAC)', 'CLP' => 'Chilean Peso', 'CNY' => 'Chinese Yuan', 'COP' => 'Colombian Peso', 'KMF' => 'Comoros Franc', 'XCP' => 'Copper Ounces', 'CRC' => 'Costa Rica Colon', 'HRK' => 'Croatian Kuna', 'CUP' => 'Cuban Peso', 'CYP' => 'Cyprus Pound', 'CZK' => 'Czech Koruna', 'DKK' => 'Danish Krone', 'DJF' => 'Dijibouti Franc', 'DOP' => 'Dominican Peso', 'XCD' => 'East Caribbean Dollar', 'ECS' => 'Ecuador Sucre', 'EGP' => 'Egyptian Pound', 'SVC' => 'El Salvador Colon', 'ERN' => 'Eritrea Nakfa', 'EEK' => 'Estonian Kroon', 'ETB' => 'Ethiopian Birr', 'EUR' => 'Euro', 'FKP' => 'Falkland Islands Pound', 'FJD' => 'Fiji Dollar', 'GMD' => 'Gambian Dalasi', 'GHC' => 'Ghanian Cedi', 'GIP' => 'Gibraltar Pound', 'XAU' => 'Gold Ounces', 'GTQ' => 'Guatemala Quetzal', 'GNF' => 'Guinea Franc', 'GYD' => 'Guyana Dollar', 'HTG' => 'Haiti Gourde', 'HNL' => 'Honduras Lempira', 'HKD' => 'Hong Kong Dollar', 'HUF' => 'Hungarian Forint', 'ISK' => 'Iceland Krona', 'INR' => 'Indian Rupee', 'IDR' => 'Indonesian Rupiah', 'IRR' => 'Iran Rial', 'IQD' => 'Iraqi Dinar', 'ILS' => 'Israeli Shekel', 'JMD' => 'Jamaican Dollar', 'JPY' => 'Japanese Yen', 'JOD' => 'Jordanian Dinar', 'KZT' => 'Kazakhstan Tenge', 'KES' => 'Kenyan Shilling', 'KRW' => 'Korean Won', 'KWD' => 'Kuwaiti Dinar', 'LAK' => 'Lao Kip', 'LVL' => 'Latvian Lat', 'LBP' => 'Lebanese Pound', 'LSL' => 'Lesotho Loti', 'LRD' => 'Liberian Dollar', 'LYD' => 'Libyan Dinar', 'LTL' => 'Lithuanian Lita', 'MOP' => 'Macau Pataca', 'MKD' => 'Macedonian Denar', 'MWK' => 'Malawi Kwacha', 'MYR' => 'Malaysian Ringgit', 'MVR' => 'Maldives Rufiyaa', 'MTL' => 'Maltese Lira', 'MRO' => 'Mauritania Ougulya', 'MUR' => 'Mauritius Rupee', 'MXN' => 'Mexican Peso', 'MDL' => 'Moldovan Leu', 'MNT' => 'Mongolian Tugrik', 'MAD' => 'Moroccan Dirham', 'MMK' => 'Myanmar Kyat', 'NAD' => 'Namibian Dollar', 'NPR' => 'Nepalese Rupee', 'ANG' => 'Neth Antilles Guilder', 'TRY' => 'New Turkish Lira', 'NZD' => 'New Zealand Dollar', 'ZWN' => 'New Zimbabwe Dollar', 'NIO' => 'Nicaragua Cordoba', 'NGN' => 'Nigerian Naira', 'KPW' => 'North Korean Won', 'NOK' => 'Norwegian Krone', 'OMR' => 'Omani Rial', 'XPF' => 'Pacific Franc', 'PKR' => 'Pakistani Rupee', 'XPD' => 'Palladium Ounces', 'PAB' => 'Panama Balboa', 'PGK' => 'Papua New Guinea Kina', 'PYG' => 'Paraguayan Guarani', 'PEN' => 'Peruvian Nuevo Sol', 'PHP' => 'Philippine Peso', 'XPT' => 'Platinum Ounces', 'PLN' => 'Polish Zloty', 'QAR' => 'Qatar Rial', 'RON' => 'Romanian New Leu', 'RUB' => 'Russian Rouble', 'RWF' => 'Rwanda Franc', 'WST' => 'Samoa Tala', 'STD' => 'Sao Tome Dobra', 'SAR' => 'Saudi Arabian Riyal', 'SCR' => 'Seychelles Rupee', 'SLL' => 'Sierra Leone Leone', 'XAG' => 'Silver Ounces', 'SGD' => 'Singapore Dollar', 'SKK' => 'Slovak Koruna', 'SIT' => 'Slovenian Tolar', 'SBD' => 'Solomon Islands Dollar', 'SOS' => 'Somali Shilling', 'ZAR' => 'South African Rand', 'LKR' => 'Sri Lanka Rupee', 'SHP' => 'St Helena Pound', 'SDD' => 'Sudanese Dinar', 'SZL' => 'Swaziland Lilageni', 'SEK' => 'Swedish Krona', 'CHF' => 'Swiss Franc', 'SYP' => 'Syrian Pound', 'TWD' => 'Taiwan Dollar', 'TZS' => 'Tanzanian Shilling', 'THB' => 'Thai Baht', 'TOP' => 'Tonga Pa\'anga', 'TTD' => 'Trinidad&Tobago Dollar', 'TND' => 'Tunisian Dinar', 'USD' => 'U.S. Dollar', 'AED' => 'UAE Dirham', 'UGX' => 'Ugandan Shilling', 'UAH' => 'Ukraine Hryvnia', 'UYU' => 'Uruguayan New Peso', 'VUV' => 'Vanuatu Vatu', 'VEB' => 'Venezuelan Bolivar', 'VND' => 'Vietnam Dong', 'YER' => 'Yemen Riyal', 'ZMK' => 'Zambian Kwacha', 'ZWD' => 'Zimbabwe Dollar' ) ;
                break ;
        }
	}

	/** Does the currency conversion lookup for the currency symbols supplied as parameters
    Also accepts an option of a date (human readable or timestamp) for historical lookups where supported (currently Yahoo2 data source only) */
	function convert( $from, $to, $date = NULL ) {
		$this->from = $from ;
		$this->to = $to ; 
        if ( !$date )
        {
            // Date parameter absent - default to now
            $date = time() ; 
        } else if ( !is_numeric( $date ) )
        { 
            // Date parameter isn't a timestamp, so treat as text representation of a date
            $date = strtotime( $date ) ;
        } 
        
        /* If the from and to currencies are the same then just set the rate to 1 and return straight away;
        avoids a problem with some converters */
        if ( $from == $to )     {
            $this->price = 1 ;
            return TRUE ;
        }


		//*************************** PARSE THE PAGE RETURNED BY THE SOURCE WEBSITE ******************************
		// This section will have to be modified if (when) Yahoo!/Google/whoever change their page format!
	    //********************************************************************************************************

        switch ( $this->source )
        {
            case 'Google':
                $url = 'http://www.google.com/finance/converter?a=1&from=' . $from . '&to=' . $to ;
                $pattern = '#\sid=currency_converter_result[^0-9]*[0-9.]+[^A-Z]*' . $from . '[^A-Z][^0-9]+([0-9.]+)[^A-Z]*' . $to . '[^A-Z]#' ;
                return $this->fetchSourceData( $url, $pattern, 1, 0, 1, 'curl' ) ;    
                break; 
            case 'Yahoo2' ;
                $url = 'http://finance.yahoo.com/currency/converter-results/' . date( 'Ymd', $date ) . '/1000000-' . $from . '-to-' . $to . '.html' ;
                $pattern = '#<span\sclass="converted-result">([0-9.]*) '. $this->to . '</span>#' ;
                return $this->fetchSourceData( $url, $pattern, 1, 0, 1000000, 'curl' ) ;            
                break ;
            case 'Yahoo':
            default:
                // URL for the source converter, which will include the currencies and amount to convert (1000000 to avoid converter rounding down)                
                $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $from . $to .'=X'; 
                // Regular expression attern to capture result field; also ensures that correct currency code is present                 
                $pattern = '#"' . $from . $to . '=X",([0-9.]*),"#' ;
                // Define maximum number of matches the pattern should make (ideally this will be 1, if the pattern is good)
                $max_matches = 1 ;
                // Define which of the matches contains the result (normally 0)
                $result_match = 0 ;
                // Define the currency amount to use in the conversion (some converters need a large number to avoid loss of precision due to rounding)
                $conversion_amount = 1 ;
                // Specify the method needed to fetch the data (curl or fopen)
                $method = 'fopen' ;
                // Get the result and return the error value (TRUE=success)
                return $this->fetchSourceData( $url, $pattern, $max_matches, $result_match, $conversion_amount, $method ) ;           
                break ;
        }

	}

    
    /** Private Method to fetch the source page, extract the result, and check that the conversion appears to be successful
    Supports both cURL and fopen methods of fetching the data.
    Returns TRUE and sets price conversion value if everything seems OK, otherwise FALSE if there was an error */
    function fetchSourceData( $url, $pattern, $max_matches, $result_match, $conversion_amount, $method="fopen" ) {
        // USED TO BE $method="curl"
        // Fetch source page
        switch ( $method ) {
            case 'curl':       
                $ch = curl_init();
                curl_setopt( $ch, CURLOPT_URL, $url ) ;
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $ch, CURLOPT_HEADER, 1 );
                curl_setopt( $ch, CURLOPT_VERBOSE, 1 ) ;
                $page = curl_exec( $ch ) ;
                curl_close ($ch);
                break;
            case 'fopen':
                $ch = @fopen( $url, 'r' ) ;      
                if ( $ch ) {  
                    $page = fgets( $ch, 4096 ) ;  
                    fclose( $ch ) ;  
                } else {
                    $page = 'FAIL' ;
                } 
                break ;
        }

        // Capture result field
        preg_match_all( $pattern, $page, $matches ) ;

        // Lookup has failed unless at least and no more than the maximum expected one non-zero matches were achieved
        if ( count( $matches[1] ) == 0 OR count( $matches[1] ) > $max_matches OR $matches[1][ $result_match ] == 0 ) 
        {
            // Parsing was unsuccessful - return error result
            // To receive an email warning if the convertor fails, enter your email address below and uncomment the line
            // mail( 'your@email.address', 'Warning: CurrencyConvert Lookup Failed', 'The CurrencyConvert installation at ' . $_SERVER[ 'SERVER_NAME' ] . ' failed to parse the result or obtained a zero result when attempting to convert from ' . $this->from . ' to ' . $this->to . ' using the ' . $this->source . ' currency converter at ' . $url . ".\n\n" . $page ) ;
            return FALSE;
        } else {
            // Set converted price and return success result if we can interpret the value as a decimal number
            $decimal_result = $matches[1][ $result_match ] / $conversion_amount ;
            if ( !settype( $decimal_result, 'float' ) ) return FALSE ;
            $this->price = $decimal_result ;
            return TRUE ;
        }
    }
    
    //*********************************** END PAGE PARSING ****************************************************    
  
  
    
    
	/** Method to return the price for the specified amount (defaults to 1 for amount) 
    Second parameter is unused in this version (was bid/ask/mid in 1.x versions); provided now just to avoid breaking existing code */
	function price( $amount = 1.00, $unused = '' ) {
		return $amount * $this->price ;
	}

	/** Method to return the long name associated with either the From or To currency symbol */
	function name( $symbol ) {
		return ( $symbol == 'from' ) ?
			$this->localised_currencies[ $this->from ] : $this->localised_currencies[ $this->to ] ;
	}

	/** Static function to return the list of currencies available, as an array of ( symbol => long name ) */
	function currencies() {
		return $this->localised_currencies ;
	}

}

//************************END OF CLASS DEFINITION ***************************


?>