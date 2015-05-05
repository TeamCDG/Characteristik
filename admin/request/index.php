<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Löschanfragen";
ob_end_flush();
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/infodesigner.php");

function getRequests($type)
{
	global $rootfolder;
	$sql = "";
	if($type == 0)
		$sql = "SELECT * FROM `request` WHERE `closed` = 0 AND `request_type`!= '2'";
	else if($type == 1)
		$sql = "SELECT * FROM `request` WHERE `closed` = 1 AND `request_type`!= '2' ORDER BY `id` DESC";
	else
		$sql = "SELECT * FROM `request` WHERE 1";
		
		
	$res = mysql_query($sql) or die ("ERROR #007: Query failed: $sql @request - ".mysql_error());
	
			
	while($row = mysql_fetch_object($res))
	{
		echo "<tr id=\"request_row_".$row->id."\" >";
		$uid = getRequestContentPoster(intval($row->type), $row->cid);
		
		if(($_SESSION['permissions']['cit_view_from'] && $row->type == "2") && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa']) 
		{
			echo "<td class=\"br by\" id=\"request_row_".$row->id."_poster\" ><div><a href=\"".$rootfolder."c/showuser/?uid=".$uid."\">".getName($uid, 0)."</a></div></td>";
		}
		else if(($_SESSION['permissions']['gossip_view_from'] && $row->type == "3") && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa'])
		{
			echo "<td class=\"br by\" id=\"request_row_".$row->id."_poster\"><div><a href=\"".$rootfolder."c/showuser/?uid=".$uid."\">".getName($uid, 0)."</a></div></td>";
		}
		else if(($_SESSION['permissions']['char_see_from_other'] && intval($row->type) < 2) && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa'])
		{
			echo "<td class=\"br by\" id=\"request_row_".$row->id."_poster\"><div><a href=\"".$rootfolder."c/showuser/?uid=".$uid."\">".getName($uid, 0)."</a></div></td>";
		}
		
		echo "<td class=\"br by\" id=\"request_row_".$row->id."_holder\"><div><a href=\"".$rootfolder."c/showuser/?uid=".$row->from."\">".getName($row->from, 0)."</a></div></td>";
		echo "<td class=\"br content\" id=\"request_row_".$row->id."_content\"><div>".getRequestContent(intval($row->type), $row->cid)."</div></td>";
		
		if($row->type == "2") 
		{
			echo "<td class=\"br by\" id=\"request_row_".$row->id."_location\" ><div>Zitate</div></td>";
		}
		else if($row->type == "3")
		{
			echo "<td class=\"br by\" id=\"request_row_".$row->id."_location\"><div>Gerüchte</div></td>";
		}
		else
		{
			$loc = getRequestCharLocation(intval($row->type), $row->cid);
			echo "<td class=\"br by\" id=\"request_row_".$row->id."_location\"><div>Charakteristik (<a href=\"".$rootfolder."c/showuser/?uid=".$loc."\">".getName($loc, intval($row->type))."</a>)</div></td>";
		}
		
		echo "<td class=\"br reason\" id=\"request_row_".$row->id."_reason\"><div>".$row->message."</div></td>";
		
		if($_SESSION['permissions']['admin_process_requests'] && $row->closed == "0")
		{
			echo "<td class=\"b delete\"><div onclick=\"closeRequest(".$row->id.")\" id=\"request_row_".$row->id."_close\" class=\"buttonlink deletebutton\" title=\"Anfrage bearbeiten\"><a>Löschen<img src=\"".$rootfolder."images/x.png\"></a></div></td>";
		}
		
		if($type == "1")
		{
			echo "<td class=\"b by\"><div><a href=\"".$rootfolder."c/showuser/?uid=".$row->eby."\">".getName($row->eby, 0)."</a></div></td>";
		}
		
		echo "<tr>";
	}
}

function getRequestContentPoster($type, $cid)
{	
	switch($type)
	{
		case 0:
			$sql = "SELECT * FROM `uchar` WHERE `id` = '".$cid."';";
			$res = mysql_query($sql) or die ("ERROR #009: Query failed: $sql @request - ".mysql_error());
			return mysql_fetch_object($res)->from;
			break;
		case 1:
			$sql = "SELECT * FROM `tchar` WHERE `id` = '".$cid."';";
			$res = mysql_query($sql) or die ("ERROR #009: Query failed: $sql @request - ".mysql_error());
			return mysql_fetch_object($res)->from;
			break;
		case 2:
			$sql = "SELECT * FROM `cit` WHERE `id` = '".$cid."';";
			$res = mysql_query($sql) or die ("ERROR #010: Query failed: $sql @request - ".mysql_error());
			return mysql_fetch_object($res)->poster;
			break;
		case 3:
			$sql = "SELECT * FROM `gossip` WHERE `id` = '".$cid."';";
			$res = mysql_query($sql) or die ("ERROR #011: Query failed: $sql @request - ".mysql_error());
			return mysql_fetch_object($res)->poster;
			break;
	}
}

function getRequestCharLocation($type, $cid)
{	
	switch($type)
	{
		case 0:
			$sql = "SELECT * FROM `uchar` WHERE `id` = '".$cid."';";
			$res = mysql_query($sql) or die ("ERROR #009: Query failed: $sql @request - ".mysql_error());
			return mysql_fetch_object($res)->holder;
			break;
		case 1:
			$sql = "SELECT * FROM `tchar` WHERE `id` = '".$cid."';";
			$res = mysql_query($sql) or die ("ERROR #009: Query failed: $sql @request - ".mysql_error());
			return mysql_fetch_object($res)->holder;
			break;
	}
}

function getRequestContent($type, $cid)
{	
	switch($type)
	{
		case 0:
			$sql = "SELECT * FROM `uchar` WHERE `id` = '".$cid."';";
			$res = mysql_query($sql) or die ("ERROR #009: Query failed: $sql @request - ".mysql_error());
			if($_SESSION['permissions']['char_read_other'])
				return mysql_fetch_object($res)->content;
			else
				return "403 Access denied: please report to android hell for a teapot...";
			break;
		case 1:
			$sql = "SELECT * FROM `tchar` WHERE `id` = '".$cid."';";
			$res = mysql_query($sql) or die ("ERROR #009: Query failed: $sql @request - ".mysql_error());
			if($_SESSION['permissions']['char_read_other'])
				return mysql_fetch_object($res)->content;
			else
				return "403 Access denied: please report to android hell for a teapot...";
			break;
		case 2:
			$sql = "SELECT * FROM `cit` WHERE `id` = '".$cid."';";
			$res = mysql_query($sql) or die ("ERROR #010: Query failed: $sql @request - ".mysql_error());
			if($_SESSION['permissions']['cit_view'])
				return mysql_fetch_object($res)->content;
			else
				return "403 Access denied: please report to android hell for a teapot...";
			break;
		case 3:
			$sql = "SELECT * FROM `gossip` WHERE `id` = '".$cid."';";
			$res = mysql_query($sql) or die ("ERROR #011: Query failed: $sql @request - ".mysql_error());
			if($_SESSION['permissions']['gossip_view'])
				return mysql_fetch_object($res)->content;
			else
				return "403 Access denied: please report to android hell for a teapot...";
			break;
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
			width: 150px;
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
		
		td.delete *.buttonlink {
			clear: both;
			margin-left: auto;
			margin-right: auto;
		}
		
		*.delete {
			width: 90px;
		}
		
		*.location {
			width: 150px;
		}
		</style>
		<script>
		 function isEmpty( el ){
			  return !$.trim(el.html())
		  }
		
		var uid = <?php echo $_SESSION['userid']; ?>;
		var adding = false;
		var addAnimationId = -1;
		function closeRequest(id)
		{
			if(adding) return;
			adding = true;
			
			clearInterval(addAnimationId);
			
			$.post( "<?php echo $rootfolder; ?>ajax/closerequest.php", { id: id }, function( data) {
				<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
				var res = JSON.parse(data);
				if(res.status == "200")
				{						
					$('#request_info').css('display', 'none');
					$('#request_info').html(res.message);
					$('#request_info').slideDown();
					$("#closed_requests_head").after("<tr id=\"request_rowc_"+res.id+"\"></tr>").next().html(
					(!isEmpty($("#request_row_"+id+"_poster"))?"<td class=\"br by\" id=\"request_row_"+id+"_poster\">"+$("#request_row_"+id+"_poster").html()+"</td>":"")+
					"<td class=\"br by\" id=\"request_row_"+id+"_holder\">"+$("#request_row_"+id+"_holder").html()+"</td>"+
					"<td class=\"br content\" id=\"request_row_"+id+"_content\">"+$("#request_row_"+id+"_content").html()+"</td>"+
					"<td class=\"br by\" id=\"request_row_"+id+"_location\">"+$("#request_row_"+id+"_location").html()+"</td>"+
					"<td class=\"br reason\" id=\"request_row_"+id+"_reason\">"+$("#request_row_"+id+"_reason").html()+"</td>"+
					"<td class=\"br by\" id=\"request_row_"+id+"_eby\"><div><a href=\"<?php echo $rootfolder; ?>c/showuser/?uid="+uid+"\">"+res.name+"</a></div></td>"
					);
					slideDownRow(id);
					fadeOutAndRemoveRow(id)
					<?php if(!$_SESSION['debug']) { ?>
					addAnimationId = setInterval(function() {
						clearInterval(addAnimationId);
						$('#request_info').slideUp();
					}, 3000);
					<?php } ?>
				}
				else
				{
					$('#request_error').css('display', 'none');
					$('#request_error').html(res.message);
					$('#request_error').slideDown();
					<?php if(!$_SESSION['debug']) { ?>
					addAnimationId = setInterval(function() {
						clearInterval(addAnimationId);
						$('#request_error').slideUp();
					}, 5000);
					<?php } ?>
				}
			});
		}
		
		function fadeOutAndRemoveRow(id)
		{
			$('tr#request_row_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.fadeOut(function() { $('tr#request_row_'+id).remove(); });
		}
		
		function slideUpAndRemoveRow(id)
		{
			$('tr#request_row_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.slideUp(function() { $(this).closest('tr').remove(); });
		}
		
		function slideDownRow(id)
		{
			$('tr#request_rowc_'+id)
			.children('td, th')
			.animate({ padding: 0 })
			.wrapInner('<div class=\"innerwrapper\" />')
			.children()
			.css('display', 'none')
			.slideDown(function() {
				$('tr#request_row_'+id)
				.children('td, th')
				.unwrapInner('div.innerwrapper')
			});
		}
		</script>
	<h1><?php echo $title; ?></h1>
	<h2>Offene Anfragen</h2>
	<div id="request_error" class="errormsg"></div>
	<div id="request_info" class="infomsg"></div>
	<table cellspacing="0" >
		<tbody id="open_requests">
			<tr id="open_requests_head">
				<?php
				if(($_SESSION['permissions']['cit_view_from'] || $_SESSION['permissions']['gossip_view_from'] || $_SESSION['permissions']['char_view_from']) && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa']) { ?>
					<th class="br by">Geschrieben von</th>
				<?php } ?>
				<th class="br by">Löschantrag von</th>
				<th class="br content">Inhalt</th>
				<th class="br location">Ort</th>
				<th class="br reason">Löschgrund</th>
				<?php if($_SESSION['permissions']['cit_direct_delete_other']) { ?>
				<th class="b delete">Bearbeiten</th>
				<?php } ?>
			</tr>
			<?php getRequests(0); ?>
		</tbody>
	</table>
	<br><br><br>
	<h2>Geschlossene Anfragen</h2>
	<table cellspacing="0" >
		<tbody id="closed_requests">
			<tr id="closed_requests_head">
				<?php
				if(($_SESSION['permissions']['cit_view_from'] || $_SESSION['permissions']['gossip_view_from'] || $_SESSION['permissions']['char_view_from']) && !$_SESSION['hidemyass'] && $_SESSION['admin_nsa']) { ?>
					<th class="br by">Geschrieben von</th>
				<?php } ?>
				<th class="br by">Löschantrag von</th>
				<th class="br content">Inhalt</th>
				<th class="br location">Ort</th>
				<th class="br reason">Löschgrund</th>
				<th class="br by">Bearbeitet von</th>
			</tr>
			<?php getRequests(1); ?>
		</tbody>
	</table>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>