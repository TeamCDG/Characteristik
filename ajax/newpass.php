<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	id			| post		| int		| id of user
//------------------------------------------------------------------
//	password	| post		| string	| new password
//------------------------------------------------------------------
//  oldpass	    | post		| string	| old password
//------------------------------------------------------------------


$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	if(isset($_POST['id']) && $_SESSION['userid'] == $_POST['id'] && isset($_POST['password']) && isset($_POST['oldpass']))
	{
		$sql = "SELECT * FROM `user` WHERE `id` = '".mysql_real_escape_string($_POST['id'])."' AND `password`='".mysql_real_escape_string($_POST['oldpass'])."';";
		$res = mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #010 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
		if($user = mysql_fetch_object($res))
		{
			$sql = "UPDATE `user` SET `password`='".mysql_real_escape_string($_POST['password'])."' WHERE `id` = '".mysql_real_escape_string($_POST['id'])."';";
			mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #011 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
			die("{\"status\":200, \"message\":\"Passwort geändert!\", \"username\":\"".$user->username."\", \"password\":\"".$_POST['password']."\"}");
		}
		die("{\"status\":406, \"message\":\"Das alte Passwort ist nicht korrekt!\"}");
	}
	else if(isset($_POST['id']) && $_SESSION['permissions']['admin_set_new_user_pass'])
	{
		$sql = "SELECT * FROM `user` WHERE `id` = '".mysql_real_escape_string($_POST['id'])."';";
		$res = mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #010 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
		$user = mysql_fetch_object($res);
		
		$sql = "UPDATE `user` SET `password`='".mysql_real_escape_string($_POST['password'])."' WHERE `id` = '".mysql_real_escape_string($_POST['id'])."';";
		mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #011 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
		
		die("{\"status\":200, \"message\":\"Passwort geändert!\", \"username\":\"".$user->username."\", \"password\":\"".$_POST['password']."\"}");
	}
	else
	{
		die("{\"status\":406, \"message\":\"no type specified\"}");
	}
	
}

?>