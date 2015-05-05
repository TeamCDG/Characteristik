<?php
//57-80
include("connect.php");

$sql = "SELECT * FROM `teacher` WHERE `visible`=0";
$res = mysql_query($sql) or die ("ERROR #938: Query failed: $sql @functions.php - ".mysql_error());

$allt = array();
while($row = mysql_fetch_object($res))
{
	array_push($allt, $row->prename." ".$row->name);
}

$minid = 47;
for($i = 57; $i <= 80; $i++)
{
	
	for($idx = 0; $idx < sizeof($allt); $idx++)
	{
		$sql = "INSERT INTO `pollanswers`(`id`, `pollid`, `text`, `voteid`) VALUES (NULL,'".$i."','".$allt[$idx]."','".($minid+$idx)."');";
		mysql_query($sql) or die ("ERROR #930: Query failed: $sql @functions.php - ".mysql_error());
	}
}
?>