<?php 
//	param		| method	| type		| desc
//------------------------------------------------------------------
//	name		| post		| string	| name of cookie
//------------------------------------------------------------------
//	value		| post		| string	| value of cookie
//------------------------------------------------------------------
//	del			| post		| bool		| 1 to delete cookie
//------------------------------------------------------------------


$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";
	
	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	if(isset($_POST['name']) && (isset($_POST['value']) || isset($_POST['del'])))
	{
		$res = "";
		if(isset($_POST['del']))			
		{
			$res = setcookie($_POST['name'], null, -1, '/');
		}
		else
		{
			$res = setcookie($_POST['name'], ""+$_POST['value'], time() + 1000*60*60*24*3000, '/');
		}
		if($res)
			die("{\"status\":200, \"message\":\"cookie gesetzt\", \"name\":\"".$_POST['name']."\", \"value\":\"".(isset($_POST['value'])?$_POST['value']:"")."\"}");
		else
			die("{\"status\":500, \"message\":\"internal server error\"}");
	}
	else
	{
		die("{\"status\":406, \"message\":\"no name or value specified\"}");
	}
	
}

?>