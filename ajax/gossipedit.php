<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	type		| post		| int		| type:
//				|			|			| 0: add
//				|			|			| 1: edit
//				|			|			| 2: deleterequest
//				|			|			| 3: delete
//------------------------------------------------------------------
//	content		| post		| string	| content of post/reason
//------------------------------------------------------------------
//	id			| post		| int		| id of edit/delete(request) post
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
			switch(intval($_POST['type']))
			{
				case 0:
					$id = addGossip($_POST['content']);
					echo "{\"status\":200, \"message\":\"erfolgreich hinzugefügt\", \"id\":".$id.", \"name\":\"".getName($_SESSION['userid'], 0)."\"}";
					break;
				case 1:
					updateGossip($_POST['id'], $_POST['content']);
					echo "{\"status\":200, \"message\":\"erfolgreich bearbeitet\"}";
					break;
				case 2:
					addRequest($_POST['id'], $_POST['content']);
					echo "{\"status\":200, \"message\":\"erfolgreich beantragt\"}";
					break;
				case 3:
					deleteGossip($_POST['id']);
					echo "{\"status\":200, \"message\":\"erfolgreich gelöscht\"}";
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


function updateGossip($id, $ncontent)
{
	$oldcontent = getGossipContent($id);
	
	if($oldcontent == $ncontent)
		return;
		
	$sql = "UPDATE `gossip` SET `content` = '".mysql_real_escape_string($ncontent)."' WHERE `id` = '$id'";
	mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #017 Query failed: $sql @ajax/gossipedit.php - ".mysql_error()."\"}");
	
	$sql = "INSERT INTO `changes` (`id`, `from`, `cid`, `type`, `oldcontent`, `content`) VALUES (NULL, '".$_SESSION['userid']."', '$id', '3', '".mysql_real_escape_string($oldcontent)."', '".mysql_real_escape_string($ncontent)."');";
	mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #018 Query failed: $sql @ajax/gossipedit.php - ".mysql_error()."\"}");
}

function addGossip($content)
{
	if(strlen(trim($content)) > 0)
	{
		$sql = "INSERT INTO `gossip` (`id`, `poster`, `content`) VALUES (null, '".$_SESSION['userid']."', '".mysql_real_escape_string(trim($content))."');";
		mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #012 Query failed: $sql @ajax/gossipedit.php - ".mysql_error()."\"}");
	}
	else
	{
		die("{\"status\":406, \"message\":\"content empty\"}");
	}
	
	$sql = "SELECT * FROM `gossip` WHERE `poster` = '".$_SESSION['userid']."' ORDER BY `id` DESC LIMIT 1;";
	$res = mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #016 Query failed: $sql @ajax/gossipedit.php - ".mysql_error()."\"}");
	
	return mysql_fetch_object($res)->id;
}

function deleteGossip($id)
{
	$sql = "UPDATE `gossip` SET `visible` = '0' WHERE `id` = $id";
	mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #013 Query failed: $sql @ajax/gossipedit.php - ".mysql_error()."\"}");
	
	if(isRequested($id, 3))
		closeRequest(getRequestId($id, 3));
	else
	{
		$sql = "INSERT INTO `request` (`id`, `from`, `request_type`, `type`, `cid`, `message`, `closed`, `status`, `eby`) VALUES (NULL, '0', '2', '3', '$id', '', '1', 'von admin gelöscht', '".$_SESSION['userid']."');";
		mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #014 Query failed: $sql @ajax/gossipedit.php - ".mysql_error()."\"}");
	}
}

function addRequest($id, $text)
{
	$sql = "INSERT INTO `request` (`id`, `from`, `request_type`, `type`, `cid`, `message`, `closed`, `status`, `eby`) VALUES (NULL, '".$_SESSION['userid']."', '1', '3', '$id', '".mysql_real_escape_string($text)."', '0', 'gemeldet', '0');";
	mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #015 Query failed: $sql @ajax/gossipedit.php - ".mysql_error()."\"}");
}

?>