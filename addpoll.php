<?php
include("loginprotection.php");


if(isset($_POST['add']) && isset($_POST['answers']) && isset($_POST['title']))
{
	
	$answers = $_POST['answers'];
	foreach($answers as $a)
	{
		$str = trim($a);
		if(empty($str))
		{
			$err[] = "mindestens eine invalide, da leere Anwtortmöglichkeit...";
		}
	}
	
	
	if(empty($err))
	{
		$multiselect = isset($_POST['multiselect']);
		$title = $_POST['title'];
		$pid = addPoll($title, $answers, $multivote, $finalchoice);
		
		header('Location: http://schwerdainbolt.de/c/showpoll.php?pollid='.$pid);
		exit;
	}
}
else
{
	$err[] = "Bitte Titel eingeben...";
	$err[] = "Bitte Antwortmöglichkeiten eingeben...";
}
?>

<html>
	<head>
		<title>Neue Umfrage</title>
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
		<script type="text/javascript">
		function addLine()
		{
			document.getElementById("votepos").innerHTML += "<tr><td align=\"left\"><input type=\"text\" name=\"answers[]\" value=\"\" style=\"width:100%\"></td><tr>";
		}
		
		function addAllP()
		{
			var xmlhttp;
			if (window.XMLHttpRequest)
			{
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			}
			else if (window.ActiveXObject)
			{
				// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			else
			{
				document.getElementById('votepos').innerHTML = "Browser unterstützt kein AJAX!";
			}
				
			xmlhttp.open("GET","/c/getallp.php",true);
			xmlhttp.send(null);

			xmlhttp.onreadystatechange=function()
			{
				if(xmlhttp.readyState == "4")
				{
					if(xmlhttp.status == "200") 
					{
						document.getElementById('votepos').innerHTML = xmlhttp.responseText;
					}
					else
					{ 
						document.getElementById('votepos').innerHTML = "Der Counter konnte nicht gestartet werden! Fehler: "+xmlhttp.status;
					}
				}
			}
		}
		
		function addAllT()
		{
			var xmlhttp;
			if (window.XMLHttpRequest)
			{
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			}
			else if (window.ActiveXObject)
			{
				// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			else
			{
				document.getElementById('votepos').innerHTML = "Browser unterstützt kein AJAX!";
			}
				
			xmlhttp.open("GET","/c/getallt.php",true);
			xmlhttp.send(null);

			xmlhttp.onreadystatechange=function()
			{
				if(xmlhttp.readyState == "4")
				{
					if(xmlhttp.status == "200") 
					{
						document.getElementById('votepos').innerHTML = xmlhttp.responseText;
					}
					else
					{ 
						document.getElementById('votepos').innerHTML = "Der Counter konnte nicht gestartet werden! Fehler: "+xmlhttp.status;
					}
				}
			}
		}
		</script>
	</head>
	<body>
		<h1>Neue Umfrage</h1>
		<form action="#" method="POST">	
			<table id="table" width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<th width="140" align="left">Umfrage hinzufügen:</th>
				</tr>	
				<tr>
					<th width="140" align="left">Titel: </th>
				</tr>				
				<tr>
					<td align="left"><input type="text" name="title" value="" style="width:100%"></td>
				</tr>
				<tr>
					<th width="140" align="left">Antwortmöglichkeiten: </th>
				</tr>	
				<tr>
					<td align="left"><input type="checkbox" name="multiselect" value="" style="width:100%"> Mehrfachauswahl</td>
				</tr>	
				<tr>
					<td align="left">
						<input type="button" name="addAllPenis" value="Alle Schüler hinzufügen" onclick="addAllP()">
						<input type="button" name="addAllTenis" value="Alle Lehrer hinzufügen" onclick="addAllT()">
					</td>
				</tr>	
			</table>
			<table id="votepos" width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<td align="left"><input type="text" name="answers[]" value="" style="width:100%"></td>
				</tr>
				<tr>
					<td align="left"><input type="text" name="answers[]" value="" style="width:100%"></td>
				</tr>
			</table>
			<input type="submit" name="add" value="Fragen!">
		</form>
		<p><a onclick="addLine();">+ Antwort</a></p>
		<p><a href="index.php">Menü</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
	</body>
</html>