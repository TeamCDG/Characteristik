<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Manager";
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/infodesigner.php");

function listBackups()
{
	global $rootfolder;
	$sql = "SELECT * FROM `backup` WHERE `deleted`=0";
	$res = mysql_query($sql) or die("ERROR 418: Query failed: ".$sql." ".mysql_error());
	while($row = mysql_fetch_array($res))
	{
		echo "<tr id=\"".$row['id']."\">".
		"<td class=\"br\"><div id=\"date\">".decodeDate($row['date'])."</div></td>".
		"<td class=\"br\"><div  class=\"boolcol\">".boolToColorLang($row['sentmail'])."</div></td>".
		"<td class=\"br\"><div>".$row['mailreciever']."</div></td>".
		"<td class=\"br\"><div><a target=\"_blank\" href=\"".$rootfolder.$row['filename']."\">".$row['filename']."</a></div></td>".
		"<td class=\"br\"><div class=\"boolcol\">".boolToColorLang($row['manual'])."</div></td>".
		"<td class=\"b\"><div onclick=\"restore(".$row['id'].")\" id=\"restore_".$row['id']."\" class=\"buttonlink restorebutton\" style=\"float: left;\"  title=\"Backup wiederherstellen\">".
		"<a>Wiederherstellen<img src=\"".$rootfolder."images/restore.png\"></a></div>".
		"<div onclick=\"delte(".$row['id'].")\" id=\"delete_".$row['id']."\" class=\"buttonlink deletebutton\" style=\"float: left;\" title=\"Backup löschen\">".
		"<a>Löschen<img src=\"".$rootfolder."images/x.png\"></a></div></td>".
		
											
		"</tr>";
	}
}
function boolToColorLang($bool)
{
	if($bool == 1 || $bool)
	{
		return "<font class=\"bool_true\" style=\"color: #00FF00; font-weight: bold;\">Ja</font>";
	}
	else
	{
		return "<font class=\"bool_false\" style=\"color: #FF0000; font-weight: bold;\">Nein</font>";
	}
}
function decodeDate($mdate)
{
	$tmp = explode(' ', $mdate);
	$date = explode('-', $tmp[0]);
	$time = explode(':', $tmp[1]);
	
	return $date[2].".".$date[1].".".$date[0]." um ".$time[0].":".$time[1].":".$time[2];
}
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

		
		th
		{
			height: 30px;
			background: rgba(255, 255, 255, .1);
		}
		
		td
		{
			height: auto;
			overflow: hidden
		}
		
		td div
		{
			margin-left: 5px;
		}
		
		#info_content tr td div
		{			
			min-height:22px;
		}
		
		td ul
		{
			height: auto;
		}
		
		*.display
		{
			width: 200px;
		}
		
		*.val
		{
			width: 150px;
		}
		
		*.value
		{
			width: calc(100% - 5px);
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

		div.buttonlink 
		{
			margin-left: auto ;
			margin-right: auto ;
			text-align: center;
		}
		#backup_spoiler div {
			
			//width: calc(100% - 10px);
			vertical-align: top;
			position: relative;
		}
		div#extend.buttonlink {
		    margin-left: auto;
		    margin-right: auto;
		    font-size: 20px;
			font-weight: bold;
		}
		*.boolcol
		{
			max-width: 100px;
		}
		</style>
		<script type="text/javascript">
		function spoiler( id )
		{
			$('li.transition').mouseenter(function() {
				$('#'+id+' *').css("z-index", "-1");
			});
			
			$('li.transition').mouseover(function() {
				$('#'+id+' *').css("z-index", "-1");
			});
			
			$('li.transition').mouseleave(function() {
				$('#'+id+' *').css("z-index", "");
			});
			
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
		
		var manual_Backup = false;
		var manualBackupAnimationId = -1;
		function manualBackup()
		{
			if(manual_Backup) return;
			manual_Backup = true;
			clearInterval(manualBackupAnimationId);
			
			var val = $('#charc').val();
			$.post( "<?php echo $rootfolder; ?>ajax/backup.php", { method: "0"}, function( data) {
				$('#backup_content').html($('#backup_content').html() + "<tr><td>BAUM</td></tr>");
				var res = JSON.parse(data);
				if(res.status == "200")
				{
					//$("#char_head").after("<tr id=\"char_row_"+res.id+"\"></tr>").next().html(byTemplate(res.id, res.name) + contentTemplate(res.id, val) + deleteTemplate(res.id));
					//slideDownRow(res.id);
					//$('#charc').val("");
					//$('#charc').focus();
					
					//$('#add_info').css('display', 'none');
					//$('#add_info').html(res.message);
					//$('#add_info').slideDown();
					<?php if(!$_SESSION['debug']) { ?>
					manualBackupAnimationId = setInterval(function() {
						clearInterval(manualBackupAnimationId);
						$('#add_info').slideUp();
					}, 3000);
					<?php } ?>
				}
				else
				{
					//$('#add_error').css('display', 'none');
					//$('#add_error').html(res.message);
					//$('#add_error').slideDown();
					<?php if(!$_SESSION['debug']) { ?>
					manualBackupAnimationId = setInterval(function() {
						clearInterval(manualBackupAnimationId);
						//$('#add_error').slideUp();
					}, 5000);
					<?php } ?>
				}
				
				manual_Backup = false;
			});
		}
		</script>
	<h1><?php echo $title; ?></h1>
	<div style="border:1px solid silver"><div onclick="spoiler('backup_spoiler')" id="extend" class="buttonlink" title="Mehr laden">
						<a>Backup<img src="<?php echo $rootfolder; ?>images/arrow_up.png"></a>
					</div>
					<div id="backup_spoiler" style="display: none;">
						<table cellspacing="0" id="backup_content">
							
							<tr>
								<th class="br">Datum</th>
								<th class="br boolcol">Email gesendet</th>
								<th class="br">Email Empfänger</th>
								<th class="br">Datei</th>
								<th class="br boolcol">Manuell</th>
								<th class="b">Wiederherstellen</th>
							</tr>
							<?php listBackups(); ?>
						</table>
						<div style="text-align: center;">
										<input type="hidden" name="uid" id="uid" value="<?php echo $_GET['uid']; ?>">
										<input type="hidden" name="t" id="t" value="<?php echo $t?"1":"0"; ?>">
										<div onclick="manualBackup()" id="manualBackup" class="buttonlink backupbutton" title="speichern">
											<a>Manuelles Backup erstellen<img src="<?php echo $rootfolder; ?>images/save.png"></a>
										</div>
									</div>
					</div></div>
	<!--<table id="info" cellspacing="0">
		<form action="#" method="POST">
			<tr>
				<th colspan="2"><div onclick="spoiler('backup_spoiler')" id="extend" class="buttonlink" title="Mehr laden">
						<a>Backup<img src="<?php echo $rootfolder; ?>images/arrow_up.png"></a>
					</div></th>
			</tr>
			<tr>
				<td colspan="2">
					<div id="backup_spoiler">
						<table cellspacing="0" id="info_content">
							
							
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
						</table>
					</div>
				</td>
			</tr>
		</form>
	</table>--!>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>