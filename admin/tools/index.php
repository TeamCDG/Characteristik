<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Tools";
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/infodesigner.php");
?>

	<h1><?php echo $title; ?></h1>
	<h2>Nutzer verschmelzen</h2>
	<div style="width: 400px; margin-left: auto; margin-right:auto;">
	<h3>Ist ein Nutzer ausversehen (oder absichtlich) doppelt eingefügt worden, kann man mit dieser Funktion zwei Nutzer zu einem verschmelzen ohne Datensätze zu verlieren.</h3>
	<label><input style="vertical-align: top;" type="checkbox" id="cookie" onchange="updateCookie()" <?php if(isset($_COOKIE['userid'])) echo "checked"; ?> >Eingeloggt bleiben</label>
	</div>
	<div align="center" style="margin-left: auto; margin-right:auto;"><img src="<?php echo $rootfolder; ?>images/construction.png" style="width:200px; height: 200px;"></div>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>