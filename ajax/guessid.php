<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	name		| post		| string	| full name (prename(s) & lastname split with ' ')
//------------------------------------------------------------------
//	t			| post		| bool		| is teacher? (currently ignored)
//				|			|			| 0: false
//				|			|			| 1: true
//------------------------------------------------------------------

$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	if(isset($_POST['name']))
	{
		$p = getUserId($_POST['name']);
		if($p[0] != "-1" && $p[0] != -1)		
			die("{\"status\":200, \"message\":\"erfolgreich gefunden!\", \"id\":".$p[0].", \"t\":".$p[1]." }");
		else
			die("{\"status\":404, \"message\":\"konnte\\\"".$_POST['name']."\\\" nicht finden!\"}");
	}
	else
	{
		die("{\"status\":406, \"message\":\"no name specified\"}");
	}
	
}


function getUserId($n)
{
	$p = explode(" ", $n);
	$prename = mysql_real_escape_string($p[0]);
	$name = mysql_real_escape_string($p[count($p)-1]);
	
	if(count($p) > 1) //check for prename + name
	{
		$sql = "SELECT * FROM `user` WHERE `name` LIKE '%".$name."%' AND `prename` LIKE '%".$prename."%';"; //user have higher priority than teacher
		$res = mysql_query($sql) or die ("{\"status\":406, \"message\":\"ERROR #100: Query failed: $sql @ajax/guessid - ".mysql_error()."\"}");
		if($obj = mysql_fetch_object($res))
		{
			return array($obj->id, 0);
		}
		else
		{
			$sql = "SELECT * FROM `teacher` WHERE `name` LIKE '%".$name."%' AND `prename` LIKE '%".$prename."%';"; // now search teacher
			$res = mysql_query($sql) or die ("{\"status\":406, \"message\":\"ERROR #101: Query failed: $sql @ajax/guessid - ".mysql_error()."\"}");
			if($obj = mysql_fetch_object($res))
			{
				return array($obj->id, 1);
			}
		}
	}
		
	//since there are no results for prename + name let's search just for the name
	$sql = "SELECT * FROM `user` WHERE `name` LIKE '%".$name."%';"; //user have higher priority than teacher
	$res = mysql_query($sql) or die ("{\"status\":406, \"message\":\"ERROR #102: Query failed: $sql @ajax/guessid - ".mysql_error()."\"}");
	if($obj = mysql_fetch_object($res))
	{
		return array($obj->id, 0);
	}
	else
	{
		$sql = "SELECT * FROM `teacher` WHERE `name` LIKE '%".$name."%';"; // now search teacher
		$res = mysql_query($sql) or die ("{\"status\":406, \"message\":\"ERROR #103: Query failed: $sql @ajax/guessid - ".mysql_error()."\"}");
		if($obj = mysql_fetch_object($res))
		{
			return array($obj->id, 1);
		}
	}
	
	//okay, last chance... search prename only
	$sql = "SELECT * FROM `user` WHERE `prename` LIKE '%".$prename."%';"; //user have higher priority than teacher
	$res = mysql_query($sql) or die ("{\"status\":406, \"message\":\"ERROR #104: Query failed: $sql @ajax/guessid - ".mysql_error()."\"}");
	if($obj = mysql_fetch_object($res))
	{
		return array($obj->id, 0);
	}
	else
	{
		$sql = "SELECT * FROM `teacher` WHERE `prename` LIKE '%".$prename."%';"; // now search teacher
		$res = mysql_query($sql) or die ("{\"status\":406, \"message\":\"ERROR #105: Query failed: $sql @ajax/guessid - ".mysql_error()."\"}");
		if($obj = mysql_fetch_object($res))
		{
			return array($obj->id, 1);
		}
	}
	
	return array(-1, -1);
}

?>