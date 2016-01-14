<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Nutzer hinzufügen";
ob_end_flush();
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");

function getGroups()
{
	$sql = "SELECT * FROM `permissions`";
	$res = mysql_query($sql) or die ("ERROR #007: Query failed: $sql @adduser - ".mysql_error());
	
	$count = mysql_num_rows($res);
	$i = 0; 
	
	while($row =  mysql_fetch_object($res))
	{
		echo "<option value=\"".$row->id."\"".($i==$count-1?"selected":"").">".$row->name."</option>";
		$i++;
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
		var adding = false;
		var addAnimationId = -1;
		function adduser()
		{
			if(adding) return;
			adding = true;
			
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
				$.post( "<?php echo $rootfolder; ?>ajax/adduser.php", { prename: prename, lastname: lastname, type: type, group: group}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{						
						$('#add_info').css('display', 'none');
						$('#add_info').html(res.message);
						$('#add_info').slideDown();
						$('#prename').val("");
						$('#lastname').val("");
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
				adding = false;
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
		</script>
	<h1><?php echo $title; ?></h1>
	
	<div id="add_error" class="errormsg" style="width: 400px; margin-left: auto; margin-right:auto;"></div>
	<div id="add_info" class="infomsg" style="width: 400px; margin-left: auto; margin-right:auto;"></div>
	<table cellspacing="0" >
		<tbody>
			<tr>
				<td class="br caption"><div>Vorname:</div></td>
				<td class="b input_container"><input type="text" id="prename"></td>
			</tr>
			<tr>
				<td class="br caption"><div>Nachname:</div></td>
				<td class="b input_container"><input type="text" id="lastname"></td>
			</tr>
			<tr>
				<td class="br caption"><div>Typ:</div></td>
				<td class="b input_container"><select onchange="toggleGroup()" id="type"><option value="0" selected>Schüler</option><option value="1">Lehrer</option></select></td>
			</tr>
			<tr>
				<td class="br caption"><div>Rechte:</div></td>
				<td class="b input_container"><select id="group"><?php getGroups(); ?></select></td>
			</tr>
			<tr>
				<td colspan="2">
					<div onclick="adduser()" style="margin-left: auto; margin-right: auto;" class="buttonlink addbutton" title="hinzufügen">
						<a>Hinzufügen<img src="<?php echo $rootfolder; ?>images/plus.png"></a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	</p>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>