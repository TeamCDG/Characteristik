<?php
include("loginprotection.php");
?>

<html>
	<head>
		<title>Show Requests</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
	</head>
	<body>
		<h1>Anfragen</h1>
		<?php
			getRequests(isset($_GET['onlyopen'])?$_GET['onlyopen']:0, isset($_GET['onlyadmin'])?$_GET['onlyadmin']:0);
		?>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>