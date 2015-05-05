<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

include("functions.php");
if(isset($_SESSION['userid']))
{
	header('Location: index.php');
	exit;
}
if(isset($_POST['user']) && isset($_POST['pass']))
{
	if(login($_POST['user'], $_POST['pass'], $_POST['keks']=="thecakeisalie"))
	{
		header('Location: index.php');
		exit;
	}
}
?>
<html>
	<head>
		<title>Login</title>
	</head>
	<body>
		<form action="#" method="post">
			<table width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<td width="140" align="left">Username:</td>
				</tr>					
				<tr>
					<td align="left"><input type="text" name="user" value="<?php if(isset($_POST['user'])) echo $_POST['user']; ?>" style="width:100%"></td>
				</tr>
				<tr>
					<td width="140" align="left">Password:</td>
				</tr>					
				<tr>
					<td align="left"><input type="password" name="pass" value="" style="width:100%"></td>
				</tr>
				<tr>
					<td width="140" align="left"><input type="checkbox" name="keks" value="thecakeisalie">Eingeloggt bleiben</td>
				</tr>				
				<tr>
					<td colspan="2" align="left" nowrap><br><input type="submit" name="send" value="Login" style="width:99%"></td>
				</tr>
			</table>
        </form>
	</body>
</html>