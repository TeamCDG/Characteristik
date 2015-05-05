<?php include("connect.php"); ?>

<?php // VERSION 0.2.0
$sql = "ALTER TABLE `user` ADD `lastseen` INT NOT NULL ;";
mysqli_multi_query($mysqli, $sql) or die("ERROR 999: multi query failed: $sql".mysqli_error());
?>