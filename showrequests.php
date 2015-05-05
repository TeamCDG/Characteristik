<?php
include("loginprotection.php");
?>

<html>
	<head>
		<title>Show Requests</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
	</head>
	<body>
		<h1>Anfragen von <?php echo getName(isset($_GET['uid'])&&$_SESSION['admin']?$_GET['uid']:$_SESSION['userid'], isset($_GET['t'])?$_GET['t']:0);?></h1>
		<?php
			getRequestsByUser(isset($_GET['uid'])&&$_SESSION['admin']?$_GET['uid']:$_SESSION['userid']);
		?>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>