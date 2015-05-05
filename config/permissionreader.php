<?php
if($_SESSION['userid'] == 0)
{
	$sql = "SELECT * FROM `permissions` LIMIT 1;";
	$res = mysql_query($sql) or die ("ERROR #011: Query failed: $sql @permissionreader - ".mysql_error());

	$permissions = mysql_fetch_array($res);


	foreach(array_keys($permissions) as $key)
	{
		
		if($key != "name" && $key != "id")
			$permissions[$key] = true;
	}

	$_SESSION['permissions'] = $permissions;
}
else
{
	$sql = "SELECT * FROM `user` WHERE `id` = '".$_SESSION['userid']."';";
	$res = mysql_query($sql) or die ("ERROR #010: Query failed: $sql @permissionreader - ".mysql_error());
	$obj = mysql_fetch_object($res);

	$sql = "SELECT * FROM `permissions` WHERE `id` = '".$obj->group."';";
	$res = mysql_query($sql) or die ("ERROR #011: Query failed: $sql @permissionreader - ".mysql_error());

	$permissions = mysql_fetch_array($res);


	foreach(array_keys($permissions) as $key)
	{
		
		if($key != "name" && $key != "id")
			$permissions[$key] = $permissions[$key] == "1";
	}

	$_SESSION['permissions'] = $permissions;
}

?>