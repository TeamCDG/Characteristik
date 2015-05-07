<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";
header('Content-Type: text/html; charset=utf-8');
session_start();

include("functions.php");
if(isset($_SESSION['userid']))
{
	header('Location: '.$rootfolder);
	exit;
}
if(isset($_POST['user']) && isset($_POST['pass']))
{
	if(login($_POST['user'], $_POST['pass'], $_POST['keks']=="thecakeisalie"))
	{
		header('Location: '.$rootfolder);
		exit;
	}
}
?>
<html>
			<script src="//code.jquery.com/jquery-1.11.0.js"></script>
		<script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
		<script src="<?php echo $rootfolder;?>lib/randint.js"></script>
		<script src="<?php echo $rootfolder;?>lib/unwrapinner.js"></script>
		<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/md5.js"></script>
	<script>
		var adding = false;
		var addAnimationId = -1;
		function login()
		{
			if(adding) return;
			adding = true;
			
			$('#error').html("");
			clearInterval(addAnimationId);
			
			var error = false;
			var username = $('#user').val();
			var pass = $('#pass').val();
			var keks = $('#keks').is(':checked');
			
			if(pass == undefined || pass.trim().length == 0)
			{
				$('#error').html("Bitte Passwort eingeben!");
				$('#pass').css('border-color', 'red');
				error = true;
			}
			else
			{
				$('#pass').css('border-color', '');
			}
			
			if(username == undefined || username.trim().length == 0)
			{
				$('#error').html("Bitte Benutzernamen eingeben!");
				$('#user').css('border-color', 'red');
				error = true;
			}
			else
			{
				$('#user').css('border-color', '');
			}
			
			if(!error)
			{
				$.post( "<?php echo $rootfolder; ?>ajax/login.php", { username: username, password: (""+CryptoJS.MD5(pass)), cookie: (keks?"1":"0")}, function( data) {
					var res = JSON.parse(data);
					if(res.status == "200")
					{						
						window.location.href = "<?php echo $rootfolder; ?>";
					}
					else
					{
						$('#error').css('display', 'none');
						$('#error').html(res.message);
						$('#error').slideDown()
					}
					adding = false;
				});
			}
			else
			{
				$('#error').slideDown();
				adding = false;
			}
			
		}
	</script>
	<head>
		<title>Login</title>
	</head>
	
	<body>
		<div id="error" class="errormsg" style="width: 400px; margin-left: auto; margin-right:auto;"></div>
		<form action="#" method="post">
			<table width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<td width="140" align="left">Username:</td>
				</tr>					
				<tr>
					<td align="left"><input type="text" id="user" name="user" value="<?php if(isset($_POST['user'])) echo $_POST['user']; ?>" style="width:100%"></td>
				</tr>
				<tr>
					<td width="140" align="left">Password:</td>
				</tr>					
				<tr>
					<td align="left"><input type="password" id="pass" name="pass" value="" style="width:100%"></td>
				</tr>
				<tr>
					<td width="140" align="left"><input type="checkbox" id="keks" name="keks" value="thecakeisalie">Eingeloggt bleiben</td>
				</tr>				
				<tr>
					<td colspan="2" align="left" nowrap><br><input type="button" onclick="login()" name="send" value="Login" style="width:99%"></td>
				</tr>
			</table>
        </form>
	</body>
</html>