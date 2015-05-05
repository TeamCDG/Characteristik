<?php
	include("loginprotection.php");
	$sql = "SELECT * FROM `info`;";
	$res = mysql_query($sql);
	
	$sql2 = "SELECT * FROM `user` WHERE `stillthere`=1;";
	$res2 = mysql_query($sql);
	
	$uids = array();
	
	while($row = mysql_fetch_object($res))
	{
		array_push($uids, intval($row->uid));
	}
	
	while($row2 = mysql_fetch_object($res2))
	{
		if($row2->id == 47 || $row2->id == 95)
			continue;
		if(!in_array(intval($row2->id), $uids))
			echo $row2->id.": ".getName($row2->id, false)."<br>\n";
	}
?>