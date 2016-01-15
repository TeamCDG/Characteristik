<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	tool		| post		| int		| type of tool used
//------------------------------------------------------------------
//				|			|			| 0: merge user
//				|			|			| 1: delete user
//				|			|			| 2: hide user
//------------------------------------------------------------------
//	uid1		| post		| int		| user id of first user to merge
//------------------------------------------------------------------
//	uid2		| post		| int		| user id of second user to merge
//------------------------------------------------------------------
//	uidn		| post		| int		| user id of merged user
//------------------------------------------------------------------
//	t			| post		| bool		| is teacher
//------------------------------------------------------------------
//	uid			| post		| int		| user id
//------------------------------------------------------------------

$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	if(isset($_POST['tool']))
	{
		if(intval($_POST['tool']) == 0)
		{			
			if( isset($_POST['uid1']) && isset($_POST['uid2'])  && isset($_POST['uidn'])  && isset($_POST['t']))
			{
				merge($_POST['uid1'], $_POST['uid2'], $_POST['uidn'], $_POST['t']);
				die(json_encode(array("status" => 200, "message"=>"erfolgreich verschmolzen", "post"=>$_POST)));
			}
			else
			{
				die("{\"status\":406, \"message\":\"post parameter wrong\"}");
			}
		}
		else if(intval($_POST['tool']) == 1)
		{			
			if( isset($_POST['uid']) && isset($_POST['t']) )
			{
				delete($_POST['uid'], $_POST['t']);
				die(json_encode(array("status" => 200, "message"=>"erfolgreich gelöscht", "post"=>$_POST)));
			}
			else
			{
				die("{\"status\":406, \"message\":\"post parameter wrong\"}");
			}
		}
		else if(intval($_POST['tool']) == 2)
		{			
			if( isset($_POST['uid']) && isset($_POST['t']) )
			{
				hide($_POST['uid'], $_POST['t']);
				die(json_encode(array("status" => 200, "message"=>"erfolgreich versteckt", "post"=>$_POST)));
			}
			else
			{
				die("{\"status\":406, \"message\":\"post parameter wrong\"}");
			}
		}
	}
	else
	{
		die("{\"status\":406, \"message\":\"no tool submitted\"}");
	}
	
}

function hide($uid, $t)
{
	$sql = "";
	if(intval($t) == 0)
	{
		$sql = "UPDATE  `user` SET  `stillthere` = '0' WHERE  `id` = '".$uid."'; ";
	}
	else
	{
		$sql = "UPDATE  `teacher` SET  `visible` = '0' WHERE  `id` = '".$uid."'; ";
	}
	mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
}

