<?php

//	param		| method	| type				| desc
//------------------------------------------------------------------
//	rowCount	| post		| int				| row count
//------------------------------------------------------------------
//	label		| post		| array(string)		| array of labels
//------------------------------------------------------------------
//	rows		| post		| array(int)		| array of row counts
//------------------------------------------------------------------
//	type		| post		| array(int)		| array of types
//------------------------------------------------------------------
//	side		| post		| array(int)		| array of sides
//------------------------------------------------------------------
//	min_length	| post		| array(int)		| array of min lengths
//------------------------------------------------------------------

$include = false;
if(isset($rootfolder))
	$include = true;

if(!$include)
{
	$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
	$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

	include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
	
	if(isset($_POST['rowCount']))
	{
		if($_SESSION['debug']) var_dump($_POST);
		
		$rowCount = intval($_POST['rowCount']);
		
		$sql = "DELETE FROM `infobuilder`";
		mysql_query($sql) or die("ERROR #010 Query failed: $sql @ajax/infodesigner.php - ".mysql_error());
		
		$sql = "INSERT INTO `infobuilder` (`id`, `label`, `rows`, `type`, `side`, `min_length`) VALUES ";
		for($i = 0; $i < $rowCount; $i++)
		{
			$sql.="('".$i."', '".
			mysql_real_escape_string($_POST['label'][$i])."', '".
			mysql_real_escape_string($_POST['rows'][$i])."', '".
			mysql_real_escape_string($_POST['type'][$i])."', '".
			mysql_real_escape_string($_POST['side'][$i])."', '".
			mysql_real_escape_string($_POST['min_length'][$i])."') ";
			
			if($i != intval($_POST['rowCount'])-1)
				$sql.=",";
		}
		
		mysql_query($sql) or die("ERROR #011 Query failed: $sql @ajax/infodesigner.php - ".mysql_error());
		
		$sql = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='info'";
		$res = mysql_query($sql) or die("ERROR #011 Query failed: $sql @ajax/infodesigner.php - ".mysql_error());
		
		$actualRows = mysql_num_rows($res);
		
		$sql="";
		
		$i = 0;
		while($row = mysql_fetch_object($res))
		{
			if($i < 2) { $i++; continue; }
			
			if($row->COLUMN_NAME != "".($i-2) && ($i-2) < $rowCount)
			{
				$sql = "ALTER TABLE `info` CHANGE `".$row->COLUMN_NAME."` `".($i-2)."` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL; ";
				mysql_query($sql) or die("ERROR #012 Query failed: $sql @ajax/infodesigner.php - ".mysql_error());
			}
			else if( ($i-2) >= $rowCount)
			{
				$sql = "ALTER TABLE `info` DROP COLUMN `".$row->COLUMN_NAME."`;";
				mysql_query($sql) or die("ERROR #013 Query failed: $sql @ajax/infodesigner.php - ".mysql_error());
			}
			
			$i++;
		}
		
		while(($i-2) < $rowCount)
		{
			$sql = "ALTER TABLE `info` ADD `".($i-2)."` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
			mysql_query($sql) or die("ERROR #014 Query failed: $sql @ajax/infodesigner.php - ".mysql_error());
			$i++;
		}
		
		if($_SESSION['debug']) var_dump($sql);
		
		echo "erfolgreich gespeichert!";
	}
}
	

function getRowCount()
{
	$sql = "SELECT COUNT(*) AS c FROM `infobuilder`";
	$res = mysql_query($sql) or die("ERROR #018 Query failed: $sql @ajax/infodesigner.php - ".mysql_error());
	return mysql_fetch_object($res)->c;
}

