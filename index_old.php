<?php
include("loginprotection.php");
//header('Content-Type: text/html; charset=utf-8');
//var_dump($_POST);
if(isset($_POST['search']) && $_POST['uid']!=-1)
{
	if(isset($_POST['teacher']))
		header('Location: http://schwerdainbolt.de/c/showuser.php?uid='.$_POST['uid'].'&t='.$_POST['teacher']);
	else
		header('Location: http://schwerdainbolt.de/c/showuser.php?uid='.$_POST['uid']);
	exit;
}
else if(isset($_POST['search']) && $_POST['uid']==-1)
{
	$id = getUserId($_POST['user']);
	if($id != -1)
	{
		if($id[1])
			header('Location: http://schwerdainbolt.de/c/showuser.php?uid='.$id[0].'&t=true');
		else
			header('Location: http://schwerdainbolt.de/c/showuser.php?uid='.$id[0]);
		exit;
	}
	else
	{
		//var_dump($_POST);
	}
}
else if(isset($_POST['hidemyass']))
{
	$_SESSION['hidemyass'] = !$_SESSION['hidemyass'];
	
	if($_SESSION['hidemyass'])
	{
		setcookie("hidemyass", 1, time() + 60*60*24*3000);
		header('Location: http://schwerdainbolt.de/c');
		exit;
	}
	else
	{
		setcookie("hidemyass", 0, time() + 60*60*24*3000);
		header('Location: http://schwerdainbolt.de/c');
		exit;
	}
}
else if(isset($_POST['design']))
{
	$_SESSION['style'] = (intval($_SESSION['style'])+1)%11;
	
	setDesign($_SESSION['userid'], 0, $_SESSION['style']);
	
	header('Location: http://schwerdainbolt.de/c');
	exit;
}
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Menü</title>
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="<?php echo $_SESSION['style']; ?>.css">
		<style type="text/css">
		div#snakelink
		{
			background-image: url("snakelink.png");
			width: 120px;
			height: 24px;
			color: #000000;
			text-align: center;
		}
		
		div#snakelink:hover
		{
			background-image: url("snakelink_red.png");
			width: 120px;
			height: 24px;
			color: #FFFFFF;
			text-align: center;
		}
		</style>
		<script>
	$(function() {
		var names = [<?php 
							getAllJSON();
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
	function sub2()
	{
		$("#searchform").submit();
		$("#searchform").reset();
	}
	</script>
	</head>
	<body onload="setvol();" onclick="gogogo();" onmousemove="gogogo();">
		<?php if(rand(0,200)==132) {  $rand = rand(0,5);?>
			<p style="position: fixed; bottom: -15px; left:0px; float: both; z-index: -1;">
				<img src="/images/weednazipope.png" alt="HEIL Jahrgang"/>
			</p>
			<script type="text/javascript">
				var playing = false;
				function gogogo()
				{
					playing = document.getElementById("igorrr").played.end(0) > 0;
					if(!playing)
					{
						document.getElementById("igorrr").play();
						playing = true;
					}
				}
				
				function setvol()
				{
					document.getElementById("igorrr").volume = 0.05;
				}
			</script>
			<audio id ="igorrr" controls autoplay style="display:none;" onplay="setvol();">
				<source src="igorrr/igorrr-<?php echo $rand; ?>.ogg" type="audio/ogg">
				<source src="igorrr/igorrr-<?php echo $rand; ?>.mp3" type="audio/mpeg">
			</audio>
		<?php } ?>
		<h1>Menü</h1>
		<?php //fillIt(); ?>
		<form id="searchform" action="#" method="post">
			<table width="300px" border="0" cellpadding="3" cellspacing="1">
				<tr>
					<td width="140" align="left">Suche:</td>
				</tr>				
				<tr>
					<td align="left"><input type="hidden" id="teacher" name="teacher" value="0"><input type="hidden" id="id" name="uid" value="-1"><input id="search" type="text" name="user" value="" style="width:70%"><input type="submit" name="search" value="suchen" onclick="sub()" style="width:29%"></td>
				</tr>
				<tr>
					<td width="140" align="left"><?php brauchtLiebe(); ?></td>
				</tr>
				<tr>
					<td width="140" align="left"><a href="showuser.php?uid=<?php echo $_SESSION['userid']; ?>">Mein Profil <?php echo "(".getNewCount().")"; ?></a></td>
				</tr>	
				<tr>
					<td width="140" align="left"><a href="info.php">Mein Steckbrief</a></td>
				</tr>
				<tr>
					<td width="140" align="left"><a href="piclist.php">Fotowunschliste</a></td>
				</tr>
				<tr>
					<td width="140" align="left"><a href="cit.php">Zitate</a></td>
				</tr>
				<tr>
					<td width="140" align="left"><a href="gossip.php">Gerüchteküche (Man munkelt...)</a></td>
				</tr>
				<tr>
					<td width="140" align="left"><a href="alluser.php">Alle Schüler</a></td>
				</tr>
				<tr>
					<td width="140" align="left"><a href="allteacher.php">Alle Lehrer</a></td>
				</tr>
				<tr>
					<td width="140" align="left"><a href="recent.php">Neuste Aktivität</a></td>
				</tr>	
				<tr>
					<td width="140" align="left"><a href="statistics.php">Statistiken</a></td>
				</tr>
				<tr>
					<td width="140" align="left"><a href="changepw.php">Passwort Ändern</a></td>
				</tr>	
				<tr>
					<td width="140" align="left"><a href="showrequests.php">Meine Anfragen</a></td>
				</tr>	
				<tr>
					<td width="140" align="left"><a href="allpolls.php">Alle Umfragen</a></td>
				</tr>
				<?php if($_SESSION['admin']) { ?>
				<tr>
					<td width="140" align="left"><a href="allrequests.php?onlyopen=1&onlyadmin=0">Offene Anfragen</a></td>
				</tr>	
				<tr>
					<td width="140" align="left"><a href="allrequests.php?onlyopen=0&onlyadmin=0">Alle Anfragen</a></td>
				</tr>	
				<tr>
					<td width="140" align="left"><a href="allrequests.php?onlyopen=0&onlyadmin=1">Von Admin gelöschte Beiträge</a></td>
				</tr>
				<tr>
					<td width="140" align="left"><a href="addpoll.php">Neue Umfrage</a></td>
				</tr>
				<tr>
					<td width="140" align="left"><a href="setnewpw.php">Neues Passwort vergeben</a></td>
				</tr>
				<tr>
					<td width="140" align="left"><form id="hma" action="#" method="POST"><input onclick="sub2()" type="submit" name="hidemyass" value="<?php echo $_SESSION['hidemyass']?"Show my ass":"Hide my ass";?>"></form></td>
				</tr>	
									
				<?php } ?>
				<tr>
					<td width="140" align="left"><form id="des" action="#" method="POST"><input type="submit" name="design" value="Design wechseln"></form></td>
				</tr>
				<tr>
					<td width="140" align="left"><a href="snake" title="play some snake :)"><div id="snakelink" width="120px" height="24px">SNAKE</div></a>
					</td>
				</tr>
			</table>
        </form>
		<p><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
		<?php if($_SESSION['admin']) { ?><p><img src="db_stats.php" alt="db stats" /></p><?php } ?>
		<!-- <?php if(rand(0,5)==3 && ($_SESSION['userid'] == 17 || $_SESSION['userid'] == 107 || $_SESSION['userid'] == 116 || $_SESSION['userid'] == 6)) { ?><p style="position: fixed; bottom: -15px; left:0px; float: both;"><img src="/images/weednazipope.png" alt="HEIL Jahrgang"/></p><?php } ?> !-->
	</body>
</html>