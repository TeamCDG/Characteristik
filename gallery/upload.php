<?php
session_start();
/**
 * Multiple file upload with progress bar php and jQuery
 * 
 * @author Resalat Haque
 * @link http://www.w3bees.com
 * 
 */
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."lib/imgcrop.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$extensions = array('jpeg', 'jpg', 'png');
if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_FILES['userImage']))
{
	foreach ( $_FILES['userImage']['name'] as $i => $name )
	{
		if(isset($_SESSION['file_upload']['abort'][$i]))
			continue;
	
		if( !in_array(pathinfo($name, PATHINFO_EXTENSION), $extensions) )
			continue;

		$sql = "SELECT * FROM `albums` WHERE `id` = '".mysql_real_escape_string($_POST['aid'])."' ; ";
		$res = mysql_query($sql) or die("iheartrainbows44");
		$album = mysql_fetch_object($res);
		$fname = $_POST['uniqid']."_".$_SESSION['userid']."_".$i."_".$_FILES['userImage']['name'][$i];
		$path = $rootfolder.$album->path;
	    if( move_uploaded_file($_FILES["userImage"]["tmp_name"][$i], $_SERVER['DOCUMENT_ROOT'].$path.$fname) )
		{
	    	$count++;
			$_SESSION['file_upload']['count'] = $count;
			$_SESSION['file_upload']['files'][$i] = $path.$fname;
			cropImg($_SERVER['DOCUMENT_ROOT'].$path.$fname, 100, 100, $_SERVER['DOCUMENT_ROOT'].$path."thumb/".$fname, 80);
			$sql = "INSERT INTO `images`(`album`, `title`, `description`, `filename`, `uploader`) VALUES ('".$_POST['aid']."','','','".mysql_real_escape_string($fname)."','".$_SESSION['userid']."'); ";
			mysql_query($sql) or die("iheartrainbows44");
		}
	}
	
}

echo json_encode(array('files' => $_SESSION['file_upload']['files']));

unset($_SESSION['file_upload']);

?>