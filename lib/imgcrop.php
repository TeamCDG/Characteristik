<?php
function cropImg($filename, $cwidth, $cheight, $savefilename, $quality)
{
	list($width, $height) = getimagesize($filename);
	$difh = abs($height - $cheight);
	$difw = abs($width - $cwidth);
	
	$crimg = null;
	
	if($difh > $difw)
	{
		$f = $width / $cwidth;
		$tmpw = $cwidth;
		$tmph = round($height / $f);
		
		$crimg = imagecreatetruecolor($tmpw, $tmph);
		$coimg = null;
		if(substr($filename, -4) == ".png")
		{
			$coimg = imagecreatefrompng($filename);
		}
		else
		{
			$coimg = imagecreatefromjpeg($filename);
		}
			
		imagecopyresampled ( $crimg, $coimg, 0, 0, 0, 0, $tmpw, $tmph, $width, $height);
		imagedestroy($coimg);
	}
	else
	{
		$f = $height / $cheight;
		$tmpw = round($width / $f);
		$tmph = $cheight;
		
		$crimg = imagecreatetruecolor($tmpw, $tmph);
		$coimg = null;
		if(substr($filename, -4) == ".png")
		{
			$coimg = imagecreatefrompng($filename);
		}
		else
		{
			$coimg = imagecreatefromjpeg($filename);
		}
			
		imagecopyresampled ( $crimg, $coimg, 0, 0, 0, 0, $tmpw, $tmph, $width, $height);
		imagedestroy($coimg);
	}
	
	$finimg = imagecreatetruecolor($cwidth, $cheight);
	
	if(imagesx($crimg) == $cwidth && imagesy($crimg) == $cheight)
	{
		imagecopy ( $finimg, $crimg, 0, 0, 0, 0, $cwidth, $cheight);
	}
	else if(imagesx($crimg) > $cwidth)
	{
		$off = round((imagesx($crimg)-$cwidth)/2);
		imagecopy ( $finimg, $crimg, 0, 0, $off, 0, $cwidth, $cheight);
	}
	else
	{
		$off = round((imagesy($crimg)-$cheight)/2);
		imagecopy ( $finimg, $crimg, 0, 0, 0, $off, $cwidth, $cheight);
	}
	
	imagejpeg($finimg, $savefilename, $quality);
	imagedestroy($crimg);
}
?>