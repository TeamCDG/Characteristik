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
					$debug = editPoll($_POST['id'],$_POST['title'], $_POST['ptype'], $_POST['answers'], $_POST['multivote']=="true"?1:0, $_POST['revote']=="true"?1:0, $_POST['result_prevote']=="true"?1:0);
					$arr = array("status" => 200, "message"=>"erfolgreich bearbeitet", "id"=>$_POST['id'], "post"=>$_POST);
					if($_SESSION['debug'])
						$arr["debug"] = $debug;
					echo json_encode($arr);
					break;
				case 2:
					closePoll($_POST['id']);
					echo json_encode(array("status" => 200, "message"=>"erfolgreich geschlossen", "id"=>$_POST['id'], "post"=>$_POST));
					break;
				case 3:
					deltePoll($_POST['id']);
					echo json_encode(array("status" => 200, "message"=>"erfolgreich gelöscht", "id"=>$_POST['id'], "post"=>$_POST));
					break;
				case 4:
					openPoll($_POST['id']);
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
function editPoll($id, $title, $type, $answers, $multivote, $revote, $result_prevote)
{
	if($_SESSION['permissions']['polls_edit'])
	{
		$poll = getPoll($id);
		if(intval($poll['multivote']) == 1 && $multivote == 0)
		{
			$sql="DELETE pv1 FROM `pollvotes` pv1, `pollvotes` pv2 WHERE pv1.id > pv2.id AND pv1.voter = pv2.voter AND pv1.pollid = pv2.pollid AND pv1.pollid='".$id."';";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		
		$sql = "UPDATE `polls` SET `title`='".mysql_real_escape_string($title)."',`type`='".mysql_real_escape_string($type)."',`multivote`='".mysql_real_escape_string($multivote)."',`revote`='".mysql_real_escape_string($revote)."' ,`result_prevote`='".mysql_real_escape_string($result_prevote)."' WHERE `id`='".mysql_real_escape_string($id)."'";
		mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		
		
		$pid = $id;
		$debug ="";
		if(intval($type) == 3)
		{
			$answ = getAnswersComplete($pid);
			//var_dump($answ);
			//var_dump($answers);
			for($i = 0; $i < min(count($answers), count($answ)); $i++)
			{
				if($answers[$i] != $answ[$i]['text'])
				{
					$debug .= "#".$i.": \"".$answers[$i]."\"->\"".$answ[$i]['text']."\"\n";
					$found = false;
					for($x = 0; $x < count($answ); $x++)
					{
						if($answers[$i] == $answ[$x]['text'])
						{
							$debug .= "mark old votes and answer with same voteid\n";
							$debug .= '$x: '.$x." / voteid: ".$answ[$x]['voteid']."\n";
							$sql = "UPDATE `pollvotes` SET `voteid`='".(-($i+1))."' WHERE `pollid` = '".$pid."' AND `voteid` = '".$i."'";
							$debug .= $sql."\n";
							mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
							//$sql = "UPDATE  `pollvotes` INNER JOIN  `pollanswers` ON  `pollvotes`.`pollid` =  `pollanswers`.`pollid` AND `pollvotes`.`voteid` =  `pollanswers`.`voteid` SET  `pollvotes`.`voteid` = '".(-($i+1)).
							//"', `pollanswers`.`voteid` = '".(-($i+1))."' WHERE  `pollvotes`.`pollid` = '".$pid."' AND `pollanswers`.`voteid` = '".$i."' AND NOT `pollanswers`.`text` ='".mysql_real_escape_string($answ[$x]['text'])."'";
							$sql = "UPDATE `pollanswers` SET `voteid`='".(-($i+1))."' WHERE `pollid` = '".$pid."' AND `voteid` = '".$i."'";
							mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
							$debug .= $sql."\n";
							
							$sql = "SELECT * FROM `pollanswers` WHERE `pollid` = '".$pid."' AND `text` ='".mysql_real_escape_string($answ[$x]['text'])."'";
							$debug .= $sql."\n";
							$res = mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
							$vid_old = mysql_fetch_array($res)['voteid'];
							
							
							$debug .= "set to new voteid and carry over votes\n"; //JOIN only works is other data is present. so find out id of answer and do it via voteid
							$sql = "UPDATE `pollvotes` SET `voteid`='".$i."' WHERE `pollid` = '".$pid."' AND `voteid` = '".$vid_old."'";
							$debug .= $sql."\n";
							mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
							//$sql = "UPDATE  `pollvotes` INNER JOIN  `pollanswers` ON  `pollvotes`.`pollid` =  `pollanswers`.`pollid` AND `pollvotes`.`voteid` =  `pollanswers`.`voteid` SET  `pollvotes`.`voteid` = '".$i.
							//"', `pollanswers`.`voteid` = '".$i."' WHERE  `pollvotes`.`pollid` = '".$pid."' AND  `pollanswers`.`text` ='".mysql_real_escape_string($answ[$x]['text'])."'";
							//mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
							$sql = "UPDATE `pollanswers` SET `voteid`='".$i."' WHERE `pollid` = '".$pid."' AND `voteid` = '".$vid_old."'";
							$debug .= $sql."\n";
							mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
							
							$found = true;
							break;
						}
					}
					
					
					if(!$found)
					{
						$debug .= "overwrite\n";						
						$debug .= "check if still exists\n";
						$sql = "SELECT * FROM `pollanswers` WHERE `pollid` = '".$pid."' AND `voteid` = '".$i."'";
						$debug .= $sql."\n";
						$res = mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
						$num = mysql_num_rows($res);
						if($num != 0)
						{
							$debug .= "still there ---> overwrite\n";
							$sql = "UPDATE `pollanswers` SET `text`='".mysql_real_escape_string($answers[$i])."' WHERE `pollid`='".$pid."' AND `voteid`='".$i."';";
							$debug .= $sql."\n";
							mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
						}
						else
						{
							$debug .= "gone away ---> create new\n";
							$sql = "INSERT INTO pollanswers (`id`, `pollid`, `text`, `voteid`) VALUES (NULL, '".$pid."', '".mysql_real_escape_string(trim($answers[$i]))."', '".$i."')";
							mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
							$debug .= $sql."\n";
						}

					}
				}
			}
			
			if(count($answ) > count($answers))
			{
				$debug .= "more old answers, some has to be deleted\n";
				$sql = "DELETE FROM `pollanswers` WHERE `pollid`='".$pid."' AND `voteid`>='".count($answers)."';";
				mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
				$debug .= $sql."\n";
				
				$sql = "DELETE FROM `pollvotes` WHERE `pollid`='".$pid."' AND `voteid`>='".count($answers)."';";
				mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
				$debug .= $sql."\n";
						
			}
			else if(count($answ) < count($answers))
			{
				$debug .= "more new answers, some has to be added\n";
				for($i = count($answ); $i < count($answers); $i++)
				{
					$sql = "INSERT INTO pollanswers (`id`, `pollid`, `text`, `voteid`) VALUES (NULL, '".$pid."', '".mysql_real_escape_string(trim($answers[$i]))."', '".$i."')";
					mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
					$debug .= $sql."\n";
				}
			}
			
			$debug .= "cleanup: remove negative voteids\n";
			$sql = "DELETE FROM `pollanswers` WHERE `pollid`='".$pid."' AND `voteid`<'0';";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
			$debug .= $sql."\n";
				
			$sql = "DELETE FROM `pollvotes` WHERE `pollid`='".$pid."' AND `voteid`<'0';";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
			$debug .= $sql."\n";
			
		}
		
		return $debug;
	}
	else
	{
		die ("ERROR: Denied @".__FILE__.":".__FUNCTION__."(".__LINE__.") - You do not have permission to do that (\"polls_edit\")");
	}
}

?>