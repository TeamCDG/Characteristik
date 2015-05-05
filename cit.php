<?php
include("loginprotection.php");



if(isset($_POST['user']) && $_POST['uid']!=-1)
{
	if(!empty($_POST['cit']))
	{
		addCit($_POST['uid'], $_POST['cit'], $_POST['teacher']=="true" || $_POST['teacher'] ===true);
	}
}
else if(isset($_POST['user']) && $_POST['uid']==-1)
{
	$id = getUserId($_POST['user']);
	
	if($id == -1)
	{
		echo "<font style=\"color: #FF0000; font-size: 72px;\"> ES GIBT KEINEN ".$_POST['user']."... verschrieben?</font>";
	}
	else
	{
	
		if(!empty($_POST['cit']))
		{
			addCit($id[0], $_POST['cit'], $id[1]);
		}
	}
}


foreach(array_keys($_POST) as $name)
{
	if(strpos($name, "id_") !== false)
	{
		$id = str_replace("id_", "", $name);
		deleteCit($id);
	}
}

?>

<html>
	<head>
		<title>Zitate</title>
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
		
		<script>
	$(function() {
		var names = [<?php 
							getAllJSON_cit();
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
	<?php echo "<font style=\"size: 42px;\">DUMP: ";
var_dump($_POST);
echo "</font>";?>
		<h1>Zitate</h1>
		<!-- <p> <br><br>Zitat hinzufÃ¼gen: <br>
		<form id="searchform" action="#" method="post">
			<table width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<td align="left"><input type="text" name="cit" size="100%"></td>
				</tr>				
				<tr>
					<td align="left">Von: <input type="hidden" id="teacher" name="teacher" value="0"><input type="hidden" id="id" name="uid" value="-1"><input id="search" type="text" name="user" value="" style="width:83%"><input type="submit" name="search" value="hinzufügen" onclick="sub()" ></td>
				</tr>
			<table>
		</form>
		</p> 
		<br>
		<br> -->
		<h2> Alle Zitate: </h2>
		<?php listCit(); ?>
		<p><a href="index.php">MenÃ¼</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>