<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	type		| post		| int		| type:
//				|			|			| 0: add
//				|			|			| 1: edit
//				|			|			| 2: close
//				|			|			| 3: delete
//				|			|			| 4: reopen
//------------------------------------------------------------------
//	id			| post		| int		| id of poll
//------------------------------------------------------------------
//	title		| post		| string	| title of poll
//------------------------------------------------------------------
//	ptype		| post		| int		| type of poll:
//				|			|			| 0: schüler
//				|			|			| 1: lehrer
//				|			|			| 2: y/n
//				|			|			| 3: custom
//------------------------------------------------------------------
//	multivote	| post		| bool		| vote for more answers
//------------------------------------------------------------------
//	finalchoice	| post		| bool		| true if choice is final
//------------------------------------------------------------------
//	diagram		| post		| int		| diagram type:
//				|			|			| 0: pie
//------------------------------------------------------------------
//	answers		| post		| string[]	| possible answers (only type 2)
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
		if(is_numeric($_POST['type']))
		{
			$t = $_POST['t']=="1" || $_POST['t']==1 || $_POST['t']=="true";
			switch(intval($_POST['type']))
			{
				case 0:
					$id = addPoll($_POST['title'], $_POST['ptype'], $_POST['answers'], $_POST['multivote']=="true"?1:0, $_POST['revote']=="true"?1:0, $_POST['result_prevote']=="true"?1:0);
					echo json_encode(array("status" => 200, "message"=>"erfolgreich hinzugefügt", "id"=>$id, "post"=>$_POST));
					break;
				case 1:
					echo json_encode(array("status" => 200, "message"=>"erfolgreich bearbeitet", "id"=>$_POST['id'], "post"=>$_POST));
					break;
				case 2:
					echo json_encode(array("status" => 200, "message"=>"erfolgreich geschlossen", "id"=>$_POST['id'], "post"=>$_POST));
					break;
				case 3:
					deltePoll($_POST['id']);
					echo json_encode(array("status" => 200, "message"=>"erfolgreich gelöscht", "id"=>$_POST['id'], "post"=>$_POST));
					break;
				case 4:
					echo json_encode(array("status" => 200, "message"=>"erfolgreich erneut geöffnet", "id"=>$_POST['id'], "post"=>$_POST));
					break;
			}
		}
		else
		{
			die("{\"status\":406, \"message\":\"type no number\"}");
		}
	}
	else
	{
		die("{\"status\":406, \"message\":\"no type specified\"}");
	}
	
}


function deletePoll($pid)
{
	//$_SESSION['permissions']['admin_backup_restore'];
}

function closePoll($id)
{
	if($_SESSION['permissions']['polls_edit'])
	{
		$sql = "UPDATE polls SET `closed` = '1' WHERE `id`='".$id."'";
		mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
	}
	else
	{
		die ("ERROR: Denied @".__FILE__.":".__FUNCTION__."(".__LINE__.") - You do not have permission to do that (\"polls_edit\")");
	}
}

function openPoll($id)
{
	if($_SESSION['permissions']['polls_edit'])
	{
		$sql = "UPDATE polls SET `closed` = '0' WHERE `id`='".$id."'";
		mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
	}
	else
	{
		die ("ERROR: Denied @".__FILE__.":".__FUNCTION__."(".__LINE__.") - You do not have permission to do that (\"polls_edit\")");
	}
}

function addPoll($title, $ptype, $answers, $multivote, $revote, $result_prevote)
{
	if($_SESSION['permissions']['polls_edit'])
	{
		$sql = "INSERT INTO polls (`id`, `by`, `type`, `title`, `closed`, `multivote`, `revote`, `result_prevote`) VALUES (NULL, '".$_SESSION['userid']."', '".$ptype."','".mysql_real_escape_string(trim($title))."', '0', '".$multivote."', '".$revote."', '".$result_prevote."')";
		mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		$sql = "SELECT * FROM polls ORDER BY `id` DESC LIMIT 1";
		$res = mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		
		$pid = mysql_fetch_object($res)->id;
		
		if(intval($ptype) == 3)
		{
			$i = 0;
			foreach($answers as $a)
			{
				$sql = "INSERT INTO pollanswers (`id`, `pollid`, `text`, `voteid`) VALUES (NULL, '".$pid."', '".mysql_real_escape_string(trim($a))."', '".$i."')";
				mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
				$i++;
			}
		}
		
		return $pid;
	}
	else
	{
		die ("ERROR: Denied @".__FILE__.":".__FUNCTION__."(".__LINE__.") - You do not have permission to do that (\"polls_edit\")");
	}
}

//TODO BUILD FUNCTION
function editPoll($id, $title, $type, $answers, $multivote, $finalchoice)
{
	if($_SESSION['permissions']['polls_edit'])
	{
		$sql = "UPDATE `polls` SET `title`='".mysql_real_escape_string($title)."',`type`='".mysql_real_escape_string($type)."',`multivote`='".mysql_real_escape_string($multivote)."',`finalchoice`='".mysql_real_escape_string($finalchoice)."' WHERE `id`='".mysql_real_escape_string($id)."'";
		mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		
		
		$pid = mysql_fetch_object($res)->id;
		
		if(intval($type) == 0)
		{
			$i = 0;
			foreach($answers as $a)
			{
				$sql = "INSERT INTO pollanswers (`id`, `pollid`, `text`, `voteid`) VALUES (NULL, '".$pit."', '".mysql_real_escape_string(trim($a))."', '".$i."')";
				mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
				$i++;
			}
		}
		
		return $pid;
	}
	else
	{
		die ("ERROR: Denied @".__FILE__.":".__FUNCTION__."(".__LINE__.") - You do not have permission to do that (\"polls_edit\")");
	}
}

?>