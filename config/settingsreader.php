<?php
$f = fopen($_SERVER['DOCUMENT_ROOT'].$rootfolder."config/settings.cfg", "r") or die("Unable to open settings!");
while(!feof($f)) {
	$line = trim(fgets($f));
	$i = strpos($line, '=');
	if(substr($line, $i+1) == "true" || substr($line, $i+1) == "false")
		$_SESSION[substr($line, 0, $i)] = substr($line, $i+1)=="true"?true:false;
	else if(is_numeric(substr($line, $i+1)))
		$_SESSION[substr($line, 0, $i)] = intval(substr($line, $i+1));
	else
		$_SESSION[substr($line, 0, $i)] = substr($line, $i+1);
}
fclose($f);
?>