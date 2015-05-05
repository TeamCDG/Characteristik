<?php
include("loginprotection.php");
header('Content-Type: text/html; charset=utf-8');
getCharsCP($_GET['uid'], ($_GET['t']=="true"||$_GET['t']==1));
?>