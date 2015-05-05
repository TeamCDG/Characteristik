<?php
include("loginprotection.php");

if(!$_SESSION['admin'])
{
	header('Location: http://schwerdainbolt.de/c/index.php');
	exit;
}

$userid = -1;

if(isset($_POST['sub']) && $_POST['uid']!=-1)
{
	if(strlen($_POST['pass']) >= 1)
	{
		setNewPass($_POST['uid'], $_POST['pass']);
		$userid = $_POST['uid'];
	}
}
else if(isset($_POST['sub']) && $_POST['uid']==-1)
{
	$id = getUserId($_POST['user']);
	
	if($id == -1)
	{
		echo "<font style=\"color: #FF0000; font-size: 72px;\"> ES GIBT KEINEN ".$_POST['user']."... verschrieben?</font>";
	}
	else
	{
		if(strlen($_POST['pass']) >= 1)
		{
			setNewPass($id[0], $_POST['pass']);
			$userid = $id[0];
		}
	}
	
}


?>

<html>
	<head>
		<title>Neues Passwort setzen</title>
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
		
		<script>
	$(function() {
		var names = [<?php 
							getAllJSON_user();
						?>
		];

		$( "#search" ).autocomplete({
			minLength: 0,
			source: names,
			focus: function( event, ui ) {
				$( "#search" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				$( "#search" ).val( ui.item.label );
				$( "#id" ).val( ui.item.id );
				$( "#teacher" ).val( ui.item.teacher );
				//sub();
				return false;
			}
		})
		.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
			return $( "<li>" )
				.append( "<a>" +((item.teacher=="true")?" <font color=\"#FF0000\" >":"")+ item.label + ((item.teacher=="true")?" </font>":"")+  "</a>" )
				.appendTo( ul );
		};
	});
	</script>
	<script type="text/javascript">
	function sub()
	{
		$("#searchform").submit();
		$("#searchform").reset();
	}
	</script>
	</head>
	<body>
		<h1>Neues Passwort setzen</h1>
		<p>
		<form id="searchform" action="#" method="post">
			<table width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<td align="left">Benutzer: <input type="hidden" id="id" name="uid" value="-1"><input id="search" type="text" name="user" value="" style="width:83%"></td>
				</tr>				
				<tr>
					<td align="left">Passwort: <input type="text" name="pass" value="" style="width:83%"></td>
				</tr>
				<tr>
					<td align="left"><input type="submit" name="sub" value="Passwort ändern" onclick="sub()" ></td>
				</tr>
			<table>
		</form>
		</p> 
		
		<?php if(isset($_POST['sub']) && strlen($_POST['pass']) >= 1) { ?>
		
			<h2>Copy Pasterino Data:</h2>
			<table width="100px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<td width="50px">Benutzername:</td><td width="50px"><?php echo getUsername($userid); ?></td>
				</tr>				
				<tr>
					<td width="50px">Passwort:</td><td width="50px"><?php echo $_POST['pass']; ?></td>
				</tr>
			<table>
		
		<?php } ?>
		<br>
		<br>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>