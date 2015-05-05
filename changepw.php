<?php
session_start();
include("functions.php");

if(isset($_POST['oldpass']) && isset($_POST['newpass']) && isset($_POST['newpass_c']))
{
	if( $_POST['newpass'] == $_POST['newpass_c'])
	{
		if(changePassword($_POST['oldpass'], $_POST['newpass']))
		{
			header("Location: http://schwerdainbolt.de/c/changepw.php?s=1");
			exit;
		}
		else
		{
			$err = array();
			$err[] = "Altes Passwort falsch!";
		}
	}
	else
	{
		$err = array();
		$err[] = "Neues Passwort nicht 2x gleich!";
	}
}
else if(!(!isset($_POST['oldpass']) && !isset($_POST['newpass']) && !isset($_POST['newpass_c'])))
{
	$err = array();
	$err[] = "Nicht alle Felder ausgefüllt!";
}
?>
<html>
	<head>
		<title>Passwort ändern</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
	</head>
	<body>
	<?php if(isset($_GET['s']))
			{
				echo "<p>Passwort geändert</p>";
			}
			if(!empty($err)){ foreach($err as $e){ echo "<p>$e</p>"; }}?>
		<form action="#" method="post">
			<table width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<td width="140" align="left">Altes Passwort:</td>
				</tr>					
				<tr>
					<td align="left"><input type="password" name="oldpass" value="" style="width:100%"></td>
				</tr>
				<tr>
					<td width="140" align="left">Neues Password:</td>
				</tr>					
				<tr>
					<td align="left"><input type="password" name="newpass" value="" style="width:100%"></td>
				</tr>
				<tr>
					<td width="140" align="left">Neues Password bestätigen:</td>
				</tr>					
				<tr>
					<td align="left"><input type="password" name="newpass_c" value="" style="width:100%"></td>
				</tr>			
				<tr>
					<td colspan="2" align="left" nowrap><br><input type="submit" name="send" value="ändern" style="width:99%"></td>
				</tr>
			</table>
        </form>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>