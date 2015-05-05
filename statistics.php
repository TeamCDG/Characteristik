<?php
include("loginprotection.php");
?>

<html>
	<head>
		<title>Statistiken</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
	</head>
	<body>
		<h1>Statistiken</h1>
		<?php
			if($_SESSION['admin']&&!$_SESSION['hidemyass']) listTopSpammer();
			listLessSpammed();
			listTopSpammed();
			listTopDeleteRequest();
			if($_SESSION['admin']&&!$_SESSION['hidemyass']) listTopDeleted();
			listTopDesigns();
			listTopSnakePlayer();
			listTopSnakeScore();
			listTopSnakeScoreTouch();
		?>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>