
<?php
	include("functions.php");
	$sql = "INSERT INTO `pizza` (`id`, `pizza`) VALUES (NULL, '".mysql_real_escape_string($_POST['pizza'])."');";
	mysql_query($sql);
?>

<form action="#" method="POST">
	<input typ="text" name="pizza">
</form>