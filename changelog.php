<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Changelog";
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/news.php");

?>


		
		<h1>Changelog</h1>
		<h2>Version 0.2.0</h2>
		<p>
			<h3>Änderungen: </h3>
			<ul>
				<li>Neues Loginsystem: Sicheres einloggen durch MD5 Passwortübertragung</li>
			</ul>
			<h3>Erweiterungen: </h3>
			<ul>
				<li>Wartungsaccount</li>
			</ul>
		</p>
		
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>