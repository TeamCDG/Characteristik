
<?php 
//$rootfolder = str_replace('//', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '/', str_replace('\\', '/', substr(__DIR__, 0, strrpos(__DIR__, '\\'))).'/'));
function getNewCharCount($id)
{
	$ls = getMaxId($id);
	$sql = "SELECT COUNT(*) AS c FROM `uchar` WHERE `holder`='".$_SESSION['userid']."' AND `id`>'".mysql_real_escape_string($ls)."' ORDER BY `id` DESC LIMIT 1;";
	$res = mysql_query($sql) or die("ERROR 418: Query failed: ".$sql." ".mysql_error());
	return mysql_fetch_object($res)->c;
}

function infoEmpty($uid)
{
	$sql = "SELECT COUNT(*) AS c FROM `info` WHERE `uid`='".$uid."'; ";
	$res = mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
	
	if($obj = mysql_fetch_array($res))
	{
		if(intval($obj['c']) > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		return true;
	}
}

function infoPartial($uid)
{
	$sql = "SELECT * FROM `info` WHERE `uid`='".$uid."'; ";
	$res = mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
	
	if(mysql_num_rows($res) == 0) {
		return false; //info inexistent
	} else {
		$userInfo = mysql_fetch_array($res);
		$sql = "SELECT * FROM `infobuilder`; ";
		$res = mysql_query($sql) or die ("ERROR: Query failed: $sql @".__FILE__.":".__FUNCTION__."(".__LINE__.") - ".mysql_error());
		
		while($row = mysql_fetch_array($res))
		{
			if(strlen($userInfo[$row['id']]) < intval($row['min_length']))
				return true;
		}
		
		return false;
	}
	
	return false;
}

function replaceTextConstants($str)
{
	$str = str_replace("%ownlink%", "<a href=\"/".$rootfolder."c/c/showuser/?uid=".$_SESSION['userid']."\">Meine Seite</a>", $str);
	
	return $str;

}
?>
 <div id="container">
        <div id="border">
            <ul id="topnav">				
				<li id="searchcontainer">
					<form id="searchform" action="#" method="post">
						<input type="hidden" id="teacher" name="teacher" value="0">
						<input type="hidden" id="id" name="uid" value="-1">
						
						<div id="searchbar">
							<!--<label> Suche: </label> !-->
							<div class="buttonlink homebutton" title="startseite" style="float:left; margin-left: 5px;"><a href="<?php echo $rootfolder; ?>"><img id="logo" src="<?php echo $rootfolder; ?>images/home.png" alt="Startseite"></a></div>
							<button id="glass" type="submit" name="search" value="" onclick="sub()" ></button>
							<span><input id="search" type="text" name="user" value=""></span>
							
							
						</div>
						
					</form>
				</li>
				<li class="transition"><a>Komitee</a>
					<ul>
						<li class="transition"><a href="<?php echo $rootfolder."com/overview/"; ?>">Übersicht</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."com/mine/"; ?>">Mein Komitee</a></li>
					</ul>
				</li>
				<li class="transition"><a>Termine</a>
					<ul>
						<li class="transition"><a href="<?php echo $rootfolder."dates/com/"; ?>">Komitee</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."dates/all/"; ?>">Gesamter Jahrgang</a></li>
					</ul>
				</li>
				<li class="transition"><a href="<?php echo $rootfolder."gallery/"; ?>">Galerie</a>
				</li>
				<li class="transition"><a>Charakteristik</a>
					<ul>
						<li class="transition"><a href="<?php echo $rootfolder."c/showuser/?uid=".$_SESSION['userid']; ?>">Meine Seite (<?php echo getNewCharCount($_SESSION['userid']); ?>)</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."c/cit/"; ?>">Zitate</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."c/gossip/"; ?>">Gerüchteküche</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."c/polls/"; ?>">Alle Umfragen</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."c/alls/"; ?>">Alle Schüler</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."c/allt/"; ?>">Alle Lehrer</a></li>
					</ul>
				</li>
				<?php if($_SESSION['permissions']['admin_view_requests'] ||
					     $_SESSION['permissions']['admin_edit_user'] ||
						 $_SESSION['permissions']['admin_set_new_user_pass'] ||
						 $_SESSION['permissions']['admin_manage_com'] ||
						 $_SESSION['permissions']['admin_manage_user'] ||
						 $_SESSION['permissions']['admin_manage_permissions'] ||
						 $_SESSION['permissions']['admin_manage_gallery'] ||
						 $_SESSION['permissions']['admin_manage_info'] ||
						 $_SESSION['permissions']['admin_manage_dates'] ||
						 $_SESSION['permissions']['admin_manage_char'] ||
						 $_SESSION['permissions']['admin_manage_cit'] ||
						 $_SESSION['permissions']['admin_manage_gossip'] ||
						 $_SESSION['permissions']['admin_manage_backup'] ||
						 $_SESSION['permissions']['admin_design_info'] ||
						 $_SESSION['permissions']['admin_backup_manual'] ||
						 $_SESSION['permissions']['admin_backup_restore'] ||
						 $_SESSION['permissions']['admin_hidemyass']) { ?>
				<li class="transition"><a>Admin</a>
					<ul>
						<li class="transition"><a href="<?php echo $rootfolder."admin/request/"; ?>">Löschanfragen</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."admin/adduser/"; ?>">Nutzer hinzufügen</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."admin/edit/"; ?>">Nutzer bearbeiten</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."admin/newpass/"; ?>">Neues Passwort vergeben</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."admin/manage/"; ?>">Backupmanager</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."admin/settings/"; ?>">Einstellungen</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."admin/infodesigner/"; ?>">Steckbrief Designer</a></li>						
						<li class="transition"><a href="<?php echo $rootfolder."admin/addpoll/"; ?>">Neue Umfrage</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."admin/tools/"; ?>">Tools</a></li>
						<li class="transition"><a href="<?php echo $rootfolder."session_debug.php"; ?>">Debug</a></li>
					</ul>
				</li>
				<?php } ?>
			</ul>
			
		    <div></div>
		</div>
		<div id="content">
		<?php 
			$info_empty = infoEmpty($_SESSION['userid']);
			$info_partial = infoPartial($_SESSION['userid']);
		
			if($_SESSION['info_empty_reminder'] && $info_empty) { echo "<div id=\"reminder\">".replaceTextConstants($_SESSION['info_empty_reminder_text'])."</div>"; } 
			else if($_SESSION['info_partial_reminder'] && $info_partial) { echo "<div id=\"reminder\">".replaceTextConstants($_SESSION['info_empty_partial_text'])."</div>"; } ?>						
				
		