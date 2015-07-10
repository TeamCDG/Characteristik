<?php 

//	param		| method	| type		| desc
//------------------------------------------------------------------
//	type		| post		| int		| type:
//				|			|			| 0: add
//				|			|			| 1: edit
//				|			|			| 3: delete
//				|			|			| 4: delete request
//------------------------------------------------------------------
//	content		| post		| string	| content of post/reason
//------------------------------------------------------------------
//	title		| post		| string	| title of album
//------------------------------------------------------------------
//	descritption| post		| string	| description of album
//------------------------------------------------------------------
//	permString	| post		| string	| permission string of album
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
					$id = addAlbum($_POST['title'], $_POST['description'], $_POST['permStr']);
					echo "{\"status\":200, \"message\":\"erfolgreich hinzugefügt\", \"id\":".$id."}";
					break;
				case 1: //needs work
					updateChar($_POST['id'], null, $_POST['content']);
					echo "{\"status\":200, \"message\":\"erfolgreich bearbeitet\"}";
					break;
				case 2://needs work
					addRequest($_POST['id'], null, $_POST['content']);
					echo "{\"status\":200, \"message\":\"erfolgreich beantragt\"}";
					break;
				case 3://needs work
					deleteChar($_POST['id'], null);
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

function genPath($title)
{
	return strtolower(str_replace(" ", "_",preg_replace(array("#[,./]#","#[^a-zA-Z0-9 ]#"),array("","-"),$title)));
}

function addAlbum($title, $description, $permStr)
{
	
	if(strlen(trim($title)) > 0)
	{
		$sql = "SELECT COUNT(*) AS c FROM `albums` WHERE `title` LIKE '%".mysql_real_escape_string($title)."%' ; ";
		$res = mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #012 Query failed: $sql @ajax/albumedit.php - ".mysql_error()."\"}");
		$count = mysql_fetch_object($res)->c;
		if(intval($count) != 0)
			$title .= " ".$count;
		$path = genPath($title);
		
		$sql = "INSERT INTO `albums` (`id`, `creator`, `title`, `permissions`, `description`, `path`) VALUES (NULL, '".$_SESSION['userid']."', '".mysql_real_escape_string($title)."
		', '".mysql_real_escape_string($permStr)."', '".mysql_real_escape_string($description)."', '".mysql_real_escape_string("gallery/albums/".$path."/")."');";
		mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #012 Query failed: $sql @ajax/albumedit.php - ".mysql_error()."\"}");
		
		$sql = "SELECT * FROM `albums` WHERE `title` LIKE '%".mysql_real_escape_string($title)."%' AND `creator` = '".$_SESSION['userid']."' ORDER BY `id` DESC LIMIT 1;";
		$res = mysql_query($sql) or die ("{\"status\":500, \"message\":\"ERROR #016 Query failed: $sql @ajax/albumedit.php - ".mysql_error()."\"}");
		
		mkdir($_SERVER['DOCUMENT_ROOT'].$rootfolder."/c/gallery/albums/".$path, 0777, true);
		mkdir($_SERVER['DOCUMENT_ROOT'].$rootfolder."/c/gallery/albums/".$path."/thumb/", 0777, true);
		
		return mysql_fetch_object($res)->id;
	}
	else
	{
		die("{\"status\":406, \"message\":\"title empty\"}");
	}
	
	
	
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