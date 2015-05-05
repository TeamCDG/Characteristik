<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

$t = false;
if( isset($_GET['t']) && ($_GET['t']=="true" || $_GET['t']=="1" || $_GET['t']==1 || strpos($_GET['t'], "true") === true))
	$t = true;
else
	$t = false;

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = getName($_GET['uid'], $t);
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");

if(isset($_POST['submit']))
{
	if($_SESSION['debug']) var_dump($_POST);
	$err = array(); 
	if(strlen($_POST['lk1']) < 1 && !$_SESSION['info_allow_empty']) {
		$err[] = "Bitte 1. LK angeben... <br>";
	}
	if(strlen($_POST['lk2']) < 1 && !$_SESSION['info_allow_empty']) {
		$err[] = "Bitte 2. LK angeben... <br>";
	}
	if(strlen($_POST['lk3']) < 1 && !$_SESSION['info_allow_empty']) {
		$err[] = "Bitte 3. LK angeben... <br>";
	}
	if(strlen($_POST['year']) < 2 && !$_SESSION['info_allow_empty']) {
		$err[] = "Bitte Jahrgang angeben... <br>";
	}
	if(!is_numeric($_POST['year']) && !$_SESSION['info_allow_empty']) {
		$err[] = "Bitte Jahrgang als Zahl angeben... <br>";
	}
	$jobs = ["Entsorgungsfachkraft", "Reinigungsfachkraft","Fachangestellter bei McDonalds"];
	$job = $_POST['jobwish'];
	if(strlen($_POST['jobwish']) <= 0) {
		if(!$_SESSION['info_disable_jobwish_easteregg'])
			$job = $jobs[rand(0, sizeof($jobs))];
		else if(!$_SESSION['info_allow_empty'])
			$err[] = "Bitte Berufswunsch angeben... <br>";
	}
	
	if($_SESSION['debug']) var_dump($err);
	if(empty($err))
	{
		$year = $_POST['year'];
		if(strlen($year) == 2)
		{
			if(intval($year) < 90)
				$year = "20".$year;
			else
				$year = "19".$year;
		}
		
		$sql = "SELECT COUNT(*) AS c FROM `info` WHERE `uid`='".$_SESSION['userid']."';";
		$res = mysql_query($sql) or die ("ERROR #419: Query failed: $sql @showuser - ".mysql_error());
		
		if(mysql_fetch_object($res)->c == 0)
		{
			$sql = "INSERT INTO `info`(`id`, `uid`, `lk1`, `lk2`, `lk3`, `year`, `jobwish`, `wiwts`, `thanks`, `nick`) VALUES (NULL,'".$_SESSION['userid']."','".mysql_real_escape_string($_POST['lk1'])."'
					,'".mysql_real_escape_string($_POST['lk2'])."','".mysql_real_escape_string($_POST['lk3'])."','".mysql_real_escape_string($year)."','".mysql_real_escape_string($job)."','".mysql_real_escape_string($_POST['wiwts'])."'
					,'".mysql_real_escape_string($_POST['thanks'])."','".mysql_real_escape_string($_POST['nick'])."');";
			$res = mysql_query($sql) or die ("ERROR #430: Query failed: $sql @showuser - ".mysql_error());
		}
		else
		{
			$sql = "UPDATE `info` SET `lk1`='".mysql_real_escape_string($_POST['lk1'])."',`lk2`='".mysql_real_escape_string($_POST['lk2'])."',`lk3`='".mysql_real_escape_string($_POST['lk3'])."',
			`year`='".mysql_real_escape_string($year)."',`jobwish`='".mysql_real_escape_string($job)."',`wiwts`='".mysql_real_escape_string($_POST['wiwts'])."',
			`thanks`='".mysql_real_escape_string($_POST['thanks'])."',`nick`='".mysql_real_escape_string($_POST['nick'])."' WHERE `uid`='".$_SESSION['userid']."';";
			$res = mysql_query($sql) or die ("ERROR #430: Query failed: $sql @showuser - ".mysql_error());
		}
		
	}
}
/*
			<?php if(!$_SESSION['hidemyass'] && $_SESSION['admin_nsa']) { ?>
				<th class="br">Von</th>
			<?php } ?>
			<th class="br">Eintrag</th>
			<th class="b">Löschen</th>*/
			

