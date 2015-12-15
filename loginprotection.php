<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ob_start();
$_SESSION['mobile']= (bool)preg_match('#\b(ip(hone|od|ad)|android|opera m(ob|in)i|windows (phone|ce)|blackberry|tablet'.
                    '|s(ymbian|eries60|amsung)|p(laybook|alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
                    '|mobile|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT'] );
$version="0.2.0";
include("functions.php");
header('Content-Type: text/html; charset=utf-8');
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."config/settingsreader.php");
if(!isset($_SESSION['userid']) && !cookieLogin())
{
	header('Location: '.$rootfolder.'login.php');
	exit;
}
else
{
	if(!isset($_SESSION['maxid']))
	{
		$_SESSION['maxid'] = getMaxId($_SESSION['userid']);
	}

	
	if(!isset($_COOKIE['hidemyass']))
	{
		setcookie("hidemyass", 0, time() + 60*60*24*3000, '/');
		$_SESSION['hidemyass'] = false;
	}
	else
	{
		$_SESSION['hidemyass'] = $_COOKIE['hidemyass'] == 1;
	}
	
	if(!isset($_COOKIE['debug']))
		{
			setcookie("debug", 0, time() + 60*60*24*3000, '/');
			$_SESSION['debug'] = false;
		}
		else
		{
			$_SESSION['debug'] = $_COOKIE['debug'] == 1;
		}
	
	if(!isset($_SESSION['style']))
	{
		$_SESSION['style'] = getDesign($_SESSION['userid'], 0);
	}
	
}

$res = mysql_query("SELECT * FROM `backup` ORDER BY `id` DESC LIMIT 1") or die ("ERROR #LEL: Query failed: $sql @connect.php - ".mysql_error());;
$b = mysql_fetch_object($res);

if( (strtotime("now")-strtotime($b->date))/3600 > intval($_SESSION['backup_delta']))
{
	include("backup.php");
	$bres = @backup($_SESSION['backup_compression'], $_SESSION['backup_send_mail'], $_SESSION['backup_email'], $_SESSION['backup_folder'], $dbname, $c);
	$sql = "INSERT INTO `backup` VALUES (NULL, NULL, '".((int)$bres['sentmail'])."', '".$bres['mailreciever']."', '".$bres['filename']."', '0', '0');";
	mysql_query($sql) or die ("ERROR #LEL: Query failed: $sql @connect.php - ".mysql_error());
}

function getMaxId($id)
{
	$sql = "SELECT * FROM `user` WHERE `id`='".mysql_real_escape_string($id)."';";
	$res = mysql_query($sql) or die("ERROR 418: Query failed: ".$sql." ".mysql_error());
	return mysql_fetch_object($res)->lastseen;
}
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."config/permissionreader.php");
?>