<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");

$title = "Nutzer bearbeiten";
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");

if(!$_SESSION['permissions']['admin_manage_user'])
{
	header('Location: '.$rootfolder.'error/missing_permission.php?permission=admin_manage_user');
}

function getGroups($u, $t)
{
	var_dump($u);
	
	$sql = "SELECT * FROM `permissions`";
	$res = mysql_query($sql) or die ("ERROR #007: Query failed: $sql @adduser - ".mysql_error());
	
	$count = mysql_num_rows($res);
	$i = 0; 
	
	while($row =  mysql_fetch_object($res))
	{
		
		echo "<option value=\"".$row->id."\"".((!$t && intval($u['group']) == intval($row->id))?"selected":"").">".$row->name."</option>";
		$i++;
	}
}

function getUser($uid)
{
	$sql = "SELECT * FROM `user` WHERE `id` = '".$uid."';";
	$res = mysql_query($sql) or die ("ERROR #221: Query failed: $sql @functions.php - ".mysql_error());
	return mysql_fetch_array($res);
}

function getTeacher($uid)
{
	$sql = "SELECT * FROM `teacher` WHERE `id` = '".$uid."';";
	$res = mysql_query($sql) or die ("ERROR #221: Query failed: $sql @functions.php - ".mysql_error());
	return mysql_fetch_array($res);
}

