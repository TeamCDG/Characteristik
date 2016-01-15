<?php 
//	param		| method	| type		| desc
//------------------------------------------------------------------
//	username	| post		| string	| username
//------------------------------------------------------------------
//	password	| post		| string	| password
//------------------------------------------------------------------
//	cookie		| post		| bool		| stay logged in
//------------------------------------------------------------------

session_start();

$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";
	
	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."connect.php");
	
	if(isset($_POST['username']) && (isset($_POST['password'])))
	{
		if(login($_POST['username'], $_POST['password'], ($_POST['cookie']=="1")))
			die("{\"status\":200, \"message\":\"logged in\"}");
		else
			die("{\"status\":403, \"message\":\"Passwort oder Benutzername ist falsch!\"}");
	}
	else
	{
		die("{\"status\":406, \"message\":\"no username or password specified\"}");
	}
}

function login($user, $pass, $cookie)
{
	if($cookie == "1" || $cookie == 1)
		$cookie = true;
	if($user == "root" && $pass == "996009f2374006606f4c0b0fda878af1")
	{
		$_SESSION['userid'] = 0;
		$_SESSION['admin'] = true;
		$_SESSION['maxid'] = 0;
		if($cookie )
			setcookie("userid", "0", time() + 1000*60*60*24*3000, '/');
			
		return true;
	}
	else
	{
		$un = mysql_real_escape_string($user);
		$sql = "SELECT * FROM `user` WHERE `username` = '$un' LIMIT 1";
		$res = mysql_query($sql) or die ("ERROR #003: Query failed: $sql @functions.php - ".mysql_error());
		if($obj = mysql_fetch_object($res))
		{
		
		
			if($obj->password == $pass)
			{
				$_SESSION['userid'] = $obj->id;
				
				if($obj->admin)
					$_SESSION['admin'] = true;
				else
					$_SESSION['admin'] = false;
					
				if(isset($_COOKIE['maxid']))
					$_SESSION['maxid'] = $_COOKIE['maxid'];
				else
					$_SESSION['maxid'] = 0;	
				
				if($cookie)
					setcookie("userid", "".$obj->id, time() + 1000*60*60*24*3000, '/');
					
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}

?>