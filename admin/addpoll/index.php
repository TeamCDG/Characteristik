<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Umfrage hinzufügen";
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

		table, tr, div#tutorial
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

		td.caption div
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
			width: 200px;
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

		*.xButton {
			margin-left: 0;
			float: right;
			width: 24px;
			height: 16px;
		}

		*.answer_text {
			width: calc(100% - 32px);
			float: left;
		}
		</style>
		<script>
		var adding = false;
		var addAnimationId = -1;
		var answercount = 2;
		var nextid = 2;
		function removeAnswer(id)
		{
			if($("#type").val() == "3")
			{
				if(answercount > 2)
				{
					answercount--;
					$('#answer_captions').attr("rowspan", answercount +1);
					$("#answer_"+id).parent().parent().remove();
				}
				else
				{
					window.alert("Abstimmungen mit nur einer Möglichkeit ergeben keinen Sinn! Wir sind hier ja nicht in Mütterchen Russland!");
				}
			}
		}

		function addanswer()
		{
			if($("#type").val() == "3")
			{
				answercount++;
				
				var nexansw = "<tr><td class=\"b\"><input class=\"answer_text\" type=\"text\" id=\"answer_"+nextid+"\">"+
							  "<div onclick=\"removeAnswer("+nextid+")\" class=\"buttonlink xButton\" title=\"entfernen\">"+
							  "<a><img src=\"<?php echo $rootfolder; ?>images/x.png\"></a></div></td></tr>";
				nextid++;
				
				
				$('#answer_captions').attr("rowspan", answercount +1);
				$(nexansw).insertAfter($(".answer_text").last().parent().parent());
			}
		}
		
		function pos()
		{
			if( $('#type').val() == "3")
			{
				$('.answer_text').removeAttr("disabled");
			}
			else
			{
				$('.answer_text').attr("disabled", "disabled"); 
			}
		}
		
		function addpoll()
		{
			if(adding) return;
			adding = true;

			$('#add_info').html("");
			$('#add_error').html("");
			clearInterval(addAnimationId);

			var error = false;
			var title = $('#title').val(); 
			var revote = $('#check_revote').prop("checked");
			var mpc = $('#check_mpc').prop("checked");
			var result_pre_vote = $('#result_pre_vote').prop("checked");
			var type = $('#type').val();
			var diag = $('#diagram').val();
			var answers = new Array();
			
			if( type == "3")
			{
				$('.answer_text').each(function(index) {
					if($(this).val() != undefined && $(this).val().trim().length != 0)
						answers.push($(this).val());
				});
			}

			if(title == undefined || title.trim().length == 0)
			{
				$('#add_error').html("Bitte Titel eingeben!");
				$('#title').css('border-color', 'red');
				error = true;
			}
			else
			{
				$('#title').css('border-color', '');
			}

			if(type == "3" && answers.length < 2)
			{
				$('#add_error').html($('#add_error').html() + (error?"<br>":"") + "Bitte min. 2 Antwortmöglichkeiten angeben!");
				$('.answer_text').css('border-color', 'red');
				error = true;
			}
			else
			{
				$('.answer_text').css('border-color', '');
			}

			console.log(answers); 
			if(!error)
			{
				$.post( "<?php echo $rootfolder; ?>ajax/polledit.php", { type: 0, title: title, revote: revote, multivote: mpc, result_prevote: result_pre_vote, ptype: type, diagram: diag, answers: answers}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{
						$('#add_info').css('display', 'none');
						$('#add_info').html(res.message);
						$('#add_info').slideDown();
						$('#title').val("");
						$('#title').focus();
						if(type == "3")
						{
							while($('.answer_text').size() > 2)
							{
								$('.answer_text').last().parent().parent().remove();
							}
							$('.answer_text').first().attr("id", "#answer_0");
							$('.answer_text').first().attr("onclick", "removeAnswer(0)");
							$('.answer_text').last().attr("id", "#answer_1");		
							$('.answer_text').first().attr("onclick", "removeAnswer(1)");						
							$('.answer_text').val("");
							$('#answer_captions').attr("rowspan", 3);
							nextid = 2;
							answercount = 2;
						}
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

	<div id="add_error" class="errormsg" style="width: 600px; margin-left: auto; margin-right:auto;"></div>
	<div id="add_info" class="infomsg" style="width: 600px; margin-left: auto; margin-right:auto;"></div>
	<table cellspacing="0" >
		<tbody>
			<tr>
				<td class="br caption"><div>Titel:</div></td>
				<td class="b input_container"><input type="text" id="title"></td>
			</tr>
			<tr>
				<td class="br caption"><div>Revote:</div></td>
				<td class="b input_container"><input type="checkbox" name="revote" value="revote" id="check_revote"></td>
			</tr>
			<tr>
				<td class="br caption"><div>Multiple Choice:</div></td>
				<td class="b input_container"><input type="checkbox" name="mpc" value="mpc" id="check_mpc"></td>
			</tr>
			<tr>
				<td class="br caption"><div>Ergebnis auch vor Vote anzeigen:</div></td>
				<td class="b input_container"><input type="checkbox" name="result_pre_vote" value="result_pre_vote" id="result_pre_vote"></td>
			</tr>
			<tr>
				<td class="br caption"><div>Diagrammtyp:</div></td>
				<td class="b input_container"><select id="diagram"><option value="0" selected>Torten</option></td>
			</tr>
			<tr>
				<td class="br caption"><div>Antwortmöglichkeiten:</div></td>
				<td class="b input_container"><select onchange="pos()" id="type"><option value="0" selected>Schüler</option><option value="1">Lehrer</option><option value="2">Ja/Nein</option><option value="3">Eigene Antworten</option></select></td>
			</tr>
			<tr>
				<td rowspan="3" id="answer_captions" class="br caption"><div>Antworten:</div></td>
				<td class="b"><input class="answer_text" type="text" id="answer_0" disabled>
					<div onclick="removeAnswer(0)" class="buttonlink xButton" title="entfernen">
						<a><img src="<?php echo $rootfolder; ?>images/x.png"></a>
					</div>
				</td>
			</tr>
			<tr>
				<td class="b"><input class="answer_text" type="text" id="answer_1" disabled>
					<div onclick="removeAnswer(1)" class="buttonlink xButton" title="entfernen">
						<a><img src="<?php echo $rootfolder; ?>images/x.png"></a>
					</div>
				</td>
			</tr>
			<tr>
				<td class="b">
					<div onclick="addanswer()" style="margin-left: auto; margin-right: auto; text-align: center;" class="buttonlink" title="hinzufügen">
						<a>Antwort hinzufügen<img src="<?php echo $rootfolder; ?>images/plus.png"></a>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div onclick="addpoll()" style="margin-left: auto; margin-right: auto; text-align: center;" class="buttonlink" title="hinzufügen">
						<a>Hinzufügen<img src="<?php echo $rootfolder; ?>images/plus.png"></a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<h2>Info:</h2>
	<div id="tutorial">
	<b><u>Revote:</u></b><br>
	<p>
		Ermöglicht Neuabgabe der Stimme (alte wird gelöscht).
	</p>
	<b><u>Multiple Choice:</u></b><br>
	<p>
		Ermöglicht das Abgeben mehrerer Stimmen.
	</p>
	<b><u>Ergebnis auch vor Vote anzeigen:</u></b><br>
	<p>
		Zeigt das bisherige Ergebnis der Umfragen auch vor Abgabe eines Votes an (Admins sehen das Ergebnis immer).
	</p>
	<b><u>Antwortentypen:</u></b><br>
	<ul>
		<li><b>Schüler:</b> Alle Schüler stehen als Antwort zur Auswahl</li>
		<li><b>Lehrer:</b> Alle Lehrer stehen als Antwort zur Auswahl</li>
		<li><b>Ja/Nein:</b> Als Antworten gibt es nur Ja und Nein</li>
		<li><b>Eigene Antworten:</b> Ihr könnt eigene Antworten angeben</li>
	</ul>
	<br>
	<i>Wenn ihr eigene Antworten angeben wollt, wählt auch als Antwortentyp "Eigene Antworten" aus.</i><br>
	</div>
<?php
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>
