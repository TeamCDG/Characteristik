<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Einstellungen";
ob_end_flush();
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");



function createSettingsTable()
{
	global $rootfolder;
	$file = file_get_contents($_SERVER['DOCUMENT_ROOT'].$rootfolder."doc/settings.json");
	$json = json_decode($file, true);
	var_dump($json);
	echo json_last_error();
	
	foreach($json as $set)
	{
		echo "<tr id=\"".$set['name']."\"><td class=\"display\">".$set['display']."</td><td class=\"description\">".$set['description']."</td>".getTypeInput($set['type'], $set['name'])."</tr>";
	}
}

function getTypeInput($type, $name)
{
	switch($type)
	{
		case 0: //bool
			return "<td><input type=\"checkbox\" class=\"value\" ".($_SESSION[$name]?"checked":"")."></td>";
			break;
		case 1: //int
			return "";
			break;
		case 2: //string
			return "";
			break;
		case 3: //email
			return "";
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
		</script>
	<h1><?php echo $title; ?></h1>
	<table>
	<?php createSettingsTable(); ?>
	</table>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>