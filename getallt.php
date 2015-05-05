<?php
include("loginprotection.php");
$sql = "SELECT * FROM `teacher`";
$res = mysql_query($sql) or die ("ERROR #420 (blaze it): Query failed: $sql @functions.php - ".mysql_error());
	
	
	
	while($row = mysql_fetch_object($res))
	{
		echo "<tr>\n";
		echo "	<td align=\"left\"><input type=\"text\" name=\"answers[]\" value=\"".escape($row->prename)." ".escape($row->name)."\" style=\"width:100%\"></td>\n";
		echo "</tr>\n";
	}
	
	
?>