<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	type		| post		| int		| type:
//				|			|			| 0: add
//				|			|			| 1: edit
//				|			|			| 2: deleterequest
//				|			|			| 3: delete
//------------------------------------------------------------------
//	uid			| post		| int		| userid of charholder
//------------------------------------------------------------------
//	t			| post		| bool		| is teacher?
//				|			|			| 0: false
//				|			|			| 1: true
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
			$t = $_POST['t']=="1" || $_POST['t']==1;
			switch(intval($_POST['type']))
			{
				case 0:
					$id = addChar($_POST['uid'], $t, $_POST['content']);
					echo "{\"status\":200, \"message\":\"erfolgreich hinzugefügt\", \"id\":".$id.", \"name\":\"".getName($_SESSION['userid'], 0)."\"}";
					break;
				case 1:
					updateChar($_POST['id'], $t, $_POST['content']);
					echo "{\"status\":200, \"message\":\"erfolgreich bearbeitet\"}";
					break;
				case 2:
					addRequest($_POST['id'], $t, $_POST['content']);
					echo "{\"status\":200, \"message\":\"erfolgreich beantragt\"}";
					break;
				case 3:
					deleteChar($_POST['id'], $t);
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


function updateChar($id, $teacher, $ncontent)
{
	$oldcontent = getCharContent($id, $teacher);
	
	if($oldcontent == $ncontent)
		return;
		
	$t = $teacher===true?"`tchar`":"`uchar`";
	$sql = "UPDATE $t SET `content` = '".mysql_real_escape_string($ncontent)."' WHERE `id` = '$id'";
	mysql_query($sql) or die ("500: ERROR #010 Query failed: $sql @ajax/charedit.php - ".mysql_error());
	
	$sql = "INSERT INTO `changes` (`id`, `from`, `cid`, `teacher`, `oldcontent`, `content`) VALUES (NULL, '".$_SESSION['userid']."', '$id', '$teacher', '".mysql_real_escape_string($oldcontent)."', '".mysql_real_escape_string($ncontent)."');";
	mysql_query($sql) or die ("500: ERROR #011 Query failed: $sql @ajax/charedit.php- ".mysql_error());
}

function addChar($to, $teacher, $content)
{
	$t = $teacher===true?"`tchar`":"`uchar`";
	
	if(strlen(trim($content)) > 0)
	{
		$sql = "INSERT INTO $t (`id`, `from`, `holder`, `content`) VALUES (NULL, '".$_SESSION['userid']."', '$to', '".mysql_real_escape_string(trim($content))."');";
		mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #012 Query failed: $sql @ajax/charedit.php - ".mysql_error()."\"}");
	}
	else
	{
		die("{\"status\":406, \"message\":\"content empty\"}");
	}
	
	$sql = "SELECT * FROM $t WHERE `holder` = '$to' AND `from` = '".$_SESSION['userid']."' ORDER BY `id` DESC LIMIT 1;";
	$res = mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #016 Query failed: $sql @ajax/charedit.php - ".mysql_error()."\"}");
	
	return mysql_fetch_object($res)->id;
	
}

function deleteChar($id, $teacher)
{
	$t = $teacher===true?"`tchar`":"`uchar`";
	$sql = "UPDATE $t SET `visible` = '0' WHERE `id` = $id";
	mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #013 Query failed: $sql @ajax/charedit.php - ".mysql_error()."\"}");
	
	if(isRequested($id, $teacher))
		closeRequest(getRequestId($id, $teacher));
	else
	{
		$sql = "INSERT INTO `request` (`id`, `from`, `request_type`, `type`, `cid`, `message`, `closed`, `status`, `eby`) VALUES (NULL, '0', '2', '$teacher', '$id', '', '1', 'von admin gelöscht', '".$_SESSION['userid']."');";
		mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #014 Query failed: $sql @ajax/charedit.php - ".mysql_error()."\"}");
	}
}

function addRequest($id, $type, $text)
{
	$sql = "INSERT INTO `request` (`id`, `from`, `request_type`, `type`, `cid`, `message`, `closed`, `status`, `eby`) VALUES (NULL, '".$_SESSION['userid']."', '1', '$type', '$id', '".mysql_real_escape_string($text)."', '0', 'gemeldet', '0');";
	mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #015 Query failed: $sql @ajax/charedit.php - ".mysql_error()."\"}");
}

?>