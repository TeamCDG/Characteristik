<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

$t = false;
if( isset($_GET['t']) && ($_GET['t']=="true" || $_GET['t']=="1" || $_GET['t']==1 || strpos($_GET['t'], "true") === true))
	$t = true;
else
	$t = false;

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Steckbrief Designer";
ob_end_flush();
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/infodesigner.php");

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
			display: block;
			padding:0px;
			margin:-1px;
		}
		
		#info_spoiler td {
			height: 21px;
			width: 50%;
			vertical-align:top;
			position: relative;
		}
		
		#info_spoiler tr {
			height: 21px;
		}
		
		*.container_left {
			width: 112px;
			float:left;
			word-wrap:break-word;
		}
		
		*.container_right {
			float: right;
			width: calc(100% - 120px);
			margin-left: 120px;
			margin-top: 0px;
			height: calc(100% - 2px);
			position: absolute;
		}
		
		textarea {
			resize: none;
		}
		
		*.container_right input {
			width: calc(100% - 5px);
		}
		
		*.container_right textarea {
			height: calc(100% - 6px);
			width: calc(100% - 6px);
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
		
		*.delete {
			width: 90px;
		}
		
		*.side {
			width: 80px;
		}
		
		*.rows {
			width: 90px;
		}
		
		*.rows input{
			width: 80px;
		}
		
		*.minlen { 
			width: 120px;
		}
		
		*.minlen input{
			width: 110px;
		}
		
		*.type {
			width: 135px;
		}
		
		div.container
		{
			margin-left: 0px;
			margin-right: 5px;
		}
		
		div.container input
		{
			width: 100%;
			display: table-cell;
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
		
		var adding = false;
		var rows = <?php echo getRowCount(); ?>;
		function addLine()
		{
			if(adding) return;
			
			saveValues();
			
			adding = true;
			var newLineTemplate = "<td class=\"br label\"><div style=\"display:none;\" class=\"container\"><input onkeyup=\"updateLabel("+rows+")\" id=\"designer_row_"+rows+"_label\" name=\"designer_row_"+rows+"_label\" width=\"100%\"></div></td>"+
								  "<td class=\"br type\"><div style=\"display:none;\"><select onchange=\"updateType("+rows+")\" id=\"designer_row_"+rows+"_type\" name=\"designer_row_"+rows+"_type\" size=\"1\"><option value=\"0\" selected>Text</option><option value=\"1\">Text (mehrzeilig)</option><option value=\"2\">Zahl</option><option value=\"3\">Job</option><option value=\"4\">Geburtsjahrgang</option></select></div></td>"+
								  "<td class=\"br minlen\"><div style=\"display:none;\"><input type=\"number\" id=\"designer_row_"+rows+"_minlen\" name=\"designer_row_"+rows+"_minlen\" step=\"1\" min=\"0\" value=\"0\"></div></td>"+
								  "<td class=\"br rows\"><div style=\"display:none;\"><input onchange=\"rebuilt()\" type=\"number\" id=\"designer_row_"+rows+"_rows\" name=\"designer_row_"+rows+"_rows\" step=\"1\" min=\"1\" value=\"1\"></div></td>"+
								  "<td class=\"br side\"><div style=\"display:none;\"><select onchange=\"rebuilt()\" id=\"designer_row_"+rows+"_side\" name=\"designer_row_"+rows+"_side\" size=\"1\"><option value=\"0\" selected>Links</option><option value=\"1\">Rechts</option><option value=\"2\">Beide</option></select></div></td>"+
								  "<td class=\"b delete\"><div style=\"display:none;\"><div onclick=\"deleteLine("+rows+")\" id=\"designer_row_"+rows+"_delete\" class=\"buttonlink deletebutton\" title=\"Löschen\"><a>Löschen<img src=\"<?php echo $rootfolder; ?>images/x.png\"></a></div></div></td>";
			var tmp = $('#designer_last').html();
			$('#designer_last').html(newLineTemplate);			
			$('#designer_last').attr('id', 'designer_row_'+rows);
			$('#infodesigner').html($('#infodesigner').html()+ "<tr id=\"designer_last\">" +tmp + "</tr>");
			$('#designer_row_'+rows).children().children().slideDown(400, function() {
				
				adding = false;
				
			});
			rows++;
			restoreValues();
			
			rebuilt();
		}
		
		var deleting = false;
		function deleteLine(row)
		{
			if(deleting) return;
			
			var done = false;
			deleting = true;
			$('#designer_row_'+row).children().children().slideUp(400, function() {
				$('#designer_row_'+row).remove();
				
				if(!done)
				{
					row++;
					while( row < rows )
					{
						$('#designer_row_'+row).attr('id', 'designer_row_'+(row-1));
						
						$('#designer_row_'+row+'_label').attr('name', 'designer_row_'+(row-1)+'_label');
						$('#designer_row_'+row+'_label').attr('id', 'designer_row_'+(row-1)+'_label');
						
						$('#designer_row_'+row+'_type').attr('name', 'designer_row_'+(row-1)+'_type');
						$('#designer_row_'+row+'_type').attr('id', 'designer_row_'+(row-1)+'_type');
						
						$('#designer_row_'+row+'_minlen').attr('name', 'designer_row_'+(row-1)+'_minlen');
						$('#designer_row_'+row+'_minlen').attr('id', 'designer_row_'+(row-1)+'_minlen');
						
						$('#designer_row_'+row+'_rows').attr('name', 'designer_row_'+(row-1)+'_rows');
						$('#designer_row_'+row+'_rows').attr('id', 'designer_row_'+(row-1)+'_rows');
						
						$('#designer_row_'+row+'_side').attr('name', 'designer_row_'+(row-1)+'_side');
						$('#designer_row_'+row+'_side').attr('id', 'designer_row_'+(row-1)+'_side');
						
						$('#designer_row_'+row+'_delete').attr('onclick', 'deleteLine('+(row-1)+')');
						$('#designer_row_'+row+'_delete').attr('id', 'designer_row_'+(row-1)+'_delete');
						
						row++;
					}
					
					rows--;
					done = true;
					
					rebuilt();
					
					deleting = false;
				}
			});
		}
		
		function rebuilt()
		{
			var table = [];
			var rowCount = 0;
			for(var i = 0; i < rows; i++)
			{
				rowCount += parseInt($('#designer_row_'+i+'_rows').val());	
				for(var add = 0; add < parseInt($('#designer_row_'+i+'_rows').val()); add++)
				{
					table.push(["",""]);
				}
			}
			
			for(var i = 0; i < rows; i++)
			{
				var rowsNeeded = parseInt($('#designer_row_'+i+'_rows').val());
				var side = parseInt($('#designer_row_'+i+'_side').val())
				
				var pos = findFirstFree(side, rowsNeeded, table);
				
				for(var p = 0; p < rowsNeeded; p++)
				{
					switch(side)
					{
						case 0:
							table[pos+p][0] = (p==0?"":"_")+i;
							break;
						case 1:
							table[pos+p][1] = (p==0?"":"_")+i;
							break;
						case 2:
							table[pos+p][0] = (p==0?"":"_")+i;
							table[pos+p][1] = (p==0?"":"_")+i;
							break;
					}
				}
			}
			
			<?php if(!$_SESSION['debug']) { ?> console.log("-------------------------------------------------------"); <?php } ?>
			for(var b = 0; b < table.length; b++)
			{
				<?php if(!$_SESSION['debug']) { ?> console.log(table[b][0]+" | "+table[b][1]); <?php } ?>
			}
			
			$("#info_content").html("");
			
			for(var i = 0; i < table.length; i++)
			{
				<?php if(!$_SESSION['debug']) { ?> console.log("-------------------------------------------------------"); <?php } ?>
				<?php if(!$_SESSION['debug']) { ?> console.log("creating "+i+" : "+table[i][0]+" | "+table[i][1]); <?php } ?>
				
				if((table[i][1] != "" && table[i][1].substr(0,1) != "_") || (table[i][0] != "" && table[i][0].substr(0,1) != "_"))
				{
					<?php if(!$_SESSION['debug']) { ?> console.log("somethings there"); <?php } ?>
					if(table[i][0] == table[i][1])
					{
						<?php if(!$_SESSION['debug']) { ?> console.log("double row!"); <?php } ?>
						$("#info_content").html($("#info_content").html() +"<tr><td class=\"b\" colspan=\"2\" rowspan=\""+getRowCount(parseInt(table[i][0]))+
						"\"><div id=\"designer_"+table[i][0]+"_label\" class=\"container_left\">"+
							getLabel(parseInt(table[i][0]))+"</div><div id=\"designer_"+table[i][0]+"_container\" class=\"container_right\">"+getFieldByType(table[i][1])+"</div></td></tr>");
					}
					else
					{
						if(table[i][0] == "" && table[i][1] != "" && table[i][1].substr(0,1) != "_")
						{
							$("#info_content").html($("#info_content").html() +"<tr><td class=\"br\"><div class=\"container_left\"></div></td><td class=\"b\" colspan=\"2\" rowspan=\""+getRowCount(parseInt(table[i][1]))+
							"\"><div id=\"designer_"+table[i][1]+"_label\" class=\"container_left\">"+
							getLabel(parseInt(table[i][1]))+"</div><div id=\"designer_"+table[i][1]+"_container\" class=\"container_right\">"+getFieldByType(table[i][1])+"</div></td></tr>");
							<?php if(!$_SESSION['debug']) { ?> console.log("left free right not"); <?php } ?>
							
						}
						else if(table[i][0] != "" && table[i][0].substr(0,1) != "_" && table[i][1] != "" && table[i][1].substr(0,1) != "_")
						{
							<?php if(!$_SESSION['debug']) { ?> console.log("stuff in both"); <?php } ?>
							$("#info_content").html($("#info_content").html() +"<tr><td class=\"br\" rowspan=\""+getRowCount(parseInt(table[i][0]))+
							"\"><div id=\"designer_"+table[i][0]+"_label\" class=\"container_left\">"+
							getLabel(parseInt(table[i][0]))+"</div><div class=\"container_right\">"+getFieldByType(table[i][0])+"</div></td>"
							+"<td class=\"b\" rowspan=\""+getRowCount(parseInt(table[i][1]))+
							"\"><div id=\"designer_"+table[i][1]+"_label\" class=\"container_left\">"+
							getLabel(parseInt(table[i][1]))+"</div><div id=\"designer_"+table[i][1]+"_container\" class=\"container_right\">"+getFieldByType(table[i][1])+"</div></td></tr>");
						}
						else
						{
							<?php if(!$_SESSION['debug']) { ?> console.log("right free left not"); <?php } ?>
							var nhtml = $("#info_content").html() +"<tr id=\"RFLN_ELEM\"><td class=\"br\" rowspan=\""+getRowCount(parseInt(table[i][0]))+
							"\"><div id=\"designer_"+table[i][0]+"_label\" class=\"container_left\">"+
							getLabel(parseInt(table[i][0]))+"</div><div id=\"designer_"+table[i][0]+"_container\" class=\"container_right\">"+getFieldByType(table[i][0])+"</div></td>";
							if(table[i][1].substr(0,1) != "_")
								nhtml += "<td class=\"b\"><div class=\"container_left\"></div></td></tr>";
							$("#info_content").html(nhtml);
						}
					}
					
					
				}
				else if(table[i][0].substr(0,1) == "_" || table[i][1].substr(0,1) == "_")
				{
					<?php if(!$_SESSION['debug']) { ?> console.log("creating empty element"); <?php } ?>
					if(table[i][0].substr(0,1) == "_" && table[i][1].substr(0,1) == "_")
					{
						$("#info_content").html($("#info_content").html() + "<tr></tr>");
					}
					else if(table[i][0].substr(0,1) == "" && table[i][1].substr(0,1) == "_")
					{
						$("#info_content").html($("#info_content").html() + "<tr><td class=\"br\"><div class=\"container_left\"></div></td></tr>");
					}
					else if(table[i][1] == "")
					{
						<?php if(!$_SESSION['debug']) { ?> console.log("inserting empty space element"); <?php } ?>
						$("#info_content").html($("#info_content").html() + "<tr><td class=\"b\"><div class=\"container_left\"></div></td></tr>");
					}
				}
						
			
			}
		}
		
		function getLabel(row)
		{
			return $('#designer_row_'+row+'_label').val();
		}
		
		function getRowCount(row)
		{
			return parseInt($('#designer_row_'+row+'_rows').val());
		}
		
		function getFieldByType(row)
		{
			switch(parseInt($('#designer_row_'+row+'_type').val()))
			{
				case 0:
					return "<input type=\"text\" id=\"designer_"+row+"_input\">";
					break;
					
				case 1:
					return "<textarea id=\"designer_"+row+"_input\"></textarea>";
					break;
					
				case 2:
					return "<input type=\"number\" id=\"designer_"+row+"_input\">";
					break;
					
				case 3:
					return "<input type=\"text\" id=\"designer_"+row+"_input\">";
					break;
					
				case 4:
					return "<input type=\"number\" min=\"1900\" max=\"2015\" id=\"designer_"+row+"_input\">";
					break;
			}
		}
		
		function findFirstFree(side, rows, table)
		{
			// 0 ==> left
			// 1 ==> right
			// 2 ==> both
			for(var i = 0; i < table.length; i++)
			{
				switch(side)
				{
					case 0:
						var free = true;
						
						for(var off = 0; off < rows; off++)
						{
							free = table[i+off][0] == "";
							if(!free) break;
						}
						
						if(free) return i;
						break;
					case 1:
						var free = true;
						
						for(var off = 0; off < rows; off++)
						{
							free = table[i+off][1] == "";
							if(!free) break;
						}
						
						if(free) return i;
						break;
					case 2:
						var free = true;
						
						for(var off = 0; off < rows; off++)
						{
							<?php if(!$_SESSION['debug']) { ?> console.log("off: "+off+" | table["
							+(i+off)+"][0].length: "+
							table[i+off][0].length+" ("+
							table[i+off][0]+") | table["+(i+off)+"][1].length: "+
							table[i+off][1].length+" ("+table[i+off][1]+")"); <?php } ?>
							free = (table[i+off][0].length == 0 && table[i+off][1].length == 0);
							if(!free) break;
						}
						
						if(free) return i;
						break;
				}
			}
			
			return -1;
		}
		
		function saveValues()
		{
			for(var row = 0; row < rows; row++)
			{		
				<?php if(!$_SESSION['debug']) { ?> console.log("saving "+row+" of "+rows); <?php } ?>
				$('#designer_row_'+row+'_label').attr('data-value', $('#designer_row_'+row+'_label').val());
						
				$('#designer_row_'+row+'_type').attr('data-value', $('#designer_row_'+row+'_type').val());
						
				$('#designer_row_'+row+'_minlen').attr('data-value', $('#designer_row_'+row+'_minlen').val());
						
				$('#designer_row_'+row+'_rows').attr('data-value', $('#designer_row_'+row+'_rows').val());
						
				$('#designer_row_'+row+'_side').attr('data-value', $('#designer_row_'+row+'_side').val());
						
			}
		}
		
		function restoreValues()
		{
			for(var row = 0; row < (rows-1); row++)
			{						
				<?php if(!$_SESSION['debug']) { ?> console.log("restore "+row+" of "+rows); <?php } ?>
				$('#designer_row_'+row+'_label').val($('#designer_row_'+row+'_label').attr('data-value'));
						
				$('#designer_row_'+row+'_type').val($('#designer_row_'+row+'_type').attr('data-value'));
						
				$('#designer_row_'+row+'_minlen').val($('#designer_row_'+row+'_minlen').attr('data-value'));
						
				$('#designer_row_'+row+'_rows').val($('#designer_row_'+row+'_rows').attr('data-value'));
						
				$('#designer_row_'+row+'_side').val($('#designer_row_'+row+'_side').attr('data-value'));
						
			}
		}
		
		function updateLabel(row)
		{
			<?php if(!$_SESSION['debug']) { ?> console.log("updating label of "+row); <?php } ?>
			$('#designer_'+row+'_label').html($('#designer_row_'+row+'_label').val());
		}
		
		function updateType(row)
		{
			<?php if(!$_SESSION['debug']) { ?> console.log("updating type of "+row); <?php } ?>
			$('#designer_'+row+'_container').html(getFieldByType(row));
		}
		
		var saving = false;
		function save()
		{
			if(saving) return;
			saving = true;
			<?php if(!$_SESSION['debug']) { ?> console.log("------------> saving"); <?php } ?>
			
			var rowCount = rows;
			var label = [];
			var row_data = [];
			var type = [];
			var side = [];
			var min_length = [];
			
			for(var row = 0; row < rows; row++)
			{	
				<?php if(!$_SESSION['debug']) { ?> console.log("pushing row "+row); <?php } ?>
				label.push($('#designer_row_'+row+'_label').val());						
				type.push($('#designer_row_'+row+'_type').val());						
				min_length.push($('#designer_row_'+row+'_minlen').val());						
				row_data.push($('#designer_row_'+row+'_rows').val());						
				side.push($('#designer_row_'+row+'_side').val());						
			}
						
			$.post('<?php echo $rootfolder; ?>ajax/infodesigner.php', {
			'rowCount': rowCount,
			'label[]': label,
			'rows[]': row_data,
			'type[]': type,
			'side[]': side,
			'min_length[]': min_length
			} , function( data) {
				
				
				<?php if(!$_SESSION['debug']) { ?> console.log(data); <?php } ?>
				
				$('*.infomsg').css('display', 'none');
				$('*.infomsg').html(data);
				$('*.infomsg').slideDown();
				saving = false;
				<?php if(!$_SESSION['debug']) { ?>
				var id = setInterval(function() {
					clearInterval(id);
					$('*.infomsg').slideUp();
				}, 3000);
				<?php } ?>
			});
			
		}
		</script>
	<h1><?php echo $title; ?></h1>
	<p>
		<div class="errormsg">
		</div>
		<div class="infomsg">
		</div>
	</p>
	<table cellspacing="0">
		<tbody id="infodesigner" >
			<tr>
				<th class="br label">Beschriftung</th>
				<th class="br type">Datentyp</th>
				<th class="br minlen">Mindestlänge</th>
				<th class="br rows">Zeilen</th>
				<th class="br side">Seite</th>
				<th class="b delete">Löschen</th>
			</tr>
			<?php printInfoBuilder($rootfolder); ?>
			<tr id="designer_last">
				<td colspan="6">
					<div style="text-align: center; width: 240px; margin-left: auto; margin-right: auto;">
						<div onclick="addLine()" id="addline" class="buttonlink" title="Feld hinzufügen" style="width: 130px; float: left;">
							<a>Feld hinzufügen<img src="<?php echo $rootfolder; ?>images/plus.png"></a>
						</div>
						<div onclick="save()" id="saveinfo" class="buttonlink savebutton" title="speichern" style="float: left; margin-left: 5px;">
							<a>Speichern<img src="<?php echo $rootfolder; ?>images/save.png"></a>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<br>
	<br>
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
						<table cellspacing="0">
							<tbody id="info_content">
								<?php printInfo(false, $_SESSION['userid']); ?>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
		</form>
	</table>
	<br><br>
		<p><a href="index.php">MenÃ¼</a><br><br><a href="logout.php">Logout</a><br>keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope"; ?></p>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>