<?php

$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";
session_start();

if(isset($_POST['postdetect']) && isset($_SESSION['userid']))
{	
	$f = file_get_contents($_SERVER['DOCUMENT_ROOT'].$rootfolder."doc/settings.json");
	$json = json_decode($f, true);
	
	$file = fopen($_SERVER['DOCUMENT_ROOT'].$rootfolder."config/settings.cfg", 'w');
	foreach($json as $set)
	{
		$val = (isset($_POST[$set['name']])?$_POST[$set['name']]:"");
		if($val == "on")
			$val = "true";
		else if($val == "")
			$val = "false";
		fwrite($file, $set['name']."=".$val."\n");
	}
	fclose($file);
}

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Einstellungen";
ob_end_flush();
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");



function createSettingsTable()
{
	global $rootfolder;
	$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].$rootfolder."doc/settings.json");
	$json = json_decode($file, true);
	
	
	foreach($json as $set)
	{
		echo "<tr id=\"".$set['name']."\"><td class=\"display br\"><div>".$set['display']."</div></td>".getTypeInput($set['type'], $set['name'])."<td class=\"description b\"><div>".$set['description']."</div></td></tr>";
	}
}

function getTypeInput($type, $name)
{
	switch($type)
	{
		case 0: //bool
			return "<td class=\"val br\"><input name=\"".$name."\" type=\"checkbox\" class=\"value\" ".($_SESSION[$name]?"checked":"")."></td>";
			break;
		case 1: //int
			return "<td class=\"val br\"><input name=\"".$name."\" type=\"number\" class=\"value\" value=\"".$_SESSION[$name]."\"></td>";
			break;
		case 2: //string
			return "<td class=\"val br\"><input name=\"".$name."\" type=\"text\" class=\"value\" value=\"".$_SESSION[$name]."\"></td>";
			break;
		case 3: //email
			return "<td class=\"val br\"><input name=\"".$name."\" type=\"email\" class=\"value\" value=\"".$_SESSION[$name]."\"></td>";
			break;
	}	
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

		*.bt div.buttonlink
		{
			width: 105px;
			margin-left: auto ;
			margin-right: auto ;
		}
		</style>
	<h1><?php echo $title; ?></h1>
	<form action="#" method="POST">
	<table cellspacing="0" >
		<tr>
			<th class="display br" >Name</th>
			<th class="val br" >Wert</th>			
			<th class="description b" >Beschreibung</th>
		</tr>
		<?php createSettingsTable(); ?>
		<tr>
			<td colspan="3" class="b"><input type="hidden" name="postdetect" value="1"><div onclick="$('form').submit();" style="margin-left:auto; margin-right:auto;" class="buttonlink savebutton" title="speichern">
				<a>Speichern<img src="<?php echo $rootfolder; ?>images/save.png"></a>
			</div></td>
		</tr>
	</table>
	</form>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>