function getChars($uid, $teacher)
{
	$t = $teacher===true?"`tchar`":"`uchar`";
	
	$sql = "SELECT COUNT(*) AS c FROM $t WHERE `holder` = $uid AND `visible` = '1'";
	$res = mysql_query($sql) or die ("ERROR #004: Query failed: $sql @showuser - ".mysql_error());
	$count = mysql_fetch_object($res)->c;
	
	$sql = "SELECT * FROM $t WHERE `holder` = $uid AND `visible` = '1'";
	$res = mysql_query($sql) or die ("ERROR #004: Query failed: $sql @showuser - ".mysql_error());
	
	if($uid != $_SESSION['userid'] &&  !$_SESSION['admin'])
	{		
		while($row = mysql_fetch_object($res))
		{
			$e = "";
			if(intval($row->from) == $_SESSION['userid'] && $_SESSION['char_edit'])
				$e = "<div class=\"input_container\"><input type=\"text\" name=\"e_".$row->id."\" value=\"".escape($row->content)."\"></div><input type=\"submit\" name=\"s_".$row->id."\" value=\"?nderung speichern\">";
			else
				$e = $row->content;
				
			echo "<tr>
					<td class=\"b\"><div class=\"char_content\">".$e."</div></td>
				  </tr>";				
		}
	}
	else if($_SESSION['admin'])
	{
		
		$maxid = 0;
		
				
		while($row = mysql_fetch_object($res))
		{
			$e = "";
			if(intval($row->from) == $_SESSION['userid'] && $_SESSION['char_edit'])
				$e = "<div class=\"input_container\"><input type=\"text\" name=\"e_".$row->id."\" value=\"".escape($row->content)."\"></div><input type=\"submit\" name=\"s_".$row->id."\" value=\"?nderung speichern\">";
			else
				$e = $row->content;
			echo "<tr>".(!$_SESSION['hidemyass']&&$_SESSION['admin_nsa']?"
					<td class=\"br by\"><div class=\"char_by\"><a href=\"showuser.php?uid=".$row->from."\">".getName($row->from, 0)."</a></div></td>":"").
					
					"<td class=\"br\"><div class=\"char_content\">".$e."</div></td>
					<td class=\"b\"><div  class=\"char_delete\"><input type=\"submit\" name=\"id_".$row->id."\" value=\"l?schen\"></td></td>
				  </tr>";
				  
			if(intval($row->id) > $maxid)
				$maxid = intval($row->id);
		}
		
		if($_SESSION['userid'] == $uid)
			$_SESSION['maxid'] = $maxid;
		
	}
	else if($_SESSION['userid'] == $uid && $t == "`uchar`")
	{
		$maxid = 0;
		
		
		while($row = mysql_fetch_object($res))
		{
			if(isRequested($row->id, $teacher))
				$str = getRequestStatus($row->id, $teacher);
			else
				$str = "Grund* : <input type=\"text\" name=\"reason[".$row->id."]\" value=\"\" style=\"width:70%\"><input type=\"submit\" name=\"id_".$row->id."\" value=\"Löschantrag\">";
			echo "<tr>
					<td class=\"br\"><div class=\"char_content\">".$row->content."</div></td>
					<td class=\"b\"><div  class=\"char_delete\">".$str."</div></td>
				  </tr>";
				  
				  
			if(intval($row->id) > $maxid)
				$maxid = intval($row->id);
		}
		
		$_SESSION['maxid'] = $maxid;
	}
}
			
