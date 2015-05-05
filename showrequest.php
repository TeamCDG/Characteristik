<?php
include("loginprotection.php");
foreach(array_keys($_POST) as $name)
{
	if(strpos($name, "c_") !== false)
	{
		$id = str_replace("c_", "", $name);
		closeRequest($id);
		header('Location: http://schwerdainbolt.de/c/showrequest.php?id='.$_GET['id']);
		exit;
	}
	else if(strpos($name, "i_") !== false)
	{
		$id = str_replace("i_", "", $name);
		ignoreRequest($id);
		header('Location: http://schwerdainbolt.de/c/showrequest.php?id='.$_GET['id']);
		exit;
	}
}
?>

<html>
	<head>
		<title>Show Request</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
	</head>
	<body>
		<h1>Anfrage #<?php echo md5($_GET['id']);?></h1>
		<?php
			getRequest($_GET['id']);
		?>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>