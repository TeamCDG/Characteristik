<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";


include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");

$poll = getPoll($_GET['pid']);
$inputType = $poll['multivote']?"checkbox":"radio";
$ownVotes = getVotesBy($_GET['pid'], $_SESSION['userid']);
$answers = getAnswers($_GET['pid']);

function containsVote($v, $vid)
{
	foreach($v as $vote)
	{
		if($vote['voteid'] == $vid)
			return true;
	}
	return false;
}

$title = $poll['title'];
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/infodesigner.php");
?>
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
				<script>
		var adding = false;
		var addAnimationId = -1;
		var pid = <?php echo $_GET['pid']; ?>;
		var revote = <?php echo $poll['revote'] == "1"?"true":"false"; ?>;
		
		function vote()
		{
			if(adding) return;
			adding = true;

			$('#add_info').html("");
			$('#add_error').html("");
			clearInterval(addAnimationId);

			var error = false;
			var votes = new Array();
			
			$(':checked').each(function(index) {
				votes.push($(this).attr('id'));
			});

			console.log(votes); 
			if(!error)
			{
				$.post( "<?php echo $rootfolder; ?>ajax/vote.php", { pid: pid, votes: votes}, function( data) {
					<?php if($_SESSION['debug']) { ?> console.log(data); <?php } ?>
					var res = JSON.parse(data);
					if(res.status == "200")
					{
						$('#add_info').css('display', 'none');
						$('#add_info').html(res.message);
						$('#add_info').slideDown();
						if(!revote) $('#vote_area').fadeOut();
						$('#vote_result').html(res.vote_result + "<img id=\"vote_result_diagram\" style=\"margin-left: 50px;\" src=\"<?php echo $rootfolder."thecakeisalie.php?pid=".$_GET['pid']."&fontcol=CCCCCC&bgcol=444444"; ?>\" alt=\"ergebnis\">");
						<?php if(!$_SESSION['debug']) { ?>
						addAnimationId = setInterval(function() {
							clearInterval(addAnimationId);
							$('#add_info').slideUp();
						}, 3000);
						<?php } ?>
					}
					else
					{
						$('#add_error').css('display', 'none');
						$('#add_error').html(res.message);
						$('#add_error').slideDown();
						<?php if(!$_SESSION['debug']) { ?>
						addAnimationId = setInterval(function() {
							clearInterval(addAnimationId);
							$('#add_error').slideUp();
						}, 5000);
						<?php } ?>
					}
				adding = false;
				});
			}
			else
			{
				$('#add_error').slideDown();
				adding = false;
				<?php if(!$_SESSION['debug']) { ?>
				addAnimationId = setInterval(function() {
					clearInterval(addAnimationId);
					$('#add_error').slideUp();
				}, 5000);
				<?php } ?>
			}
		}
		
		<?php if($_SESSION['permissions']['polls_edit']) { ?>
		var closing = false;
		function closePoll()
		{
			if(closing) return;
			closing = true;
			var pid = <?php echo $_GET['pid']; ?>;
			$.post( "<?php echo $rootfolder; ?>ajax/polledit.php", { type: 2, id: pid}, function( data) {
				<?php if($_SESSION['debug']) { ?> console.log(data); <?php } echo "\n"; ?>
				var res = JSON.parse(data);
					
				alert(res.message);
				if(res.status == "200")
					window.location = "<?php echo $rootfolder; ?>/c/polls/showpoll/?pid=<?php echo $_GET['pid']; ?>";
				deleting = false;
			});
		}
		
		function openPoll()
		{
			if(closing) return;
			closing = true;
			var pid = <?php echo $_GET['pid']; ?>;
			$.post( "<?php echo $rootfolder; ?>ajax/polledit.php", { type: 4, id: pid}, function( data) {
				<?php if($_SESSION['debug']) { ?> console.log(data); <?php } echo "\n"; ?>
				var res = JSON.parse(data);
					
				alert(res.message);
				if(res.status == "200")
					window.location = "<?php echo $rootfolder; ?>/c/polls/showpoll/?pid=<?php echo $_GET['pid']; ?>";
				deleting = false;
			});
		}
		
		function deletePoll()
		{
		}
		<?php } ?>
		</script>
	<h1><?php echo $title; ?></h1>
	<?php if($_SESSION['permissions']['polls_edit']) { ?>
	<h2><div style="margin-left: auto; margin-right: auto; text-align: center;" class="buttonlink" title="bearbeiten">
			<a href="<?php echo $rootfolder; ?>admin/editpoll/?pid=<?php echo $_GET['pid']; ?>">Bearbeiten<img src="<?php echo $rootfolder; ?>images/edit.png"></a>
		</div>
		<?php if(intval($poll['closed']) == 0) { ?>
		<div id="closePoll" onclick="closePoll()" style="margin-left: auto; margin-right: auto; text-align: center;" class="buttonlink" title="schließen">
			<a>Schließen<img src="<?php echo $rootfolder; ?>images/lock.png"></a>
		</div>
		<?php } else { ?>
		<div id="openPoll" onclick="openPoll()" style="margin-left: auto; margin-right: auto; text-align: center;" class="buttonlink" title="öffnen">
			<a>Öffnen<img src="<?php echo $rootfolder; ?>images/key.png"></a>
		</div>
		<?php } ?>
		<div onclick="deletePoll()" style="margin-left: auto; margin-right: auto; text-align: center;" class="buttonlink" title="löschen">
			<a>Löschen<img src="<?php echo $rootfolder; ?>images/x.png"></a>
		</div></h2>
	<?php } ?>
	<div id="add_info"></div>
	<div id="add_error"></div>
	<?php $colcount = 2;
	if((!$poll['voted'] || $poll['revote']) && intval($poll['closed']) != 1) { ?>
	<table id="vote_area" cellspacing="0">
		<?php if($poll['type'] == 0) { 
		$ascii = 65;
		
		$users = getAllUser(true, 'name`, `prename');		
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
				echo "<td class=\"b\"><input name=\"vote\" type=\"".$inputType."\" id=\"".$user->id."\" ".(containsVote($ownVotes, $user->id)?"checked=\"checked\"":"")."><label for=\"".$user->id."\">".$user->prename." ".$user->name."</label></td>";
			else
				echo "<td class=\"br\"><input name=\"vote\" type=\"".$inputType."\" id=\"".$user->id."\" ".(containsVote($ownVotes, $user->id)?"checked=\"checked\"":"")."><label for=\"".$user->id."\">".$user->prename." ".$user->name."</label></td>";
			
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
		
		echo "<tr>"; ?>
		<?php } else if($poll['type'] == 1) { 
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
				echo "<td class=\"b\"><input name=\"vote\" type=\"".$inputType."\" id=\"".$user->id."\" ".(containsVote($ownVotes, $user->id)?"checked=\"checked\"":"")."><label for=\"".$user->id."\">".$user->prename." ".$user->name."</label></td>";
			else
				echo "<td class=\"br\"><input name=\"vote\" type=\"".$inputType."\" id=\"".$user->id."\" ".(containsVote($ownVotes, $user->id)?"checked=\"checked\"":"")."><label for=\"".$user->id."\">".$user->prename." ".$user->name."</label></td>";
			
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
		
		echo "<tr>"; ?>
		
		<?php } else if($poll['type'] == 2) { ?>
			<tr>
				<td class="br"><input name="vote" type="<?php echo $inputType; ?>" id="<?php echo array_keys($answers)[0]; ?>" <?php if(containsVote($ownVotes, 0)) echo "checked=\"checked\""; ?>><label for="<?php echo array_keys($answers)[0]; ?>"><?php echo $answers[array_keys($answers)[0]]; ?></label></td>
				<td class="b"><input name="vote" type="<?php echo $inputType; ?>" id="<?php echo array_keys($answers)[1]; ?>" <?php if(containsVote($ownVotes, 1)) echo "checked=\"checked\""; ?>><label for="<?php echo array_keys($answers)[1]; ?>"><?php echo $answers[array_keys($answers)[1]]; ?></label></td>
			</tr>
		<?php } else { 
		$colc = 0;
		$colcount = min(4, round(sqrt(count($answers))));
		$keys = array_keys($answers);
		
		for($i = 0; $i < count($keys); $i++)
		{
			
			
			if($colc % $colcount == 0)
			{
				echo "</tr><tr>";
				$colc = 0;
			}
			
			
			if(($colc +1) % $colcount == 0)
				echo "<td class=\"b\"><input name=\"vote\" type=\"".$inputType."\" id=\"".$keys[$i]."\" ".(containsVote($ownVotes, $keys[$i])?"checked=\"checked\"":"")."><label for=\"".$keys[$i]."\">".$answers[$keys[$i]]."</label></td>";
			else
				echo "<td class=\"br\"><input name=\"vote\" type=\"".$inputType."\" id=\"".$keys[$i]."\" ".(containsVote($ownVotes, $keys[$i])?"checked=\"checked\"":"")."><label for=\"".$keys[$i]."\">".$answers[$keys[$i]]."</label></td>";
			
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
		
		echo "<tr>"; } ?>
	<tr><td class="b" colspan="<?php echo $colcount; ?>">
		<div onclick="vote()" style="margin-left: auto; margin-right: auto; text-align: center;" class="buttonlink" title="abstimmen">
			<a>Abstimmen<img src="<?php echo $rootfolder; ?>images/vote.png"></a>
		</div>
	</td></tr>
	</table>
	<?php } else if (intval($poll['closed']) == 1) { ?>
	<h2><img style="width: 24px; height: 24px; vertical-align: middle;" src="<?php echo $rootfolder; ?>images/lock.png"> Die Umfrage ist geschlossen, daher kann nicht mehr abgestimmt werden. <img style="width: 24px; height: 24px; vertical-align: middle;" src="<?php echo $rootfolder; ?>images/lock.png"></h2>
	<?php } ?>
	<div id="vote_result">
	<?php if($poll['voted'] || $poll['result_prevote'] || $_SESSION['permissions']['polls_see_other_votes'] || intval($poll['closed']) == 1) { 
	
	$votes = array();
	$sql = "SELECT COUNT(*) as c FROM pollvotes WHERE `pollid`='".mysql_real_escape_string($_GET['pid'])."'";
	$res = mysql_query($sql) or die ("ERROR #031: Query failed: $sql @thecakeisalie.php - ".mysql_error());
	$vcount = mysql_fetch_object($res)->c;

	$sql = "SELECT COUNT(*) as c, voteid FROM pollvotes WHERE `pollid`='".mysql_real_escape_string($_GET['pid'])."' GROUP BY voteid ORDER BY c DESC";
	$res = mysql_query($sql) or die ("ERROR #032: Query failed: $sql @thecakeisalie.php - ".mysql_error());

	$c = 0;
	$allco = 0;
	while($row = mysql_fetch_object($res))
	{
		$tmp = array();
		array_push($tmp, $row->c);
		
		$allco += $row->c;
		
		array_push($tmp, $answers[$row->voteid]);
		
		array_push($votes, $tmp);
		$c++;
		
		if($c >= 10)
		{
			$stuff = array();
			array_push($stuff, $vcount - $allco);
			array_push($stuff, "Sonstige");
			array_push($votes, $stuff);
			break;
		}
	}
	echo "<ul style=\"float: left;\">";
	for($i = 0; $i < count($votes); $i++)
	{
		echo "<li>".($i+1).": ".$votes[$i][1]." ".$votes[$i][0]."/".$vcount." (".round((100.0 / floatval($vcount)) * $votes[$i][0], 2)."%)</li>";
	}
	echo "</ul>";
	?><img id="vote_result_diagram" style="margin-left: 50px;" src="<?php echo $rootfolder."thecakeisalie.php?pid=".$_GET['pid']."&fontcol=CCCCCC&bgcol=444444"; ?>" alt="ergebnis"></div>
	
	
	<?php } if($_SESSION['debug']) { var_dump($poll); var_dump($ownVotes); }  ?>
	<br><br><br><br>
	<div align="center" style="margin-left: auto; margin-right:auto;"><img src="<?php echo $rootfolder; ?>images/construction.png" style="width:200px; height: 200px;"></div>
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>