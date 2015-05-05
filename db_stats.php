<?php
session_start();
include("connect.php");
//Creates pie diagram based on the data given via get parameters
//Copyright (C) 2013  Joshua Theis <http://cdg.bplaced.net>
//
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation, either version 3 of the License, or
//(at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program.  If not, see <http://www.gnu.org/licenses/>.
//Special thanks to: Johannes, Mazzl, Ricarda & Celine

//param "width": the width of the final image (default: 512)
//param "height": the height of the final image (default: 256)
//param "pw": the width of the pie, when used as 2D pie diagram, it is the height as well (default: 256)
//param "ph": the height of the pie (default: 128)
//param "px": the x of the pie center (default: 129)
//param "py": the y of the pie center (default: 128)
//param "poff": the offset of the pie 3D base (default: 40)
//param "font": the path to the ttf font file (default: "fonts/font.ttf")
//param "fsize": the size of the font (default: 10)
//param "3D": true if the pie diagram should be 3D (default: true)
//param "al": true if the lines should be anti-alised (default: false)
//param "hl": the headline over the legend (default: "Legend")
//param "as": true if the size of the image should be autosized with it's content (default: true)
//param "pollid": id of the poll

function drawLine($degree, $img, $width, $height, $centerX, $centerY, $color)
{
	imageline($img, $centerX, $centerY, round($centerX+cos(deg2rad($degree)) * $width,0), round($centerY+sin(deg2rad($degree)) * $height,0), $color); 
}

/*function entenc($text)
{
	$text = (string) $text;
	$text_out = "";
	
	for($i = 0, $n = strlen($text); $i < $n; $i++) {
		$text_out .= "&#".ord($text[$i]).";";
		}
		
	return $text_out;
}*/


function entenc_WTF($text){
    $text = mb_convert_encoding($text, "ISO-8859-1", "UTF-8");
    $text = preg_replace('~^(&([a-zA-Z0-9]);)~',htmlentities('${1}'),$text);
    return($text); 
}

function entenc($text)
{
	$text = $text;
    $res = '';
    for ($i = 0; $i < strlen($text); $i++)
    {
        $cc = ord($text{$i});
        if ($cc >= 128 || $cc == 38)
            $res .= "&#$cc;";
        else
            $res .= chr($cc);
    }
    return $res;
}

header ('Content-Type: image/png');

if(isset($_GET["width"]))
{
	$width = $_GET["width"];
}
else
{
	$width = 1024;
}

if(isset($_GET["height"]))
{
	$height = $_GET["height"];
}
else
{
	$height = 256;
}

if(isset($_GET["pw"]))
{
	$pie_width = $_GET["pw"];
}
else
{
	$pie_width = 256;
}

if(isset($_GET["ph"]))
{
	$pie_height = $_GET["ph"];
}
else
{
	$pie_height = 128;
}

if(isset($_GET["px"]))
{
	$pie_x = $_GET["px"];
}
else
{
	$pie_x = 129;
}

if(isset($_GET["py"]))
{
	$pie_y = $_GET["py"];
}
else
{
	$pie_y = 128;
}

if(isset($_GET["poff"]))
{
	$pie_off = $_GET["poff"];
}
else
{
	$pie_off = 40;
}

if(isset($_GET["as"]))
{
	$as = $_GET["as"] === true;
}
else
{
	$as = true;
}

if(isset($_GET["font"]))
{
	$ttf = $_GET["font"];
}
else
{
	$ttf = "fonts/font.ttf";
}

if(isset($_GET["fsize"]))
{
	$ttfsize = $_GET["fsize"];
}
else
{
	$ttfsize = 10;
}


$hl = utf8_encode(entenc("Datenbank Statistiken:"));



$pie3D = ($_GET["3D"] || !isset($_GET["3D"])) && $_GET["3D"] != "false";
$al = true;
$votes = array(); //0 => absolute vote count; 1 => text
$vcount = 0; //absolute count

$sql = "SELECT COUNT(*) as c FROM changes ";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$o = mysql_fetch_object($res);
array_push($votes, [intval($o->c),"Veränderungen"]);
$vcount += intval($o->c);

$sql = "SELECT COUNT(*) as c FROM pollanswers ";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$o = mysql_fetch_object($res);
array_push($votes, [intval($o->c),"Umfrage Antwortmöglichkeiten"]);
$vcount += intval($o->c);

$sql = "SELECT COUNT(*) as c FROM polls ";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$o = mysql_fetch_object($res);
if($o->c > 10) array_push($votes, [intval($o->c),"Umfragen"]);
if($o->c > 10) $vcount += intval($o->c);

$sql = "SELECT COUNT(*) as c FROM pollvotes ";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$o = mysql_fetch_object($res);
array_push($votes, [intval($o->c),"Umfrage Antworten"]);
$vcount += intval($o->c);

