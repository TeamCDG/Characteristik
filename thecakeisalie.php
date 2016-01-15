<?php
session_start();
include("functions.php");
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
//param "bgcol": hexadezimal color of background
//param "fontcol": hexadezimal color of font

function drawLine($degree, $img, $width, $height, $centerX, $centerY, $color)
{
	imageline($img, $centerX, $centerY, round($centerX+cos(deg2rad($degree)) * $width,0), round($centerY+sin(deg2rad($degree)) * $height,0), $color); 
}
define('EMPTY_STRING', '');

function foxy_utf8_to_nce( 
  $utf = EMPTY_STRING 
) { 
  if($utf == EMPTY_STRING) return($utf); 

  $max_count = 5; // flag-bits in $max_mark ( 1111 1000 == 5 times 1) 
  $max_mark = 248; // marker for a (theoretical ;-)) 5-byte-char and mask for a 4-byte-char; 

  $html = EMPTY_STRING; 
  for($str_pos = 0; $str_pos < strlen($utf); $str_pos++) { 
    $old_chr = $utf{$str_pos}; 
    $old_val = ord( $utf{$str_pos} ); 
    $new_val = 0; 

    $utf8_marker = 0; 

    // skip non-utf-8-chars 
    if( $old_val > 127 ) { 
      $mark = $max_mark; 
      for($byte_ctr = $max_count; $byte_ctr > 2; $byte_ctr--) { 
        // actual byte is utf-8-marker? 
        if( ( $old_val & $mark  ) == ( ($mark << 1) & 255 ) ) { 
          $utf8_marker = $byte_ctr - 1; 
          break; 
        } 
        $mark = ($mark << 1) & 255; 
      } 
    } 

    // marker found: collect following bytes 
    if($utf8_marker > 1 and isset( $utf{$str_pos + 1} ) ) { 
      $str_off = 0; 
      $new_val = $old_val & (127 >> $utf8_marker); 
      for($byte_ctr = $utf8_marker; $byte_ctr > 1; $byte_ctr--) { 

        // check if following chars are UTF8 additional data blocks 
        // UTF8 and ord() > 127 
        if( (ord($utf{$str_pos + 1}) & 192) == 128 ) { 
          $new_val = $new_val << 6; 
          $str_off++; 
          // no need for Addition, bitwise OR is sufficient 
          // 63: more UTF8-bytes; 0011 1111 
          $new_val = $new_val | ( ord( $utf{$str_pos + $str_off} ) & 63 ); 
        } 
        // no UTF8, but ord() > 127 
        // nevertheless convert first char to NCE 
        else { 
          $new_val = $old_val; 
        } 
      } 
      // build NCE-Code 
      $html .= '&#'.$new_val.';'; 
      // Skip additional UTF-8-Bytes 
      $str_pos = $str_pos + $str_off; 
    } 
    else { 
      $html .= chr($old_val); 
      $new_val = $old_val; 
    } 
  } 
  return($html); 
}

header ('Content-Type: image/png');

if(isset($_GET["width"]))
{
	$width = $_GET["width"];
}
else
{
	$width = 896;
}

if(isset($_GET["height"]))
{
	$height = $_GET["height"];
}
else
{
	$height = 320;
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
	$ttf = "fonts/consolas.ttf";
}

if(isset($_GET["fsize"]))
{
	$ttfsize = $_GET["fsize"];
}
else
{
	$ttfsize = 10;
}

$poll = getPoll($_GET['pid']);
$answers = getAnswers($_GET['pid']);

$hl = utf8_encode(foxy_utf8_to_nce($poll['title']));



$pie3D = true;
if(isset($_GET["3D"])) $pie3D = ($_GET["3D"] || !isset($_GET["3D"])) && $_GET["3D"] != "false";
$al = true;
if(isset($_GET["al"])) $al = $_GET["al"] || $_GET["al"]=="true";
$votes = array(); //0 => absolute vote count; 1 => text
$vcount = 0;



$sql = "SELECT COUNT(*) as c FROM pollvotes WHERE `pollid`='".mysql_real_escape_string($_GET['pid'])."'";
$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
$vcount = mysql_fetch_object($res)->c;

