<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Alle Umfragen";
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/infodesigner.php");
?>
	<style type="text/css">
		ul.polllist 
		{
			margin-left: auto;
			margin-right: auto;
			width: auto;
			min-width: 400px;
			max-width: 600px;
		}
	</style>
	<h1><?php echo $title; ?></h1>
	<?php
	$polls = getAllPolls();
	?>
	<h2>Unbeantworte Umfragen</h2>
	<ul class="polllist">
	<?php 
	foreach($polls as $poll)
	{
		if(!$poll['voted'])
			echo "<li><a href=\"showpoll/?pid=".$poll['id']."\" >".$poll['title']."</a></li>";
	}
	?>	
	</ul>
	<h2>Beantworte Umfragen</h2>
	<ul class="polllist">
	<?php 
	foreach($polls as $poll)
	{
		if($poll['voted'])
			echo "<li><a href=\"showpoll/?pid=".$poll['id']."\" >".$poll['title']."</a></li>";
	}
	?>	
	</ul>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>