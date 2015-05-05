<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Benutzer Einstellungen";
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
			width: 120px;
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
		function editpass()
		{
			if(adding) return;
			adding = true;
			
			$('#add_info').html("");
			$('#add_error').html("");
			clearInterval(addAnimationId);
			
			var error = false;
			var oldpass = $('#oldpass').val();
			var pass = $('#pass').val();
			
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
			
			if(oldpass == undefined || oldpass.trim().length == 0)
			{
				$('#add_error').html("Bitte altes Passwort eingeben!");
				$('#oldpass').css('border-color', 'red');
				error = true;
			}
			else
			{
				$('#oldpass').css('border-color', '');
			}
			
			postAdd(oldpass, pass, error);
			
		}
		var uid = <?php echo $_SESSION['userid']; ?>;
		function postAdd(oldpass, pass, error)
		{
			if(!error)
			{
				$.post( "<?php echo $rootfolder; ?>ajax/newpass.php", { id: uid, oldpass: (""+CryptoJS.MD5(oldpass)), password: (""+CryptoJS.MD5(pass))}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{						
						$('#add_info').css('display', 'none');
						$('#add_info').html($('#add_info').html() + res.message);
						$('#add_info').slideDown();
						$('#oldpass').val("");
						$('#pass').val("");
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
		
		function updateHideMyAss()
		{
			if($('#hidemyass').is(':checked'))
			{
				$.post( "<?php echo $rootfolder; ?>ajax/setcookie.php", { name: "hidemyass", value: 1 }, function( data) { <?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?> });
			}
			else
			{
				$.post( "<?php echo $rootfolder; ?>ajax/setcookie.php", { name: "hidemyass", value: 0 }, function( data) { <?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?> });
			}
		}
		
		function updateDebug()
		{
			if($('#debug').is(':checked'))
			{
				$.post( "<?php echo $rootfolder; ?>ajax/setcookie.php", { name: "debug", value: 1 }, function( data) { <?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?> });
			}
			else
			{
				$.post( "<?php echo $rootfolder; ?>ajax/setcookie.php", { name: "debug", value: 0 }, function( data) { <?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?> });
			}
		}
		
		function updateCookie()
		{
			if($('#cookie').is(':checked'))
			{
				$.post( "<?php echo $rootfolder; ?>ajax/setcookie.php", { name: "userid", value: <?php echo $_SESSION['userid']; ?> }, function( data) { <?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?> });
			}
			else
			{
				$.post( "<?php echo $rootfolder; ?>ajax/setcookie.php", { name: "userid", del: 0 }, function( data) { <?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?> });
			}
		}
		</script>
	<h1><?php echo $title; ?></h1>
	
	<h2>Passwort ändern</h2>
	<div id="add_error" class="errormsg" style="width: 400px; margin-left: auto; margin-right:auto;"></div>
	<div id="add_info" class="infomsg" style="width: 400px; margin-left: auto; margin-right:auto;"></div>
	<table cellspacing="0" >
		<tbody>
			<tr>
				<td class="br caption"><div>Neues Passwort:</div></td>
				<td class="b input_container"><input type="password" id="pass"></td>
			</tr>
			<tr>
				<td class="br caption"><div>Altes Passwort:</div></td>
				<td class="b input_container"><input type="password" id="oldpass"></td>
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
	
	<br>
	<hr>
	<h2>Eingeloggt bleiben</h2>
	<div style="width: 400px; margin-left: auto; margin-right:auto;">
	<h3>Durch aktiveren wird man beim nächsten Besuch automatisch eingeloggt. Dieser Zustand bleibt bestehen, bis man entweder diese Funktion deaktiviert, oder sich ausloggt.</h3>
	<label><input style="vertical-align: top;" type="checkbox" id="cookie" onchange="updateCookie()" <?php if(isset($_COOKIE['userid'])) echo "checked"; ?> >Eingeloggt bleiben</label>
	</div>
	
	<?php if($_SESSION['permissions']['admin_hidemyass']) { ?>
	<br>
	<hr>
	<h2>Hide My Ass</h2>
	<div style="width: 400px; margin-left: auto; margin-right:auto;">
	<h3>Mit der Hide My Ass Funktion wird nicht mehr angezeigt, wer etwas gepostet hat. Man erscheint dann wie ein normaler Nutzer.</h3>
	<label><input style="vertical-align: top;" type="checkbox" id="hidemyass" onchange="updateHideMyAss()" <?php if($_SESSION['hidemyass']) echo "checked"; ?> >Hide My Ass aktivieren</label>
	</div>
	<?php } ?>
	
	<?php if($_SESSION['permissions']['admin_debug']) { ?>
	<br>
	<hr>
	<h2>Debug</h2>
	<div style="width: 400px; margin-left: auto; margin-right:auto;">
	<h3>Durch den Debug Modus werden mehr Informationen über den internen ablauf angezeigt.</h3>
	<label><input style="vertical-align: top;" type="checkbox" id="debug" onchange="updateDebug()" <?php if($_SESSION['debug']) echo "checked"; ?> >Debug Modus aktivieren</label>
	</div>
	<?php } ?>
	
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>