$sql = "SELECT COUNT(*) AS c, `info`.* FROM `info` WHERE `uid`='".mysql_real_escape_string($_GET['uid'])."';";
$res = mysql_query($sql) or die ("ERROR #151: Query failed: $sql @showuser - ".mysql_error());
$info = mysql_fetch_object($res);
?>
	<style type="text/css">
		table
		{
			border-top: 1px solid silver;
			border-left: 1px solid silver;
			border-right: 1px solid silver;
			border-bottom: 1px solid silver;
		}
		
		table, tr
		{
			width: 100%;
			
		}
		
		table#info th, table#info td
		{
			width: 50%;
		}
		
		table th, td
		{
			padding: 0px;
			margin: 0px;
		}
		
		table#info_content td {
			height: 100%;
			width: 50%;
			vertical-align:top;
			position: relative;
		}
		
		
		*.container_left {
			width: 115px;
			float:left;
			background: blue;
		}
		
		*.container_right {
			float: right;
			width: calc(100% - 120px);
			margin-left: 118px;
			margin-top: -1px;
			height: calc(100% - 2px);
			background: red;
			position: absolute;
		}
		
		textarea {
			resize: none;
			opacity: 0.5;
		}
		
		*.container_right input {
			width: calc(100% - 5px);
		}
		
		*.container_right textarea {
			height: calc(100% - 6px);
			width: calc(100% - 6px);
		}
		
		th
		{
			height: 30px;
			background: rgba(255, 255, 255, .1);
		}
		
		td
		{
			height: auto;
		}
		
		td div
		{
			margin-left: 5px;
		}
		
		td ul
		{
			height: auto;
		}
		
		*.by
		{
			width: 200px;
		}
		
		*.br
		{
			border-right: 1px solid silver;
			border-bottom: 1px solid silver;
		}
		
		*.r
		{
			border-right: 1px solid silver;
		}
		
		*.b
		{
			border-bottom: 1px solid silver;
		}
		
		*.bt
		{
			text-align: center;
			vertical-align: middle;
			border-bottom: 1px solid silver;
			border-top: 1px solid silver;
			height: 30px;
		}

		*.bt div.buttonlink
		{
			width: 105px;
			margin-left: auto ;
			margin-right: auto ;
		}
		
		div#extend.buttonlink
		{
			margin-left: auto;
			margin-right: auto;
			font-size: 20px;
		}
		
		div#info_spoiler
		{
			display: none;
			padding:0px;
			margin:-1px;
		}
		
		div.info_left_container
		{
			float: left;
		}
		
		div.info_right_container
		{
			margin-left: 120px;	
			margin-right: 6px;
		}
		
		div.info_right_container input
		{
			width: 100%;
			display: table-cell;
		}
		
		div.infor_left_container
		{
			float: left;
			width: 110px;
			background: blue;
		}
		
		div.infor_right_container
		{
			display: inline-grid;
			margin-left: 120px;	
			margin-right: 7px;
			min-height: 100%;
			background: red;
		}
		
		div.infor_right_container textarea
		{
			width: 100%; 
		}
		
		textarea {
			resize: none;
		}
		
		div#addchar
		{
			border: 1px solid silver;
			width: 100%;
		}
		
		input#charc
		{
			width: 100%;
			display: table-cell;
		}
		
		div#saveinfo
		{
			margin-left: auto;
			margin-right: auto;
		}
		</style>
		
		<script type="text/javascript">
		function spoiler( id )
		{
			if($('#'+id).css('display') == 'none')
			{
				$('#'+id).slideDown();
				$('#extend img').attr('src', "<?php echo $rootfolder; ?>images/arrow_down.png");
			}
			else
			{
				$('#'+id).slideUp();
				$('#extend img').attr('src', "<?php echo $rootfolder; ?>images/arrow_up.png");
			}
		}
		
		var uid = <?php echo $_GET['uid']; ?>;
		var t = <?php if($t) echo "1"; else echo "0"; ?>;
		function addchar()
		{
			$.post( "<?php echo $rootfolder; ?>ajax/charedit.php", { type: "0", uid: uid, t: t, content: $('#charc').val()}, function( data) { 
				if(data.substr(0, 3) == "200")
				{
					$('#add_info').html("erfolgreich hinzugefügt");
					$('#charc').val("");
					$('#charc').focus();
				}
				else
				{
					$('#add_error').html(data);
				}
			});
		}
		</script>
	<h1><?php echo $title; ?></h1>
	<?php if(!empty($err)) { ?>
	<p>
		<font class="errormsg">
			<?php
				foreach($err as $e)
				{
					echo $e;
				}
			?>
		</font>
	</p>
	<?php } ?>
	<?php if(!$t) { ?>
	<table id="info" cellspacing="0">
		<form action="#" method="POST">
			<tr>
				<th colspan="2"><div onclick="spoiler('info_spoiler')" id="extend" class="buttonlink" title="Mehr laden">
						<a>Steckbrief<img src="<?php echo $rootfolder; ?>images/arrow_up.png"></a>
					</div></th>
			</tr>
			<tr>
				<td colspan="2">
					<div id="info_spoiler">
						<table cellspacing="0" id="info_content">
							<tr>
								<td class="br">
									<div class="info_left_container">1.LK:&nbsp;</div>
									<div class="info_right_container">
										<?php if($_GET['uid'] == $_SESSION['userid']) { ?>
											<input type="text" id="lk1" name="lk1" width="100%" value="<?php echo $info->lk1; ?>">
										<?php } else { ?>
											<span><?php echo $info->lk1; ?></span>
										<?php } ?>
									</div>
								</td>
								<td class="b" rowspan="3">
									<div class="infor_left_container">Was ich schon immer sagen wollte:&nbsp;</div>
									<div class="infor_right_container">
										<?php if($_GET['uid'] == $_SESSION['userid']) { ?>
											<textarea id="wiwts" name="wiwts"><?php echo $info->wiwts; ?></textarea>
										<?php } else { ?>
											<span><?php echo $info->wiwts; ?></span>
										<?php } ?>
									</div>
								</td>
							</tr>
							<tr>
								<td class="br">
									<div class="info_left_container">2.LK:&nbsp;</div>
									<div class="info_right_container">
										<?php if($_GET['uid'] == $_SESSION['userid']) { ?>
											<input type="text" id="lk2" name="lk2" width="100%" value="<?php echo $info->lk2; ?>">
										<?php } else { ?>
											<span><?php echo $info->lk2; ?></span>
										<?php } ?>
									</div>
								</td>
							</tr>
							<tr>
								<td class="br">
									<div class="info_left_container">3.LK:&nbsp;</div>
									<div class="info_right_container">
										<?php if($_GET['uid'] == $_SESSION['userid']) { ?>
											<input type="text" id="lk3" name="lk3" width="100%" value="<?php echo $info->lk3; ?>">
										<?php } else { ?>
											<span><?php echo $info->lk3; ?></span>
										<?php } ?>
									</div>
								</td>
							</tr>
							<tr>
								<td class="br">
									<div class="info_left_container">Jahrgang:&nbsp;</div>
									<div class="info_right_container">
										<?php if($_GET['uid'] == $_SESSION['userid']) { ?>
											<input type="text" id="year" name="year" width="100%" value="<?php echo $info->year; ?>">
										<?php } else { ?>
											<span><?php echo $info->year; ?></span>
										<?php } ?>
									</div>
								</td>
								<td <?php if($_GET['uid'] == $_SESSION['userid']) echo "class=\"b\""; ?> rowspan="3">
									<div class="infor_left_container">Ich danke:&nbsp;</div>
									<div class="infor_right_container">
										<?php if($_GET['uid'] == $_SESSION['userid']) { ?>
											<textarea id="thanks" name="thanks"><?php echo $info->thanks; ?></textarea>
										<?php } else { ?>
											<span><?php echo $info->thanks; ?></span>
										<?php } ?>
									</div>
								</td>
							</tr>
							<tr>
								<td class="br">
									<div class="info_left_container">Berufswunsch:&nbsp;</div>
									<div class="info_right_container">
										<?php if($_GET['uid'] == $_SESSION['userid']) { ?>
											<input type="text" id="jobwish" name="jobwish" width="100%" value="<?php echo $info->jobwish; ?>">
										<?php } else { ?>
											<span><?php echo $info->jobwish; ?></span>
										<?php } ?>
									</div>
								</td>
							</tr>
							<tr>
								<td class="<?php if($_GET['uid'] == $_SESSION['userid']) echo "b"; ?>r">
									<div class="info_left_container">Spitzname:&nbsp;</div>
									<div class="info_right_container">
										<?php if($_GET['uid'] == $_SESSION['userid']) { ?>
											<input type="text" id="nick" name="nick" width="100%" value="<?php echo $info->nick; ?>">
										<?php } else { ?>
											<span><?php echo $info->nick; ?></span>
										<?php } ?>
									</div>
								</td>
							</tr>
							<?php if($_GET['uid'] == $_SESSION['userid']) { ?>
							<tr>
								<td colspan="2">
									<div style="text-align: center;">
										<input type="hidden" name="uid" id="uid" value="<?php echo $_GET['uid']; ?>">
										<input type="hidden" name="t" id="t" value="<?php echo $t?"1":"0"; ?>">
										<div onclick="saveinfo()" id="saveinfo" class="buttonlink savebutton" title="speichern">
											<a>Speichern<img src="<?php echo $rootfolder; ?>images/save.png"></a>
										</div>
									</div>
								</td>
							</tr>
							<?php } ?>
						</table>
					</div>
				</td>
			</tr>
		</form>
	</table>
	<br><br>
	<?php } ?>
	<?php if($_GET['uid'] != $_SESSION['userid']) { ?>
	<p>
		<h2>Charakteristik hinzufügen</h2>
		<div id="add_error" class="errormsg"></div>
		<div id="add_info" class="infomsg"></div>
		<div id="addchar">
			<input style="float:right;width:115px;" type="button" onclick="addchar()" value="hinzufügen">
			<div style="margin-right: 120px;">
				<input type="text" name="charc" id="charc">
			</div>
		</div>
	</p>
	<br><br>
	<?php } ?>
	
	<table cellspacing="0">
		<tr>
			<?php if(!$_SESSION['hidemyass'] && $_SESSION['admin_nsa'] && $_SESSION['admin']) { ?>
				<th class="br by">Von</th>
			<?php } ?>
			<th class="br">Eintrag</th>
			<th class="b delete">Löschen</th>
		</tr>
		<?php getChars($_GET['uid'], $t); ?>
	</table>
		<?php
			//getChars($_GET['uid'], $t);
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
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>