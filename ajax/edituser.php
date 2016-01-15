<?php 

//	param			| method	| type		| desc
//------------------------------------------------------------------
//	prename			| post		| string	| pername of new user
//------------------------------------------------------------------
//	lastname		| post		| string	| lastname of new user
//------------------------------------------------------------------
//	type			| post		| int		| type of new user
//------------------------------------------------------------------
//	group			| post		| int		| group of new user
//------------------------------------------------------------------
//	com				| post		| int		| id of comitee
//------------------------------------------------------------------
//	id				| post		| int		| id of user to edit
//------------------------------------------------------------------
//	initialTeacher	| post		| bool		| true if user was initial Teacher
//------------------------------------------------------------------
$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	//todo: permission checkdate
	//todo: check if user exists
	
	if(intval($_POST['type']) == 1)
	{
		if(intval($_POST['initialTeacher']) == 1)
		{
			//teacher stays teacher, so just update
			$sql = "UPDATE `teacher` SET ".
			"`name`='".mysql_real_escape_string($_POST['lastname'])."',".
			"`prename`='".mysql_real_escape_string($_POST['prename'])."' ".
			" WHERE `id`='".mysql_real_escape_string($_POST['id'])."'";
			mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #012 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
		}
		else
		{
			//user turned teacher, insert teacher, delete user
			$sql = "INSERT INTO `teacher`(`name`, `prename`, `sub0`, `sub1`, `sub2`, `sub3`, `visible`) VALUES ('".mysql_real_escape_string($_POST['lastname']).
			"','".mysql_real_escape_string($_POST['prename'])."','','','','','1')";
			mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #012 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
			
			$sql = "DELETE FROM `user` WHERE `id`='".mysql_real_escape_string($_POST['id'])."';";
			mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #011 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
		}
	}
	else
	{
		if(intval($_POST['initialTeacher']) == 1)
		{
			//teacher turned user, insert user, delete teacher
			$sql = "INSERT INTO `user`(`name`, `prename`, `username`, `password`, `admin`, `style`, `stillthere`, `group`) VALUES ('".mysql_real_escape_string($_POST['lastname']).
					"','".mysql_real_escape_string($_POST['prename'])."','".mysql_real_escape_string($username)."','".mysql_real_escape_string(md5(strval(mt_rand())."BAUM"))."','0','0','1','".mysql_real_escape_string($_POST['group'])."')";
			mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #011 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
			
			$sql = "DELETE FROM `teacher` WHERE `id`='".mysql_real_escape_string($_POST['id'])."';";
			mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #011 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
		}
		else
		{
			//user stays user, so just update
			$sql = "UPDATE `user` SET `name`='".mysql_real_escape_string($_POST['lastname'])."',".
					"`prename`='".mysql_real_escape_string($_POST['prename'])."',".
					"`group`='".mysql_real_escape_string($_POST['group'])."',".
					"`com`='".mysql_real_escape_string($_POST['com'])."'".
					" WHERE `id`='".mysql_real_escape_string($_POST['id'])."'";
					
			mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #011 Query failed: $sql @ajax/adduser.php - ".mysql_error()."\"}");
		}

		
	}
	die("{\"status\":200, \"message\":\"Nutzer bearbeitet!\"}");
}
else
{
	die("{\"status\":406, \"message\":\"blah\"}");
}
	


?>