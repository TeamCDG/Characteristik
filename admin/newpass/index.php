<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Passwort ändern";
ob_end_flush();
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");


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
		$(function() {
			var names = [<?php 
								getAllJSON_user();
							?>
			];

			$( "#user" ).autocomplete({
				minLength: 0,
				source: names,
				focus: function( event, ui ) {
					$( "#user" ).val( ui.item.label );
					return false;
				},
				select: function( event, ui ) {
					$( "#user" ).val( ui.item.label );
					$( "#uid" ).val( ui.item.id );
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
		
		var adding = false;
		var addAnimationId = -1;
		function editpass()
		{
			if(adding) return;
			adding = true;
			
			$('#add_info').html("");
			$('#add_error').html("");
			clearInterval(addAnimationId);
			
			var error = false;
			var user = $('#user').val();
			var pass = $('#pass').val();
			var uid = $('#uid').val();
			
			if(pass == undefined || pass.trim().length == 0)
			{
				$('#add_error').html("Bitte neues Passwort eingeben!");
				$('#pass').css('border-color', 'red');
				error = true;
			}
			else
			{
				$('#pass').css('border-color', '');
			}
			if(uid == undefined || uid == -1 || uid == "-1")
			{
				if(user == undefined || user.trim().length == 0)
				{
					$('#add_error').html($('#add_error').html()+(error?"<br>":"")+"Bitte Person angeben!");
					$('#user').css('border-color', 'red');
					error = true;
					postAdd(uid, pass, error);
				}
				else
				{
					$.post( "<?php echo $rootfolder; ?>ajax/guessid.php", { name: user, t:0 }, function( data) {
						<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
						var res = JSON.parse(data);
						
						if(res.status == "200")
						{
							$('#add_info').html("Person wurde aufgrund Namenseingabe geraten...");
							uid = res.id;
							$('#user').css('border-color', '');
						}
						else
						{
							$('#add_error').html($('#add_error').html()+(error?"<br>":"")+"Person konnte durch Eingabe nicht erraten werden! vertippt?");
							$('#user').css('border-color', 'red');
							error = true;
						}
						postAdd(uid, pass, error);
					});
				}
			}
			else
			{	
				$('#user').css('border-color', '');
				postAdd(uid, pass, error);
			}
			
		}
		
		function postAdd(uid, pass, error)
		{
			if(!error)
			{
				$.post( "<?php echo $rootfolder; ?>ajax/newpass.php", { id: uid, password: (""+CryptoJS.MD5(pass))}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{						
						$('#add_info').css('display', 'none');
						$('#add_info').html($('#add_info').html() + res.message);
						$('#copypasterinologin').html("Benutzername: "+res.username+"<br>Passwort: "+pass);
						$('#add_info').slideDown();
						$('#user').val("");
						$('#pass').val("");
						$('#user').focus();
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
		</script>
	<h1><?php echo $title; ?></h1>
	
	<div id="add_error" class="errormsg" style="width: 400px; margin-left: auto; margin-right:auto;"></div>
	<div id="add_info" class="infomsg" style="width: 400px; margin-left: auto; margin-right:auto;"></div>
	<table cellspacing="0" >
		<tbody>
			<tr>
				<td class="br caption"><div>Benutzer:</div></td>
				<td class="b input_container">
					<input type="text" id="user">				
					<input type="hidden" id="uid" name="uid" value="-1">
				</td>
			</tr>
			<tr>
				<td class="br caption"><div>Password:</div></td>
				<td class="b input_container"><input type="text" id="pass"></td>
			</tr>
			<tr>
				<td colspan="2">
					<div onclick="editpass()" style="margin-left: auto; margin-right: auto;" class="buttonlink deletebutton" title="hinzufügen">
						<a>Ändern<img src="<?php echo $rootfolder; ?>images/edit.png"></a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="copypasterinologin" class="infomsg" style="width: 400px; margin-left: auto; margin-right:auto;"></div>
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>