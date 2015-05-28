<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Manager";
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
		
		*.display
		{
			width: 200px;
		}
		
		*.val
		{
			width: 150px;
		}
		
		*.value
		{
			width: calc(100% - 5px);
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
		</style>
		<script type="text/javascript">
		function spoiler( id )
		{
			$('li.transition').mouseenter(function() {
				$('#'+id+' td').css("z-index", "-1");
			});
			
			$('li.transition').mouseover(function() {
				$('#'+id+' td').css("z-index", "-1");
			});
			
			$('li.transition').mouseleave(function() {
				$('#'+id+' td').css("z-index", "");
			});
			
			if($('#'+id).css('display') == 'none')
			{
				$('#'+id).slideDown();
				$('#extend img').attr('src', "<?php echo $rootfolder; ?>images/arrow_down.png");
			}
			else
			{
				$('#'+id).slideUp();
				$('#extend img').attr('src', "<?php echo $rootfolder; ?>images/arrow_up.png");
			}
		}
		</script>
	<h1><?php echo $title; ?></h1>
	<table id="info" cellspacing="0">
		<form action="#" method="POST">
			<tr>
				<th colspan="2"><div onclick="spoiler('backup_spoiler')" id="extend" class="buttonlink" title="Mehr laden">
						<a>Backup<img src="<?php echo $rootfolder; ?>images/arrow_up.png"></a>
					</div></th>
			</tr>
			<tr>
				<td colspan="2">
					<div id="backup_spoiler">
						<table cellspacing="0" id="info_content">
							
							
							<tr>
								<td colspan="2">
									<div style="text-align: center;">
										<input type="hidden" name="uid" id="uid" value="<?php echo $_GET['uid']; ?>">
										<input type="hidden" name="t" id="t" value="<?php echo $t?"1":"0"; ?>">
										<div onclick="saveinfo()" id="saveinfo" class="buttonlink savebutton" title="speichern">
											<a>Speichern<img src="<?php echo $rootfolder; ?>images/save.png"></a>
										</div>
									</div>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</form>
	</table>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>