function merge($uid1, $uid2, $uidn, $t)
{
	$uidm = $uid1;
	if($uid1 == $uidn)
		$uidm = $uid2;
	if($_SESSION['permissions']['admin_manage_user'])
	{
		//update pollvotes
		$sql = "UPDATE  `pollvotes` INNER JOIN  `polls` ON  `pollvotes`.`pollid` =  `polls`.`id` SET  `pollvotes`.`voteid` = '".$uidn."' WHERE  `polls`.`type` = '0' AND  `pollvotes`.`voteid` = '".$uidm."'; ";
		mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		//update char
		if(intval($t) == 0)
		{
			$sql = "UPDATE  `uchar` SET  `from` = '".$uidn."' WHERE  `from` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
			$sql = "UPDATE  `uchar` SET  `holder` = '".$uidn."' WHERE  `holder` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		else
		{
			$sql = "UPDATE  `tchar` SET  `from` = '".$uidn."' WHERE  `from` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
			$sql = "UPDATE  `tchar` SET  `holder` = '".$uidn."' WHERE  `holder` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}		
		//update cit
		if(intval($t) == 0)
		{
			$sql = "UPDATE  `cit` SET  `poster` = '".$uidn."' WHERE  `poster` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		$sql = "UPDATE  `cit` SET  `holder` = '".$uidn."' WHERE  `holder` = '".$uidm."' AND `teacher` = '".$t."'; ";
		mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		//update gossip
		if(intval($t) == 0)
		{
			$sql = "UPDATE  `gossip` SET  `poster` = '".$uidn."' WHERE  `poster` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		//update request
		if(intval($t) == 0)
		{
			$sql = "UPDATE  `request` SET  `from` = '".$uidn."' WHERE  `from` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		//update changes
		if(intval($t) == 0)
		{
			$sql = "UPDATE  `changes` SET  `from` = '".$uidn."' WHERE  `from` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		//update info
		if(intval($t) == 0)
		{
			$sql = "SELECT * FROM `info` WHERE `uid` = '".$uidm."'; ";
			$res = mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
			if($res != false)
			{
				$sql = "SELECT * FROM `info` WHERE `uid` = '".$uidn."'; ";
				$res = mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
				if($res == false)
				{
					$sql = "UPDATE  `info` SET  `uid` = '".$uidn."' WHERE  `uid` = '".$uidm."'; ";
					mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
				}
				else
				{
					$sql = "DELETE FROM `info` WHERE  `uid` = '".$uidm."'; ";
					mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
				}
			}
		}
		//update snakescore
		if(intval($t) == 0)
		{
			$sql = "UPDATE  `snakescore` SET  `uid` = '".$uidn."' WHERE  `uid` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		//update albums
		if(intval($t) == 0)
		{
			$sql = "UPDATE  `albums` SET  `creator` = '".$uidn."' WHERE  `creator` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		//finally delete
		if(intval($t) == 0)
		{
			$sql = "DELETE FROM `user` WHERE  `id` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		else
		{
			$sql = "DELETE FROM `teacher` WHERE  `id` = '".$uidm."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}		
	}
	else
	{
		die ("ERROR: Denied @".__FILE__.":".__FUNCTION__."(".__LINE__.") - You do not have permission to do that (\"polls_edit\")");
	}
}

function delete($uid, $t)
{
	if($_SESSION['permissions']['admin_manage_user'])
	{
		//update pollvotes
		$sql = "DELETE `pollvotes` FROM `pollvotes` INNER JOIN  `polls` ON  `pollvotes`.`pollid` =  `polls`.`id` WHERE  ((`polls`.`type` = '0' OR `polls`.`type`= '1') AND  `pollvotes`.`voteid` = '".$uid."') OR `pollvotes`.`voter` = '".$uid."'; ";
		mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		//update char
		if(intval($t) == 0)
		{
			$sql = "DELETE FROM `uchar` WHERE  `from` = '".$uid."' OR `holder` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
			$sql = "DELETE FROM `tchar` WHERE  `from` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		else
		{
			$sql = "DELETE FROM `tchar` WHERE  `holder` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}		
		//update cit
		if(intval($t) == 0)
		{
			$sql = "DELETE FROM  `cit` WHERE  `poster` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		
		$sql = "DELETE FROM `cit` WHERE  `holder` = '".$uid."' AND `teacher` = '".$t."'; ";
		mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		//update gossip
		if(intval($t) == 0)
		{
			$sql = "DELETE FROM `gossip` WHERE  `poster` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		//update request
		if(intval($t) == 0)
		{
			$sql = "DELETE FROM  `request` WHERE  `from` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		//update changes
		if(intval($t) == 0)
		{
			$sql = "DELETE FROM  `changes`  WHERE  `from` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		//update info
		if(intval($t) == 0)
		{
			$sql = "DELETE FROM `info` WHERE  `uid` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		//update snakescore
		if(intval($t) == 0)
		{
			$sql = "DELETE FROM  `snakescore` WHERE  `uid` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		//update albums
		if(intval($t) == 0)
		{
			$sql = "DELETE FROM `albums` WHERE  `creator` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		//finally delete
		if(intval($t) == 0)
		{
			$sql = "DELETE FROM `user` WHERE  `id` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}
		else
		{
			$sql = "DELETE FROM `teacher` WHERE  `id` = '".$uid."'; ";
			mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		}		
	}
	else
	{
		die ("ERROR: Denied @".__FILE__.":".__FUNCTION__."(".__LINE__.") - You do not have permission to do that (\"polls_edit\")");
	}
}

?>