<?php
class metadata {
	private $iptc;
	private $charset = 'utf-8';
	private $image;
	//private $replaceChar = array(chr(13).chr(10), "%20", "+", "'", "\"", "\r\n", "\n\r", "\n", "\r");
	private $replaceChar = '';
	
	public function __construct($image)
	{
		$this->iptc = array();
		$this->exif = array();
		$this->image = $image;
	}
	
	public function setCharset($charset)
	{
		$this->charset = $charset;
	}
	
	public function getIPTC()
	{
		$size = getimagesize($this->image, $info);
		
		if(isset($info['APP13']))
		{
			$iptcparsed = iptcparse($info["APP13"]);
		}
		
		if(is_array($iptcparsed))
		{
			# KEYWORDS
			if(is_array($iptcparsed['2#025']))
			{
				$keywords = array();
				foreach($iptcparsed['2#025'] as $kwkey => $kwval)
				{
					$keywords[] =  $this->cleanMetadata($kwval);
				}
			}
			
			$this->iptc['keywords'] 			= $keywords;
			$this->iptc['description'] 			= $this->cleanMetadata($iptcparsed['2#120'][0]);
			$this->iptc['title'] 				= $this->cleanMetadata($iptcparsed['2#005'][0]);
			$this->iptc['instructions'] 		= $this->cleanMetadata($iptcparsed['2#040'][0]);
			$this->iptc['date_created'] 		= $iptcparsed['2#055'][0];
			$this->iptc['author'] 				= $this->cleanMetadata($iptcparsed['2#080'][0]);
			$this->iptc['creator_title'] 		= $this->cleanMetadata($iptcparsed['2#085'][0]);
			$this->iptc['city'] 				= $this->cleanMetadata($iptcparsed['2#090'][0]);
			$this->iptc['state'] 				= $this->cleanMetadata($iptcparsed['2#095'][0]);
			$this->iptc['country'] 				= $this->cleanMetadata($iptcparsed['2#101'][0]);
			$this->iptc['job_identifier'] 		= $this->cleanMetadata($iptcparsed['2#103'][0]); // ALSO TRANSMISSION REFERENCE
			$this->iptc['headline'] 			= $this->cleanMetadata($iptcparsed['2#105'][0]);
			$this->iptc['provider'] 			= $this->cleanMetadata($iptcparsed['2#110'][0]); // ALSO CREDIT
			$this->iptc['source'] 				= $this->cleanMetadata($iptcparsed['2#115'][0]);
			$this->iptc['description_writer'] 	= $this->cleanMetadata($iptcparsed['2#122'][0]);
			$this->iptc['urgency'] 				= $this->cleanMetadata($iptcparsed['2#010'][0]);
			$this->iptc['copyright_notice']		= $this->cleanMetadata($iptcparsed['2#116'][0]);
		}
		return $this->iptc;
	}
	
	public function cleanEXIF($input) // NOT USED
	{
		return addslashes($input);
	}
	
	public function getEXIF()
	{
		@$this->exif = exif_read_data($this->image);	
		//$this->exif = array_map(array('metadata','cleanEXIF'),$this->exif);
		return $this->exif;
	}
	
	public function getXMP()
	{
		/*
		$buffer = NULL;
		if (($file_pointer = fopen($this->image, 'r')) === FALSE)
		{
			//throw new RuntimeException('Could not open file for reading');
			exit;
		}
		$found_start = FALSE;
		
		while(($chunk = fread($file_pointer, 1024)) !== FALSE)
		{
			if (($pos = strpos($chunk, '<x:xmpmeta')) !== FALSE)
			{
				$found_start = TRUE;
				$buffer .= substr($chunk, $pos);
			}
			elseif (($pos = strpos($chunk, '</x:xmpmeta>')) !== FALSE)
			{
				$buffer .= substr($chunk, 0, $pos + 12);
				break;
			}
			elseif ($found_start)
			{
				$buffer .= $chunk;
			}      // end elseif // ($found_start) //   
		}   // end while // (($chunk = fread($file_pointer, $chunk_size) !== FALSE) //   
											 
		fclose($file_pointer);
		return $buffer;
		*/
	}
	
	public function cleanMetadata($metadataField)
	{
		if($this->charset == 'utf-8')
		{
			$metadataField = utf8_encode($metadataField);
		}
		return str_replace($this->replaceChar, "", $metadataField);
	}		
}
/*
$myiptc = new metadata('../tmp/exiftest.jpg');
$exif = $myiptc->getXMP();
echo $exif;
*/
?>