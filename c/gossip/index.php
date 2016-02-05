<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Gerüchteküche";
ob_end_flush();
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");

function listGossip()
{
	global $rootfolder;
	
	$sql = "SELECT * FROM gossip WHERE `visible` = 1 ORDER BY `id` DESC;";
	$res = mysql_query($sql) or die ("ERROR #113: Query failed: $sql @gossip - ".mysql_error());
	
	while($row = mysql_fetch_object($res))
	{
		echo "<tr id=\"gossip_row_".$row->id."\">";
		if($_SESSION['permissions']['gossip_view_from'] && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa'])
		{
			echo "<td class=\"br by\"><div><a href=\"".$rootfolder."c/showuser/?uid=".$row->poster."\">".getName($row->poster, 0)."</a></div></td>";
		}
		if($_SESSION['permissions']['gossip_view'])
		{
			if($_SESSION['permissions']['gossip_edit_own'] && $_SESSION['userid'] == $row->poster && $_SESSION['gossip_edit']) {
				echo "<td class=\"br content\"><div class=\"edit_container\" id=\"gossip_edit_".$row->id."_container\">".$row->content."</div><div id=\"gossip_edit_".$row->id."_button\" style=\"float:right;\" onclick=\"editgossip(".$row->id.")\" class=\"buttonlink editbutton\" title=\"bearbeiten\"><a>Bearbeiten<img src=\"".$rootfolder."images/edit.png\"></a></div></td>";
			}
			else
			{
				echo "<td class=\"br content\"><div class=\"rightspace\">".$row->content."</div></td>";
			}
		}
		else
		{
			echo "<td class=\"br content\"><div class=\"rightspace\">403 Access denied: please report to android hell for a teapot...</div></td>";
		}
		
		if((($_SESSION['permissions']['gossip_direct_delete_own'] && $_SESSION['userid'] == $row->poster) || $_SESSION['permissions']['gossip_direct_delete_other']))
		{
			echo "<td class=\"b delete\"><div onclick=\"deletegossip(".$row->id.")\" id=\"gossip_row_".$row->id."_delete\" class=\"buttonlink deletebutton\" title=\"Löschen\"><a>Löschen<img src=\"".$rootfolder."images/x.png\"></a></div></td>";
		}
		else if($_SESSION['permissions']['gossip_delete_request'] && $_SESSION['gossip_delete_request'])
		{
			echo "<td class=\"b delete\">";
			if(isRequested($row->id, 3))
			{
				echo "<div>".getRequestStatus($row->id, 3)."</div>";
			}
			else
			{
				echo "<div class=\"reason_container\"><div class=\"reason_name\">Grund: </div><input type=\"text\" id=\"gossip_row_".$row->id."_reason\"></div>";
				echo "<div onclick=\"deleteRequestgossip(".$row->id.")\" id=\"gossip_row_".$row->id."_request\" class=\"buttonlink requestbutton\" title=\"löschantrag\"><a>Löschenantrag<img src=\"".$rootfolder."images/x.png\"></a></div>";
			}
			echo "</td>";
		}
		else if($_SESSION['permissions']['gossip_direct_delete_own'])
		{
			echo "<td class=\"b delete\"><div></div></td>";
		}
		
		echo "</tr>";
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
		
		*.delete {
			<?php if($_SESSION['permissions']['gossip_direct_delete_other']) { ?>
			width: 90px;
			<?php } else if($_SESSION['permissions']['gossip_delete_request']) { ?>
			width: 200px;
			<?php } else {?>
			width: 90px:
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
		
		input#gossipc {
			width: 100%;
			margin-top: 1px;
			display: table-cell;
		}
		</style>
		<script>
		
		var reason_minlen = <?php echo $_SESSION['gossip_delete_request_reason_minlen']; ?>;
		var requesting = false;
		var requestAnimationId = -1;
		function deleteRequestgossip(id)
		{
			if(requesting) return;
			requesting = true;
			clearInterval(requestAnimationId);
			
			var reason = $('#gossip_row_'+id+'_reason').val();
			
			if(reason == undefined || reason.trim().length < reason_minlen)
			{
				$('#gossip_row_'+id+'_reason').css('border-color', 'red');
				$('#gossip_errormsg').css('display', 'none');
				$('#gossip_errormsg').html('Grund muss mindestens '+reason_minlen+' Zeichen beinhalten');
				$('#gossip_errormsg').slideDown();
				<?php if(!$_SESSION['debug']) { ?>
				requestAnimationId = setInterval(function() {
					clearInterval(requestAnimationId);
					$('#gossip_errormsg').slideUp();
				}, 5000);
				<?php } ?>
				requesting = false;
			}
			else
			{
				$.post( "<?php echo $rootfolder; ?>ajax/gossipedit.php", { type: "2", id: id, content: reason}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{
						$('#gossip_row_'+id+'_reason').closest('td').html('<div>gemeldet</div>');
						$('#gossip_infomsg').css('display', 'none');
						$('#gossip_infomsg').html(res.message);
						$('#gossip_infomsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						requestAnimationId = setInterval(function() {
							clearInterval(requestAnimationId);
							$('#gossip_infomsg').slideUp();
						}, 3000);
						<?php } ?>
					}
					else
					{
						$('#gossip_errormsg').css('display', 'none');
						$('#gossip_errormsg').html(res.message);
						$('#gossip_errormsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						requestAnimationId = setInterval(function() {
							clearInterval(requestAnimationId);
							$('#gossip_errormsg').slideUp();
						}, 5000);
						<?php } ?>
					}
					
					requesting = false;
				});
			}
		}
		
		var adding = false;
		var addAnimationId = -1;
		function addgossip()
		{
			if(adding) return;
			adding = true;
			
			$('#add_info').html("");
			$('#add_error').html("");
			clearInterval(addAnimationId);
			
			var error = false;
			var val = $('#gossipc').val();
			
			if(val == undefined || val.trim().length == 0)
			{
				$('#add_error').html("Bitte Gerücht eingeben!");
				$('#gossipc').css('border-color', 'red');
				$('#add_error').slideDown();
				adding = false;
				<?php if(!$_SESSION['debug']) { ?>
				addAnimationId = setInterval(function() {
					clearInterval(addAnimationId);
					$('#add_error').slideUp();
				}, 5000);
				<?php } ?>
			}
			else
			{
				$('#gossipc').css('border-color', '');
				$.post( "<?php echo $rootfolder; ?>ajax/gossipedit.php", { type: "0", content: val}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{
						$("#gossip_head").after("<tr id=\"gossip_row_"+res.id+"\"></tr>").next().html(byTemplate(res.id, res.name) + contentTemplate(res.id, val) + deleteTemplate(res.id));
						slideDownRow(res.id);
						$('#gossipc').val("");
						$('#gossipc').focus();
						
						$('#add_info').css('display', 'none');
						$('#add_info').html($('#add_info').html() + res.message);
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
		}
		
		function deleteTemplate(id)
		{
		
			<?php if($_SESSION['permissions']['gossip_direct_delete_own'] || $_SESSION['permissions']['gossip_direct_delete_other']) { ?>
				return "<td class=\"b delete\"><div onclick=\"deletegossip("+id+")\" id=\"gossip_row_"+id+"_delete\" class=\"buttonlink deletebutton\" title=\"Löschen\"><a>Löschen<img src=\"<?php echo $rootfolder; ?>images/x.png\"></a></div></td>";
			<?php } else if($_SESSION['permissions']['gossip_delete_request'] && $_SESSION['gossip_delete_request']) { ?>
				var r = "<td class=\"b delete\">";
				r += "<div class=\"reason_container\"><div class=\"reason_name\">Grund: </div><input type=\"text\" id=\"gossip_row_"+id+"_reason\"></div>";
				return (r + "<div onclick=\"deleteRequestgossip("+id+")\" id=\"gossip_row_"+id+"_request\" class=\"buttonlink requestbutton\" title=\"löschantrag\"><a>Löschenantrag<img src=\"<?php echo $rootfolder; ?>images/x.png\"></a></div></td>");
			<?php } ?>
			
		}
		
		function contentTemplate(id, content)
		{
			<?php if($_SESSION['permissions']['gossip_view']) {
				if($_SESSION['gossip_edit'] && $_SESSION['permissions']['gossip_edit_own']) { ?>
					return "<td class=\"br content\"><div class=\"edit_container\" id=\"gossip_edit_"+id+
					"_container\">"+content+"</div><div id=\"gossip_edit_"+id+"_button\" style=\"float:right;\" onclick=\"editgossip("+id+
					")\" class=\"buttonlink editbutton\" title=\"bearbeiten\"><a>Bearbeiten<img src=\"<?php echo $rootfolder; ?>images/edit.png\"></a></div></td>";
				<?php } else { ?>
					return "<td class=\"br content\"><div>"+content+"</div></td>";
				<?php } 
			} else { ?>
				return "<td class=\"br content\"><div>403 Access denied: please report to android hell for a teapot...</div></td>";
			<?php } ?>
		}
		
		function byTemplate(id, name)
		{
			<?php if($_SESSION['permissions']['gossip_view_from'] && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa']) { ?>
			return "<td class=\"br by\"><div class=\"gossip_by\"><a href=\"<?php echo $rootfolder; ?>c/showuser/?uid="+id+"\">"+name+"</a></div></td>";
			<?php } else {?>
			return "";
			<?php } ?>
		}
		
		function editgossip(id)
		{
			$('#gossip_edit_'+id+'_container').wrapInner('<textarea />').children().css('width', '100%').css('height', 'auto').css('resize', 'vertical');
			$('#gossip_edit_'+id+'_button').html("<a>Speichern<img src=\"<?php echo $rootfolder; ?>images/save.png\"></a>").removeClass("editbutton").
			addClass("savebutton").attr('title', 'bearbeiten').attr('onclick', 'saveEdit('+id+')');
			
		}
		
			
		var saveedits = false;
		var editAnimationId = -1;
		function saveEdit(id)
		{
			if(saveedits) return;			
			saveedits = true;
			clearInterval(editAnimationId);
			
			$.post('<?php echo $rootfolder; ?>ajax/gossipedit.php', {
				'type': 1,
				'id': id,							
				'content': $('#gossip_edit_'+id+'_container').children().val()
				} , function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					
					if(res.status == 200)
					{
						$('#gossip_edit_'+id+'_container').css('width', '').html($('#gossip_edit_'+id+'_container').children().val());
						$('#gossip_edit_'+id+'_button').html("<a>Bearbeiten<img src=\"<?php echo $rootfolder; ?>images/edit.png\"></a>").removeClass("savebutton").
						addClass("editbutton").attr('title', 'speichern').attr('onclick', 'editgossip('+id+')');
						
						$('#gossip_infomsg').css('display', 'none');
						$('#gossip_infomsg').html(res.message);
						$('#gossip_infomsg').slideDown();
						saving = false;
						<?php if(!$_SESSION['debug']) { ?>
						editAnimationId = setInterval(function() {
							clearInterval(editAnimationId);
							$('#gossip_infomsg').slideUp();
						}, 3000);
						<?php } ?>
					}
					else
					{
						$('#gossip_errormsg').css('display', 'none');
						$('#gossip_errormsg').html(res.message);
						$('#gossip_errormsg').slideDown();
						saving = false;
						<?php if(!$_SESSION['debug']) { ?>
						editAnimationId = setInterval(function() {
							clearInterval(editAnimationId);
							$('#gossip_errormsg').slideUp();
						}, 5000);
						<?php } ?>
					}
					<?php if(!$_SESSION['debug']) { ?> console.log(data); <?php } ?>
					
					
					saveedits = false;
				});
			
			
		}
		
		var deleting = false;
		var deleteAnimationId = -1;
		function deletegossip(id)
		{
			if(deleting) return;
			deleting = true;
			clearInterval(deleteAnimationId);
			
			$.post('<?php echo $rootfolder; ?>ajax/gossipedit.php', {
				'type': 3,
				'id': id
				} , 
				function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{
						fadeOutAndRemoveRow(id);
						$('#gossip_infomsg').css('display', 'none');
						$('#gossip_infomsg').html(res.message);
						$('#gossip_infomsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						deleteAnimationId = setInterval(function() {
							clearInterval(deleteAnimationId);
							$('#gossip_infomsg').slideUp();
						}, 3000);
						<?php } ?>
					}
					else
					{
						$('#gossip_errormsg').css('display', 'none');
						$('#gossip_errormsg').html(res.message);
						$('#gossip_errormsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						deleteAnimationId = setInterval(function() {
							clearInterval(deleteAnimationId);
							$('#gossip_errormsg').slideUp();
						}, 5000);
						<?php } ?>
					}
					<?php if(!$_SESSION['debug']) { ?> console.log(data); <?php } ?>
					
					
					deleting = false;
					
				});
		}
		
		function fadeOutAndRemoveRow(id)
		{
			$('tr#gossip_row_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.fadeOut(function() { $(this).closest('tr').remove(); });
		}
		
		function slideUpAndRemoveRow(id)
		{
			$('tr#gossip_row_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.slideUp(function() { $(this).closest('tr').remove(); });
		}
		
		function slideDownRow(id)
		{
			$('tr#gossip_row_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.css('display', 'none')
			.slideDown(function() {
				$('tr#gossip_row_'+id)
				.children('td, th')
				.unwrapInner('div.innerwrapper')
			});
		}
		
		function copyPasterino()
		{
			var cp = "";
			var sep = $('#seperator_input').val().replace("\\n","\n");
			var len = $('td.content').length;
			$('td.content').each(function( index ) {	
			console.log($(this).html());
				cp += $(this).find("div.rightspace").text();
				cp += $(this).find("div.edit_container").text();
				if(index < len -1)
					cp += sep;
			});
			$('#copyPasterino').val(cp);
		}
		</script>
	<h1><?php echo $title; ?></h1>
	
	<?php if($_SESSION['permissions']['gossip_add'] && $_SESSION['gossip']) { ?>
	<p>
		<h2>Gerücht verbreiten</h2>
		<div id="add_error" class="errormsg"></div>
		<div id="add_info" class="infomsg"></div>
		<div id="addgossip">
			<div onclick="addgossip()" style="float:right;" class="buttonlink addbutton" title="hinzufügen">
				<a>Hinzufügen<img src="<?php echo $rootfolder; ?>images/plus.png"></a>
			</div>
			<div style="margin-right: 120px;">
				<input type="text" name="gossipc" id="gossipc">
			</div>
		</div>
	</p>
	<?php } ?>
	<p>
	<div id="gossip_errormsg" class="errormsg"></div>
	<div id="gossip_infomsg" class="infomsg"></div>
	<table cellspacing="0" >
		<tbody id="gossip_content">
			<tr id="gossip_head">
				<?php
				if($_SESSION['permissions']['gossip_view_from'] && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa']) { ?>
					<th class="br by">Von</th>
				<?php } ?>
				<th class="br">Eintrag</th>
				<?php if($_SESSION['permissions']['gossip_direct_delete_other']) { ?>
				<th class="b delete">Löschen</th>
				<?php } else if($_SESSION['permissions']['gossip_delete_request'] && $_SESSION['permissions']['gossip_delete_request']) { ?>
				<th class="b delete">Löschantrag</th>
				<?php } else if($_SESSION['permissions']['gossip_direct_delete_own']) {?>
				<th class="b delete">Löschen</th>
				<?php } ?>
			</tr>
			<?php listgossip(); ?>
		</tbody>
	</table>
	</p>
		<?php if($_SESSION['permissions']['char_copy_pasterino']) { ?>
	<h2>Copy Pasterino Generator</h2>
	<div class="border">
		<div style="margin-top: 2px; margin-left: 2px;">
			<div onclick="copyPasterino()" style="margin-right: 2px; float: right;" class="buttonlink addbutton" title="hinzufügen">
					<a>Generieren<img src="<?php echo $rootfolder; ?>images/plus.png"></a>
				</div>
				<div style="float: left;">Trennzeichen (\n für neue Zeile):</div>
				<div><input style="width: calc(100% - 332px); margin-left: 5px;" type="text" name="seperator" id="seperator_input"></div>
			
		
		</div>
		<div width="100%"><textarea id="copyPasterino" style="width: calc(100% - 7px); min-height: 100px;" ></textarea></div>
		</div>
	</p>
	<?php } ?>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>