$sql = "SELECT COUNT(*) as c FROM request ";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$o = mysql_fetch_object($res);
array_push($votes, [intval($o->c),"Löschanfragen"]);
$vcount += intval($o->c);

$sql = "SELECT COUNT(*) as c FROM snakescore ";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$o = mysql_fetch_object($res);
array_push($votes, [intval($o->c),"Snake Scores"]);
$vcount += intval($o->c);

$sql = "SELECT COUNT(*) as c FROM tchar ";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$o = mysql_fetch_object($res);
array_push($votes, [intval($o->c),"Lehrer Charakteristik"]);
$vcount += intval($o->c);

$sql = "SELECT COUNT(*) as c FROM tcit ";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$o = mysql_fetch_object($res);
if($o->c > 10) array_push($votes, [intval($o->c),"Lehrer Zitate"]);
if($o->c > 10) $vcount += intval($o->c);

$sql = "SELECT COUNT(*) as c FROM teacher ";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$o = mysql_fetch_object($res);
array_push($votes, [intval($o->c),"Lehrer"]);
$vcount += intval($o->c);

$sql = "SELECT COUNT(*) as c FROM uchar ";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$o = mysql_fetch_object($res);
array_push($votes, [intval($o->c),"Schüler Charakteristik"]);
$vcount += intval($o->c);

$sql = "SELECT COUNT(*) as c FROM user ";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$o = mysql_fetch_object($res);
array_push($votes, [intval($o->c),"Schüler"]);
$vcount += intval($o->c);

$hl = utf8_encode(entenc("Datenbank Statistiken (".$vcount." Datensätze):"));

//var_dump($votes);
//echo "\nvcount: ".$vcount;
if($as)
{
	for($c = 0; $c < count($votes); $c++)
	{
		$txt = ": ".$votes[$c][1]." ".$votes[$c][0]."/".$vcount." (".round((100.0 / floatval($vcount)) * $votes[$c][0], 2)."%)";
		$edge = imagettfbbox($ttfsize, 0, $ttf, utf8_encode(entenc($txt)));
		if($pie_x+($pie_width/2)+44+abs($edge[4])+$ttfsize > $width)
		{
			$width = $pie_x+($pie_width/2)+44+abs($edge[4])+$ttfsize;
		}
		if(40+20*($c+1)+abs($edge[3])+$ttfsize >$height)
		{
			$height = 40+20*($c+1)+abs($edge[3])+$ttfsize;
		}
	}
	$hle = imagettfbbox($ttfsize+8, 0, $ttf, $hl);
	if($pie_x+($pie_width/2)+24+abs($hle[4])+$ttfsize > $width)
	{
		$width = 24+abs($hle[4])+$ttfsize;
	}
	if($pie_x+$pie_height+$pie_off>$height && $pie3D)
	{
		$height = $pie_x+$pie_height+$pie_off;
	}
}


$img = @imagecreatetruecolor($width, $height) or die('Cannot Initialize new GD image stream');

$linecolor = ImageColorAllocate($img, 0, 0, 0);
$colors = array(0=>ImageColorAllocate($img, 222, 222, 222), ImageColorAllocate($img, 222, 0, 0), ImageColorAllocate($img, 0, 222, 0), 
				   ImageColorAllocate($img, 0, 0, 222), ImageColorAllocate($img, 222, 222, 0), ImageColorAllocate($img, 222, 0, 222),
				   ImageColorAllocate($img, 52, 52, 52), ImageColorAllocate($img, 110, 0, 0), ImageColorAllocate($img, 0, 110, 0), 
				   ImageColorAllocate($img, 0, 0, 110), ImageColorAllocate($img, 110, 110, 110), ImageColorAllocate($img, 110, 0, 110));
				   
$colors_dark = array(0=>ImageColorAllocate($img, 170, 170, 170), ImageColorAllocate($img, 170, 0, 0), ImageColorAllocate($img, 0, 170, 0), 
						ImageColorAllocate($img, 0, 0, 170), ImageColorAllocate($img, 170, 170, 0), ImageColorAllocate($img, 170, 0, 170),
						ImageColorAllocate($img, 0, 0, 0), ImageColorAllocate($img, 58, 0, 0), ImageColorAllocate($img, 0, 58, 0), 
						ImageColorAllocate($img, 0, 0, 58), ImageColorAllocate($img, 58, 58, 58), ImageColorAllocate($img, 58, 0, 58));

imagefilledrectangle($img,0,0,$width-1,$height-1,ImageColorAllocate($img, 255, 255, 255));
imageantialias($img,$al);

if(!isset($_SESSION['userid']))
{
	imagettftext($img, $ttfsize+8, 0, 24, 30, $linecolor, $ttf, "NIZE TRY FGT GET REKT");
	imagepng($img);
	imagedestroy($img);
	die();
}

imagettftext($img, $ttfsize+8, 0, 24, 30, $linecolor, $ttf, $hl);

