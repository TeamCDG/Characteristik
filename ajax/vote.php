<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	pid			| post		| int		| id of poll
//------------------------------------------------------------------
//	votes		| post		| int[]		| vote id(s) of selected votes

$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	if(isset($_POST['votes']) && isset($_POST['pid']) && count($_POST['votes']) > 0)
	{
		$hv = hasVoted($_SESSION['userid'], $_POST['pid']);
		if(!$hv)
		{
			vote($_POST['pid'], $_POST['votes']);
		}
		else
		{
			revote($_POST['pid'], $_POST['votes']);
		}
		$answers = getAnswers($_POST['pid']);
		$votes = array();
		$sql = "SELECT COUNT(*) as c FROM pollvotes WHERE `pollid`='".mysql_real_escape_string($_POST['pid'])."'";
		$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
		$vcount = mysql_fetch_object($res)->c;

		$sql = "SELECT COUNT(*) as c, voteid FROM pollvotes WHERE `pollid`='".mysql_real_escape_string($_POST['pid'])."' GROUP BY voteid ORDER BY c DESC";
		$res = mysql_query($sql) or die ("ERROR #032: Query failed: $sql @thecakeisalie.php - ".mysql_error());

		$c = 0;
		$allco = 0;
		while($row = mysql_fetch_object($res))
		{
			$tmp = array();
			array_push($tmp, $row->c);
			
			$allco += $row->c;
			
			array_push($tmp, $answers[$row->voteid]);
			
			array_push($votes, $tmp);
			$c++;
			
			if($c >= 10)
			{
				$stuff = array();
				array_push($stuff, $vcount - $allco);
				array_push($stuff, "Sonstige");
				array_push($votes, $stuff);
				break;
			}
		}
		$vtr = "<ul style=\"float: left;\">";
		for($i = 0; $i < count($votes); $i++)
		{
			$vtr .= "<li>".($i+1).": ".$votes[$i][1]." ".$votes[$i][0]."/".$vcount." (".round((100.0 / floatval($vcount)) * $votes[$i][0], 2)."%)</li>";
		}
		$vtr .= "</ul>";
		die(json_encode(array("status" => 200, "message"=>"erfolgreich abgestimmt", "vote_result"=>$vtr, "hv"=>$hv, "post"=>$_POST)));
	}
	else
	{
		die("{\"status\":406, \"message\":\"no votes submitted\"}");
	}
	
}


function vote($pid, $votes)
{
	if($_SESSION['permissions']['polls_vote'])
	{
		for($i = 0; $i < count($votes); $i++)
		{
			$sql = "INSERT INTO `pollvotes`(`id`, `pollid`, `voteid`, `voter`, `time`) VALUES (NULL, '".$pid."', '".$votes[$i]."', '".$_SESSION['userid']."' , CURRENT_TIMESTAMP);";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
	}
	else
	{
		die ("ERROR: Denied @".__FILE__.":".__FUNCTION__."(".__LINE__.") - You do not have permission to do that (\"polls_edit\")");
	}
}

function revote($pid, $votes)
{
	if($_SESSION['permissions']['polls_vote'])
	{
		$sql = "DELETE FROM pollvotes WHERE `voter`='".$_SESSION['userid']."' AND `pollid`='".$pid."'";
		mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		vote($pid, $votes);
	}
	else
	{
		die ("ERROR: Denied @".__FILE__.":".__FUNCTION__."(".__LINE__.") - You do not have permission to do that (\"polls_edit\")");
	}
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