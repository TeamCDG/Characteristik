<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/guessid.php");
if(isset($_POST['uid']) && $_POST['uid']!=-1)
{
	if(isset($_POST['teacher']))
		header('Location: '.$rootfolder.'c/showuser/?uid='.$_POST['uid'].'&t='.$_POST['teacher']);
	else
		header('Location: '.$rootfolder.'c/showuser/?uid='.$_POST['uid']);
	exit;
}
else if(isset($_POST['uid']) && $_POST['uid']==-1)
{
	$id = getUserId($_POST['user']);
	if($id[0] != -1)
	{
		if($id[1] == 1)
			header('Location: '.$rootfolder.'c/showuser/?uid='.$id[0].'&t=true');
		else
			header('Location: '.$rootfolder.'c/showuser/?uid='.$id[0]);
		exit;
	}
	else
	{
		//var_dump($_POST);
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
	<!--
		☐ Not REKT ☑ REKT
	!-->
		<?php 
			if($_SESSION['mobile']) { ?>
				<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<?php } ?>
		
		<title><?php echo $title; ?></title>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		
		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
		
		<link rel="stylesheet" href="<?php echo $rootfolder;?>style/skin0.css">
		<style type="text/css">
		<?php include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/layout.php"); ?>
		</style>
		
		<script src="//code.jquery.com/jquery-1.11.0.js"></script>
		<script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
		<script src="<?php echo $rootfolder;?>lib/randint.js"></script>
		<script src="<?php echo $rootfolder;?>lib/unwrapinner.js"></script>
		<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/md5.js"></script>
		<script>
		$(function() {
			var names = [<?php 
								getAllJSON();
							?>
			];

			$( "#search" ).autocomplete({
				minLength: 0,
				source: names,
				focus: function( event, ui ) {
					$( "#search" ).val( ui.item.label );
					return false;
				},
				select: function( event, ui ) {
					$( "#search" ).val( ui.item.label );
					$( "#id" ).val( ui.item.id );
					$( "#teacher" ).val( ui.item.teacher );
					$("#searchform").submit();
					return false;
				}
			})
			.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li>" )
					.append( "<a>" +((item.teacher=="1")?" <font color=\"#FF0000\" >":"")+ item.label + ((item.teacher=="1")?" </font>":"")+  "</a>" )
					.appendTo( ul );
			};
		});
		</script>
		<script type="text/javascript">
		function sub()
		{
			$("#searchform").submit();
			$("#searchform").reset();
		}
		</script>
		

			
	</head>
	<body>