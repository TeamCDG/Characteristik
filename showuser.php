<?php
include("loginprotection.php");

if( isset($_GET['t']) && ($_GET['t']=="true" || $_GET['t']=="1" || $_GET['t']==1 || strpos($_GET['t'], "true") === true))
	$t = true;
else
	$t = false;
	
if(empty(t))
	$t = false;

if(isset($_POST['editsave']) || isset($_POST['teacher']))
{
	$id = -1;
	
	foreach(array_keys($_POST) as $name)
	{
		if(strpos($name, "s_") !== false)
		{
			$id = str_replace("s_", "", $name);
			updateChar($id, $t, $_POST["e_".$id]);
			header('Location: http://schwerdainbolt.de/c/showuser.php?uid='.$_GET['uid'].(empty($t)?"":"&t=true"));
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
		deleteChar($id, $t);
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
		header('Location: http://schwerdainbolt.de/c/showuser.php?uid='.$_GET['uid'].(empty($t)?"":"&t=true"));
		exit;
	}
}
else if(isset($_POST['char']))
{
	addChar($_GET['uid'], $t, $_POST['char']);
	header('Location: http://schwerdainbolt.de/c/showuser.php?uid='.$_GET['uid'].(empty($t)?"":"&t=true"));
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
		<h1><?php echo getName($_GET['uid'], $t); 
				  if($t===false) 
				  { 
					echo " - <a href=\"info.php?uid=".$_GET['uid']."\">Steckbrief</a>(<a href=\"infocp.php?uid=".$_GET['uid']."\">CP</a>) - "; 
				  } 
				  echo "<a href=\"copypasterinochar.php?uid=".$_GET['uid'].(empty($t)?"":"&t=true")."\">Copy Pasterino</a>";  ?></h1>
		<?php
			getChars($_GET['uid'], $t);
			//var_dump($_GET);
			//var_dump($t);
			//echo "==\"true\": ".($_GET['t']=="true")."<br>";
			//echo "==\"1\": ".($_GET['t']=="1")."<br>";
			//echo "==1: ".($_GET['t']==1)."<br>";
			//echo "strpos != -1: ".(strpos($_GET['t'], "true") === true)."<br>";
		?>
		<p><font size="1">*Pflichtfeld</font></p>
		<?php if($_GET['uid'] != $_SESSION['userid'] && false) { ?>
		<form action="#" method="POST">	
			<table width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<th width="140" align="left">Charakteristik hinzufÃ¼gen:</th>
				</tr>	
				<tr>
					<th width="140" align="left">Mehrere Nachrichten mit "|" trennen.</th>
				</tr>				
				<tr>
					<td align="left"><input type="text" name="char" value="" style="width:100%"></td>
				</tr>
				<tr>
					<td colspan="2" align="left" nowrap><br><input type="submit" name="add" value="hinzufÃ¼gen" style="width:99%"></td>
				</tr>
			</table>
		</form>
		<?php } ?>
		<p><a href="index.php">MenÃ¼</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>