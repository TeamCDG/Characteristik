<?php
include("loginprotection.php");


if(isset($_POST['delete']))
{
	piclistDeleteMe();
}

if(isset($_POST['search']) && $_POST['uid']!=-1)
{
	addPiclist($_POST['uid']);
}
else if(isset($_POST['search']) && $_POST['uid']==-1)
{
	$id = getUserId($_POST['user']);
	
	if($id == -1)
	{
		echo "<font style=\"color: #FF0000; font-size: 72px;\"> ES GIBT KEINEN ".$_POST['user']."... verschrieben?</font>";
		return;
	}
	else
	{
		addPiclist($id[0]);
	}
}
?>

<html>
	<head>
		<title>Fotowunschliste</title>
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
		
		<script>
	$(function() {
		var names = [<?php 
							getAllJSON_piclist();
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
		<h1>Fotowunschliste</h1>
		<?php showPiclist(); ?>
		<br>
		<br>
		Voting auf Wunsch von Jahrgangsführerin Ines beendet.
		<?php if(!inPiclist($_SESSION['userid']) &&false) { ?>
			<form id="searchform" action="#" method="post">
			<table width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<td width="140" align="left">Ich möchte aufs Bild mit:</td>
				</tr>				
				<tr>
					<td align="left"><input type="hidden" id="teacher" name="teacher" value="0"><input type="hidden" id="id" name="uid" value="-1"><input id="search" type="text" name="user" value="" style="width:70%"><input type="submit" name="search" value="hinzufügen" onclick="sub()" style="width:29%"></td>
				</tr>
			</table>
			</form>
		<?php } else if(false) {?>
			<form id="del" action="#" method="post">
				<input type="submit" name="delete" value="ihh, mir dem will ich nicht aufs Bild" style="width:29%">
			</form>
		<?php } ?>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>