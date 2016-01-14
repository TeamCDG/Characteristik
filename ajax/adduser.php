<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	prename		| post		| string	| pername of new user
//------------------------------------------------------------------
//	lastname	| post		| string	| lastname of new user
//------------------------------------------------------------------
//	type		| post		| int		| type of new user
//------------------------------------------------------------------
//	group		| post		| int		| group of new user
//------------------------------------------------------------------

$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	if(isset($_POST['type']))
	{
		if($_POST['type'] == 0)
		{
			$username = substr($_POST['lastname'], 0, 3).substr($_POST['prename'], 0, 3);
			$sql = "SELECT * FROM `user` WHERE `username` = '".mysql_real_escape_string($username)."';";
			$res = mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #010 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
			
			if(mysql_num_rows($res) != 0)
			{
				$username .= mysql_num_rows($res);
			}
			
			$sql = "INSERT INTO `user`(`name`, `prename`, `username`, `password`, `admin`, `style`, `stillthere`, `group`) VALUES ('".mysql_real_escape_string($_POST['lastname']).
			"','".mysql_real_escape_string($_POST['prename'])."','".mysql_real_escape_string($username)."','".mysql_real_escape_string(md5(strval(mt_rand())."BAUM"))."','0','0','1','".mysql_real_escape_string($_POST['group'])."')";
			mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #011 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
		}
		else
		{
			$sql = "INSERT INTO `teacher`(`name`, `prename`, `sub0`, `sub1`, `sub2`, `sub3`, `visible`) VALUES ('".mysql_real_escape_string($_POST['lastname']).
			"','".mysql_real_escape_string($_POST['prename'])."','','','','','1')";
			mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #012 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
		}
		die("{\"status\":200, \"message\":\"Nutzer hinzugefügt!\"}");
	}
	else
	{
		die("{\"status\":406, \"message\":\"no type specified\"}");
	}
	
}

?>