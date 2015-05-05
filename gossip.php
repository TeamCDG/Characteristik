<?php
include("loginprotection.php");


if(isset($_POST['search']))
{
	addGossip($_POST['cit']);
}


foreach(array_keys($_POST) as $name)
{
	if(strpos($name, "id_") !== false)
	{
		$id = str_replace("id_", "", $name);
		deleteGossip($id);
	}
}

?>

<html>
	<head>
		<title>Man munkelt...</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
	</head>
	<body>
		<h1>Man munkelt...</h1>
		<!-- <p> <br><br>Hinzufügen: <br>
		<form id="searchform" action="#" method="post">
			<table width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<td align="left"><input type="text" name="cit" size="100%"></td>
				</tr>				
				<tr>
					<td><input type="submit" name="search" value="hinzufügen" style="width:29%"></td>
				</tr>
			<table>
		</form>
		</p>
		<br>
		<br> -->
		<h2> Man munkelt, ... </h2>
		<?php getGossip(); ?>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>