$sql = "SELECT COUNT(*) as c, voteid FROM pollvotes WHERE `pollid`='".mysql_real_escape_string($_GET['pid'])."' GROUP BY voteid ORDER BY c DESC";
$res = mysql_query($sql) or die ("ERROR #032: Query failed: $sql @thecakeisalie.php - ".mysql_error());

$c = 0;
$allco = 0;
while($row = mysql_fetch_object($res))
{
	$tmp = array();
	array_push($tmp, $row->c);
	
	$allco += $row->c;
	
	array_push($tmp, $answers[$row->voteid]);
	
	array_push($votes, $tmp);
	$c++;
	
	if($c >= 10)
	{
		$stuff = array();
		array_push($stuff, $vcount - $allco);
		array_push($stuff, "Sonstige");
		array_push($votes, $stuff);
		break;
	}
}


if($as)
{
	for($c = 0; $c < count($votes); $c++)
	{
		$txt = ": ".$votes[$c][1]." ".$votes[$c][0]."/".$vcount." (".round((100.0 / floatval($vcount)) * $votes[$c][0], 2)."%)";
		$edge = imagettfbbox($ttfsize, 0, $ttf, utf8_encode(foxy_utf8_to_nce($txt)));
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

$linecolor = null; 
if(isset($_GET['fontcol']))
{
	$linecolor = ImageColorAllocate($img, hexdec(substr($_GET['fontcol'], 0, 2)), hexdec(substr($_GET['fontcol'], 2, 2)), hexdec(substr($_GET['fontcol'], 4, 2)));
}
else
{
	$linecolor = ImageColorAllocate($img, 0, 0, 0);
}

$bgcol = null;
if(isset($_GET['bgcol']))
{
	$bgcol = ImageColorAllocate($img, hexdec(substr($_GET['bgcol'], 0, 2)), hexdec(substr($_GET['bgcol'], 2, 2)), hexdec(substr($_GET['bgcol'], 4, 2)));
}
else
{
	$bgcol = ImageColorAllocate($img, 255, 255, 255);
}

$colors = array(0=>ImageColorAllocate($img, 222, 222, 222), ImageColorAllocate($img, 222, 0, 0), ImageColorAllocate($img, 0, 222, 0), 
				   ImageColorAllocate($img, 0, 0, 222), ImageColorAllocate($img, 222, 222, 0), ImageColorAllocate($img, 222, 0, 222),
				   ImageColorAllocate($img, 52, 52, 52), ImageColorAllocate($img, 110, 0, 0), ImageColorAllocate($img, 0, 110, 0), 
				   ImageColorAllocate($img, 0, 0, 110), ImageColorAllocate($img, 110, 110, 110), ImageColorAllocate($img, 110, 0, 110));
				   
$colors_dark = array(0=>ImageColorAllocate($img, 170, 170, 170), ImageColorAllocate($img, 170, 0, 0), ImageColorAllocate($img, 0, 170, 0), 
						ImageColorAllocate($img, 0, 0, 170), ImageColorAllocate($img, 170, 170, 0), ImageColorAllocate($img, 170, 0, 170),
						ImageColorAllocate($img, 0, 0, 0), ImageColorAllocate($img, 58, 0, 0), ImageColorAllocate($img, 0, 58, 0), 
						ImageColorAllocate($img, 0, 0, 58), ImageColorAllocate($img, 58, 58, 58), ImageColorAllocate($img, 58, 0, 58));

imagefilledrectangle($img,0,0,$width-1,$height-1,$bgcol);
imageantialias($img,$al);

if(!isset($_SESSION['userid']))
{
	imagettftext($img, $ttfsize+8, 0, 24, 30, $linecolor, $ttf, "NIZE TRY FGT GET REKT");
	imagepng($img);
	imagedestroy($img);
	die();
}
if($pie3D)
	imagettftext($img, $ttfsize+8, 0, 24, 30, $linecolor, $ttf, $hl);
else
	imagettftext($img, $ttfsize+8, 0, $pie_x+($pie_width/2)+24, 30, $linecolor, $ttf, $hl);

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
		imagettftext($img, $ttfsize, 0, $pie_x+($pie_width/2)+44, 40+20*($i+1), $linecolor, $ttf, foxy_utf8_to_nce($txt));
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
		imagettftext($img, $ttfsize, 0, $pie_x+($pie_width/2)+44, 40+20*($i+1), $linecolor, $ttf, foxy_utf8_to_nce($txt));
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