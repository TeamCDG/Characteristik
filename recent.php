<?php
include("loginprotection.php");
?>

<html>
	<head>
		<title>Neuste Aktivität</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
	</head>
	<body>
		<h1>Neuste Aktivität</h1>
		<?php
			recentChar();
		?>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>