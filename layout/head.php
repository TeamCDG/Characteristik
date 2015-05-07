<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/guessid.php");
$illuminati = isset($_GET['illuminati']);
if(isset($_POST['search']) && $_POST['uid']!=-1)
{
	if(isset($_POST['teacher']))
		header('Location: '.$rootfolder.'c/showuser/?uid='.$_POST['uid'].'&t='.$_POST['teacher']);
	else
		header('Location: '.$rootfolder.'c/showuser/?uid='.$_POST['uid']);
	exit;
}
else if(isset($_POST['search']) && $_POST['uid']==-1)
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
		if(strtolower($_POST['user']) == "illuminati")
		{
			$illuminati = true;
		}
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
		
		</script>
		<script type="text/javascript">
		function sub()
		{
			$("#searchform").submit();
			$("#searchform").reset();
		}
		</script>
		

			
	</head>
	<body <?php if($illuminati) { ?>  onkeydown="unilluminati()" <?php } ?> >
	<?php if($illuminati) { ?>
		<div onload="setvol();" id="illuminati" style="width: 800px; height: 800px; z-index: 599; pointer-events: none; left: calc(50% - 400px); position: absolute;">
			<img id="illuminati_image" width="800px" height="800px" title="Illuminati confirmed" style="opacity: 0.65; alt="Illuminati confirmed" src="<?php echo $rootfolder; ?>images/illuminati.png">
			
			<audio id ="illuminati_sound" controls loop autoplay style="display:none;" onplay="setvol();">
				<source src="<?php echo $rootfolder; ?>sound/illuminati.ogg" type="audio/ogg">
				<source src="<?php echo $rootfolder; ?>sound/illuminati.mp3" type="audio/mpeg">
			</audio>
		</div>
		<script type="text/javascript">
			
			
			var illuminatiId = -1;		
			var rot = 0;
			function unilluminati()
			{
				clearInterval(illuminatiId);
				$('#illuminati_sound').stop();
				$('#illuminati').remove();
			}
			
			function setvol()
			{
				 $("#illuminati_sound").prop("volume", 0.1);
				 
				illuminatiId = setInterval(function(){
					rot -= 1;
					rot = rot % 360;
					$('#illuminati_image').css({  
                                '-webkit-transform': 'rotate(' + rot + 'deg)',  //Safari 3.1+, Chrome  
                                '-moz-transform': 'rotate(' + rot + 'deg)',     //Firefox 3.5-15  
                                '-ms-transform': 'rotate(' + rot + 'deg)',      //IE9+  
                                '-o-transform': 'rotate(' + rot + 'deg)',       //Opera 10.5-12.00  
                                'transform': 'rotate(' + rot + 'deg)'          //Firefox 16+, Opera 12.50+  
    
					// console.log("rot: "+rot+" / realrot: "+$('#illuminati_image').css("transform"));
                            })  
				}, 33);
			}
			
		</script>
	<?php } ?>