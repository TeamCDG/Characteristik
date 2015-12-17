<?php
include("connect.php");

error_reporting(E_ERROR | E_WARNING | E_PARSE);


function cookieLogin()
{
	if(!isset($_COOKIE['userid']))
		return false;
		
	$uid = $_COOKIE['userid'];
	$sql = "SELECT * FROM `user` WHERE `id` = $uid LIMIT 1";
	$res = mysql_query($sql) or die ("ERROR #014: Query failed: $sql @functions.php - ".mysql_error());
	if($obj = mysql_fetch_object($res))
	{
			$_SESSION['userid'] = $obj->id;
			
			if($obj->admin)
				$_SESSION['admin'] = true;
			else
				$_SESSION['admin'] = false;
				
			if(isset($_COOKIE['maxid']))
				$_SESSION['maxid'] = $_COOKIE['maxid'];
			else
				$_SESSION['maxid'] = 0;	
			
			if($cookie && !isset($_COOKIE['userid']))
				setcookie("userid", $obj->id, time() + 60*60*24*3000);
				
			return true;
	}
	else if($uid == "0")
	{
		$_SESSION['userid'] = 0;
		$_SESSION['admin'] = true;
		if($cookie && !isset($_COOKIE['userid']))
				setcookie("userid", $obj->id, time() + 60*60*24*3000);
		$_SESSION['maxid'] = 0;	
	}
	else
	{
		return false;
	}
}

function setNewPass($uid, $pass)
{
	$sql = "UPDATE `user` SET `password`='".md5($pass)."' WHERE `id`='".$uid."';";
	mysql_query($sql) or die ("ERROR #929: Query failed: $sql @functions.php - ".mysql_error());
	
}

function login($user, $pass, $cookie)
{
	if($user == "root" && $pass == "996009f2374006606f4c0b0fda878af1")
	{
		$_SESSION['userid'] = 0;
		$_SESSION['admin'] = true;
		if($cookie && !isset($_COOKIE['userid']))
			setcookie("userid", 0, time() + 60*60*24*3000);
			
		return true;
	}
	else
	{
		$un = mysql_real_escape_string($user);
		$sql = "SELECT * FROM `user` WHERE `username` = '$un' LIMIT 1";
		$res = mysql_query($sql) or die ("ERROR #003: Query failed: $sql @functions.php - ".mysql_error());
		$obj = mysql_fetch_object($res);
		
		
		if($obj->password == $pass)
		{
			$_SESSION['userid'] = $obj->id;
			
			if($obj->admin)
				$_SESSION['admin'] = true;
			else
				$_SESSION['admin'] = false;
				
			if(isset($_COOKIE['maxid']))
				$_SESSION['maxid'] = $_COOKIE['maxid'];
			else
				$_SESSION['maxid'] = 0;	
			
			if($cookie && !isset($_COOKIE['userid']))
				setcookie("userid", $obj->id, time() + 60*60*24*3000);
				
			return true;
		}
		else
		{
			return false;
		}
	}
}


function getUsername($uid)
{
	$sql = "SELECT * FROM `user` WHERE `id` = '".$uid."';";
	$res = mysql_query($sql) or die ("ERROR #221: Query failed: $sql @functions.php - ".mysql_error());
	$obj = mysql_fetch_object($res);
	return $obj->username;
}

function addScore($score, $uid, $touch)
{
	$sql = "INSERT INTO `snakescore` (`id`, `score`, `uid`, `touch`) VALUES (NULL, '".mysql_real_escape_string($score)."', '".mysql_real_escape_string($uid)."', '".mysql_real_escape_string($touch)."');";
	mysql_query($sql) or die ("ERROR #043: Query failed: $sql @functions.php - ".mysql_error());
}

function logout()
{
	session_destroy();
	if(isset($_COOKIE['userid']))
	{
		unset($_COOKIE['userid']);
		setcookie("userid", false, time()-3600, '/');
	}		
}

function getAllJSON_user()
{
	$sql = "SELECT * FROM `user`";
	$res = mysql_query($sql) or die ("ERROR #017: Query failed: $sql @functions.php - ".mysql_error());
	while($row = mysql_fetch_object($res))
	{
		echo "{id:\"".$row->id."\",teacher:\"0\",label:\"".$row->prename." ".$row->name."\"},";
	}
}

function getAllJSON_piclist()
{
	$sql = "SELECT * FROM `user` WHERE `stillthere` = 1";
	$res = mysql_query($sql) or die ("ERROR #017: Query failed: $sql @functions.php - ".mysql_error());
	while($row = mysql_fetch_object($res))
	{
		if(!inPiclist($row->id)) echo "{id:\"".$row->id."\",teacher:\"0\",label:\"".$row->prename." ".$row->name."\"},";
	}
}

function getAllJSON_cit()
{
	$sql = "SELECT * FROM `user` WHERE `stillthere` = 1";
	$res = mysql_query($sql) or die ("ERROR #017: Query failed: $sql @functions.php - ".mysql_error());
	while($row = mysql_fetch_object($res))
	{
		echo "{id:\"".$row->id."\",teacher:\"0\",label:\"".$row->prename." ".$row->name."\"},";
	}
	
	$sql = "SELECT * FROM `teacher` ";
	$res = mysql_query($sql) or die ("ERROR #018: Query failed: $sql @functions.php - ".mysql_error());
	while($row = mysql_fetch_object($res))
	{
		echo "{id:\"".$row->id."\",teacher:\"1\",label:\"".$row->prename." ".$row->name."\"},";
	}
}

function getAllJSON()
{
	$sql = "SELECT * FROM `user` WHERE `stillthere` = 1";
	$res = mysql_query($sql) or die ("ERROR #017: Query failed: $sql @functions.php - ".mysql_error());
	while($row = mysql_fetch_object($res))
	{
		echo "{id:\"".$row->id."\",teacher:\"0\",label:\"".$row->prename." ".$row->name."\"},";
	}
	
	$sql = "SELECT * FROM `teacher` WHERE `visible`='1'";
	$res = mysql_query($sql) or die ("ERROR #018: Query failed: $sql @functions.php - ".mysql_error());
	while($row = mysql_fetch_object($res))
	{
		echo "{id:\"".$row->id."\",teacher:\"1\",label:\"".$row->prename." ".$row->name."\"},";
	}
}

