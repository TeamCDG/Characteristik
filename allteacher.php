<?php
include("loginprotection.php");
?>

<html>
	<head>
		<title>Alle Lehrer</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
	</head>
	<body>
		<h1>Alle Lehrer</h1>
		<?php
			listAllTeacher();
		?>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>