<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	type		| get		| int		| used for selecting news type:
//				|			|			| 0: user
//				|			|			| 1: teacher
//				|			|			| 2: cit
//				|			|			| 3: gossip
//------------------------------------------------------------------
//	min			| get		| int		| starting point of limit
//------------------------------------------------------------------
//	count		| get		| int		| data count
//------------------------------------------------------------------
$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	news(intval($_GET['type']), $_GET['min'], $_GET['count'], $rootfolder);
}

function news($type, $min, $count, $rootfolder)
{

	$table = "";

	switch ($type) {
		case 0:
			$table = "uchar";
			break;
		case 1:
			$table = "tchar";	
			break;
		case 2:
			$table = "cit";
			break;
		case 3:
			$table = "gossip";
			break;
		default:
			die("418");
			break;
	}
	
	
	
	$sql = "SELECT * FROM `".mysql_real_escape_string($table)."` WHERE `visible` = 1 ".($min == -1?"":"AND `id` < '".mysql_real_escape_string($min)."'")." ORDER BY `id` DESC LIMIT ".mysql_real_escape_string($count);
	$res = mysql_query($sql) or die ("ERROR #027: Query failed: $sql @functions.php - ".mysql_error());


	if($type == 0 || $type == 1)
	{	
		while($row = mysql_fetch_object($res))
		{
			if(($row->holder == $_SESSION['userid'] && $_SESSION['permissions']['char_read_own']) || $_SESSION['permissions']['char_read_other'])
			{
				echo "<li id=\"news_".$type."_".$row->id."\">\"".$row->content."\"".
					 (((($row->holder == $_SESSION['userid'] && $_SESSION['permissions']['char_see_from_own']) || $_SESSION['permissions']['char_see_from_other']) && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa'])?" von <a href=\"".$rootfolder."c/showuser/?uid=".$row->from."\">".getName($row->from, 0)."</a>":"").
					 " an <a href=\"".$rootfolder."c/showuser/?uid=".$row->holder."&t=".((bool)$type)."\">".getName($row->holder, $type)."</a></li>";
			}
			else
			{
				echo "<li id=\"news_".$type."_".$row->id."\">403 Access denied: please report to android hell for a teapot...".
					 (((($row->holder == $_SESSION['userid'] && $_SESSION['permissions']['char_see_from_own']) || $_SESSION['permissions']['char_see_from_other']) && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa'])?" von <a href=\"".$rootfolder."c/showuser/?uid=".$row->from."\">".getName($row->from, 0)."</a>":"").
					 " an <a href=\"".$rootfolder."c/showuser/?uid=".$row->holder."&t=".((bool)$type)."\">".getName($row->holder, $type)."</a></li>";
			}
		} 
	}
	else if($type == 2)
	{
		while($row = mysql_fetch_object($res))
		{
			if($_SESSION['permissions']['cit_view'])
			{
				echo "<li id=\"news_".$type."_".$row->id."\">\"".$row->content."\" - <a href=\"".$rootfolder."c/showuser/?uid=".$row->holder."&t=".((bool)$row->teacher)."\">".getName($row->holder, $row->teacher)."</a>".
					 ((($_SESSION['permissions']['cit_view_from']) && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa'])?" (<a href=\"".$rootfolder."c/showuser/?uid=".$row->poster."\">".getName($row->poster, 0)."</a>)":"").
					 "</li>";
			}
			else
			{
				echo "<li id=\"news_".$type."_".$row->id."\">403 Access denied: please report to android hell for a teapot... - <a href=\"".$rootfolder."c/showuser/?uid=".$row->holder."&t=".((bool)$row->teacher)."\">".getName($row->holder, $row->teacher)."</a>".
					 ((($_SESSION['permissions']['cit_view_from']) && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa'])?" (<a href=\"".$rootfolder."c/showuser/?uid=".$row->poster."\">".getName($row->poster, 0)."</a>)":"").
					 "</li>";
			}
		}
	}
	else if($type == 3)
	{
		while($row = mysql_fetch_object($res))
		{
			if($_SESSION['permissions']['gossip_view'])
			{
				echo "<li id=\"news_".$type."_".$row->id."\">".$row->content.
					 ((($_SESSION['permissions']['gossip_view_from']) && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa'])?" (<a href=\"".$rootfolder."c/showuser/?uid=".$row->poster."\">".getName($row->poster, 0)."</a>)":"")
					 ."</li>";
			}
			else
			{
				echo "<li id=\"news_".$type."_".$row->id."\">403 Access denied: please report to android hell for a teapot...".
					 ((($_SESSION['permissions']['gossip_view_from']) && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa'])?" (<a href=\"".$rootfolder."c/showuser/?uid=".$row->poster."\">".getName($row->poster, 0)."</a>)":"")
					 ."</li>";
			}
		}
	}
	else
	{
		echo "418";
	}
}
?>