function printInfoBuilder($rootfolder)
{
	$sql = "SELECT * FROM `infobuilder`";
	$res = mysql_query($sql) or die("ERROR #017 Query failed: $sql @ajax/infodesigner.php - ".mysql_error());
	while($r = mysql_fetch_object($res))
	{
		echo "<tr id=\"designer_row_".$r->id."\"><td class=\"br label\"><div class=\"container\"><input onkeyup=\"updateLabel(".$r->id.
		")\" id=\"designer_row_".$r->id."_label\" name=\"designer_row_".$r->id."_label\" width=\"100%\" value=\"".$r->label."\"></div></td>".
		
		"<td class=\"br type\"><div><select onchange=\"updateType(".$r->id.")\" id=\"designer_row_".$r->id.
		"_type\" name=\"designer_row_".$r->id."_type\" size=\"1\"><option value=\"0\" ".($r->type=="0"?"selected":"").">Text</option><option value=\"1\" ".($r->type=="1"?"selected":"").">Text (mehrzeilig)</option>".
		"<option value=\"2\" ".($r->type=="2"?"selected":"").">Zahl</option><option value=\"3\" ".($r->type=="3"?"selected":"").">Job</option><option value=\"4\" ".($r->type=="4"?"selected":"").">Geburtsjahrgang</option></select></div></td>".
		
		"<td class=\"br minlen\"><div><input type=\"number\" id=\"designer_row_".$r->id.
		"_minlen\" name=\"designer_row_".$r->id."_minlen\" step=\"1\" min=\"0\" value=\"".$r->min_length."\"></div></td>".
		
		"<td class=\"br rows\"><div><input onchange=\"rebuilt()\" type=\"number\" id=\"designer_row_".$r->id.
		"_rows\" name=\"designer_row_".$r->id."_rows\" step=\"1\" min=\"1\" value=\"".$r->rows."\"></div></td>".
		
		"<td class=\"br side\"><div><select onchange=\"rebuilt()\" id=\"designer_row_".$r->id.
		"_side\" name=\"designer_row_".$r->id."_side\" size=\"1\"><option value=\"0\" ".($r->side=="0"?"selected":"").">Links</option><option value=\"1\" ".($r->side=="1"?"selected":"").">Rechts</option>".
		"<option value=\"2\" ".($r->side=="2"?"selected":"").">Beide</option></select></div></td>".
		
		"<td class=\"b delete\"><div><div onclick=\"deleteLine(".$r->id.")\" id=\"designer_row_".$r->id."_delete\" ".
		"class=\"buttonlink deletebutton\" title=\"Löschen\"><a>Löschen<img src=\"".$rootfolder."images/x.png\"></a></div></div></td></tr>";
	}
}

function printInfo($fillInfo = false, $uid = -1)
{
	$info = null;
	
	if($fillInfo)
	{
		if($uid == -1) $uid = $_SESSION['userid'];
		
		$sql = "SELECT * FROM `info` WHERE `uid` = '".$uid."'; ";
		$res = mysql_query($sql) or die("ERROR #015 Query failed: $sql @ajax/infodesigner.php - ".mysql_error());
		$info = mysql_fetch_array($res);
	}
	
	$edit = $uid ==  $_SESSION['userid'];

	$sql = "SELECT * FROM `infobuilder`";
	$res = mysql_query($sql) or die("ERROR #016 Query failed: $sql @ajax/infodesigner.php - ".mysql_error());
	$rows = [];
	
	while($r = mysql_fetch_object($res))
	{
		$rows[] = $r;
	}
	

	$table = [];
	$rowCount = 0;
	for($i = 0; $i < count($rows); $i++)
	{
		$rowCount += $rows[$i]->rows;	
		for($add = 0; $add < $rows[$i]->rows; $add++)
		{
			$table[] = ["",""];
		}
	}

	for($i = 0; $i < count($rows); $i++)
	{
		$rowsNeeded = $rows[$i]->rows;
		$side = $rows[$i]->side;
		
		$pos = findFirstFree($side, $rowsNeeded, $table);
		
		for($p = 0; $p < $rowsNeeded; $p++)
		{
			switch($side)
			{
				case 0:
					$table[$pos+$p][0] = ($p==0?"":"_").$i;
					break;
				case 1:
					$table[$pos+$p][1] = ($p==0?"":"_").$i;
					break;
				case 2:
					$table[$pos+$p][0] = ($p==0?"":"_").$i;
					$table[$pos+$p][1] = ($p==0?"":"_").$i;
					break;
			}
		}
	}
			

			
	for($i = 0; $i < count($table); $i++)
	{
				
		if(($table[$i][1] != "" && substr($table[$i][1],0,1) != "_") || ($table[$i][0] != "" && substr($table[$i][0],0,1) != "_"))
		{
			if($table[$i][0] == $table[$i][1])
			{
				echo "<tr><td class=\"b\" colspan=\"2\" rowspan=\"".($rows[intval($table[$i][0])]->rows).
				"\"><div id=\"designer_".$table[i][0]."_label\" class=\"container_left\">".
				($rows[intval($table[$i][0])]->label)."</div><div id=\"designer_".$table[$i][0]."_container\" class=\"container_right\">".
				getFieldByType($table[$i][0], $rows, $fillInfo, $info, $edit)."</div></td></tr>";
			}
			else
			{
				if($table[$i][0] == "" && $table[$i][1] != "" && substr($table[$i][1],0,1) != "_")
				{
					echo "<tr><td class=\"br\"><div class=\"container_left\"></div></td><td class=\"b\" colspan=\"2\" rowspan=\"".($rows[intval($table[$i][1])]->rows).
					"\"><div id=\"designer_".$table[$i][1]."_label\" class=\"container_left\">".
					($rows[intval($table[$i][1])]->label)."</div><div id=\"designer_".$table[$i][1]."_container\" class=\"container_right\">".
					getFieldByType($table[$i][1], $rows, $fillInfo, $info, $edit)."</div></td></tr>";
							
							
				}
				else if($table[$i][0] != "" && substr($table[$i][0],0,1) != "_" && $table[$i][1] != "" && substr($table[$i][1],0,1) != "_")
				{
					echo "<tr><td class=\"br\" rowspan=\"".($rows[intval($table[$i][0])]->rows).
					"\"><div id=\"designer_".$table[$i][0]."_label\" class=\"container_left\">".
					($rows[intval($table[$i][0])]->label)."</div><div class=\"container_right\">".
					getFieldByType($table[$i][0], $rows, $fillInfo, $info, $edit)."</div></td>".
					"<td class=\"b\" rowspan=\"".($rows[intval($table[$i][1])]->rows).
					"\"><div id=\"designer_".$table[$i][1]."_label\" class=\"container_left\">".
					($rows[intval($table[$i][1])]->label)."</div><div id=\"designer_".$table[$i][1]."_container\" class=\"container_right\">".
					getFieldByType($table[$i][1], $rows, $fillInfo, $info, $edit)."</div></td></tr>";
				}
				else
				{
					echo "<tr id=\"RFLN_ELEM\"><td class=\"br\" rowspan=\"".($rows[intval($table[$i][0])]->rows).
					"\"><div id=\"designer_".$table[$i][0]."_label\" class=\"container_left\">".
					($rows[intval($table[$i][0])]->label)."</div><div id=\"designer_".$table[$i][0]."_container\" class=\"container_right\">".
					getFieldByType($table[$i][0], $rows, $fillInfo, $info, $edit)."</div></td>";
					if(substr($table[$i][1],0,1) != "_")
						echo "<td class=\"b\"><div class=\"container_left\"></div></td></tr>";
				}
			}
			
			
		}
		else if(substr($table[$i][0],0,1) == "_" || substr($table[$i][1],0,1) == "_")
		{
			if(substr($table[$i][0],0,1) == "_" && substr($table[$i][1],0,1) == "_")
			{
				echo "<tr></tr>";
			}
			else if(substr($table[$i][0],0,1) == "" && substr($table[$i][1],0,1) == "_")
			{
				echo "<tr><td class=\"br\"><div class=\"container_left\"></div></td></tr>";
			}
			else if($table[$i][1] == "")
			{
				echo "<tr><td class=\"b\"><div class=\"container_left\"></div></td></tr>";
			}
		}
				
	
	}
}
		