function getAllUser($onlyStillThere = true, $orderBy = 'id')
{
	$sql = "SELECT * FROM `user`";
	if($onlyStillThere)
		$sql.=" WHERE `stillthere` = 1 ";
	$sql .= "ORDER BY `".mysql_real_escape_string($orderBy)."` "; //TODO: fix if field is not there
	$res = mysql_query($sql) or die ("ERROR #027: Query failed: $sql @functions.php - ".mysql_error());
	
	$user = array();
	
	while($row = mysql_fetch_object($res))
	{
		array_push($user, $row);
	}
	
	return $user;
}

function getAllTeacher($onlyVisible = true, $orderBy = 'id')
{
	$sql = "SELECT * FROM `teacher`";
	if($onlyStillThere)
		$sql.=" WHERE `visible` = 1 ";
	$sql .= "ORDER BY `".mysql_real_escape_string($orderBy)."` "; //TODO: fix if field is not there
	$res = mysql_query($sql) or die ("ERROR #027: Query failed: $sql @functions.php - ".mysql_error());
	
	$user = array();
	
	while($row = mysql_fetch_object($res))
	{
		array_push($user, $row);
	}
	
	return $user;
}


function listAllUser()
{
	$sql = "SELECT * FROM `user` WHERE `stillthere` = 1";
	$res = mysql_query($sql) or die ("ERROR #027: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<ul>";
	
	while($row = mysql_fetch_object($res))
	{
		echo "<li><a href=\"showuser.php?uid=".$row->id."\">".$row->prename." ".$row->name."</a></li>";
	}
	
	echo "</ul>";
}

function listAllTeacher()
{
	$sql = "SELECT * FROM `teacher` WHERE `visible`='1'";
	$res = mysql_query($sql) or die ("ERROR #027: Query failed: $sql @functions.php");
	
	echo "<ul>";
	
	while($row = mysql_fetch_object($res))
	{
		echo "<li><a href=\"showuser.php?uid=".$row->id."&t=true\">".$row->prename." ".$row->name."</a></li>";
	}
	
	echo "</ul>";
}


function getName($uid, $teacher)
{
	if(strlen((string)$uid) == 0)
		return "unknown";
	$t = "";
	if($teacher==true || $teacher==1 || $teacher=="1")
		$t = "`teacher`";
	else
		$t = "`user`";
	/*echo "<br>".$teacher;
	echo "<br>1:".($teacher==true);
	echo "<br>2:".($teacher=="true");
	echo "<br>3:".($teacher==1);
	echo "<br>4:".($teacher=="1");
	echo "<br>".$t;
	echo "<br>---------------------------------------------------------------------------------";*/
	$sql = "SELECT * FROM $t WHERE `id` = '".$uid."'";
	$res = mysql_query($sql) or die ("ERROR #005: Query failed: $sql @functions.php - ".mysql_error());
	$obj = mysql_fetch_object($res);
	return $obj->prename." ".$obj->name;
}


function setDesign($uid, $teacher, $design)
{
	$t = $teacher===true?"`teacher`":"`user`";
	$sql = "UPDATE $t SET `style` = '$design' WHERE `id` = '".$uid."'";
	$res = mysql_query($sql) or die ("ERROR #040: Query failed: $sql @functions.php - ".mysql_error());
}



function getDesign($uid, $teacher)
{
	if(strlen((string)$uid) == 0)
		return 0;
	$t = $teacher===true?"`teacher`":"`user`";
	$sql = "SELECT * FROM $t WHERE `id` = '".$uid."'";
	$res = mysql_query($sql) or die ("ERROR #040: Query failed: $sql @functions.php - ".mysql_error());
	$obj = mysql_fetch_object($res);
	
	return $obj->style;
}

function getCharContent($id, $teacher)
{
	$t = $teacher===true?"`tchar`":"`uchar`";
	$sql = "SELECT * FROM $t WHERE `id` = '$id'";
	$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @functions.php - ".mysql_error());
	$obj = mysql_fetch_object($res);
	return $obj->content;
}

function getCharPoster($id, $teacher)
{
	$t = $teacher===true?"`tchar`":"`uchar`";
	$sql = "SELECT * FROM $t WHERE `id` = '$id'";
	$res = mysql_query($sql) or die ("ERROR #301: Query failed: $sql @functions.php - ".mysql_error());
	$obj = mysql_fetch_object($res);
	return $obj->from;
}

/*
function updateChar($id, $teacher, $ncontent)
{
	$oldcontent = getCharContent($id, $teacher);
	
	if($oldcontent == $ncontent)
		return;
		
	$t = $teacher===true?"`tchar`":"`uchar`";
	$sql = "UPDATE $t SET `content` = '".mysql_real_escape_string($ncontent)."' WHERE `id` = '$id'";
	mysql_query($sql) or die ("ERROR #030: Query failed: $sql @functions.php - ".mysql_error());
	
	$sql = "INSERT INTO `changes` (`id`, `from`, `cid`, `teacher`, `oldcontent`, `content`) VALUES (NULL, '".$_SESSION['userid']."', '$id', '$teacher', '".mysql_real_escape_string($oldcontent)."', '".mysql_real_escape_string($ncontent)."');";
	mysql_query($sql) or die ("ERROR #030: Query failed: $sql @functions.php - ".mysql_error());
}
*/
function getGossip()
{

	$sql = "SELECT * FROM gossip WHERE `visible` = '1'";
	$res = mysql_query($sql) or die ("ERROR #230: Query failed: $sql @functions.php - ".mysql_error());
	
	if($_SESSION['admin'])
	{
		echo "<form action=\"#\" method=\"POST\"><table width=\"100%\" border=\"1px\">
				<tr>".(!$_SESSION['hidemyass']?"
					<th>Von</th>":"").
					"<th>Inhalt</th>
					<th>L?schen</th>
				</tr>";
		
				
		while($row = mysql_fetch_object($res))
		{
			$e = "";
			//if(intval($row->from) == $_SESSION['userid'])
			//	$e = "<input type=\"hidden\" name=\"editsave\" value=\"huehuehue\"><input width=\"100%\"  type=\"text\" name=\"e_".$row->id."\" value=\"".escape($row->text)."\"><input type=\"submit\" name=\"s_".$row->id."\" value=\"?nderung speichern\">";
			//else
				$e = $row->text;
			echo "<tr>".(!$_SESSION['hidemyass']?"
					<td><a href=\"showuser.php?uid=".$row->by."\">".getName($row->by, 0)."</a></td>":"").
					
					"<td>".$e."</td>
					<td><input type=\"submit\" name=\"id_".$row->id."\" value=\"l?schen\"></td>
				  </tr>";
		}
		
		echo "</table></form>";
	}
	else
	{
		echo "<ul>";	
		
		while($row = mysql_fetch_object($res))
		{
			echo "<li>".$row->text."</li>";
		}
		
		echo "</ul>";
	}
}


/*
function getChars($uid, $teacher)
{
	$EDITMODE = false;

	$t = $teacher===true?"`tchar`":"`uchar`";
	

	
	$sql = "SELECT * FROM $t WHERE `holder` = $uid AND `visible` = '1'";
	$res = mysql_query($sql) or die ("ERROR #004: Query failed: $sql @functions.php - ".mysql_error());
	
	if($uid != $_SESSION['userid'] &&  !$_SESSION['admin'])
	{
	
		echo "<form action=\"#\" method=\"POST\"><table width=\"100%\" border=\"1px\">
				<input type=\"hidden\" name=\"teacher\" value=\"".$teacher."\" />
				<tr>
					<th>Inhalt</th>
				</tr>";
		//$str = "";
		
		
		while($row = mysql_fetch_object($res))
		{
			$e = "";
			if(intval($row->from) == $_SESSION['userid'] && $EDITMODE)
				$e = "<input type=\"hidden\" name=\"editsave\" value=\"huehuehue\"><input width=\"100%\" type=\"text\" name=\"e_".$row->id."\" value=\"".escape($row->content)."\"><input type=\"submit\" name=\"s_".$row->id."\" value=\"?nderung speichern\">";
			else
				$e = $row->content;
				
			echo "<tr>
					<td>".$e."</td>
				  </tr>";	
				  
			
			//$str .= $row->content."; ";
			
		}
		
		//echo $str;
		echo "</table></form>";
	}
	else if($_SESSION['admin'])
	{
		echo "<form action=\"#\" method=\"POST\"><table width=\"100%\" border=\"1px\">
				<input type=\"hidden\" name=\"teacher\" value=\"".$teacher."\" />
				<tr>".(!$_SESSION['hidemyass']?"
					<th>Von</th>":"").
					"<th>Inhalt</th>
					<th>L?schen</th>
				</tr>";
		
		$maxid = 0;
		
				
		while($row = mysql_fetch_object($res))
		{
			$e = "";
			if(intval($row->from) == $_SESSION['userid'] && $EDITMODE )
				$e = "<input type=\"hidden\" name=\"editsave\" value=\"huehuehue\"><input width=\"100%\"  type=\"text\" name=\"e_".$row->id."\" value=\"".escape($row->content)."\"><input type=\"submit\" name=\"s_".$row->id."\" value=\"?nderung speichern\">";
			else
				$e = $row->content;
			echo "<tr>".(!$_SESSION['hidemyass']?"
					<td><a href=\"showuser.php?uid=".$row->from."\">".getName($row->from, 0)."</a></td>":"").
					
					"<td>".$e."</td>
					<td><input type=\"submit\" name=\"id_".$row->id."\" value=\"l?schen\"></td>
				  </tr>";
				  
			if(intval($row->id) > $maxid)
				$maxid = intval($row->id);
		}
		
		if($_SESSION['userid'] == $uid)
			$_SESSION['maxid'] = $maxid;
		
		echo "</table></form>";
	}
	else if($_SESSION['userid'] == $uid && $t == "`uchar`")
	{
		echo "<form action=\"#\" method=\"POST\"><table width=\"100%\" border=\"1px\">
				<input type=\"hidden\" name=\"teacher\" value=\"".$teacher."\" />
				<tr>
					<th>Inhalt</th>
					<th>Löschantrag</th>
				</tr>";
				
		$maxid = 0;
		
		
		while($row = mysql_fetch_object($res))
		{
			if(isRequested($row->id, $teacher))
				$str = getRequestStatus($row->id, $teacher);
			else
				$str = "Grund* : <input type=\"text\" name=\"reason[".$row->id."]\" value=\"\" style=\"width:70%\"><input type=\"submit\" name=\"id_".$row->id."\" value=\"Löschantrag\">";
			echo "<tr>
					<td>".$row->content."</td>
					<td>".$str."</td>
				  </tr>";
				  
				  
			if(intval($row->id) > $maxid)
				$maxid = intval($row->id);
		}
		
		$_SESSION['maxid'] = $maxid;
		
		echo "</table></form>";
	}
}*/

function getCharsCP($uid, $teacher)
{

	$t = $teacher===true?"`tchar`":"`uchar`";
	

	
	$sql = "SELECT * FROM $t WHERE `holder` = $uid AND `visible` = '1'";
	$res = mysql_query($sql) or die ("ERROR #345: Query failed: $sql @functions.php - ".mysql_error());
	
	while($row = mysql_fetch_object($res))
	{
		echo $row->content." • ";
	}
}

function mb_str_replace($needle, $replacement, $haystack)
{
    $needle_len = mb_strlen($needle);
    $replacement_len = mb_strlen($replacement);
    $pos = mb_strpos($haystack, $needle);
    while ($pos !== false)
    {
        $haystack = mb_substr($haystack, 0, $pos) . $replacement
                . mb_substr($haystack, $pos + $needle_len);
        $pos = mb_strpos($haystack, $needle, $pos + $replacement_len);
    }
    return $haystack;
}

function escape($str)
{
	$str = mb_convert_encoding($str, "ISO-8859-1");
	$str = str_replace("&", "&amp;", $str);
	$str = str_replace("Ä", "&Auml;", $str);
	$str = str_replace("Ö", "&Ouml;", $str);
	$str = str_replace("Ü", "&Uuml;", $str);
	$str = str_replace("ä", "&auml;", $str);
	$str = str_replace("ö", "&ouml;", $str);
	$str = str_replace("ü", "&uuml;", $str);
	$str = str_replace("\"", "&quot;", $str);
	$str = str_replace("'", "&#039;", $str);
	$str = str_replace("<", "&lt;", $str);
	$str = str_replace(">", "&gt;", $str);
	$str = str_replace("?", "&szlig;", $str);
	
	return $str;
}


function getNewCount()
{
	if(!isset($_SESSION['maxid']))
		$_SESSION['maxid'] = 0;
		
	$sql = "SELECT COUNT(*) AS c FROM `uchar` WHERE `visible` = 1 AND `holder` = '".$_SESSION['userid']."' AND `id` > '".$_SESSION['maxid']."'";
	$res = mysql_query($sql) or die ("ERROR #032: Query failed: $sql @functions.php - ".mysql_error());
	$obj = mysql_fetch_object($res);
	return $obj->c;
}

function recentChar()
{
	$sql = "SELECT * FROM `uchar` WHERE `visible` = 1 ORDER BY `id` DESC LIMIT 0, 50";
	$res = mysql_query($sql) or die ("ERROR #027: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<p>50 Neuste Posts bei Sch?lern:<br><ul>";
	
	while($row = mysql_fetch_object($res))
	{
		if($_SESSION['admin']&&!$_SESSION['hidemyass'])
			echo "<li>\"".$row->content."\" von <a href=\"showuser.php?uid=".$row->from."\">".getName($row->from, 0)."</a> an <a href=\"showuser.php?uid=".$row->holder."\">".getName($row->holder, 0)."</a></li>";
		else
			echo "<li>\"".$row->content."\" an <a href=\"showuser.php?uid=".$row->holder."\">".getName($row->holder, 0)."</a></li>";
	}
	
	echo "</ul></p>";
	
	$sql = "SELECT * FROM `tchar` ORDER BY `id` DESC LIMIT 0, 50";
	$res = mysql_query($sql) or die ("ERROR #027: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<p>50 Neuste Posts bei Lehrern:<br><ul>";
	
	while($row = mysql_fetch_object($res))
	{
		if($_SESSION['admin']&&!$_SESSION['hidemyass'])
			echo "<li>\"".$row->content."\" von <a href=\"showuser.php?uid=".$row->from."\">".getName($row->from, 0)."</a> an <a href=\"showuser.php?uid=".$row->holder."&t=1\">".getName($row->holder, true)."</a></li>";
		else
			echo "<li>\"".$row->content."\" an <a href=\"showuser.php?uid=".$row->holder."&t=1\">".getName($row->holder, true)."</a></li>";
	}
	
	echo "</ul></p>";
	
}


function listTopDesigns()
{
	$sql = "SELECT COUNT(*) AS c, `style` FROM `user` GROUP BY `style` ORDER BY c DESC";
	$res = mysql_query($sql) or die ("ERROR #042: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<h2>Top Designs:</h2><ol>";
	while($row = mysql_fetch_object($res))
	{
		echo "<li><img width=\"64px\" height=\"64px\" border=\"1px\" src=\"".$row->style.".png\" alt=\"\"> Style #".$row->style.": ".$row->c."</li>";
	}
	echo "</ol>";
}

function listTopSnakePlayer()
{
	$sql = "SELECT COUNT(*) AS c, `uid` FROM `snakescore` GROUP BY `uid` ORDER BY c DESC LIMIT 0, 10";
	$res = mysql_query($sql) or die ("ERROR #044: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<h2>Top 10 Snake Spieler (Anzahl Partien):</h2><ol>";
	while($row = mysql_fetch_object($res))
	{
		echo "<li>".getName($row->uid, 0).": ".$row->c."</li>";
	}
	echo "</ol>";
}

function listTopSnakeScore()
{
	$sql = "SELECT `score`, `uid` FROM `snakescore` WHERE `touch` = '0' ORDER BY `score` DESC LIMIT 0, 10";
	$res = mysql_query($sql) or die ("ERROR #045: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<h2>Top 10 Snake Spieler (Score):</h2><ol>";
	while($row = mysql_fetch_object($res))
	{
		echo "<li>".getName($row->uid, 0).": ".$row->score."</li>";
	}
	echo "</ol>";
}

function listTopSnakeScoreTouch()
{
	$sql = "SELECT `score`, `uid` FROM `snakescore` WHERE `touch` = '1' ORDER BY `score` DESC LIMIT 0, 10";
	$res = mysql_query($sql) or die ("ERROR #046: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<h2>Top 10 Snake Spieler (Score, Touch):</h2><ol>";
	while($row = mysql_fetch_object($res))
	{
		echo "<li>".getName($row->uid, 0).": ".$row->score."</li>";
	}
	echo "</ol>";
}

function listTopSpammer()
{
	$sql = "SELECT COUNT(*) AS c, `from` FROM `uchar` GROUP BY `from` ORDER BY c DESC LIMIT 0, 10";
	$res = mysql_query($sql) or die ("ERROR #033: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<h2>Top 10 Spammer:</h2><ol>";
	while($row = mysql_fetch_object($res))
	{
		echo "<li>".getName($row->from, 0).": ".$row->c."</li>";
	}
	echo "</ol>";
}

function listTopDeleteRequest()
{
	$sql = "SELECT COUNT(*) AS c, `from` FROM `request` WHERE `from` > 0 AND `from` <> 95 GROUP BY `from` ORDER BY c DESC LIMIT 0, 10";
	$res = mysql_query($sql) or die ("ERROR #033: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<h2>Top 10 dern abgegebenen L?schantr?ge:</h2><ol>";
	while($row = mysql_fetch_object($res))
	{
		echo "<li>".getName($row->from, 0).": ".$row->c."</li>";
	}
	echo "</ol>";
}

function listTopDeleted()
{
	$sql = "SELECT COUNT(*) AS c, `from` FROM `uchar` WHERE `visible` = 0 AND `from` > 0 AND `from` <> 95  GROUP BY `from` ORDER BY c DESC LIMIT 0, 10";
	$res = mysql_query($sql) or die ("ERROR #033: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<h2>Top 10 gel?schte Beitr?ge geschrieben:</h2><ol>";
	while($row = mysql_fetch_object($res))
	{
		echo "<li>".getName($row->from, 0).": ".$row->c."</li>";
	}
	echo "</ol>";
}

function listTopSpammed()
{
	$sql = "SELECT COUNT(*) AS c, `holder` FROM `uchar` GROUP BY `holder` ORDER BY c DESC LIMIT 0, 10";
	$res = mysql_query($sql) or die ("ERROR #033: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<h2>Top 10 zugespammte:</h2><ol>";
	while($row = mysql_fetch_object($res))
	{
		echo "<li><a href=\"showuser.php?uid=".$row->holder."\">".getName($row->holder, 0)."</a>: ".$row->c."</li>";
	}
	echo "</ol>";
}

function brauchtLiebe()
{
	$sql = "SELECT COUNT(*) AS c, `holder` FROM `uchar` INNER JOIN `user` ON `uchar`.`holder`=`user`.`id` AND `user`.`stillthere`=1 AND `uchar`.`visible`=1  GROUP BY `holder` ORDER BY c ASC LIMIT 30;";
	$res = mysql_query($sql) or die ("ERROR #033: Query failed: $sql @functions.php - ".mysql_error());
	$obj = mysql_fetch_object($res);
	$r = rand(0, 29);
	$i = 0;
	while($row = mysql_fetch_object($res))
	{
		if($i == $r)
		{
			echo "<div id=\"liebe\">Die Charakteristik von <a href=\"showuser.php?uid=".$row->holder."\">".getName($row->holder, 0)."</a> braucht Liebe... Trage doch etwas dazu bei :)</div>";
			break;
		}
		$i++;
	}
	
}

function listLessSpammed()
{
	$sql = "SELECT COUNT(*) AS c, `holder` FROM `uchar` INNER JOIN `user` ON `uchar`.`holder`=`user`.`id` AND `user`.`stillthere`=1 AND `uchar`.`visible`=1  GROUP BY `holder` ORDER BY c ASC LIMIT 0,10;";
	$res = mysql_query($sql) or die ("ERROR #033: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<h2>Top 10 mit den wenigsten Beitr?gen:</h2><ol>";
	while($row = mysql_fetch_object($res))
	{
		echo "<li><a href=\"showuser.php?uid=".$row->holder."\">".getName($row->holder, 0)."</a>: ".$row->c."</li>";
	}
	echo "</ol>";
}

function hasVoted($uid, $pid)
{
	$sql = "SELECT * FROM pollvotes WHERE `pollid` = '".$pid."' AND `voter` = '".$uid."'";
	$res = mysql_query($sql) or die ("ERROR #035: Query failed: $sql @functions.php - ".mysql_error());
	
	//$obj = mysql_fetch_object($res);
	
	
	
	return mysql_num_rows($res) > 0;
}

function getVote($uid, $pid)
{
	$sql = "SELECT * FROM pollvotes WHERE `pollid`='".$pid."' AND `voter`='".$uid."'";
	$res = mysql_query($sql) or die ("ERROR #035: Query failed: $sql @functions.php - ".mysql_error());
	
	$obj = mysql_fetch_object($res);
	
	
	
	return $obj->voteid;
}

function getVotesBy($pid, $uid)
{
	$sql = "SELECT * FROM pollvotes WHERE `pollid`='".$pid."' AND `voter`='".$uid."'";
	$res = mysql_query($sql) or die ("ERROR #035: Query failed: $sql @functions.php - ".mysql_error());
	
	$votes = array();
	while($row = mysql_fetch_array($res))
	{
		array_push($votes, $row);
	}
	
	return $votes;
}

function getAllPolls()
{
	$sql = "SELECT * FROM polls ORDER BY `closed` ASC";
	$res = mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
	
	$polls = array();
	while($row = mysql_fetch_array($res))
	{
		$row['voted'] = hasVoted($_SESSION['userid'], $row['id']);
		array_push($polls, $row);
	}
	
	return $polls;
}

function getPoll($id)
{
	$sql = "SELECT * FROM polls WHERE `id` = '".$id."'";
	$res = mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
	
	$poll = mysql_fetch_array($res);
	$poll['voted'] = hasVoted($_SESSION['userid'], $poll['id']);
	
	return $poll;
}
/*
function listAllPolls()
{
	$sql = "SELECT * FROM polls ORDER BY `closed` ASC";
	$res = mysql_query($sql) or die ("ERROR #036: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<h2>Unbeantwortete Umfragen:</h2><ul>";
	while($row = mysql_fetch_object($res))
	{
		if(!hasVoted($_SESSION['userid'], $row->id))
		{
			echo "<li><a href=\"showpoll.php?pollid=".$row->id."\">".$row->title."</a></li>";
		}
	}
	echo "</ul>";
	
	
	$sql = "SELECT * FROM polls ORDER BY `closed` ASC";
	$res = mysql_query($sql) or die ("ERROR #036: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<h2>Beantwortete Umfragen:</h2><ul>";
	while($row = mysql_fetch_object($res))
	{
		if(hasVoted($_SESSION['userid'], $row->id))
		{
			echo "<li><a href=\"showpoll.php?pollid=".$row->id."\">".$row->title."</a></li>";
		}
	}
	echo "</ul>";
	
	
}*/

function getAnswersComplete($pid)
{
	$poll = getPoll($pid);
	if($poll['type'] != 3)
		return -1;
	
	$answers = array();
	$sql = "SELECT * FROM pollanswers WHERE `pollid`='".$pid."' ;";
	$res = mysql_query($sql) or die ("ERROR #1302: Query failed: $sql @functions.php - ".mysql_error());
	while($row = mysql_fetch_array($res))
	{
		array_push($answers, $row);
	}
	
	return $answers;
}

function getAnswers($pid)
{
	$poll = getPoll($pid);
	
	$answers = array();
	if($poll['type'] == 0)
	{
		$sql = "SELECT * FROM user WHERE `stillthere`='1';";
		$res = mysql_query($sql) or die ("ERROR #1302: Query failed: $sql @functions.php - ".mysql_error());

		while($obj = mysql_fetch_object($res))
		{
			$answers[$obj->id] =  $obj->prename." ".$obj->name;
		}

	}
	else if($poll['type'] == 1)
	{
		$sql = "SELECT * FROM teacher WHERE `visible`='1';";
		$res = mysql_query($sql) or die ("ERROR #1302: Query failed: $sql @functions.php - ".mysql_error());
		
		while($obj = mysql_fetch_object($res))
		{
			$answers[$obj->id] =  $obj->prename." ".$obj->name;
		}
	}
	else if($poll['type'] == 2)
	{
		$answers['0'] = "Ja"; 
		$answers['1'] = "Nein"; 
	}
	else
	{
		$sql = "SELECT * FROM pollanswers WHERE `pollid`='".$pid."' ;";
		$res = mysql_query($sql) or die ("ERROR #1302: Query failed: $sql @functions.php - ".mysql_error());
		while($obj = mysql_fetch_object($res))
		{
			$answers[$obj->voteid] = $obj->text;
		}
	}
	
	return $answers;
}

function getAnswerText($pid, $vid)
{
	$poll = getPoll($pid);
	
	if($poll['type'] == 0)
	{
		$sql = "SELECT * FROM user WHERE `id`='".$vid."';";
		$res = mysql_query($sql) or die ("ERROR #1302: Query failed: $sql @functions.php - ".mysql_error());
		$obj = mysql_fetch_object($res);
		
		return $obj->prename." ".$obj->name;
	}
	else if($poll['type'] == 1)
	{
		$sql = "SELECT * FROM teacher WHERE `id`='".$vid."';";
		$res = mysql_query($sql) or die ("ERROR #1302: Query failed: $sql @functions.php - ".mysql_error());
		$obj = mysql_fetch_object($res);
		
		return $obj->prename." ".$obj->name;
	}
	else if($poll['type'] == 2)
	{
		return $vid==0?"Ja":"Nein";
	}
	else
	{
		$sql = "SELECT * FROM pollanswers WHERE `pollid`='".$pid."' AND `voteid`='".$vid."';";
		$res = mysql_query($sql) or die ("ERROR #1302: Query failed: $sql @functions.php - ".mysql_error());
		$obj = mysql_fetch_object($res);
		
		return $obj->text;
	}
}

function showpoll($pid)
{
	
	$sql = "SELECT * FROM polls WHERE `id`='".$pid."'";
	$res = mysql_query($sql) or die ("ERROR #037: Query failed: $sql @functions.php - ".mysql_error());
	$obj = mysql_fetch_object($res);
	
	
	
	
	
	if($obj->multiselect == 1)
		$box = "checkbox";
	else
		$box = "radio";
		
	echo "<h2>".$obj->title."</h2>";
	if ($pid > 1) { 
		echo "<a href=\"showpoll.php?pollid=".($pid-1)."\" >Vorherige Umfrage</a><br><br>";
	} 
	if ($pid < mysql_fetch_object(mysql_query("SELECT COUNT(*) AS c FROM `polls`;"))->c) { 
		echo "<a  href=\"showpoll.php?pollid=".($pid+1)."\" >N?chste Umfrage</a><br><br>";
	} 
	echo "<a href=\"index.php\">Men?</a><br><br></p>";
	
	$hv = true;//hasVoted($_SESSION['userid'], $pid);
	
	if($_SESSION['admin'] || $hv)
	{
		if($_SESSION['admin']) echo "<p>Bos(s)hafte Admins sehen das Ergebnis auch ohne zu voten! <br></p>";
		echo "<img src=\"thecakeisalie.php?as=true&pollid=".$pid."\" alt=\"ergebnis\" />";
		echo "<p>Ergebnis:<br><ul>";
		$sql = "SELECT COUNT(*) as c, voteid FROM pollvotes WHERE `pollid`='".mysql_real_escape_string($_GET['pollid'])."' GROUP BY voteid ORDER BY c DESC LIMIT 10";
		$res = mysql_query($sql) or die ("ERROR #420: Blaze it: $sql @functions.php - ".mysql_error());
		$i = 1;
		while($row = mysql_fetch_object($res))
		{
			
			$sql = "SELECT * FROM pollanswers WHERE `pollid`=".mysql_real_escape_string($pid)." AND `voteid`=".$row->voteid."";
			$r = mysql_query($sql) or die ("ERROR #033: Query failed: $sql @thecakeisalie.php - ".mysql_error());
			
			$o = mysql_fetch_object($r);
			
			echo "<li>".$i.": ".$o->text."&nbsp;( ".$row->c." )</li>";
			//echo "<li>".$i.": ".getAnswerText($pid, $row->voteid)."&nbsp;( ".$row->c." )</li>";
			$i++;
			
		}
		echo "</p></ul>";
	}
	else
	{
		echo "<h3>Vote um das Ergebnis zu sehen...</h3>";
	}
	
	
	if(!$hv)
	{
		echo "<form action=\"#\" method=\"POST\"><table>";
			
		$sql = "SELECT COUNT(*) AS c FROM pollanswers WHERE `pollid`='".$pid."'";
		$res = mysql_query($sql) or die ("ERROR #338: Query failed: $sql @functions.php - ".mysql_error());
		$c = mysql_fetch_object($res)->c;
			
		$col = intval(min(5,round(sqrt($c))));
			
		$sql = "SELECT * FROM pollanswers WHERE `pollid`='".$pid."'";
		$res = mysql_query($sql) or die ("ERROR #038: Query failed: $sql @functions.php - ".mysql_error());
		
		$vid = getVote($_SESSION['userid'], $pid);
			
		$ccol = 0;
		while($row = mysql_fetch_object($res))
		{
			if(($ccol % $col) == 0 ) echo "</tr><tr>";
			
			if($vid == $row->voteid && $obj->multiselect == 0) echo "<td><input type=\"".$box."\" name=\"vote[]\" value=\"".$row->voteid."\" checked>".$row->text."</td>";	
			else echo "<td><input type=\"".$box."\" name=\"vote[]\" value=\"".$row->voteid."\">".$row->text."</td>";
			$ccol++;
		}
			
		echo "</td>";
		
		for($i = ($ccol % $col); $i > 0; $i--)
		{
			echo "<tr></tr>";
		}
			
		echo "</table><input type=\"submit\" name=\"voted\" value=\"Vote!\"><input type=\"hidden\" name=\"pid\" value=\"".$pid."\"></from>";
	}
	else
	{
		echo "Sorry, Editierfunktion wurde von der Jahrgangsf?hrung verboten... Hate bitte gegen sie richten :) ~Josh <br>";
	}
}
/*
function vote($pid, $answers)
{
	if(hasVoted($_SESSION['userid'], $pid))
	{	
		$sql = "UPDATE pollvotes SET `voteid`='".$answers[0]."' WHERE `voter`='".$_SESSION['userid']."';";
		mysql_query($sql) or die ("ERROR #028: Query failed: $sql @functions.php - ".mysql_error());
	}
	else
	{
		foreach($answers as $vote)
		{
			$sql = "INSERT INTO pollvotes (`id`, `pollid`, `voteid`, `voter`) VALUES (NULL, '".$pid."', '".$vote."', '".$_SESSION['userid']."')";
			mysql_query($sql) or die ("ERROR #028: Query failed: $sql @functions.php - ".mysql_error());
		}
	}
}*/
/*
function closePoll($id)
{
	$sql = "UPDATE polls SET `closed` = '1' WHERE `id`='".$id."'";
	mysql_query($sql) or die ("ERROR #027: Query failed: $sql @functions.php - ".mysql_error());
}*/

/*function addAllUserPoll($title, $multivote, $finalchoice)
{
	$answers = array();
	array_push();
	
}*/
/*
function addPoll($title, $answers, $multivote, $finalchoice)
{
	$sql = "INSERT INTO polls (`id`, `by`, `title`, `closed`, `multivote`, `finalchoice`) VALUES (NULL, '".$_SESSION['userid']."', '".mysql_real_escape_string(trim($title))."', '0', '".$multivote."', '".$finalchoice."')";
	mysql_query($sql) or die ("ERROR #024: Query failed: $sql @functions.php - ".mysql_error());
	$sql = "SELECT * FROM polls ORDER BY `id` DESC LIMIT 1";
	$res = mysql_query($sql) or die ("ERROR #025: Query failed: $sql @functions.php - ".mysql_error());
	
	$pit = mysql_fetch_object($res)->id;
	
	$i = 0;
	foreach($answers as $a)
	{
		$sql = "INSERT INTO pollanswers (`id`, `pollid`, `text`, `voteid`) VALUES (NULL, '".$pit."', '".mysql_real_escape_string(trim($a))."', '".$i."')";
		mysql_query($sql) or die ("ERROR #026: Query failed: $sql @functions.php - ".mysql_error());
		$i++;
	}
	
	$sql = "SELECT * FROM polls ORDER BY `id` DESC LIMIT 1";
	$res = mysql_query($sql) or die ("ERROR #039: Query failed: $sql @functions.php - ".mysql_error());
	return mysql_fetch_object($res)->id;
	
}
*/

/*
function addChar($to, $teacher, $content)
{
	$t = $teacher===true?"`tchar`":"`uchar`";
	$sp = explode("|",$content);
	foreach($sp as $str)
	{
		if(strlen($str) > 0)
		{
			$sql = "INSERT INTO $t (`id`, `from`, `holder`, `content`) VALUES (NULL, '".$_SESSION['userid']."', '$to', '".mysql_real_escape_string(trim($str))."');";
			mysql_query($sql) or die ("ERROR #016: Query failed: $sql @functions.php - ".mysql_error());
		}
	}
}*/

/*
function deleteChar($id, $teacher)
{
	$t = $teacher===true?"`tchar`":"`uchar`";
	$sql = "UPDATE $t SET `visible` = '0' WHERE `id` = $id";
	mysql_query($sql) or die ("ERROR #015: Query failed: $sql @functions.php - ".mysql_error());
	
	if(isRequested($id, $teacher))
		closeRequest(getRequestId($id, $teacher));
	else
	{
		$sql = "INSERT INTO `request` (`id`, `from`, `type`, `teacher`, `cid`, `message`, `closed`, `status`, `eby`) VALUES (NULL, '0', '2', '$teacher', '$id', '', '1', 'von admin gel?scht', '".$_SESSION['userid']."');";
		mysql_query($sql) or die ("ERROR #023: Query failed: $sql @functions.php - ".mysql_error());
	}
}*/

function changePassword($oldpass, $newpass)
{
	$sql = "SELECT * FROM `user` WHERE `id` = '".$_SESSION['userid']."' LIMIT 1";
	$res = mysql_query($sql) or die ("ERROR #024: Query failed: $sql @functions.php - ".mysql_error());
	$obj = mysql_fetch_object($res);
	
	if($obj->password == md5($oldpass))
	{
		$sql = "UPDATE `user` SET `password` = '".md5($newpass)."' WHERE `id` = '".$_SESSION['userid']."';";
		mysql_query($sql) or die ("ERROR #025: Query failed: $sql @functions.php - ".mysql_error());
		return true;
	}
	else
	{
		return false;
	}
}

/*
function addRequest($id, $teacher, $text)
{
	$sql = "INSERT INTO `request` (`id`, `from`, `type`, `teacher`, `cid`, `message`, `closed`, `status`, `eby`) VALUES (NULL, '".$_SESSION['userid']."', '1', '$teacher', '$id', '".mysql_real_escape_string($text)."', '0', 'gemeldet', '0');";
	mysql_query($sql) or die ("ERROR #023: Query failed: $sql @functions.php - ".mysql_error());
}*/

function isRequested($id, $type)
{
	$sql = "SELECT * FROM `request` WHERE `cid` = '$id' AND `type` = '$type'";
	$res = mysql_query($sql) or die ("ERROR #006: Query failed: $sql @functions.php - ".mysql_error());
	if(mysql_fetch_object($res))
		return true;
	else
		return false;
}

function getRequestStatus($id, $type)
{
	$sql = "SELECT * FROM `request` WHERE `cid` = '$id' AND `type` = '$type'";
	$res = mysql_query($sql) or die ("ERROR #006: Query failed: $sql @functions.php - ".mysql_error());
	if($obj = mysql_fetch_object($res))
		return $obj->status;
	else
		return false;
}

function getRequestId($id, $type)
{
	$sql = "SELECT * FROM `request` WHERE `cid` = '$id' AND `type` = '$type'";
	$res = mysql_query($sql) or die ("ERROR #006: Query failed: $sql @functions.php - ".mysql_error());
	if($obj = mysql_fetch_object($res))
		return $obj->id;
	else
		return false;
}



function getRequestsByUser($uid)
{
	$sql = "SELECT * FROM `request` WHERE `from` = $uid";
	$res = mysql_query($sql) or die ("ERROR #008: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<table width=\"300px\" border=\"1px\">
			<tr>
				<th>Id</th>
				<th>Von</th>
				<th>Inhalt</th>
				<th>Status</th>
			<tr>";
			
	while($row = mysql_fetch_object($res))
	{
		echo "<tr>
				<td><a href=\"showrequest.php?id=".$row->id."\">".md5($row->id)."</a></td>
				<td>".getName($row->from, 0)."</td>
				<td>".getCharContent($row->cid, $row->teacher)."</td>
				<td>".$row->status."</td>
			  </tr>";
	}
	
	echo "</table>";
}

function getRequest($id)
{
	$sql = "SELECT * FROM `request` WHERE `id` = $id";
	$res = mysql_query($sql) or die ("ERROR #021: Query failed: $sql @functions.php - ".mysql_error());
	
	if($_SESSION['admin'])
	{
		echo "<form action=\"#\" method=\"POST\"><table width=\"100%\" border=\"1px\">
				<tr>
					<th>Id</th>
					<th>Von</th>
					<th>Inhalt</th>
					<th>Grund</th>
					<th>Status</th>
					<th>Aktion</th>
					<th>bearbeitet von </th>
				<tr>";
				
		if($obj = mysql_fetch_object($res))
		{
			$str = "-";
			
			if($obj->closed == 0)
				$str = "<input type=\"submit\" name=\"c_".$obj->id."\" value=\"bearbeiten\"><input type=\"submit\" name=\"i_".$obj->id."\" value=\"ignorieren\">";
			
			echo "<tr>
					<td><a href=\"showrequest.php?id=".$obj->id."\">".md5($obj->id)."</a></td>
					<td>".getName($obj->from, 0)."</td>
					<td>".getCharContent($obj->cid, $obj->teacher)."</td>
					<td>".$obj->message."</td>
					<td>".$obj->status."</td>
					<td>".$str."</td>
					<td>".($obj->eby!=0?getName($obj->eby,0):"-")."</td>
				  </tr>";
		}
		
		echo "</table><form>";
	}
	else
	{
		echo "<table width=\"100%\" border=\"1px\">
				<tr>
					<th>Id</th>
					<th>Von</th>
					<th>Inhalt</th>
					<th>Grund</th>
					<th>Status</th>
					<th>Bearbeitet von</th>
				<tr>";
				
		if($obj = mysql_fetch_object($res))
		{
			echo "<tr>
					<td><a href=\"showrequest.php?id=".$obj->id."\">".md5($obj->id)."</a></td>
					<td>".getName($obj->from, 0)."</td>
					<td>".getCharContent($obj->cid, $obj->teacher)."</td>
					<td>".$obj->message."</td>
					<td>".$obj->status."</td>
					<td>".($obj->eby!=0?getName($obj->eby,0):"-")."</td>
				  </tr>";
		}
		
		echo "</table>";
	}
	
	
}

function getGossipContent($id)
{
	$sql = "SELECT * FROM `gossip` WHERE `id`='".$id."'; ";
	$res = mysql_query($sql) or die ("ERROR #188: Query failed: $sql @functions.php - ".mysql_error());
	return mysql_fetch_object($res)->content;
}

function getCitContent($id)
{
	$sql = "SELECT * FROM `cit` WHERE `id`='".$id."'; ";
	$res = mysql_query($sql) or die ("ERROR #188: Query failed: $sql @functions.php - ".mysql_error());
	return mysql_fetch_object($res)->content;
}

function closeRequest($id)
{	
	$sql = "SELECT * FROM `request` WHERE `id` = $id";
	$res = mysql_query($sql) or die ("ERROR #011: Query failed: $sql @functions.php - ".mysql_error());
	$obj = mysql_fetch_object($res);
	$t = "";
	if($obj->type=="0")
		$t = "uchar";
	else if($obj->type=="1")
		$t = "tchar";
	else if($obj->type=="2")
		$t = "cit";
	else if($obj->type=="3")
		$t = "gossip";
	$i = $obj->cid;
	$sql = "UPDATE $t SET `visible` = '0' WHERE `id` = $i";
	mysql_query($sql) or die ("ERROR #012: Query failed: $sql @functions.php - ".mysql_error());

	$sql = "UPDATE `request` SET `closed` = 1, `status` = 'geschlossen', `eby` = '".$_SESSION['userid']."' WHERE `id` = $id";
	mysql_query($sql) or die ("ERROR #013: Query failed: $sql @functions.php - ".mysql_error());	
}

function ignoreRequest($id)
{
	$sql = "UPDATE `request` SET `closed` = 1, `status` = 'verworfen', `eby` = '".$_SESSION['userid']."' WHERE `id` = $id";
	mysql_query($sql) or die ("ERROR #022: Query failed: $sql @functions.php - ".mysql_error());	
}


function addPiclist($uid2)
{
	if(inPiclist($_SESSION['userid']))
	{
		echo "<font style=\"color: #FF0000; font-size: 72px;\">".getName($_SESSION['userid'], 0)." IST BEREITS IN DER LISTE! W?HLE JEMAND ANDEREN</font>";
		return;
	}
	else if(inPiclist($uid2))
	{
		echo "<font style=\"color: #FF0000; font-size: 72px;\">".getName($uid2, 0)." IST BEREITS IN DER LISTE! W?HLE JEMAND ANDEREN</font>";
		return;
	}
	$sql = "INSERT INTO `piclist` (`id`, `uid1`, `uid2`) VALUES (NULL, '".$_SESSION['userid']."', '".$uid2."');";
	$res = mysql_query($sql) or die ("ERROR #666: Query failed: $sql @functions.php - ".mysql_error());
}

function showPiclist()
{
	$sql = "SELECT * FROM `piclist`;";
	$res = mysql_query($sql) or die ("ERROR #555: Query failed: $sql @functions.php - ".mysql_error());
	
	echo "<ul>";
	while($row = mysql_fetch_object($res))
	{
		echo "<li>".getName($row->uid1, false)." mit ".getName($row->uid2, false)."</li>";
	}
	echo "</ul>";
	
}

function piclistDeleteMe()
{
	$sql = "DELETE FROM `piclist` WHERE `uid1`='".$_SESSION['userid']."' OR `uid2`='".$_SESSION['userid']."';";
	mysql_query($sql) or die ("ERROR #418: I'm a teapot: $sql @functions.php - ".mysql_error());
}

function fillIt()
{
	$sql = "SELECT COUNT(*) AS c, `info`.* FROM `info` WHERE `uid`='".$_SESSION['userid']."';";
	$res = mysql_query($sql) or die ("ERROR #151: Query failed: $sql @functions.php - ".mysql_error());
	$o = mysql_fetch_object($res); 
	
	
	if(strlen($o->lk1) <= 0 || strlen($o->lk2) <= 0 || strlen($o->lk3) <= 0 || strlen($o->year) <= 0)
	{
		echo "<h2><font style=\"font-size:48px; color:#FF0000;\">Bitte Steckbrief ausfüllen</font></h2>";
	}
}

?>