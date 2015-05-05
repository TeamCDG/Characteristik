<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	id			| post		| int		| id of request to close
//------------------------------------------------------------------

$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	if(isset($_POST['id']))
	{
		closeRequest($_POST['id']);
		die("{\"status\":200, \"message\":\"Löschantrag bearbeitet!\", \"id\":\"".$_POST['id']."\", \"name\":\"".getName($_SESSION['userid'], 0)."\"}");
	}
	else
	{
		die("{\"status\":406, \"message\":\"no id specified\"}");
	}
	
}

?>