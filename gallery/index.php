<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Galerie";
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
		
		*.preview_image {
	width: 100px;
	height: 100px;
	background-size: cover;
	
}
*.preview_placeholder {
	width: 100px;
	height: 100px;
	border: 1px dashed silver;
	margin-right: 5px;
	margin-bottom: 5px;
	float: left
}

#preview_space {
	overflow: hidden;
}

div.preview_queued {
	background-image: url('queued.png')
}

div.preview_loading {
	background-image: url('loading.gif');
}

div.preview_remove {
	width: 20px;
	height: 20px;
	background-color: red;
	margin-left: 80px;
	text-align: center;
	position: fixed;
}

*.preview_link_loading
{
	pointer-events: none;
}


#progress-bar {
	background-color: #12CC1A;
	height:20px;color: #FFFFFF;
	width:0%;
	-webkit-transition: width .3s;
	-moz-transition: width .3s;
	transition: width .3s;
}

.btnSubmit
{
	background-color:#09f;
	border:0;
	padding:10px 40px;
	color:#FFF;
	border:#F0F0F0 1px solid; 
	border-radius:4px;
	width: calc(100% - 10px);
	margin: 5px;
}

.btnSubmit:hover
{
	background-color:#2bf;
}

#progress-div {
	border:1px solid silver;
	margin:5px 5px;
	border-radius:4px;
	text-align:center;
}
		</style>
	<script type="text/javascript">
	
		$(document).ready(function() { 
			 $('#uploadForm').submit(function(e) {	
			 
				e.preventDefault();
				if($('#files').val()) {
					$(this).ajaxSubmit({ 
						target:   '#targetLayer', 
						beforeSubmit: function() {
						  $("#progress-bar").width('0%');
						  $(".preview_image").attr('src', '<?php echo $rootfolder; ?>images/loading.gif');
						},
						uploadProgress: function (event, position, total, percentComplete){	
							console.log(position+" / "+total);
						
							$("#progress-bar").width(percentComplete + '%');
							$("#progress-bar").html('<div id="progress-status">' + percentComplete +' %</div>')
						},
						success:function (){
							showImages();
						},
						resetForm: true 
					}); 
					return false; 
				}
			});
		}); 

		function showImages()
		{
			var files = JSON.parse($('#targetLayer').html());
			for(var i = 0; i < files.files.length; i++)
			{
				// $('#file_'+i).removeClass('preview_loading').css("background-image", "url('"+files.files[i].replace('/', '/thumb/')+"')");
				$('#file_'+i).attr('src',files.files[i].split("").reverse().join("").replace('/', '/bmuht/').split("").reverse().join(""));
				$('#preview_link_'+i).attr('href', files.files[i]).removeClass('preview_link_loading');
				
			}
			$('.preview_remove').fadeIn();
		}

		function putPreview()
		{
			var fc = $('#files')[0].files.length;
			$('#preview_space').html("");
			for(var i = 0; i < fc; i++)
			{
				var nelem = "<div class=\"preview_placeholder\"><div style=\"display:none;\" class=\"preview_remove\">X</div><a href=\"\" class=\"preview_link_loading\" data-lightbox=\"preview\" id=\"preview_link_"+i+"\" ><img class=\"preview_image\" id=\"file_"+i+"\" src=\"<?php echo $rootfolder; ?>images/queued.png\"></a></div>";
				$('#preview_space').html($('#preview_space').html() + nelem);
			}
		}
		
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
				$('#extend_'+id+' img').attr('src', "<?php echo $rootfolder; ?>images/arrow_down.png");
			}
			else
			{
				$('#'+id).slideUp();
				$('#extend_'+id+' img').attr('src', "<?php echo $rootfolder; ?>images/arrow_up.png");
			}
		}
		
		
		var adding = false;
		var addAnimationId = -1;
		function addAlbum()
		{		
			if(adding) return;
			adding = true;
			clearInterval(addAnimationId);
			
			$('#new_album_error').html("");
			if($('#new_album_title').val().length == 0)
			{
				$('#new_album_error').html("Titel darf nicht leer sein");
				$('#new_album_title').css("background-color", "red");
				return;
			}
			
			
			var groups = new Array();
			$( "#new_album_permissions tr" ).each( function( index, element ){
				if($(this).attr('id') != undefined && $(this).attr('id').indexOf("group_") > -1)
				{
					groups.push($(this).attr('id').split("_")[1]);
				}
			});
			
			var permStr = "";
			for(var i = 0; i < groups.length; i++)
			{
				permStr += groups[i] + ":";
				permStr += ($('#'+groups[i]+"_view").is(':checked')?"1":"0");
				permStr += ($('#'+groups[i]+"_add").is(':checked')?"1":"0");
				permStr += ($('#'+groups[i]+"_edit_album").is(':checked')?"1":"0");
				
				if(i+1 < groups.length)
					permStr += ";";
			}
			
			$.post( "<?php echo $rootfolder; ?>ajax/albumedit.php", { type: "0", title: $('#new_album_title').val(), description: $('#new_album_description').val(), permStr: permStr}, function( data) {
				console.log(data);
				var res = JSON.parse(data);
				if(res.status == "200")
				{
					//ADD TO SELECT
					$('#new_album_error').css('display', 'none');
					$('#new_album_error').html(res.message);
					$('#new_album_error').slideDown();
					<?php if(!$_SESSION['debug']) { ?>
					addAnimationId = setInterval(function() {
						clearInterval(addAnimationId);
						$('#new_album_error').slideUp();
					}, 3000);
					<?php } ?>
				}
				else
				{
					$('#new_album_error').css('display', 'none');
					$('#new_album_error').html(res.message);
					$('#new_album_error').slideDown();
					<?php if(!$_SESSION['debug']) { ?>
					addAnimationId = setInterval(function() {
						clearInterval(addAnimationId);
						$('#new_album_error').slideUp();
					}, 5000);
					<?php } ?>
				}
				
				adding = false;
			});
		}
		
		
		function clearbg(obj)
		{
			$(obj).css("background-color", "");
		}
	</script>
	<h1><?php echo $title; ?></h1>
	<?php if($_SESSION['permissions']['gallery_add']) { ?>
		<div style="border:1px solid silver;"><div style="text-align:center;" onclick="spoiler('upload_spoiler')" id="extend_upload_spoiler" class="buttonlink" title="Mehr laden">
						<a>Upload<img src="<?php echo $rootfolder; ?>images/arrow_up.png"></a>
					</div>
					<div id="upload_spoiler" style="display: none;">
					<form id="uploadForm" action="upload.php" method="post">
<div>
	<table cellspacing="0">
		<tr>
			<td width="150px" class="br"><div>Bilder auswählen: </div></td>
			<td class="b"><div><input onchange="putPreview()" name="userImage[]" id="files" type="file" multiple="sbaum" class="demoInputBox" accept="image/jpeg, image/png"/></div></td>
		</tr>
		<tr>
			<td width="150px" class="r"><div>Album: </div></td>
			<td ><div><select name="aid" id="aid">
							<?php 

								$sql = "SELECT * FROM `albums` ; ";
								$res = mysql_query($sql) or die("iheartrainbows44");
								while($row = mysql_fetch_object($res))
								{
									if(strpos($row->permissions, $_SESSION['permissions']['id'].":1") || $_SESSION['permissions']['gallery_view'])
										echo "<option value=\"".$row->id."\">".$row->title."</option>";
								}
							?>
							</select></div>
			</td>
		</tr>
	</table>
<input type="hidden" name="uniqid" id="uniqid" value="<?php echo uniqid($_SESSION['userid']); ?>">

</div>
<div><input type="submit" id="btnSubmit" value="Hochladen!" class="btnSubmit" /></div>
<div id="progress-div"><div id="progress-bar"></div></div>
<div id="targetLayer" style="display: none;"></div>
</form>
<div id="preview_space"></div>
					</div></div>
					<br><br>
	<?php } ?>
	<?php if($_SESSION['permissions']['gallery_new_album']) { ?>
	<div id="new_album_error"></div>
	<div style="border:1px solid silver; "><div style="text-align:center;" onclick="spoiler('new_album_spoiler')" id="extend_new_album_spoiler" class="buttonlink" title="Mehr laden">
						<a>Neues Album<img src="<?php echo $rootfolder; ?>images/arrow_up.png"></a>
					</div>
					<div id="new_album_spoiler" style="display: none;">
					<form>
						<table cellspacing="0" id="new_album_content" width="100%">
							
							<tr>
								<td class="br" width="200px">Titel:</th>
								<td class="b"><input style="width: calc(100% - 4px);" type="text" name="new_album_title" id="new_album_title" onchange="clearbg(this);"></th>
							</tr>
							<tr>
								<td class="br" width="200px">Beschreibung:</th>
								<td class="b"><textarea style="width: calc(100% - 6px);" id="new_album_description" name="new_album_description"></textarea></th>
							</tr>
						</table>
						<br>
						<p style="text-align:center;">INFO: Hier eingestellte Rechte überschreiben globale Einstellungen.</p>
						<table cellspacing="0" id="new_album_permissions" width="100%">
							<tr>
								<th class="br">Gruppe</th>
								<th class="br boolcol">Album ansehen</th>
								<th class="br">Bilder hinzufügen</th>
								<th class="b">Album bearbeiten</th>
							</tr>
							<?php 
							$sql = "SELECT * FROM `permissions`";
							$res = mysql_query($sql) or die("ERROR #666: Hot as hell");
							
							while($row = mysql_fetch_object($res))
							{
								echo "<tr id=\"group_".$row->id."\">";
									echo "<td class=\"br\"><div>".$row->name."</div></td>";
									echo "<td class=\"br\"><div><input type=\"checkbox\" name=\"".$row->id."_view\" id=\"".$row->id."_view\"".($row->gallery_view==1?"checked":"")."></div></td>";
									echo "<td class=\"br\"><div><input type=\"checkbox\" name=\"".$row->id."_add\" id=\"".$row->id."_add\"".($row->gallery_add==1?"checked":"")."></div></td>";
									echo "<td class=\"b\"><div><input type=\"checkbox\" name=\"".$row->id."_edit_album\" id=\"".$row->id."_edit_album\"".($row->gallery_new_album==1?"checked":"")."></div></td>";
								echo "</tr>";
							}
							?>
						</table>
						<div style="text-align: center;">
										<div onclick="addAlbum()" id="addAlbum" class="buttonlink backupbutton" title="speichern">
											<a>Neues Album erstellen<img src="<?php echo $rootfolder; ?>images/save.png"></a>
										</div>
									</div>
					</form>
					</div></div>
					<br><br>
	<?php } ?>
	<?php 
	$sql = "SELECT * FROM `albums`;";
	$res = mysql_query($sql);
	while($row = mysql_fetch_object($res))
	{
		if($_SESSION['permissions']['gallery_view'] || strpos($row->permissions, $_SESSION['permissions']['id'].":1"))
		{
	?>
	<div style="border:1px solid silver; "><div style="text-align:center;" onclick="spoiler('new_<?php echo "album_".$row->id; ?>_spoiler')" id="extend_<?php echo "album_".$row->id; ?>_spoiler" class="buttonlink" title="Mehr laden">
						<a><?php echo $row->title; ?><img src="<?php echo $rootfolder; ?>images/arrow_up.png"></a>
					</div>
					<div style="overflow: hidden;" id="new_<?php echo "album_".$row->id; ?>_spoiler" style="display: none;">
					<div style="border-top: 1px solid silver; text-align: center;"><a href="<?php echo $rootfolder."gallery/albums/?aid=".$row->id; ?>">Permalink</a></div>
					<div  style="border-top: 1px solid silver; border-bottom: 1px solid silver; text-align: center;" ><br><?php echo $row->description; ?><br><br></div>
				
					<?php 
						$sql = "SELECT * FROM `images` WHERE `album`='".$row->id."' ; ";
						$resi = mysql_query($sql);
						$i = 0;
						while($img = mysql_fetch_object($resi))
						{
							echo "<div class=\"preview_placeholder\"><div style=\"display:none;\" 
							class=\"preview_remove\">X</div><a href=\"".$rootfolder.$row->path.$img->filename."\" 
							class=\"preview_link_loading\" data-lightbox=\"album_".$row->id."\" id=\"album_".$row->id."_link_".$i."\" >
							<img class=\"preview_image\" id=\"album_".$row->id."_img_".$i."\" src=\"".$rootfolder.$row->path."thumb/".$img->filename."\"></a></div>";
							
							$i++;
						}
					?>
					</div>
	</div>
					<br><br>
	<?php } 
	}?>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>