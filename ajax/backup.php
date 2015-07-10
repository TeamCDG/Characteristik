<?php

//	param		| method	| type				| desc
//------------------------------------------------------------------
//	method		| post		| int				| 0: delete  backup
//				| 			| 					| 1: restore backup
//				| 			| 					| 2: create  backup
//------------------------------------------------------------------
//	id			| post		| int 				| id of backup
//------------------------------------------------------------------
//	llid		| post		| int				| last line id
//------------------------------------------------------------------

$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	
	if(isset($_POST['method']))
	{
		if(intval($_POST['method']) == 2)
		{
			$bres = manualBackup();
		}
	}
	
	
}

function manualBackup()
{
	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."backup.php");
	$bres = @backup($_SESSION['backup_compression'], $_SESSION['backup_send_mail'], $_SESSION['backup_email'], $_SESSION['backup_folder'], $dbname, $c);
	$sql = "INSERT INTO `backup` VALUES (NULL, NULL, '".((int)$bres['sentmail'])."', '".$bres['mailreciever']."', '".$bres['filename']."', '1', '0');";
	mysql_query($sql) or die ("ERROR #001: Query failed: $sql @ajax/backup.php - ".mysql_error());
	return $bres;
}

function restore($rootfolder, $bip, $llid)
{
}	

//wir lieben die dunkelheit 
//vermeiden das licht
//am ende des tunnels
//vom herannahenden zug

//wir springen hin und her 
//zwischen heiterer manie 
//und düsterer melancholie
//lieben das grau des lebens

//wir fliegen hoch
//nur um noch tiefer zu fallen
//sind geboren um zu laufen
//doch kriechen am boden

//wir warten lange auf den einen tag
//aber ist er endlich da,
//dann rennt die zeit
//wie ein hungriger gepard

//wir suchen das glück 
//doch haben angst es zu finden
//trauern um verpasste chancen

//wir geben auf, scmheißen es hin
//wieder viel zu früh 
//und nicht früh genug
//sodass es weh tut

//wir waren die richtigen leute
//nur zur falschen zeit am falschen ort

?>