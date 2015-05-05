<?php

//$myfile = fopen("testfile.txt", "w");
//fclose($myfile);



/*
function backup($db_name, $sql_connection)
{
	return backup(false, false, "", "backup/", $db_name, $sql_connection);
}


function backup($compression, $db_name, $sql_connection)
{
	return backup($compression, false, "", "backup/", $db_name, $sql_connection);
}


function backup($compression, $backup_folder, $db_name, $sql_connection)
{
	return backup($compression, false, "", $backup_folder, $db_name, $sql_connection);
}
*/

function backup($compression, $backup_send_mail, $backup_mail, $backup_folder, $db_name, $sql_connection)
{
	mysql_select_db($db_name, $sql_connection);
	$cur_time = date("D d.m.Y H:i:s"); 
	$timename = date("D_d.m.Y_H.i.s"); 
	$dat="-- Backup of '$db_name': $cur_time \n"; 
	$dat .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";

	$tables = mysql_list_tables($db_name,$sql_connection); 
	$num_tables = @mysql_num_rows($tables); 
	$i = 0; 
	while($i < $num_tables) 
    { 
		$table = mysql_tablename($tables, $i); 

        $dat .= "\n-- ----------------------------------------------------------\n--\n"; 
        $dat .= "-- Structure for Table '$table'\n--\n"; 
        $dat .= get_def($db_name,$table, $sql_connection); 
        $dat .= "\n\n"; 

        $dat .= "--\n-- Data for table '$table'\n--\n"; 
        $dat .= get_content($db_name,$table, $sql_connection); 
        $dat .= "\n\n"; 
        $i++; 
    } 
	$gzip = "";
	if($compression == true && extension_loaded("zlib"))
		$gzip = ".gz";
	else
		$compression = false;
	writeBackupFile($backup_folder.$db_name."_".$timename.".sql".$gzip, $compression, $dat);
	
	$sent = false;
	if($backup_send_mail)
		$sent = sendFileMail($backup_mail, "Backup of ".$db_name." on ".$cur_time, $backup_folder.$db_name."_".$timename.".sql".$gzip);
		
	return array("sentmail"=>$sent, "mailreciever"=>$backup_mail, "filename"=>$backup_folder.$db_name."_".$timename.".sql".$gzip, "md5"=>md5_file($backup_folder.$db_name."_".$timename.".sql".$gzip));
}

function get_def($db_name, $table, $sql_connection) { 
	
    $def = ""; 

    $def .= "CREATE TABLE IF NOT EXISTS `$table` (\n"; 
    $result = mysql_query("SHOW FULL COLUMNS FROM $table",$sql_connection); 
	if(!$result)
		return "";
		
		
    while($row = mysql_fetch_array($result)) { 
        $def .= "   `$row[Field]` $row[Type]"; 
		if (strlen($row["Collation"])> 0) $def .= " CHARACTER SET ".explode("_", $row["Collation"])[0]." COLLATE ".$row["Collation"]; 
        if ($row["Default"] != "") if($row['Default'] != "CURRENT_TIMESTAMP" && $row['Default'] != "NULL") $def .= " DEFAULT '$row[Default]'"; else $def .= " DEFAULT $row[Default]";		
        if ($row["Null"] != "YES") $def .= " NOT NULL"; 
        if ($row[Extra] != "") $def .= " $row[Extra]"; 
        $def .= ",\n"; 
    } 
    $def = ereg_replace(",\n$","", $def); 
    $result = mysql_query("SHOW KEYS FROM $table",$sql_connection); 
    while($row = mysql_fetch_array($result)) { 
          $kname=$row[Key_name]; 
          if(($kname != "PRIMARY") && ($row[Non_unique] == 0)) $kname="UNIQUE|$kname"; 
          if(!isset($index[$kname])) $index[$kname] = array(); 
          $index[$kname][] = $row[Column_name]; 
    } 
    while(list($x, $columns) = @each($index)) { 
          $def .= ",\n"; 
          if($x == "PRIMARY") $def .= "  PRIMARY KEY (" . implode($columns, ", ") . ")"; 
          else if (substr($x,0,6) == "UNIQUE") $def .= "  UNIQUE ".substr($x,7)." (" . implode($columns, ", ") . ")"; 
          else $def .= "  KEY $x (" . implode($columns, ", ") . ")"; 
    } 
	$sql = "SHOW TABLE STATUS FROM `".$db_name."` LIKE '$table';";
	$status = mysql_fetch_object(mysql_query($sql,$sql_connection));
	$def .= "\n) ";
	$def .= "ENGINE=".$status->Engine." DEFAULT CHARSET=".explode("_", $status->Collation)[0]." AUTO_INCREMENT=".$status->Auto_increment.";"; 
    return (stripslashes($def)); 
} 

function get_content($dbname, $table, $sql_connection) { 
    $content=""; 
    $result = mysql_query("SELECT * FROM $table",$sql_connection); 
    while($row = mysql_fetch_row($result)) { 
        $insert = "INSERT IGNORE INTO $table VALUES ("; 
        for($j=0; $j<mysql_num_fields($result);$j++) { 
            if(!isset($row[$j])) $insert .= "NULL,"; 
            else if($row[$j] != "") $insert .= "'".addslashes($row[$j])."',"; 
            else $insert .= "'',"; 
        } 
        $insert = ereg_replace(",$","",$insert); 
        $insert .= ");\n"; 
        $content .= $insert; 
    } 
    return $content; 
} 

function writeBackupFile($filename, $compression, $dat)
{
	if ($compression==1 || $compression) 
    { 
		$fp = gzopen($filename,"w"); 
		gzwrite ($fp,$dat); 
		gzclose ($fp);
    } 
    else 
    { 
		$fp = fopen ($filename,"w"); 
		fwrite ($fp,$dat); 
		fclose ($fp);
    } 
}

function sendFileMail($mail, $subject, $file)
{
	$mime_boundary = "-----=" . md5(uniqid(rand(), 1)); 
	$header = "From: backup@server.com \r\n"; 
    $header.= "MIME-Version: 1.0\r\n"; 
    $header.= "Content-Type: multipart/mixed;\r\n"; 
    $header.= " boundary=\"".$mime_boundary."\"\r\n"; 

    $content = "\r\n\r\nThis is a multi-part message in MIME format.\r\n\r\n"; 
	$content.= "--".$mime_boundary."\r\n"; 
	$content.= "Content-type: text/plain;charset=utf-8\r\n"; 
	$content.= "MD5 of $name :".md5_file($file);
	$name = basename($file); 
    $data = chunk_split(base64_encode(implode("", file($file)))); 
    $len = filesize($file); 
    $content.= "--".$mime_boundary."\r\n"; 
    $content.= "Content-Disposition: attachment;\r\n"; 
    $content.= "\tfilename=\"$name\";\r\n"; 
    $content.= "Content-Length: .$len;\r\n"; 
    $content.= "Content-Type: application/x-gzip; name=\"".$file."\"\r\n"; 
    $content.= "Content-Transfer-Encoding: base64\r\n\r\n"; 
    $content.= $data."\r\n";
	
    if(mail($mail, $subject, $content, $header)) return true; 
    else return false;
}


?>