function getUserArray($uid, $t)
{
	if($t)
		return getTeacher($uid);
	else
		return getUser($uid);
}
?>
	
	<?php 
		$t = isset($_GET['t']) && (intval($_GET['t']) == 1 || $_GET['t'] == "true");
		$u = getUserArray($_GET['uid'], $t); 
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
			width: 400px;
			margin-left: auto;
			margin-right: auto;	
		}
		
		table#info th, table#info td
		{
			width: 50%;
		}
		
		table th, td
		{
			pediting: 0px;
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
			pediting:0px;
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
		
		div#addgossip
		{
			border: 1px solid silver;
			width: 100%;
			height: 23px;
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
		
			
		*.caption {
			width: 100px;
		}
		
		*.rows {
			width: 90px;
		}
		
		*.input_container input {
			width: calc(100% - 4px);
		}
		
		*.input_container textarea {
			width: calc(100% - 6px);
			resize: vertical;
		}
		
		select {
			width: 100%;
		}
		
		</style>
		<script>
		var editing = false;
		var addAnimationId = -1;
		var initialTeacher = <?php echo $t?"1":"0"; ?>;
		var id = <?php echo $_GET['uid']; ?>;
		
		function adduser()
		{
			if(editing) return;
			editing = true;
			
			$('#add_info').html("");
			$('#add_error').html("");
			clearInterval(addAnimationId);
			
			var error = false;
			var prename = $('#prename').val();
			var lastname = $('#lastname').val();
			var type = $('#type').val();
			var group = $('#group').val();
			
			if(prename == undefined || prename.trim().length == 0)
			{
				$('#add_error').html("Bitte Vornamen eingeben!");
				$('#prename').css('border-color', 'red');
				error = true;
			}
			else
			{
				$('#prename').css('border-color', '');
			}
			
			if(lastname == undefined || lastname.trim().length == 0)
			{
				$('#add_error').html($('#add_error').html() + (error?"<br>":"") + "Bitte Nachnamen angeben!");
				$('#lastname').css('border-color', 'red');
				error = true;
			}
			else
			{
				$('#lastname').css('border-color', '');
			}
			
			if(!error)
			{
				$.post( "<?php echo $rootfolder; ?>ajax/edituser.php", { prename: prename, lastname: lastname, type: type, group: group, id: id, initialTeacher: initialTeacher, com: -1}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{						
						$('#add_info').css('display', 'none');
						$('#add_info').html(res.message);
						$('#add_info').slideDown();
						$('#prename').focus();
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
				});
			}
			else
			{
				$('#add_error').slideDown();
				editing = false;
				<?php if(!$_SESSION['debug']) { ?>
				addAnimationId = setInterval(function() {
					clearInterval(addAnimationId);
					$('#add_error').slideUp();
				}, 5000);
				<?php } ?>
			}
		}
		
		function toggleGroup()
		{
			if($('#type').val() == "0")
			{
				$('#group').prop( "disabled", false );
			}
			else
			{
				$('#group').prop( "disabled", true );
			}
		}
		
		$(function() {
			var names = [<?php 
								getAllJSON_complete();
							?>
			];

			$( "#edituser" ).autocomplete({
				minLength: 0,
				source: names,
				focus: function( event, ui ) {
					$( "#edituser" ).val( ui.item.label + "");
					return false;
				},
				select: function( event, ui ) {
					$( "#edituser" ).val( ui.item.label + "");
					id = parseInt(ui.item.id);
					initialTeacher = parseInt(ui.item.teacher);
					
					$("#prename").val(ui.item.prename);
					$("#lastname").val(ui.item.name);
					
					$('#type').val(ui.item.teacher);
					if(parseInt(ui.item.teacher) == 0)
					{
						$("#group").val(ui.item.group);
						$('#group').prop( "disabled", false );
					}
					else
					{
						$('#group').prop( "disabled", true );
					}
					//sub();
					return false;
				}
			})
			.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" )
					.append( "<a>" +((item.teacher=="1")?" <font color=\"#FF0000\" >":"")+ item.label + "" + ((item.teacher=="1")?" </font>":"")+  "</a>" )
					.appendTo( ul );
			};
			
		});
		
		function edituser()
		{
			$('#selectuser').hide();
			$('#editbox').show();
		}
		</script>
	<h1><?php echo $title; ?></h1>
	
	<?php if(!$u) { ?>
	<table cellspacing="0" id="selectuser">
		<tbody>
			<tr>
				<td class="b" colspan="2"><div>Der ausgewählte Nutzer existiert nicht (mehr), es kann aber ein anderer zu Bearbeitung ausgewählt werden.</div></td>
			</tr>
			<tr>
				<td class="br caption"><div>Nutzer:</div></td>
				<td class="b input_container"><input type="text" id="edituser" value="<?php echo $u['name']; ?>"></td>
			</tr>
			<tr>
				<td colspan="2">
					<div onclick="edituser()" style="margin-left: auto; margin-right: auto;" class="buttonlink editbutton" title="bearbeiten">
						<a>Bearbeiten<img src="<?php echo $rootfolder; ?>images/edit.png"></a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<?php } ?>
	
	<div id="add_error" class="errormsg" style="width: 400px; margin-left: auto; margin-right:auto;"></div>
	<div id="add_info" class="infomsg" style="width: 400px; margin-left: auto; margin-right:auto;"></div>
	<table cellspacing="0" id="editbox" <?php if(!$u) echo "style=\"display: none;\""; ?>>
		<tbody>
			<tr>
				<td class="br caption"><div>Vorname:</div></td>
				<td class="b input_container"><input type="text" id="prename" value="<?php echo $u['prename']; ?>"></td>
			</tr>
			<tr>
				<td class="br caption"><div>Nachname:</div></td>
				<td class="b input_container"><input type="text" id="lastname" value="<?php echo $u['name']; ?>"></td>
			</tr>
			<tr>
				<td class="br caption"><div>Typ:</div></td>
				<td class="b input_container"><select onchange="toggleGroup()" id="type"><option value="0" <?php if(!$t){ echo "selected"; }?>>Schüler</option><option value="1" <?php if($t) { echo "selected"; }?>>Lehrer</option></select></td>
			</tr>
			<tr>
				<td class="br caption"><div>Rechte:</div></td>
				<td class="b input_container"><select id="group" <?php if($t) echo "disabled"; ?>><?php getGroups($u, $t); ?></select></td>
			</tr>
			<tr>
				<td colspan="2">
					<div onclick="adduser()" style="margin-left: auto; margin-right: auto;" class="buttonlink addbutton" title="hinzufügen">
						<a>Speichern<img src="<?php echo $rootfolder; ?>images/save.png"></a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>