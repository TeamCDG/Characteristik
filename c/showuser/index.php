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
ob_end_flush();
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/infodesigner.php");

/*
			<?php if(!$_SESSION['hidemyass'] && $_SESSION['admin_nsa']) { ?>
				<th class="br">Von</th>
			<?php } ?>
			<th class="br">Eintrag</th>
			<th class="b">Löschen</th>*/
			
if($_SESSION['userid'] == $_GET['uid'])
{
	setMaxId($_SESSION['userid']);
}

function setMaxId($id)
{
	$sql = "SELECT * FROM `uchar` WHERE `holder`='".mysql_real_escape_string($id)."' ORDER BY `id` DESC LIMIT 1;";
	$res = mysql_query($sql) or die("ERROR 418: Query failed: ".$sql." ".mysql_error());
	$ls = mysql_fetch_object($res)->id;
	
	$sql = "UPDATE `user` SET `lastseen`='".$ls."' WHERE `id`='".mysql_real_escape_string($id)."';";
	$res = mysql_query($sql) or die("ERROR 418: Query failed: ".$sql." ".mysql_error());
}

function getChars($uid, $teacher)
{
	global $rootfolder;
	$table = $teacher===true?"`tchar`":"`uchar`";
	
	// $sql = "SELECT COUNT(*) AS c FROM $table WHERE `holder` = $uid AND `visible` = '1'";
	// $res = mysql_query($sql) or die ("ERROR #004: Query failed: $sql @showuser - ".mysql_error());
	// $count = mysql_fetch_object($res)->c;
	
	$sql = "SELECT * FROM $table WHERE `holder` = $uid AND `visible` = '1' ORDER BY `id` DESC";
	$res = mysql_query($sql) or die ("ERROR #004: Query failed: $sql @showuser - ".mysql_error());
	
	while($row = mysql_fetch_object($res))
	{
		echo "<tr id=\"char_row_".$row->id."\">"; 
		if((($_SESSION['permissions']['char_see_from_own'] && $_SESSION['userid'] == $uid) || $_SESSION['permissions']['char_see_from_other']) && $_SESSION['admin_nsa'] && !$_SESSION['hidemyass'])
		{
			echo "<td class=\"br by\"><div class=\"char_by\"><a href=\"".$rootfolder."c/showuser/?uid=".$row->from."\">".getName($row->from, 0)."</a></div></td>";
		}
		
		if((($_SESSION['permissions']['char_read_own'] && $_SESSION['userid'] == $uid) || $_SESSION['permissions']['char_read_other']))
		{
			if($_SESSION['permissions']['char_edit_own'] && $_SESSION['userid'] == $row->from && $_SESSION['char_edit']) {
				echo "<td class=\"br content\"><div class=\"edit_container\"  id=\"char_edit_".$row->id."_container\">".$row->content."</div><div id=\"char_edit_".$row->id."_button\" style=\"float:right;\" onclick=\"editChar(".$row->id.")\" class=\"buttonlink editbutton\" title=\"bearbeiten\"><a>Bearbeiten<img src=\"".$rootfolder."images/edit.png\"></a></div></td>";
			}
			else
			{
				echo "<td class=\"br content\"><div>".$row->content."</div></td>";
			}
		}
		else
		{
			echo "<td class=\"br content\"><div>403 Access denied: please report to android hell for a teapot...</div></td>";
		}
		
		if((($_SESSION['permissions']['char_direct_delete_own'] && $_SESSION['userid'] == $uid) || $_SESSION['permissions']['char_direct_delete_other']))
		{
			echo "<td class=\"b delete\"><div onclick=\"deleteChar(".$row->id.")\" id=\"char_row_".$row->id."_delete\" class=\"buttonlink deletebutton\" title=\"Löschen\"><a>Löschen<img src=\"".$rootfolder."images/x.png\"></a></div></td>";
		}
		else if((($_SESSION['permissions']['char_delete_request_own'] && $_SESSION['userid'] == $uid) || $_SESSION['permissions']['char_delete_request_other']))
		{
			echo "<td class=\"b delete\">";
			if(isRequested($row->id, $teacher?1:0))
			{
				echo "<div>".getRequestStatus($row->id, $teacher?1:0)."</div>";
			}
			else
			{
				
				if($_SESSION['char_delete_request_reason_possible'])
				{
					echo "<div class=\"reason_container\"><div class=\"reason_name\">Grund: </div><input type=\"text\" id=\"char_row_".$row->id."_reason\"></div>";
				}
				echo "<div onclick=\"deleteRequestChar(".$row->id.")\" id=\"char_row_".$row->id."_request\" class=\"buttonlink requestbutton\" title=\"löschantrag\"><a>Löschenantrag<img src=\"".$rootfolder."images/x.png\"></a></div>";
			}
			echo "</td>";
		}
		
		echo "</tr>";
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
			display: none;
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
			height: 23px;
		}
		
		input#charc
		{
			width: 100%;
			margin-top:1px;
			display: table-cell;
		}
		
		div#saveinfo
		{
			margin-left: auto;
			margin-right: auto;
		}
		
		*.reason_name {
			width: 50px;
			float: left;
			margin-left: -2px;
		}
		
		*.reason_container input {
			float:right;
			width: calc(100% - 52px);
		}
		
		td.delete *.buttonlink {
			clear: both;
			margin-left: auto;
			margin-right: auto;
		}
		
		*.delete {
			<?php if(($_SESSION['permissions']['char_direct_delete_own'] && $_SESSION['userid'] == $_GET['uid']) || $_SESSION['permissions']['char_direct_delete_other']) { ?>
			width: 90px;
			<?php } else if(($_SESSION['permissions']['char_delete_request_own'] && $_SESSION['userid'] == $_GET['uid']) || $_SESSION['permissions']['char_delete_request_other']) { ?>
			width: 200px;
			<?php } ?>
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
		
		*.edit_container {
			width: calc(100% - 110px);
			float:left;
		}
		
		*.innerwrapper {
			margin-left: 0px;
		}
		
		*.input_container {
		}
		</style>
		
		<script type="text/javascript">
		function spoiler( id )
		{
			$('li.transition').mouseenter(function() {
				$('#info_spoiler td').css("z-index", "-1");
			});
			
			$('li.transition').mouseover(function() {
				$('#info_spoiler td').css("z-index", "-1");
			});
			
			$('li.transition').mouseleave(function() {
				$('#info_spoiler td').css("z-index", "");
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
		
		var uid = <?php echo $_GET['uid']; ?>;
		var t = <?php if($t) echo "1"; else echo "0"; ?>;
		
		var reason_minlen = <?php echo $_SESSION['char_delete_request_reason_minlen']; ?>;
		var requesting = false;
		var requestAnimationId = -1;
		function deleteRequestChar(id)
		{
			if(requesting) return;
			requesting = true;
			clearInterval(requestAnimationId);
			
			var reason = $('#char_row_'+id+'_reason').val();
			
			if(reason == undefined || reason.trim().length < reason_minlen)
			{
				$('#char_row_'+id+'_reason').css('border-color', 'red');
				$('#char_errormsg').css('display', 'none');
				$('#char_errormsg').html('Grund muss mindestens '+reason_minlen+' Zeichen beinhalten');
				$('#char_errormsg').slideDown();
				<?php if(!$_SESSION['debug']) { ?>
				requestAnimationId = setInterval(function() {
					clearInterval(requestAnimationId);
					$('#char_errormsg').slideUp();
				}, 5000);
				<?php } ?>
				requesting = false;
			}
			else
			{
				$.post( "<?php echo $rootfolder; ?>ajax/charedit.php", { type: "2", id: id, t: t, content: reason}, function( data) {
					var res = JSON.parse(data);
					if(res.status == "200")
					{
						$('#char_row_'+id+'_reason').closest('td').html('<div>gemeldet</div>');
						$('#char_infomsg').css('display', 'none');
						$('#char_infomsg').html(res.message);
						$('#char_infomsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						requestAnimationId = setInterval(function() {
							clearInterval(requestAnimationId);
							$('#char_infomsg').slideUp();
						}, 3000);
						<?php } ?>
					}
					else
					{
						$('#char_errormsg').css('display', 'none');
						$('#char_errormsg').html(res.message);
						$('#char_errormsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						requestAnimationId = setInterval(function() {
							clearInterval(requestAnimationId);
							$('#char_errormsg').slideUp();
						}, 5000);
						<?php } ?>
					}
					
					requesting = false;
				});
			}
		}
		
		var adding = false;
		var addAnimationId = -1;
		function addchar()
		{
			if(adding) return;
			adding = true;
			clearInterval(addAnimationId);
			
			var val = $('#charc').val();
			$.post( "<?php echo $rootfolder; ?>ajax/charedit.php", { type: "0", uid: uid, t: t, content: val}, function( data) {

				<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>

				var res = JSON.parse(data);
				if(res.status == "200")
				{
					$("#char_head").after("<tr id=\"char_row_"+res.id+"\"></tr>").next().html(byTemplate(res.id, res.name) + contentTemplate(res.id, val) + deleteTemplate(res.id));
					slideDownRow(res.id);
					$('#charc').val("");
					$('#charc').focus();
					console.log(res.post);
					$('#add_info').css('display', 'none');
					$('#add_info').html(res.message);
					$('#add_info').slideDown();
					<?php if(!$_SESSION['debug']) { ?>
					addAnimationId = setInterval(function() {
						clearInterval(addAnimationId);
						$('#add_info').slideUp();
					}, 3000);
					<?php } ?>
				}
				else
				{
					$('#add_error').css('display', 'none');
					$('#add_error').html(res.message);
					$('#add_error').slideDown();
					<?php if(!$_SESSION['debug']) { ?>
					addAnimationId = setInterval(function() {
						clearInterval(addAnimationId);
						$('#add_error').slideUp();
					}, 5000);
					<?php } ?>
				}
				
				adding = false;
			});
		}
		
		function deleteTemplate(id)
		{
			<?php if((($_SESSION['permissions']['char_direct_delete_own'] && $_SESSION['userid'] == $_GET['uid']) || $_SESSION['permissions']['char_direct_delete_other'])) { ?>
				return "<td class=\"b delete\"><div onclick=\"deleteChar("+id+")\" id=\"char_row_"+id+"_delete\" class=\"buttonlink deletebutton\" title=\"Löschen\"><a>Löschen<img src=\"<?php echo $rootfolder; ?>images/x.png\"></a></div></td>";
			<?php } else if((($_SESSION['permissions']['char_delete_request_own'] && $_SESSION['userid'] == $_GET['uid']) || $_SESSION['permissions']['char_delete_request_other'])) { ?>
				var r = "<td class=\"b delete\">";
				<?php if($_SESSION['char_delete_request_reason_possible']) { ?>
						r += "<div class=\"reason_container\"><div class=\"reason_name\">Grund: </div><input type=\"text\" id=\"char_row_"+id+"_reason\"></div>";
				<?php } ?>
					return (r + "<div onclick=\"deleteRequestChar("+id+")\" id=\"char_row_"+id+"_request\" class=\"buttonlink requestbutton\" title=\"löschantrag\"><a>Löschenantrag<img src=\"<?php echo $rootfolder; ?>images/x.png\"></a></div></td>");
				<?php } ?>
			
		}
		
		function contentTemplate(id, content)
		{
			<?php if((($_SESSION['permissions']['char_read_own'] && $_SESSION['userid'] == $_GET['uid']) || $_SESSION['permissions']['char_read_other'])) {
				if($_SESSION['char_edit']) { ?>
					return "<td class=\"br content\"><div class=\"edit_container\"  id=\"char_edit_"+id+"_container\">"+content+"</div><div id=\"char_edit_"+id+"_button\" style=\"float:right;\" onclick=\"editChar("+id+")\" class=\"buttonlink editbutton\" title=\"bearbeiten\"><a>Bearbeiten<img src=\"<?php echo $rootfolder; ?>images/edit.png\"></a></div></td>";
				<?php } else { ?>
					return "<td class=\"br content\"><div>"+content+"</div></td>";
				<?php } } else { ?>
				return "<td class=\"br content\"><div>403 Access denied: please report to android hell for a teapot...</div></td>";
			<?php } ?>
		}
		
		function byTemplate(id, name)
		{
			<?php if((($_SESSION['permissions']['char_see_from_own'] && $_SESSION['userid'] == $_GET['uid']) || $_SESSION['permissions']['char_see_from_other']) && $_SESSION['admin_nsa'] && !$_SESSION['hidemyass']) { ?>
			return "<td class=\"br by\"><div class=\"char_by\"><a href=\"<?php echo $rootfolder; ?>c/showuser/?uid="+id+"\">"+name+"</a></div></td>";
			<?php } else {?>
			return "";
			<?php } ?>
		}
		
		var rows = <?php echo getRowCount(); ?>;
		var info_empty_allowed = <?php echo ($_SESSION['info_allow_empty']?"true":"false"); ?>;
		var info_jobwish_easteregg = <?php echo (!$_SESSION['info_disable_jobwish_easteregg']?"true":"false"); ?>;
		var jobs = ["Entsorgungsfachkraft", "Reinigungsfachkraft","Fachangestellte/r bei McDonalds", "Professioneller Arbeitslosengeld 2 Bezieher/in"];
		var intervalid = -1;
		var saving = false;
		var uid = <?php echo $_GET['uid']; ?>;
		function saveinfo()
		{
			if(saving) return;
			saving = true;
			
			clearInterval(intervalid);
			$('#info_errormsg').fadeOut();
			
			values = [];
			errors = [];
			for(var i = 0; i < rows; i++)
			{
				if(errors.length == 0) {
					if($('#info_'+i+'_input').attr('data-type') == "3" && $('#info_'+i+'_input').val().length == 0 && info_jobwish_easteregg)
						$('#info_'+i+'_input').val(jobs[randint(0, jobs.length)]);
						
					if($('#info_'+i+'_input').attr('data-type') == "4" && $('#info_'+i+'_input').val().length == 2 )
					{
						if(parseInt($('#info_'+i+'_input').val()) < 90)
							$('#info_'+i+'_input').val("20"+$('#info_'+i+'_input').val());
						else
							$('#info_'+i+'_input').val("19"+$('#info_'+i+'_input').val());
					}
						
						
					values.push($('#info_'+i+'_input').val()); 
				}
				
							
				console.log("val: "+$('#info_'+i+'_input').val());
				if($('#info_'+i+'_input').val().length == 0 && !info_empty_allowed)
				{
					errors.push([i, 0]);
				}
				else if($('#info_'+i+'_input').val().length < parseInt($('#info_'+i+'_input').attr('data-minlen')))
				{
					errors.push([i, 1]);
				}
				else
				{
					$('#info_'+i+'_input').css("border-color", "");
				}
			}
						
			if(errors.length > 0)
			{
				//error 0: empty not allowed
				//error 1: min length not reached
				
				$('#info_errormsg').html("");
				for(var i = 0; i < errors.length; i++)
				{
					
				
					$('#info_'+errors[i][0]+'_input').css("border-color", "red");
					var err = "";
					if(errors[i][1] == 0) 
						err = "Feld \""+$('#designer_'+errors[i][0]+'_label').html()+"\" darf nicht leer sein...";
					else if(errors[i][1] == 1) 
						err = "Feld \""+$('#designer_'+errors[i][0]+'_label').html()+"\" muss mindestens eine Länge von "+$('#info_'+errors[i][0]+'_input').attr('data-minlen')+" haben...";
						
					var html = $('#info_errormsg').html();
					if(i+1 < errors.length)
						$('#info_errormsg').html(html + err +"<br>");
					else
						$('#info_errormsg').html(html + err);
					
					
				}
				
				$('#info_errormsg').fadeIn();
				intervalid = setInterval(function() {
				
					$('#info_errormsg').fadeOut();
					clearInterval(intervalid);
				}, 5000);
				saving = false;
			}
			else
			{
				$('#info_errormsg').css("display", "none");
				$.post('<?php echo $rootfolder; ?>ajax/infoedit.php', {
				'uid': uid,
				'count': values.length,								
				'values[]': values
				} , function( data) {
					
					
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					
					$('#info_infomsg').css('display', 'none');
					$('#info_infomsg').html(data);
					$('#info_infomsg').slideDown();
					saving = false;
					<?php if(!$_SESSION['debug']) { ?>
					var id = setInterval(function() {
						clearInterval(id);
						$('#info_infomsg').slideUp();
					}, 3000);
					<?php } ?>
				});
			}
			
			
			
		}
		
		function editChar(id)
		{
			$('#char_edit_'+id+'_container').wrapInner('<textarea />').children().css('width', '100%').css('height', 'auto').css('resize', 'vertical');
			$('#char_edit_'+id+'_button').html("<a>Speichern<img src=\"<?php echo $rootfolder; ?>images/save.png\"></a>").removeClass("editbutton").
			addClass("savebutton").attr('title', 'bearbeiten').attr('onclick', 'saveEdit('+id+')');
			
		}
		
		var t = <?php echo $t?"true":"false"; ?>;		
		var saveedits = false;
		var editAnimationId = -1;
		function saveEdit(id)
		{
			if(saveedits) return;			
			saveedits = true;
			clearInterval(editAnimationId);
			
			$.post('<?php echo $rootfolder; ?>ajax/charedit.php', {
				'type': 1,
				'id': id,
				't': t,								
				'content': $('#char_edit_'+id+'_container').children().val()
				} , function( data) {
					var res = JSON.parse(data);
					
					if(res.status == 200)
					{
						$('#char_edit_'+id+'_container').html($('#char_edit_'+id+'_container').children().val());
						$('#char_edit_'+id+'_button').html("<a>Bearbeiten<img src=\"<?php echo $rootfolder; ?>images/edit.png\"></a>").removeClass("savebutton").
						addClass("editbutton").attr('title', 'speichern').attr('onclick', 'editChar('+id+')');
						
						$('#char_infomsg').css('display', 'none');
						$('#char_infomsg').html(res.message);
						$('#char_infomsg').slideDown();
						saving = false;
						<?php if(!$_SESSION['debug']) { ?>
						editAnimationId = setInterval(function() {
							clearInterval(editAnimationId);
							$('#char_infomsg').slideUp();
						}, 3000);
						<?php } ?>
					}
					else
					{
						$('#char_errormsg').css('display', 'none');
						$('#char_errormsg').html(res.message);
						$('#char_errormsg').slideDown();
						saving = false;
						<?php if(!$_SESSION['debug']) { ?>
						editAnimationId = setInterval(function() {
							clearInterval(editAnimationId);
							$('#char_errormsg').slideUp();
						}, 5000);
						<?php } ?>
					}
					<?php if(!$_SESSION['debug']) { ?> console.log(data); <?php } ?>
					
					
					saveedits = false;
				});
			
			
		}
		
		var deleting = false;
		var deleteAnimationId = -1;
		function deleteChar(id)
		{
			if(deleting) return;
			deleting = true;
			clearInterval(deleteAnimationId);
			
			$.post('<?php echo $rootfolder; ?>ajax/charedit.php', {
				'type': 3,
				'id': id,
				't': t
				} , 
				function( data) {
					var res = JSON.parse(data);
					if(res.status == "200")
					{
						fadeOutAndRemoveRow(id);
						$('#char_infomsg').css('display', 'none');
						$('#char_infomsg').html(res.message);
						$('#char_infomsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						deleteAnimationId = setInterval(function() {
							clearInterval(deleteAnimationId);
							$('#char_infomsg').slideUp();
						}, 3000);
						<?php } ?>
					}
					else
					{
						$('#char_errormsg').css('display', 'none');
						$('#char_errormsg').html(res.message);
						$('#char_errormsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						deleteAnimationId = setInterval(function() {
							clearInterval(deleteAnimationId);
							$('#char_errormsg').slideUp();
						}, 5000);
						<?php } ?>
					}
					<?php if(!$_SESSION['debug']) { ?> console.log(data); <?php } ?>
					
					
					deleting = false;
					
				});
		}
		
		function fadeOutAndRemoveRow(id)
		{
			$('tr#char_row_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.fadeOut(function() { $(this).closest('tr').remove(); });
		}
		
		function slideUpAndRemoveRow(id)
		{
			$('tr#char_row_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.slideUp(function() { $(this).closest('tr').remove(); });
		}
		
		function slideDownRow(id)
		{
			$('tr#char_row_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.css('display', 'none')
			.slideDown(function() {
				$('tr#char_row_'+id)
				.children('td, th')
				.unwrapInner('div.innerwrapper')
			});
		}
		<?php if($_SESSION['permissions']['admin_manage_user']) { ?>
		var deleting = false;
		function del()
		{
			if(deleting) return;
			deleting = true;
			var uid = <?php echo $_GET['uid']; ?>;
			var t = <?php echo (isset($_GET['t']) && intval($_GET['t']) == 1 ?"1":"0"); ?>;
			if(confirm("Benutzer wirklich löschen? Alle Daten gehen unwiederbringlich verloren!!")){
				$.post( "<?php echo $rootfolder; ?>ajax/tools.php", { tool: 1, uid: uid, t: t}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } echo "\n"; ?>
					var res = JSON.parse(data);
					
					alert(res.message);
					if(res.status == "200")
						window.location = "<?php echo $rootfolder; ?>";
					deleting = false;
				});
			}
		}
		<?php } ?>
		</script>
	<h1><?php echo $title; ?></h1>
		<?php if($_SESSION['permissions']['admin_manage_user']) { ?>
	<h2><div style="margin-left: auto; margin-right: auto; text-align: center;" class="buttonlink" title="bearbeiten">
			<a href="<?php echo $rootfolder; ?>admin/edituser/?uid=<?php echo $_GET['uid']; ?>&t=<?php echo $_GET['t']; ?>">Bearbeiten<img src="<?php echo $rootfolder; ?>images/edit.png"></a>
		</div>
		<div onclick="del()" style="margin-left: auto; margin-right: auto; text-align: center;" class="buttonlink" title="löschen">
			<a>Löschen<img src="<?php echo $rootfolder; ?>images/x.png"></a>
		</div></h2>
	<?php } ?>
	<p>
		<font id="info_errormsg" class="errormsg" style="display: none;">
			<?php
			 if(!empty($err)) {
				foreach($err as $e)
				{
					echo $e;
				}
			}
			?>
		</font>
		<font id="info_infomsg" class="infomsg" style="display: none;">
		</font>
	</p>
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
							
							<?php printInfo(true, $_GET['uid']);
							if($_GET['uid'] == $_SESSION['userid']) { ?>
							
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
	<?php if($_GET['uid'] != $_SESSION['userid'] && $_SESSION['permissions']['char_post'] && $_SESSION['char']) { ?>
	<p>
		<h2>Charakteristik hinzufügen</h2>
		<div id="add_error" class="errormsg"></div>
		<div id="add_info" class="infomsg"></div>
		<div id="addchar">
			<div onclick="addchar()" style="float:right;" class="buttonlink addbutton" title="hinzufügen">
				<a>Hinzufügen<img src="<?php echo $rootfolder; ?>images/plus.png"></a>
			</div>
			<div style="margin-right: 120px;">
				<input type="text" name="charc" id="charc">
			</div>
		</div>
	</p>
	<?php } ?>
	<p>
	<div id="char_errormsg" class="errormsg"></div>
	<div id="char_infomsg" class="infomsg"></div>
	<table cellspacing="0" >
		<tbody id="char_content">
			<tr id="char_head">
				<?php
				if((($_SESSION['permissions']['char_see_from_own'] && $_SESSION['userid'] == $_GET['uid']) || $_SESSION['permissions']['char_see_from_other']) && $_SESSION['admin_nsa'] && !$_SESSION['hidemyass']) { ?>
					<th class="br by">Von</th>
				<?php } ?>
				<th class="br">Eintrag</th>
				<?php if(($_SESSION['permissions']['char_direct_delete_own'] && $_SESSION['userid'] == $_GET['uid']) || $_SESSION['permissions']['char_direct_delete_other']) { ?>
				<th class="b delete">Löschen</th>
				<?php } else if(($_SESSION['permissions']['char_delete_request_own'] && $_SESSION['userid'] == $_GET['uid']) || $_SESSION['permissions']['char_delete_request_other']) { ?>
				<th class="b delete">Löschantrag</th>
				<?php } ?>
			</tr>
			<?php getChars($_GET['uid'], $t); ?>
		</tbody>
	</table>
	</p>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>