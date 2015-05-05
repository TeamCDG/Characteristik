<?php
session_start();

ob_start();
$_SESSION['mobile']= (bool)preg_match('#\b(ip(hone|od|ad)|android|opera m(ob|in)i|windows (phone|ce)|blackberry|tablet'.
                    '|s(ymbian|eries60|amsung)|p(laybook|alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
                    '|mobile|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT'] );
include("functions.php");
header('Content-Type: text/html; charset=utf-8');
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."config/settingsreader.php");
if(!isset($_SESSION['userid']) && !cookieLogin())
{
	header('Location: login.php');
	exit;
}
else
{
	if(!isset($_COOKIE['maxid']))
	{
		if(!isset($_SESSION['maxid']))
		{
			setcookie("maxid", 0, time() + 60*60*24*3000, '/');
		}
		else
		{
			setcookie("maxid", $_SESSION['maxid'], time() + 60*60*24*3000, '/');
		}
	}
	else if(intval($_COOKIE['maxid']) <  intval($_SESSION['maxid']))
	{
		setcookie("maxid", $_SESSION['maxid'], time() + 60*60*24*3000, '/');
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


include($_SERVER['DOCUMENT_ROOT'].$rootfolder."config/permissionreader.php");
?>