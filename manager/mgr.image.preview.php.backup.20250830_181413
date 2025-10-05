<?php
	# KEEP THE PAGE FROM CACHING
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past	
	
	# START THE PAGE LOAD TIMER
	$pltime 						= microtime();
	$pltime 						= explode(" ", $pltime);
	$plstart 						= $pltime[1] + $pltime[0];
	
	$icon_width = ($_GET['width'])? $_GET['width'] : 200;
	$quality = ($_GET['quality'])? $_GET['quality']: 80;
	$watermark = $_GET['watermark'];
	$save  = ($_GET['save']) ? $_GET['save'] : '';
	$src = ($_GET['src']) ? '/'.$_GET['src'] : "/images/samples/mgr.sample.photo1.350px.jpg";

	//echo $src; exit;

	//$mgrBasePath = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . "/";
	//$basePath = $_SERVER['DOCUMENT_ROOT'] . dirname(dirname($_SERVER['PHP_SELF']));
	//echo $basePath; exit;
	
	$mgrBasePath = dirname(__FILE__); //$_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . "/";
	$basePath = dirname(dirname(__FILE__)); //$_SERVER['DOCUMENT_ROOT'] . dirname(dirname($_SERVER['PHP_SELF']));

	$src = $mgrBasePath.$src; // Added in 4.4.6

	//echo $src; exit;

	if($_GET['processor'] == 'im')
	{
		try
		{
			//echo $basePath.$src; exit;
			
			$image = new Imagick($src);
			$image->setImageFormat('jpg');
			
			//$size = getimagesize($src);
			$ratio = $icon_width/$image->getImageWidth();
			$height = round($image->getImageWidth() * $ratio);
			
			$image->setImageCompression(imagick::COMPRESSION_JPEG); 
			$image->setImageCompressionQuality($quality);
			$image->stripImage();
			
			$image->thumbnailImage($icon_width, 0);
			
			# ADD WATERMARK
			if(!empty($watermark) and file_exists($basePath."/assets/watermarks/" . $watermark)){
				
				$imwatermark = new Imagick($basePath."/assets/watermarks/" . $watermark);				

				$wwidth = round($imwatermark->getImageWidth());
				$wheight = round($imwatermark->getImageHeight());
				
				//$imwatermark->thumbnailImage($wwidth, 0);
				
				$horizextra = $icon_width - $wwidth;
				$vertextra = $image->getImageHeight() - $wheight;
						
				$horizmargin = round($horizextra/2);
				$vertmargin = round($vertextra/2);	
			
				$image->compositeImage($imwatermark,Imagick::COMPOSITE_OVER,$horizmargin,$vertmargin);
				
			}
			
			if($_GET['sharpen'] == 1)
			{
				$image->sharpenImage(1,1);
			}
			
			
			# SAVE
			if($save)
			{
				$image->writeImages($save, true);
				
				# JUST RETURN FILE SIZE AND EXIT
				if($_GET['return_size'])
				{
					if(file_exists($save))
					{
						# PAGE END TIMER
						$pltime = microtime();
						$pltime = explode(" ", $pltime);
						$plfinish = $pltime[1] + $pltime[0];
						$pltotaltime = ($plfinish - $plstart);
						
						$test_file_size = round(filesize($save)/1025);
						echo "$test_file_size|".round($pltotaltime,2);
					}
					else
					{
						echo "error|moreinfo";
					}
					exit;
				}
			}
			
			header("Content-type: image/jpeg");
			echo $image;
		}
		catch(Exception $e)
		{
			echo $e->getMessage();	
			die();
		}
	}
	else
	{
		//usleep(500000); // 1000000 = 1 sec
		
		$size = getimagesize($src);
		$ratio = $icon_width/$size[0];
		$height = round($size[1] * $ratio);
		
		$src_img = imagecreatefromjpeg($src);
		$dst_img = imagecreatetruecolor($icon_width, $height);
		
		// HEIGHT PROPORTIONAL SCALING, TO BE USED ON THE PUBLIC SIDE ONLY, ONLY IF CROPPING IS ON
		// $pheight = .75;
		
		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $icon_width, $height, imagesx($src_img), imagesy($src_img));
		
		# ADD WATERMARK
		if(!empty($watermark) and file_exists("../assets/watermarks/" . $watermark)){
			$watermarkinfo = getimagesize("../assets/watermarks/" . $watermark);		
			$watermarkImage = imagecreatefrompng("../assets/watermarks/" . $watermark);		
			
			/*
			# RESCALE WATERMARK
			if($_GET['rescale'] == 1){
				$wwidth = round($watermarkinfo[0] * $ratio);
				$wheight = round($watermarkinfo[1] * $ratio);
				
				$horizextra = $icon_width - $wwidth;
				$vertextra = $height - $wheight;
		
				$horizmargin = round($horizextra/2);
				$vertmargin = round($vertextra/2);	
				
				//echo $wwidth . " = " . $wheight; exit;
			
				imagealphablending($watermarkImage, false);
				imagesavealpha($watermarkImage, true);
				$dst_wimg = imagecreatetruecolor($wwidth, $wheight);		
				imagealphablending($dst_wimg, false);		
				imagecopyresampled($dst_wimg, $watermarkImage, 0, 0, 0, 0, $wwidth, $wheight, $watermarkinfo[0], $watermarkinfo[1]);
				imagesavealpha($dst_wimg, true);
				
				imagecopy($dst_img, $dst_wimg, $horizmargin, $vertmargin, 0, 0, $wwidth, $wheight);
			} else {
			*/
			# DO NOT SCALE WATERMARK
			$horizextra = $icon_width - $watermarkinfo[0];
			$vertextra = $height - $watermarkinfo[1];
	
			$horizmargin = round($horizextra/2);
			$vertmargin = round($vertextra/2);		
			imagecopy($dst_img, $watermarkImage, $horizmargin, $vertmargin, 0, 0, $watermarkinfo[0], $watermarkinfo[1]);

			imagedestroy($watermarkImage);
		}
		
		if($_GET['sharpen'] == 1)
		{
			$sharpen_matrix = array(array(-1,-1,-1,),array(-1, 16,-1,),array(-1,-1,-1));
			$divisor = 8;
			$offset = 0;
			imageconvolution($dst_img, $sharpen_matrix, $divisor, $offset);
		}
		
		# SAVE
		if($save)
		{
			imagejpeg($dst_img,$save, $quality);
			# JUST RETURN FILE SIZE AND EXIT
			if($_GET['return_size'] and function_exists("imageconvolution"))
			{
				if(file_exists($save))
				{
					# PAGE END TIMER
					$pltime = microtime();
					$pltime = explode(" ", $pltime);
					$plfinish = $pltime[1] + $pltime[0];
					$pltotaltime = ($plfinish - $plstart);
					
					$test_file_size = round(filesize($save)/1025);
					echo "$test_file_size|".round($pltotaltime,2);
				}
				else
				{
					echo "error|moreinfo";
				}
				exit;
			}
		}
		
		# OUTPUT AND DESTROY
		imagejpeg($dst_img,NULL,$quality);
		//imagejpeg($dst_img,'', $quality);
		imagedestroy($src_img); 
		imagedestroy($dst_img);
	}
?>