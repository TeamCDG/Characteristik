<?php

//	param		| method	| type				| desc
//------------------------------------------------------------------
//	uid			| post		| int				| userid (ignored)
//------------------------------------------------------------------
//	count		| post		| int 				| count of values
//------------------------------------------------------------------
//	values		| post		| array(string)		| array of values
//------------------------------------------------------------------

$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	
	if(isset($_POST['values']))
	{
		$sql = "SELECT COUNT(*) AS c FROM `info` WHERE `uid`='".$_SESSION['userid']."';";
		$res = mysql_query($sql) or die ("ERROR #419: Query failed: $sql @showuser - ".mysql_error());
		
		
		if(mysql_fetch_object($res)->c == 0)
		{
			$sql = "INSERT INTO `info`(`id`, `uid`";
			$values =  " VALUES (NULL,'".$_SESSION['userid']."'";
			for($i = 0; $i < count($_POST['values']); $i++)
			{
				$sql .= ", `".$i."`";
				$values .= ", '".mysql_real_escape_string($_POST['values'][$i])."' ";
			}
			
			$sql = $sql.") ".$values." ); ";
			
			
			$res = mysql_query($sql) or die ("ERROR #430: Query failed: $sql @showuser - ".mysql_error());
			
			
			echo "erfolgreich gespeichert!";
		}
		else
		{
			$sql = "UPDATE `info` SET ";
			for($i = 0; $i < count($_POST['values']); $i++)
			{
				$sql .= ($i!=0?",":"")."`".$i."`='".mysql_real_escape_string($_POST['values'][$i])."' ";
			}
			$sql .= "WHERE `uid`='".$_SESSION['userid']."';";
			$res = mysql_query($sql) or die ("ERROR #430: Query failed: $sql @showuser - ".mysql_error());
			
			echo "erfolgreich gespeichert!";
		}
			
	}
	
	
}
		
?>