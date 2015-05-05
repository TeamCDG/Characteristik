<?php
include("loginprotection.php");

$t = isset($_GET['t']) && ($_GET['t']=="true" || $_GET['t']=="1" || $_GET['t']==1 || strpos($_GET['t'], "#") != -1);

if(isset($_POST['editsave']) || isset($_POST['teacher']))
{
	$id = -1;
	
	foreach(array_keys($_POST) as $name)
	{
		if(strpos($name, "s_") !== false)
		{
			$id = str_replace("s_", "", $name);
			updateChar($id, isset($_GET['t'])?$_GET['t']:0, $_POST["e_".$id]);
			header('Location: http://schwerdainbolt.de/c/showuser.php?uid='.$_GET['uid'].'&t='.(isset($_GET['t'])?$_GET['t']:"false"));
			exit;
		}
	}
	
	foreach(array_keys($_POST) as $name)
	{
		if(strpos($name, "id_") !== false)
		{
			$id = str_replace("id_", "", $name);
			break;
		}
	}
	
	$done = false;
	
	if($_SESSION['admin'])
	{
		deleteChar($id, $_POST["teacher"]);
		$done = true;
	}
	else
	{
		if(strlen($_POST["reason"][$id]) >= 5)
		{
			addRequest($id, $_POST["teacher"], $_POST["reason"][$id]);
			$done = true;
		}
		else
		{
			$err = "Der Grund um einen Beitrag zu melden muss mindestens 5 (fünf) Zeichen lang sein.";
		}
	}
	
	if($done)
	{
		header('Location: http://schwerdainbolt.de/c/showuser.php?uid='.$_GET['uid'].'&t='.(isset($_GET['t'])?$_GET['t']:"false"));
		exit;
	}
}
else if(isset($_POST['char']))
{
	addChar($_GET['uid'], isset($_GET['t'])?$_GET['t']:"false", $_POST['char']);
	header('Location: http://schwerdainbolt.de/c/showuser.php?uid='.$_GET['uid'].'&t='.(isset($_GET['t'])?$_GET['t']:"false"));
	exit;
}
?>

<html>
	<head>
		<title>Show User</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
	</head>
	<body>
		<p><font size="14" color="#FF0000"><b><?php if(isset($err)) echo $err ?></b></font></p>
		<h1><?php echo getName($_GET['uid'], isset($_GET['t'])?$_GET['t']:false);?></h1>
		<?php
			getChars($_GET['uid'], isset($_GET['t'])?$_GET['t']:false);
		?>
		<p><font size="1">*Pflichtfeld</font></p>
		<?php if($_GET['uid'] != $_SESSION['userid']) { ?>
		<form action="#" method="POST">	
			<table width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<th width="140" align="left">Charakteristik hinzufügen:</th>
				</tr>	
				<tr>
					<th width="140" align="left">Mehrere Nachrichten mit "|" trennen.</th>
				</tr>				
				<tr>
					<td align="left"><input type="text" name="char" value="" style="width:100%"></td>
				</tr>
				<tr>
					<td colspan="2" align="left" nowrap><br><input type="submit" name="add" value="hinzufügen" style="width:99%"></td>
				</tr>
			</table>
		</form>
		<?php } ?>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>