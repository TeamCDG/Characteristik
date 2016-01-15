<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Alle Lehrer";
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/infodesigner.php");
?>

	<h1><?php echo $title; ?></h1>
	<style type="text/css">
		table
		{
			border-top: 1px solid silver;
			border-left: 1px solid silver;
			border-right: 1px solid silver;
		}
		
		table, tr
		{
			width: 100%;
			
		}
		
		th, td
		{
			width: 50%;
		}
		
		th
		{
			height: 30px;
			background: rgba(255, 255, 255, .1);
		}
		
		td
		{
			vertical-align:top;
			height: 20px;
		}
		
		td ul
		{
			height: auto;
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
		
		td a
		{
			text-decoration:none;
			margin-left: 5px;
		}
		</style>
	<table cellspacing="0">
	<?php 
		$ascii = 65;
		
		$users = getAllTeacher(true, 'name`, `prename');		
		$colc = 0;
		$colcount = 4;
		while(chr($ascii) != strtoupper(substr($users[0]->name, 0, 1)) )
		{
			$ascii++;
			if($ascii > 90) break;
		}
		if($ascii <= 90)
			echo "<tr><th class=\"b\" colspan=\"".$colcount."\">".chr($ascii)."</td></tr><tr>";
		
		foreach($users as $user)
		{
			
			if(strtoupper(substr($user->name, 0, 1)) != chr($ascii) && $ascii <= 90)
			{
				while($colc % $colcount != 0)
				{
					if(($colc +1) % $colcount == 0)
						echo "<td class=\"b\"></td>";
					else
						echo "<td class=\"br\"></td>";
					$colc++;
				}
				
				echo "</tr>";
				
				$ascii++;
				while(chr($ascii) != strtoupper(substr($user->name, 0, 1)) )
				{
					$ascii++;
					if($ascii > 90) break;
				}
				
				if($ascii <= 90)
					echo "<tr><th class=\"b\" colspan=\"".$colcount."\">".chr($ascii)."</td></tr><tr>";
				
			}
			
			if($colc % $colcount == 0)
			{
				echo "</tr><tr>";
				$colc = 0;
			}
			
			
			if(($colc +1) % $colcount == 0)
				echo "<td class=\"b\"><a href=\"".$rootfolder."c/showuser/?uid=".$user->id."&t=1\">".$user->prename." ".$user->name."</a></td>";
			else
				echo "<td class=\"br\"><a href=\"".$rootfolder."c/showuser/?uid=".$user->id."&t=1\">".$user->prename." ".$user->name."</a></td>";
			
			$colc++;
		}
		
		while($colc % $colcount != 0)
		{
			if(($colc +1) % $colcount == 0)
				echo "<td class=\"b\"></td>";
			else
				echo "<td class=\"br\"></td>";
			$colc++;
		}
		
		echo "<tr>";
	?>
	</table>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>