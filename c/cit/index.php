<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Zitate";
ob_end_flush();
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");

function listCit()
{
	global $rootfolder;
	
	$sql = "SELECT * FROM cit WHERE `visible` = 1 ORDER BY `id` DESC;";
	$res = mysql_query($sql) or die ("ERROR #113: Query failed: $sql @cit - ".mysql_error());
	
	while($row = mysql_fetch_object($res))
	{
		echo "<tr id=\"cit_row_".$row->id."\">";
		if($_SESSION['permissions']['cit_view_from'] && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa'])
		{
			echo "<td class=\"br by\"><div><a href=\"".$rootfolder."c/showuser/?uid=".$row->poster."\">".getName($row->poster, 0)."</a></div></td>";
		}
		if($_SESSION['permissions']['cit_view'])
		{
			$by = " - <a href=\"".$rootfolder."c/showuser/?uid=".$row->holder."&t=".$row->teacher."\" >".getName($row->holder, $row->teacher)."</a>";
			if($_SESSION['permissions']['cit_edit_own'] && $_SESSION['userid'] == $row->poster && $_SESSION['cit_edit']) {
				echo "<td class=\"br content\"><div class=\"edit_container\"><div class=\"floating\">\"</div><div class=\"floating\" id=\"cit_edit_".$row->id."_container\">".$row->content."</div>\"".$by."</div><div id=\"cit_edit_".$row->id."_button\" style=\"float:right;\" onclick=\"editCit(".$row->id.")\" class=\"buttonlink editbutton\" title=\"bearbeiten\"><a>Bearbeiten<img src=\"".$rootfolder."images/edit.png\"></a></div></td>";
			}
			else
			{
				echo "<td class=\"br content\"><div class=\"rightspace\">\"".$row->content."\"".$by."</div></td>";
			}
		}
		else
		{
			echo "<td class=\"br content\"><div class=\"rightspace\">403 Access denied: please report to android hell for a teapot...</div></td>";
		}
		
		if((($_SESSION['permissions']['cit_direct_delete_own'] && $_SESSION['userid'] == $row->poster) || $_SESSION['permissions']['cit_direct_delete_other']))
		{
			echo "<td class=\"b delete\"><div onclick=\"deleteCit(".$row->id.")\" id=\"cit_row_".$row->id."_delete\" class=\"buttonlink deletebutton\" title=\"Löschen\"><a>Löschen<img src=\"".$rootfolder."images/x.png\"></a></div></td>";
		}
		else if($_SESSION['permissions']['cit_delete_request'] && $_SESSION['cit_delete_request'])
		{
			echo "<td class=\"b delete\">";
			if(isRequested($row->id, 2))
			{
				echo "<div>".getRequestStatus($row->id, 2)."</div>";
			}
			else
			{
				echo "<div class=\"reason_container\"><div class=\"reason_name\">Grund: </div><input type=\"text\" id=\"cit_row_".$row->id."_reason\"></div>";
				echo "<div onclick=\"deleteRequestCit(".$row->id.")\" id=\"cit_row_".$row->id."_request\" class=\"buttonlink requestbutton\" title=\"löschantrag\"><a>Löschenantrag<img src=\"".$rootfolder."images/x.png\"></a></div>";
			}
			echo "</td>";
		}
		else if($_SESSION['permissions']['cit_direct_delete_own'])
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
		
		div#addcit
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
			<?php if($_SESSION['permissions']['cit_direct_delete_other']) { ?>
			width: 90px;
			<?php } else if($_SESSION['permissions']['cit_delete_request']) { ?>
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
		
		*.floating {
			float: left;
			margin-left: 0px;
		}
		
		*.rightspace {
			margin-right: 5px;
		}
		</style>
		<script>
		
		$(function() {
			var names = [<?php 
								getAllJSON();
							?>
			];

			$( "#citb" ).autocomplete({
				minLength: 0,
				source: names,
				focus: function( event, ui ) {
					$( "#citb" ).val( ui.item.label );
					return false;
				},
				select: function( event, ui ) {
					$( "#citb" ).val( ui.item.label );
					$( "#by_id" ).val( ui.item.id );
					$( "#by_teacher" ).val( ui.item.teacher );
					//sub();
					return false;
				}
			})
			.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" )
					.append( "<a>" +((item.teacher=="1")?" <font color=\"#FF0000\" >":"")+ item.label + ((item.teacher=="1")?" </font>":"")+  "</a>" )
					.appendTo( ul );
			};
		});
		
		
		var reason_minlen = <?php echo $_SESSION['cit_delete_request_reason_minlen']; ?>;
		var requesting = false;
		var requestAnimationId = -1;
		function deleteRequestCit(id)
		{
			if(requesting) return;
			requesting = true;
			clearInterval(requestAnimationId);
			
			var reason = $('#cit_row_'+id+'_reason').val();
			
			if(reason == undefined || reason.trim().length < reason_minlen)
			{
				$('#cit_row_'+id+'_reason').css('border-color', 'red');
				$('#cit_errormsg').css('display', 'none');
				$('#cit_errormsg').html('Grund muss mindestens '+reason_minlen+' Zeichen beinhalten');
				$('#cit_errormsg').slideDown();
				<?php if(!$_SESSION['debug']) { ?>
				requestAnimationId = setInterval(function() {
					clearInterval(requestAnimationId);
					$('#cit_errormsg').slideUp();
				}, 5000);
				<?php } ?>
				requesting = false;
			}
			else
			{
				$.post( "<?php echo $rootfolder; ?>ajax/citedit.php", { type: "2", id: id, content: reason}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{
						$('#cit_row_'+id+'_reason').closest('td').html('<div>gemeldet</div>');
						$('#cit_infomsg').css('display', 'none');
						$('#cit_infomsg').html(res.message);
						$('#cit_infomsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						requestAnimationId = setInterval(function() {
							clearInterval(requestAnimationId);
							$('#cit_infomsg').slideUp();
						}, 3000);
						<?php } ?>
					}
					else
					{
						$('#cit_errormsg').css('display', 'none');
						$('#cit_errormsg').html(res.message);
						$('#cit_errormsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						requestAnimationId = setInterval(function() {
							clearInterval(requestAnimationId);
							$('#cit_errormsg').slideUp();
						}, 5000);
						<?php } ?>
					}
					
					requesting = false;
				});
			}
		}
		
		var adding = false;
		var addAnimationId = -1;
		function addcit()
		{
			if(adding) return;
			adding = true;
			
			$('#add_info').html("");
			$('#add_error').html("");
			clearInterval(addAnimationId);
			
			var error = false;
			var t = $('#by_teacher').val();	
			var val = $('#citc').val();
			var uid = $('#by_id').val();
			var p_name = $('#citb').val();
			
			if(val == undefined || val.trim().length == 0)
			{
				$('#add_error').html("Bitte Zitattext eingeben!");
				$('#citc').css('border-color', 'red');
				error = true;
			}
			else
			{
				$('#citc').css('border-color', '');
			}
			if(uid == undefined || uid == -1 || uid == "-1")
			{
				if(p_name == undefined || p_name.trim().length == 0)
				{
					$('#add_error').html($('#add_error').html()+(error?"<br>":"")+"Bitte zitierte Person angeben!");
					$('#citb').css('border-color', 'red');
					error = true;
					postAdd(uid, t, val, p_name, error);
				}
				else
				{
					$.post( "<?php echo $rootfolder; ?>ajax/guessid.php", { name: p_name }, function( data) {
						<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
						var res = JSON.parse(data);
						
						if(res.status == "200")
						{
							$('#add_info').html("Zitierte Person wurde aufgrund Namenseingabe geraten...");
							uid = res.id;
							t = res.t;
							$('#citb').css('border-color', '');
						}
						else
						{
							$('#add_error').html($('#add_error').html()+(error?"<br>":"")+"Zitierte Person konnte durch Eingabe nicht erraten werden! vertippt?");
							$('#citb').css('border-color', 'red');
							error = true;
						}
						postAdd(uid, t, val, p_name, error);
					});
				}
			}
			else
			{	
				$('#citb').css('border-color', '');
				postAdd(uid, t, val, p_name, error);
			}
			
		}
		
		function postAdd(uid, t, val, p_name, error)
		{
			if(!error)
			{
				$.post( "<?php echo $rootfolder; ?>ajax/citedit.php", { type: "0", uid: uid, t: t, content: val}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{
						$("#cit_head").after("<tr id=\"cit_row_"+res.id+"\"></tr>").next().html(byTemplate(res.id, res.name) + contentTemplate(res.id, val, res.hname, uid, t) + deleteTemplate(res.id));
						slideDownRow(res.id);
						$('#citc').val("");
						$('#citc').focus();
						$('#citb').val("");
						$('#by_teacher').val("0");	
						$('#by_id').val("-1");
						
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
		
		function deleteTemplate(id)
		{
		
			<?php if($_SESSION['permissions']['cit_direct_delete_own'] || $_SESSION['permissions']['cit_direct_delete_other']) { ?>
				return "<td class=\"b delete\"><div onclick=\"deleteCit("+id+")\" id=\"cit_row_"+id+"_delete\" class=\"buttonlink deletebutton\" title=\"Löschen\"><a>Löschen<img src=\"<?php echo $rootfolder; ?>images/x.png\"></a></div></td>";
			<?php } else if($_SESSION['permissions']['cit_delete_request'] && $_SESSION['cit_delete_request']) { ?>
				var r = "<td class=\"b delete\">";
				r += "<div class=\"reason_container\"><div class=\"reason_name\">Grund: </div><input type=\"text\" id=\"cit_row_"+id+"_reason\"></div>";
				return (r + "<div onclick=\"deleteRequestCit("+id+")\" id=\"cit_row_"+id+"_request\" class=\"buttonlink requestbutton\" title=\"löschantrag\"><a>Löschenantrag<img src=\"<?php echo $rootfolder; ?>images/x.png\"></a></div></td>");
			<?php } ?>
			
		}
		
		function contentTemplate(id, content, name, holder, teacher)
		{
			var by = " - <a href=\"<?php echo $rootfolder; ?>c/showuser/?uid="+holder+"&t="+teacher+"\" >"+name+"</a>";
			<?php if($_SESSION['permissions']['cit_view']) {
				if($_SESSION['cit_edit'] && $_SESSION['permissions']['cit_edit_own']) { ?>
					return "<td class=\"br content\"><div class=\"edit_container\"><div class=\"floating\">\"</div><div class=\"floating\" id=\"cit_edit_"+id+
					"_container\">"+content+"</div>\""+by+"</div><div id=\"cit_edit_"+id+"_button\" style=\"float:right;\" onclick=\"editCit("+id+
					")\" class=\"buttonlink editbutton\" title=\"bearbeiten\"><a>Bearbeiten<img src=\"<?php echo $rootfolder; ?>images/edit.png\"></a></div></td>";
				<?php } else { ?>
					return "<td class=\"br content\"><div class=\"rightspace\">\""+content+"\""+by+"</div></td>";
				<?php } 
			} else { ?>
				return "<td class=\"br content\"><div class=\"rightspace\">403 Access denied: please report to android hell for a teapot...</div></td>";
			<?php } ?>
		}
		
		function byTemplate(id, name)
		{
			<?php if($_SESSION['permissions']['cit_view_from'] && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa']) { ?>
			return "<td class=\"br by\"><div class=\"cit_by\"><a href=\"<?php echo $rootfolder; ?>c/showuser/?uid="+id+"\">"+name+"</a></div></td>";
			<?php } else {?>
			return "";
			<?php } ?>
		}
		
		function editCit(id)
		{
			$('#cit_edit_'+id+'_container').css('width', 'calc(100% - 200px)').wrapInner('<textarea />').children().css('width', '100%').css('height', 'auto').css('resize', 'vertical');
			$('#cit_edit_'+id+'_button').html("<a>Speichern<img src=\"<?php echo $rootfolder; ?>images/save.png\"></a>").removeClass("editbutton").
			addClass("savebutton").attr('title', 'bearbeiten').attr('onclick', 'saveEdit('+id+')');
			
		}
		
			
		var saveedits = false;
		var editAnimationId = -1;
		function saveEdit(id)
		{
			if(saveedits) return;			
			saveedits = true;
			clearInterval(editAnimationId);
			
			$.post('<?php echo $rootfolder; ?>ajax/citedit.php', {
				'type': 1,
				'id': id,							
				'content': $('#cit_edit_'+id+'_container').children().val()
				} , function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					
					if(res.status == 200)
					{
						$('#cit_edit_'+id+'_container').css('width', '').html($('#cit_edit_'+id+'_container').children().val());
						$('#cit_edit_'+id+'_button').html("<a>Bearbeiten<img src=\"<?php echo $rootfolder; ?>images/edit.png\"></a>").removeClass("savebutton").
						addClass("editbutton").attr('title', 'speichern').attr('onclick', 'editCit('+id+')');
						
						$('#cit_infomsg').css('display', 'none');
						$('#cit_infomsg').html(res.message);
						$('#cit_infomsg').slideDown();
						saving = false;
						<?php if(!$_SESSION['debug']) { ?>
						editAnimationId = setInterval(function() {
							clearInterval(editAnimationId);
							$('#cit_infomsg').slideUp();
						}, 3000);
						<?php } ?>
					}
					else
					{
						$('#cit_errormsg').css('display', 'none');
						$('#cit_errormsg').html(res.message);
						$('#cit_errormsg').slideDown();
						saving = false;
						<?php if(!$_SESSION['debug']) { ?>
						editAnimationId = setInterval(function() {
							clearInterval(editAnimationId);
							$('#cit_errormsg').slideUp();
						}, 5000);
						<?php } ?>
					}
					<?php if(!$_SESSION['debug']) { ?> console.log(data); <?php } ?>
					
					
					saveedits = false;
				});
			
			
		}
		
		var deleting = false;
		var deleteAnimationId = -1;
		function deleteCit(id)
		{
			if(deleting) return;
			deleting = true;
			clearInterval(deleteAnimationId);
			
			$.post('<?php echo $rootfolder; ?>ajax/citedit.php', {
				'type': 3,
				'id': id
				} , 
				function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{
						fadeOutAndRemoveRow(id);
						$('#cit_infomsg').css('display', 'none');
						$('#cit_infomsg').html(res.message);
						$('#cit_infomsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						deleteAnimationId = setInterval(function() {
							clearInterval(deleteAnimationId);
							$('#cit_infomsg').slideUp();
						}, 3000);
						<?php } ?>
					}
					else
					{
						$('#cit_errormsg').css('display', 'none');
						$('#cit_errormsg').html(res.message);
						$('#cit_errormsg').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						deleteAnimationId = setInterval(function() {
							clearInterval(deleteAnimationId);
							$('#cit_errormsg').slideUp();
						}, 5000);
						<?php } ?>
					}
					<?php if(!$_SESSION['debug']) { ?> console.log(data); <?php } ?>
					
					
					deleting = false;
					
				});
		}
		
		function fadeOutAndRemoveRow(id)
		{
			$('tr#cit_row_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.fadeOut(function() { $(this).closest('tr').remove(); });
		}
		
		function slideUpAndRemoveRow(id)
		{
			$('tr#cit_row_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.slideUp(function() { $(this).closest('tr').remove(); });
		}
		
		function slideDownRow(id)
		{
			$('tr#cit_row_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.css('display', 'none')
			.slideDown(function() {
				$('tr#cit_row_'+id)
				.children('td, th')
				.unwrapInner('div.innerwrapper')
			});
		}
		</script>
	<h1><?php echo $title; ?></h1>
	
	<?php if($_SESSION['permissions']['cit_add'] && $_SESSION['cit']) { ?>
	<p>
		<h2>Zitat hinzufügen</h2>
		<div id="add_error" class="errormsg"></div>
		<div id="add_info" class="infomsg"></div>
		<div id="addcit">
			<div onclick="addcit()" style="float:right;" class="buttonlink addbutton" title="hinzufügen">
				<a>Hinzufügen<img src="<?php echo $rootfolder; ?>images/plus.png"></a>
			</div>
			<div style="margin-right: 200px;margin-top:1px;">
				<input style="float:left; width: calc(100% - 100px);" type="text" name="citc" id="citc">
			</div>
			<div style="float:left;" >&nbsp;-&nbsp; 
			</div>
			<div>
				<input type="text" name="citb" id="citb">
				<input type="hidden" id="by_teacher" name="teacher" value="0">
				<input type="hidden" id="by_id" name="uid" value="-1">
			</div>
		</div>
	</p>
	<?php } ?>
	<p>
	<div id="cit_errormsg" class="errormsg"></div>
	<div id="cit_infomsg" class="infomsg"></div>
	<table cellspacing="0" >
		<tbody id="cit_content">
			<tr id="cit_head">
				<?php
				if($_SESSION['permissions']['cit_view_from'] && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa']) { ?>
					<th class="br by">Von</th>
				<?php } ?>
				<th class="br">Eintrag</th>
				<?php if($_SESSION['permissions']['cit_direct_delete_other']) { ?>
				<th class="b delete">Löschen</th>
				<?php } else if($_SESSION['permissions']['cit_delete_request'] && $_SESSION['permissions']['cit_delete_request']) { ?>
				<th class="b delete">Löschantrag</th>
				<?php } else if($_SESSION['permissions']['cit_direct_delete_own']) {?>
				<th class="b delete">Löschen</th>
				<?php } ?>
			</tr>
			<?php listCit(); ?>
		</tbody>
	</table>
	</p>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>