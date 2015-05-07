<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	subject		| post		| string	| subject of mail
//------------------------------------------------------------------
//	site		| post		| string	| site of problem
//------------------------------------------------------------------
//	desc		| post		| string	| description of problem
//------------------------------------------------------------------

$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	if(isset($_POST['subject']) && isset($_POST['site']) && isset($_POST['desc']))
	{
		if(mail("cdg.josh@googlemail.com", "Error report: ".$_POST['subject'], "Seite: ".$_POST['site']."\nVon:".getName($_SESSION['userid'], 0)."\n".$_POST['desc']."\nSession\n".var_export($_SESSION, true)."\nCookie\n".var_export($_COOKIE, true)))
		{
			die("{\"status\":200, \"message\":\"Problembericht gesendet!\"}");
		}
		else
		{
			die("{\"status\":500, \"message\":\"Konnte Problembericht nicht senden!\"}");
		}
	}
	else
	{
		die("{\"status\":406, \"message\":\"at least one value empty\"}");
	}
	
}
?>