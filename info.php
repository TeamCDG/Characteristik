<?php
include("loginprotection.php");

$uid = $_GET['uid'];
if(!isset($_GET['uid']))
	$uid = $_SESSION['userid'];
	
	
if(isset($_POST['done']))
{
	$err = false; 
	if(strlen($_POST['lk1']) < 2) {
		echo "Bitte 1. LK angeben... <br>";
		$err = true;
	}
	if(strlen($_POST['lk2']) < 2) {
		echo "Bitte 2. LK angeben... <br>";
		$err = true;
	}
	if(strlen($_POST['lk3']) < 2) {
		echo "Bitte 3. LK angeben... <br>";
		$err = true;
	}
	if(strlen($_POST['year']) < 2) {
		echo "Bitte Jahrgang angeben... <br>";
		$err = true;
	}
	if(!is_numeric($_POST['year'])) {
		echo "Bitte Jahrgang als Zahl angeben... <br>";
		$err = true;
	}
	$jobs = ["Entsorgungsfachkraft", "Reinigungsfachkraft","Fachangestellter bei McDonalds"];
	$job = $_POST['jobwish'];
	if(strlen($_POST['jobwish']) <= 0) {
		$job = $jobs[rand(0, sizeof($jobs))];
	}
	
	if(!$err)
	{
		$year = $_POST['year'];
		if(strlen($year) == 2)
		{
			$year = "19".$year;
		}
		
		setInfo($_POST['lk1'], $_POST['lk2'], $_POST['lk3'], $year, $job, $_POST['wiwts'], $_POST['thanks'], $_POST['nick']);
	}
}


?>

<html>
	<head>
		<title>Info</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
	</head>
	<body>
		<h1>Steckbrief <?php echo getName($uid, 0)." "; ?>:</h1>
		
		<?php showInfo($uid); ?>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>