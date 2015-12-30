<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Tools";
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/infodesigner.php");
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
			width: 600px;
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
		$(function() {
			var names = [<?php 
								getAllJSON();
							?>
			];
			$( "#user_delete" ).autocomplete({
				minLength: 0,
				source: names,
				focus: function( event, ui ) {
					$( "#user_delete" ).val( ui.item.label + " (ID: " + ui.item.id + ")");
					return false;
				},
				select: function( event, ui ) {
					$( "#user_delete" ).val( ui.item.label + " (ID: " + ui.item.id + ")");
					$( "#user_delete_id" ).val( ui.item.id );
					$( "#user_delete_teacher" ).val( ui.item.teacher );
					//sub();
					return false;
				}
			})
			.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" )
					.append( "<a>" +((item.teacher=="1")?" <font color=\"#FF0000\" >":"")+ item.label + " (ID: " + item.id + ")" + ((item.teacher=="1")?" </font>":"")+  "</a>" )
					.appendTo( ul );
			};

			$( "#user_1" ).autocomplete({
				minLength: 0,
				source: names,
				focus: function( event, ui ) {
					$( "#user_1" ).val( ui.item.label + " (ID: " + ui.item.id + ")");
					return false;
				},
				select: function( event, ui ) {
					$( "#user_1" ).val( ui.item.label + " (ID: " + ui.item.id + ")");
					$( "#user_1_id" ).val( ui.item.id );
					$( "#user_1_teacher" ).val( ui.item.teacher );
					//sub();
					return false;
				}
			})
			.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" )
					.append( "<a>" +((item.teacher=="1")?" <font color=\"#FF0000\" >":"")+ item.label + " (ID: " + item.id + ")" + ((item.teacher=="1")?" </font>":"")+  "</a>" )
					.appendTo( ul );
			};
			
			$( "#user_2" ).autocomplete({
				minLength: 0,
				source: names,
				focus: function( event, ui ) {
					$( "#user_2" ).val( ui.item.label + " (ID: " + ui.item.id + ")");
					return false;
				},
				select: function( event, ui ) {
					$( "#user_2" ).val( ui.item.label + " (ID: " + ui.item.id + ")");
					$( "#user_2_id" ).val( ui.item.id );
					$( "#user_2_teacher" ).val( ui.item.teacher );
					//sub();
					return false;
				}
			})
			.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" )
					.append( "<a>" +((item.teacher=="1")?" <font color=\"#FF0000\" >":"")+ item.label + " (ID: " + item.id + ")" + ((item.teacher=="1")?" </font>":"")+  "</a>" )
					.appendTo( ul );
			};
			
			$( "#user_n" ).autocomplete({
				minLength: 0,
				source: names,
				focus: function( event, ui ) {
					$( "#user_n" ).val( ui.item.label + " (ID: " + ui.item.id + ")");
					return false;
				},
				select: function( event, ui ) {
					$( "#user_n" ).val( ui.item.label + " (ID: " + ui.item.id + ")");
					$( "#user_n_id" ).val( ui.item.id );
					$( "#user_n_teacher" ).val( ui.item.teacher );
					//sub();
					return false;
				}
			})
			.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" )
					.append( "<a>" +((item.teacher=="1")?" <font color=\"#FF0000\" >":"")+ item.label + " (ID: " + item.id + ")" + ((item.teacher=="1")?" </font>":"")+  "</a>" )
					.appendTo( ul );
			};
		});
		
		<?php if($_SESSION['permissions']['admin_manage_user']) { ?>
		var adding = false;
		var addAnimationId = -1;
		var uid = <?php echo $_SESSION['userid']; ?>;
		function merge()
		{
			if(adding) return;
			adding = true;
			
			$('#add_info').html("");
			$('#add_error').html("");
			clearInterval(addAnimationId);
			
			var error = false;
			var uid1 = $('#user_1_id').val();
			var uid2 = $('#user_2_id').val();
			var uidn = $('#user_n_id').val();
			var t = $('#user_n_teacher').val();
			
			if(uid1 == undefined || uid1.trim().length == 0)
			{
				$('#add_error').html($('#add_error').html()+"<br>"+"Bitte Nutzer 1 aus der Liste auswählen!");
				$('#user_1').css('border-color', 'red');
				error = true;
			}
			else if(!error)
			{
				$('#user_1').css('border-color', '');
			}
			
			if(uid2 == undefined || uid2.trim().length == 0)
			{
				$('#add_error').html($('#add_error').html()+"<br>"+"Bitte Nutzer 2 aus der Liste auswählen!");
				$('#user_2').css('border-color', 'red');
				error = true;
			}
			else if(!error)
			{
				$('#user_2').css('border-color', '');
			}
			
			if(uidn == undefined || uidn.trim().length == 0)
			{
				$('#add_error').html($('#add_error').html()+"<br>"+"Bitte Nutzer neu aus der Liste auswählen!");
				$('#user_n').css('border-color', 'red');
				error = true;
			}
			else if(!error)
			{
				$('#user_n').css('border-color', '');
			}
			
			if($('#user_n_teacher').val() != $('#user_1_teacher').val() || $('#user_n_teacher').val() != $('#user_2_teacher').val())
			{
				$('#add_error').html($('#add_error').html()+"<br>"+"Nutzer müssen entweder alles Schüler, oder alles Lehrer sein!");
				$('#user_1').css('border-color', 'red');
				$('#user_2').css('border-color', 'red');
				$('#user_n').css('border-color', 'red');
				error = true;
			}
			else if(!error)
			{
				$('#user_1').css('border-color', '');
				$('#user_2').css('border-color', '');
				$('#user_n').css('border-color', '');
			}
			
			if(uid1 != uidn && uid2 != uidn)
			{
				$('#add_error').html($('#add_error').html()+"<br>"+"Nutzer neu muss entweder Nutzer 1 oder Nutzer 2 sein!");
				$('#user_n').css('border-color', 'red');
				error = true;
			}
			else if(!error)
			{
				$('#user_n').css('border-color', '');
			}
			
			if(uid1 == uid2)
			{
				$('#add_error').html($('#add_error').html()+"<br>"+"Es ist totaler Blödsinn dieselben Nutzer zu verschmelzen...");
				$('#user_1').css('border-color', 'red');
				$('#user_2').css('border-color', 'red');
				error = true;
			}
			else if(!error)
			{
				$('#user_1').css('border-color', '');
				$('#user_2').css('border-color', '');
			}
			
			if(!error)
			{
				$.post( "<?php echo $rootfolder; ?>ajax/tools.php", { tool: 0, uid1: uid1, uid2: uid2, uidn: uidn, t: t}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{						
						$('#add_info').css('display', 'none');
						$('#add_info').html($('#add_info').html() + res.message);
						$('#add_info').slideDown();
						
						$('#user_1').val("");
						$('#user_1_id').val("");
						$('#user_1_teacher').val("");
						
						$('#user_2').val("");
						$('#user_2_id').val("");
						$('#user_2_teacher').val("");
						
						$('#user_n').val("");
						$('#user_n_id').val("");
						$('#user_n_teacher').val("");
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
		
				var deleting = false;
		var deleteAnimationId = -1;
		function del()
		{
			if(deleting) return;
			deleting = true;
			
			$('#delete_info').html("");
			$('#delete_error').html("");
			clearInterval(deleteAnimationId);
			
			var error = false;
			var uid = $('#user_delete_id').val();
			var t = $('#user_delete_teacher').val();
			
			if(uid == undefined || uid.trim().length == 0)
			{
				$('#delete_error').html($('#delete_error').html()+"<br>"+"Bitte Nutzer zum Löschen aus der Liste auswählen!");
				$('#user_delete').css('border-color', 'red');
				error = true;
			}
			else if(!error)
			{
				$('#user_delete').css('border-color', '');
			}
			
			if(!error && confirm("Benutzer wirklich löschen? Alle Daten gehen unwiederbringlich verloren!!"))
			{
				$.post( "<?php echo $rootfolder; ?>ajax/tools.php", { tool: 1, uid: uid, t: t}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } echo "\n"; ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{						
						$('#delete_info').css('display', 'none');
						$('#delete_info').html($('#add_info').html() + res.message);
						$('#delete_info').slideDown();
						
						$('#user_delete').val("");
						$('#user_delete_id').val("");
						$('#user_delete_teacher').val("");
						<?php if(!$_SESSION['debug']) { ?>
						deleteAnimationId = setInterval(function() {
							clearInterval(deleteAnimationId);
							$('#delete_info').slideUp();
						}, 3000);
						<?php } ?>
					}
					else
					{
						$('#delete_error').css('display', 'none');
						$('#delete_error').html(res.message);
						$('#delete_error').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						deleteAnimationId = setInterval(function() {
							clearInterval(deleteAnimationId);
							$('#delete_error').slideUp();
						}, 5000);
						<?php } ?>
					}
					deleting = false;
				});
			}
			else
			{
				$('#delete_error').slideDown();
				deleting = false;
				<?php if(!$_SESSION['debug']) { ?>
				deleteAnimationId = setInterval(function() {
					clearInterval(deleteAnimationId);
					$('#delete_error').slideUp();
				}, 5000);
				<?php } ?>
			}
		}
		<?php } ?>
		</script>
	<h1><?php echo $title; ?></h1>
	<?php if($_SESSION['permissions']['admin_manage_user']) { ?>
	<h2>Nutzer verschmelzen</h2>
	<div style="width: 600px; margin-left: auto; margin-right:auto;">
	<h3>Ist ein Nutzer ausversehen (oder absichtlich) doppelt eingefügt worden, kann man mit dieser Funktion zwei Nutzer zu einem verschmelzen ohne Datensätze zu verlieren.</h3>
		<div id="add_error" class="errormsg" style="width: 600px; margin-left: auto; margin-right:auto;"></div>
	<div id="add_info" class="infomsg" style="width: 600px; margin-left: auto; margin-right:auto;"></div>
	<table cellspacing="0" >
		<tbody>
			<tr>
				<td class="br caption"><div>Nutzer 1:</div></td>
				<td class="br input_container"><input type="text" id="user_1"><input type="hidden" id="user_1_teacher"><input type="hidden" id="user_1_id"></td>
				<td class="br caption" rowspan="2" align="center"><div><span style="font-size: 38px;margin-top: -26px;margin-left: -50px;position: absolute;"><b>}</b></span>
					<span style="position: absolute;margin-left: -30px; margin-top: -8px;">Nutzer neu:</span></div></td>
				<td class="b input_container" rowspan="2"><input type="text" id="user_n"><input type="hidden" id="user_n_teacher"><input type="hidden" id="user_n_id"></td>
			</tr>
			<tr>
				<td class="br caption"><div>Nutzer 2:</div></td>
				<td class="br input_container"><input type="text" id="user_2"><input type="hidden" id="user_2_teacher"><input type="hidden" id="user_2_id"></td>
			</tr>
			<tr>
				<td colspan="4">
					<div onclick="merge()" style="margin-left: auto; margin-right: auto; text-align:center;" class="buttonlink" title="verschmelzen">
						<a>Verschmelzen<img src="<?php echo $rootfolder; ?>images/merge.png"></a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	</div>
	<hr>
	<h2>Nutzer löschen</h2>
	<div style="width: 600px; margin-left: auto; margin-right:auto;">
	<h3>Löscht alle Datensätze, die der Nutzer erstellt hat, oder die diesen Nutzer beinhalten. Es ist, als habe der Nutzer nie existiert.<br><span style="color: #ff2222;"><b>WARNUNG: </b>Daten gehen unwiederbringlich verloren.</h3>
		<div id="delete_error" class="errormsg" style="width: 600px; margin-left: auto; margin-right:auto;"></div>
	<div id="delete_info" class="infomsg" style="width: 600px; margin-left: auto; margin-right:auto;"></div>
	<table cellspacing="0" >
		<tbody>
			<tr>
				<td class="br caption"><div>Nutzer:</div></td>
				<td class="br input_container"><input type="text" id="user_delete"><input type="hidden" id="user_delete_teacher"><input type="hidden" id="user_delete_id"></td>
			</tr>
			<tr>
				<td colspan="2">
					<div onclick="del()" style="margin-left: auto; margin-right: auto; text-align:center;" class="buttonlink" title="löschen">
						<a>Löschen<img src="<?php echo $rootfolder; ?>images/x.png"></a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	</div>
	<?php } ?>
	<div align="center" style="margin-left: auto; margin-right:auto;"><img src="<?php echo $rootfolder; ?>images/construction.png" style="width:200px; height: 200px;"></div>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>