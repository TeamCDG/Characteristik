<?php 
$c = mysql_connect("localhost:3306","sbolt_char","schwerdainbolt") or die ("ERROR #001: No connection possible @connect.php");
mysql_select_db("sbolt_char") or die ("ERROR #002: Database nonexistent @connect.php");
mysql_set_charset('utf8');
mysql_query("SET NAMES 'utf8'");


$settings = mysql_fetch_object(mysql_query("SELECT * FROM `settings` LIMIT 1"));
$res = mysql_query("SELECT * FROM `backup` ORDER BY `id` DESC LIMIT 1") or die ("ERROR #LEL: Query failed: $sql @connect.php - ".mysql_error());;
$b = mysql_fetch_object($res);

include("backup.php");
	$bres = backup($settings->backup_compression, $settings->backup_send_mail, $settings->backup_email, $settings->backup_folder, "sbolt_char", $c);
	$sql = "INSERT INTO `backup` VALUES (NULL, NULL, '".((int)$bres['sentmail'])."', '".$bres['mailreciever']."', '".$bres['filename']."', '0');";
	//mysql_query($sql) or die ("ERROR #LEL: Query failed: $sql @connect.php - ".mysql_error());


?>