// function getLabel(row)
// {
	// return $('#designer_row_'+row+'_label').val();
// }

// function getRowCount(row)
// {
	// return parseInt($('#designer_row_'+row+'_rows').val());
// }

function getFieldByType($row, $rows, $fillInfo = false, $info = null, $edit = true)
{
	if($edit)
	{
		switch(intval($rows[intval($row)]->type))
		{
			case 0:
				return "<input type=\"text\" data-type=\"0\" data-minlen=\"".$rows[intval($row)]->min_length."\" id=\"info_".$row."_input\"".($fillInfo?" value=\"".$info["".$row]."\" ":"").">";
				break;
				
			case 1:
				return "<textarea id=\"info_".$row."_input\" data-type=\"1\" data-minlen=\"".$rows[intval($row)]->min_length."\">".($fillInfo?$info["".$row]:"")."</textarea>";
				break;
				
			case 2:
				return "<input type=\"number\" data-type=\"2\" data-minlen=\"".$rows[intval($row)]->min_length."\" id=\"info_".$row."_input\" ".($fillInfo?" value=\"".$info["".$row]."\" ":"").">";
				break;
				
			case 3:
				return "<input type=\"text\" data-type=\"3\" data-minlen=\"".$rows[intval($row)]->min_length."\" id=\"info_".$row."_input\" ".($fillInfo?" value=\"".$info["".$row]."\" ":"").">";
				break;
				
			case 4:
				return "<input type=\"number\" data-type=\"4\" data-minlen=\"".$rows[intval($row)]->min_length."\" min=\"1900\" max=\"2015\" id=\"info_".$row."_input\" ".($fillInfo?" value=\"".$info["".$row]."\" ":"").">";
				break;
		}
	}
	else
	{
		return $info["".$row];
	}
}

function findFirstFree($side, $rows, $table)
{
	// 0 ==> left
	// 1 ==> right
	// 2 ==> both
	for($i = 0; $i < count($table); $i++)
	{
		switch($side)
		{
			case 0:
				$free = true;
				
				for($off = 0; $off < $rows; $off++)
				{
					$free = $table[$i+$off][0] == "";
					if(!$free) break;
				}
				
				if($free) return $i;
				break;
			case 1:
				$free = true;
				
				for($off = 0; $off < $rows; $off++)
				{
					$free = $table[$i+$off][1] == "";
					if(!$free) break;
				}
				
				if($free) return $i;
				break;
			case 2:
				$free = true;
				
				for($off = 0; $off < $rows; $off++)
				{
					$free = (strlen($table[$i+$off][0]) == 0 && strlen($table[$i+$off][1]) == 0);
					if(!$free) break;
				}
				
				if($free) return $i;
				break;
		}
	}
	
	return -1;
}
?>