if($pie3D && $vcount > 0)
{	

	for($o = 1; $o <= $pie_off; $o++)
	{	
		$last_degree = -90;
		for($e = 0; $e < count($votes); $e++)
		{
			$degree = round($last_degree + (360.0 * (round((100.0 / floatval($vcount)) * $votes[$e][0], 2)/100.0)),0);
			if(!($degree == $last_degree || $last_degree > 179) && $degree > 0)
			{
				imagearc($img, $pie_x, $pie_y+$o, $pie_width, $pie_height, $last_degree, $degree, $colors_dark[$e]);
				
			}
			$last_degree = round($degree,0);
		}
	}

	
	$last_degree = -90;	
	for($i = 0; $i < count($votes); $i++)
	{
		$txt = ": ".$votes[$i][1]." ".$votes[$i][0]."/".$vcount." (".round((100.0 / floatval($vcount)) * $votes[$i][0], 2)."%)";
		$degree = round($last_degree + (360.0 * (round((100.0 / floatval($vcount)) * $votes[$i][0], 2)/100.0)),0);		
		if($degree == $last_degree)
		{
			continue;
		}
		$x = $pie_x+($pie_width/2)+24;
		$y = 40+20*($i);
		imagefilledrectangle($img, $x, $y, $x+20, $y+20, $colors[$i]);
		imagerectangle($img, $x, $y, $x+20, $y+20,$linecolor);
		imagettftext($img, $ttfsize, 0, $pie_x+($pie_width/2)+44, 40+20*($i+1), $linecolor, $ttf, utf8_encode(entenc($txt)));
		imagefilledarc($img, $pie_x, $pie_y, $pie_width, $pie_height, $last_degree, $degree, $colors[$i], IMG_ARC_PIE);
		imagearc($img,$pie_x, $pie_y, $pie_width, $pie_height, $last_degree, $degree, $linecolor);	
				
		$last_degree = round($degree,0);
	}
	
	$last_degree = -90;	
	for($i = 0; $i < count($votes); $i++)
	{
		
		$degree = round($last_degree + (360.0 * (round((100.0 / floatval($vcount)) * $votes[$i][0], 2)/100.0)),0);	
		drawLine($degree, $img, ($pie_width/2)-1, ($pie_height/2)-1, $pie_x, $pie_y, $linecolor);
		if($degree > 0 && $degree < 180)
		{
			$x = round($pie_x+cos(deg2rad($degree)) * (($pie_width-2)/2),0);
			$y = round($pie_y+sin(deg2rad($degree)) * (($pie_height-2)/2),0);
			imageline($img, $x, $y, $x, $y+$pie_off, $linecolor);
		}
		
				
		$last_degree = round($degree,0);
	}
	
	
	imagearc($img,$pie_x, $pie_y+$pie_off, $pie_width, $pie_height, 0, 180, $linecolor);
	imageline($img, $pie_x-($pie_width/2), $pie_y, $pie_x-($pie_width/2), $pie_y+$pie_off, $linecolor);
	imageline($img, $pie_x+($pie_width/2), $pie_y, $pie_x+($pie_width/2), $pie_y+$pie_off, $linecolor);		
}
else if($vcount > 0)
{
	$last_degree = -90;	
	for($i = 0; $i < count($votes); $i++)
	{
		$txt = ": ".$votes[$i][1]." ".$votes[$i][0]."/".$vcount." (".round((100.0 / floatval($vcount)) * $votes[$i][0], 2)."%)";
		$degree = round($last_degree + (360.0 * (round((100.0 / floatval($vcount)) * $votes[$i][0], 2)/100.0)),0);	
		if($degree == $last_degree)
		{
			continue;
		}
		$x = $pie_x+($pie_width/2)+24;
		$y = 40+20*($i);
		imagefilledrectangle($img, $x, $y, $x+20, $y+20,$colors[$i]);
		imagerectangle($img, $x, $y, $x+20, $y+20,$linecolor);
		imagettftext($img, $ttfsize, 0, $pie_x+($pie_width/2)+44, 40+20*($i+1), $linecolor, $ttf, utf8_encode(entenc($txt)));
		imagefilledarc($img, $pie_x, $pie_y, $pie_width, $pie_width, $last_degree, $degree, $colors[$i], IMG_ARC_PIE);
		imagearc($img,$pie_x, $pie_y, $pie_width, $pie_width, $last_degree, $degree, $linecolor);	
				
		$last_degree = round($degree,0);
	}
	
	$last_degree = -90;	
	for($i = 0; $i < count($votes); $i++)
	{
		
		$degree = round($last_degree + (360.0 * (round((100.0 / floatval($vcount)) * $votes[$i][0], 2)/100.0)),0);		
		drawLine($degree, $img, ($pie_width/2), ($pie_width/2), $pie_x, $pie_y, $linecolor);
				
		$last_degree = round($degree,0);
	}
}

imagepng($img);
imagedestroy($img);
?>