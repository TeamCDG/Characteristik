<?php
include("loginprotection.php");

if(isset($_POST['voted']))
{
	vote($_GET['pollid'], $_POST['vote']);
}
?>

<html>
	<head>
		<title>Umfrage</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
	</head>
	<body>
		
		<?php
			showpoll($_GET['pollid']);
		?>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php donthatemycar(); echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>