<?php 	
session_start();
if(!isset($_SESSION['style']))
	$_SESSION['style'] = 9;
$width=24;
			$height=24;
			$rows=22;
			$columns=40;
			$cwidth = 40; 
			$cheight=40; ?>
<html>
	<head>
		<title>JSSnake</title>
		<link rel="stylesheet" type="text/css" href="snake.css">
		<link rel="stylesheet" href="../<?php echo $_SESSION['style']; ?>.css">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> <!--320-->
		<script type="text/javascript">
		
		
		function preventDefault(e) {
		  e = e || window.event;
		  if (e.preventDefault)
			  e.preventDefault();
		  e.returnValue = false;  
		}

		
		
		var dead = false;
		var start = false;
		var score = 0;
		var running = false;
		var pause = false;
		
		var supportsTouch = ("ontouchstart" in window) || window.navigator.msMaxTouchPoints > 0;
		
		var direction = 0;
		var actualDir = 0;
		
		var rows = <?php echo $rows; ?>;
		var columns = <?php echo $columns; ?>;
		var userid = <?php echo isset($_SESSION['userid'])?$_SESSION['userid']:-1; ?>;
		
		function onTick()
		{
		
			if(!dead && start && !pause)
			{
				running = true;
				foodSpawn();
				move();
			}
			else
			{
				running = false;
			}
		}
		
		
		function move()
		{
			xmove = 0;
			ymove = 0;
			
			if(direction == 0) ymove = -1;			
			else if(direction == 1) xmove = 1;
			else if(direction == 2) ymove = 1;
			else if(direction == 3) xmove = -1;
			
			var td = document.getElementsByTagName("td");
			var next = null;
			
			for(var i = 0; i < td.length; i++)
			{
				if(td[i].className.indexOf("snakehead")!=-1)
				{
					next = td[i];
					break;
				}
			}
						
			var tmp = next.id.replace(/x/g, "").split("y");		
			var grow = false;			
			var nsh = document.getElementById('x'+(parseInt(tmp[0])+xmove)+'y'+(parseInt(tmp[1])+ymove));
			
			if(nsh.className == "food")
				grow = true;
			else if(nsh.className == "stone")
			{
				dead = true;
				saveScore();
			}
			else if(nsh.className.indexOf("snake") != -1)
			{
				dead = true;
				saveScore();
				return;
			}
			
			nsh.className = next.className.split("_")[0]+"_"+direction;
			nsh.setAttribute("snakeid", next.getAttribute("snakeid"));
			nsh.setAttribute("snakedir", direction);
			
			actualDir = direction;
			
			if(grow)
			{
				for(var i = 0; i < td.length; i++)
				{
					if(td[i].className.indexOf("snaketail")!=-1 || td[i].className.indexOf("snakebody")!=-1)
					{
						td[i].setAttribute("snakeid", parseInt(td[i].getAttribute("snakeid"))+1);
					}
				}
				
				
				next.setAttribute("snakeid", 1);
				next.setAttribute("snakedir", nsh.getAttribute("snakedir"));
				next.className = "snakebody_"+next.getAttribute("snakedir");
				
				for(var i = 0; i < td.length; i++)
				{
					if(td[i].className.indexOf("snaketail")!=-1)
					{
						var previous = getPreviousBySnakeid(td, td[i].getAttribute("snakeid"));
						td[i].setAttribute("snakedir", previous.getAttribute("snakedir"));
						td[i].className = td[i].className.split("_")[0]+"_"+td[i].getAttribute("snakedir");
						break;
					}
				}
				
				score++;
				document.getElementById("score").innerHTML = "Score: "+score;
				
				return;
			}
			
			
			while(!next.className.indexOf("snaketail")!=-1)
			{
				var tmp = getNextBySnakeid(td, next.getAttribute("snakeid"));
				if(tmp == undefined)
					break;
				next.className = tmp.className;
				next.setAttribute("snakeid", tmp.getAttribute("snakeid"));
				//next.setAttribute("snakedir", tmp.getAttribute("snakedir"));
				
				next.className = next.className.split("_")[0]+"_"+next.getAttribute("snakedir");
				
				next = tmp;
			}
			
			next.className = "grass";
			next.setAttribute("snakeid", -1);
			next.setAttribute("snakedir", -1);
			
			for(var i = 0; i < td.length; i++)
			{
				if(td[i].className.indexOf("snaketail")!=-1)
				{
					td[i].setAttribute("snakedir", getPreviousBySnakeid(td, td[i].getAttribute("snakeid")).getAttribute("snakedir"));
					td[i].className = td[i].className.split("_")[0]+"_"+td[i].getAttribute("snakedir");
					break;
				}
			}
		}
		
		function getPos(el)
		{
			return el.id.replace(/x/g, "").split("y");	
		}
		
		function getPreviousBySnakeid(elem, curId)
		{
			for(var i = 0; i < elem.length; i++)
			{			
				if(elem[i].className.indexOf("snake")!=-1 && parseInt(elem[i].getAttribute("snakeid")) == parseInt(curId)-1)
				{					
					return elem[i];
				}
			}
		}
		
		function getNextBySnakeid(elem, curId)
		{
			for(var i = 0; i < elem.length; i++)
			{			
				if(elem[i].className.indexOf("snake")!=-1 && parseInt(elem[i].getAttribute("snakeid")) == parseInt(curId)+1)
				{					
					return elem[i];
				}
			}
		}
		
		function foodSpawn()
		{
			if(document.getElementsByClassName('food').length == 0)
			{
				var x = 0;
				var y = 0;
				
				while(true)
				{
					y = Math.floor((Math.random() * (<?php echo $rows; ?>-1)) + 1);
					x = Math.floor((Math.random() * (<?php echo $columns; ?>-1)) + 1);
					
					if(document.getElementById('x'+x+'y'+y).className == "grass")
					{
						document.getElementById('x'+x+'y'+y).className = "food";
						break;
					}
				}
			}
		} 
		
		function keycheck(e)
		{
			if(e.keyCode == 38)
			{	up();}
			else if(e.keyCode == 40)
			{	down();}
			else if(e.keyCode == 37)
				left();
			else if(e.keyCode == 39)
				right();
			else if(e.keyCode == 13 || e.keyCode == 32)
				restart();
			else if(e.keyCode == 80)
				pause = !pause;
				
			document.getElementById("debug").innerHTML = getActualDir()+"/"+direction;
		}
		
		
		//onTick();
		var size = Math.floor(Math.min((window.innerWidth-120)/columns, (window.innerHeight-0)/rows))-1;
		function init()
		{
			var td = document.getElementsByTagName("td");	
			
			for(var i = 0; i < td.length; i++)
			{
				if(td[i].id != "noedit" && td[i].id != "score" && td[i].id != "start")
				{
					td[i].style.width = size+"px";
					td[i].style.height = size+"px";
				}
			}
		}
		
		function restart()
		{
			if((dead || running) && !pause)
			{
				start = false;
				dead = false;
				
				var td = document.getElementsByTagName("td");
				
				for(var i = 0; i < td.length; i++)
				{
					var tmp = getPos(td[i]);
					if(tmp[0] == 0 || tmp[0]==columns-1 || tmp[1] == 0 || tmp[1] == rows-1)
					{
						td[i].className = "stone";						
					}					
					else if((td[i].className.indexOf("stone")==-1)&& td[i].id != "noedit" && td[i].id != "score" && td[i].id != "start" )
					{
						td[i].className = "grass";
					}

					td[i].setAttribute("snakedir",-1);	
					td[i].setAttribute("snakeid",-1);					
				}
				
				var x = parseInt((columns-1)/2);
				var y = parseInt((rows-1)/2);
				
				document.getElementById("x"+x+"y"+y).className = "snakehead_0";
				document.getElementById("x"+x+"y"+y).setAttribute("snakeid", 0);
				document.getElementById("x"+x+"y"+y).setAttribute("snakedir", 0);
				
				document.getElementById("x"+x+"y"+(y+1)).className = "snakebody_0";
				document.getElementById("x"+x+"y"+(y+1)).setAttribute("snakeid", 1);
				document.getElementById("x"+x+"y"+(y+1)).setAttribute("snakedir", 0);
				
				document.getElementById("x"+x+"y"+(y+2)).className = "snaketail_0";
				document.getElementById("x"+x+"y"+(y+2)).setAttribute("snakeid", 2);
				document.getElementById("x"+x+"y"+(y+2)).setAttribute("snakedir", 0);
				
				score = 0;
				pause = false;
				
				document.getElementById("score").innerHTML = "Score: 0";
				
			}	
			else
			{
				start = true;				
				document.getElementById("start").innerHTML = "RESTART";
			}
		}
		
		function unscroll(e)
		{
			preventDefault(e);
		}
		
		function down()
		{
			if(actualDir != 0 && !pause)
				direction = 2;
		}
		
		function up()
		{
			if(actualDir != 2 && !pause)
				direction = 0;
		}
		
		function left()
		{
			if(actualDir != 1 && !pause)
				direction = 3;
		}
				
		function right()
		{
			if(actualDir != 3 && !pause)
				direction = 1;
		}
			
		function saveScore()
		{
			var xmlhttp;
			if (window.XMLHttpRequest)
			{
				// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp=new XMLHttpRequest();
			}
			else if (window.ActiveXObject)
			{
				// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			
			if(userid != -1)
			{
				xmlhttp.open("GET","../addscore.php?score="+score+"&uid="+userid+"&touch="+supportsTouch,true);
				xmlhttp.send(null);
			}
		}
		
		
		document.onkeyup = keycheck;
		document.onkeydown = unscroll;
		
		if(supportsTouch)
			window.setInterval(onTick, 200);
		else
			window.setInterval(onTick, 100);
		</script>
	</head>
	<body onload="init();">
		<img width="0px" height="0px" src="food.png" alt="">
		<img width="0px" height="0px" src="grass.png" alt="">
		<img width="0px" height="0px" src="stone.png" alt="">
		
		<img width="0px" height="0px" src="snakebody_0.png" alt="">
		<img width="0px" height="0px" src="snakebody_1.png" alt="">
		<img width="0px" height="0px" src="snakebody_2.png" alt="">
		<img width="0px" height="0px" src="snakebody_3.png" alt="">
		
		<img width="0px" height="0px" src="snakehead_0.png" alt="">
		<img width="0px" height="0px" src="snakehead_1.png" alt="">
		<img width="0px" height="0px" src="snakehead_2.png" alt="">
		<img width="0px" height="0px" src="snakehead_3.png" alt="">
		
		<img width="0px" height="0px" src="snaketail_0.png" alt="">
		<img width="0px" height="0px" src="snaketail_1.png" alt="">
		<img width="0px" height="0px" src="snaketail_2.png" alt="">
		<img width="0px" height="0px" src="snaketail_3.png" alt="">
		<table style="float:left;">
			<tr>
				<td id="noedit" width="<?php echo $cwidth."px"; ?>" height="<?php echo $cheight."px"; ?>"></td>
				<td id="noedit" width="<?php echo $cwidth."px"; ?>" height="<?php echo $cheight."px"; ?>" style="background-image: url('grass.png'); text-align: center; color: #FFFFFF;" onclick="up();">/\</td>
				<td id="noedit" width="<?php echo $cwidth."px"; ?>" height="<?php echo $cheight."px"; ?>"></td>
			</tr>
			<tr>
				<td id="noedit" width="<?php echo $cwidth."px"; ?>" height="<?php echo $cheight."px"; ?>" style="background-image: url('grass.png'); text-align: center; color: #FFFFFF;" onclick="left()"><</td>
				<td id="noedit" width="<?php echo $cwidth."px"; ?>" height="<?php echo $cheight."px"; ?>"></td>
				<td id="noedit" width="<?php echo $cwidth."px"; ?>" height="<?php echo $cheight."px"; ?>" style="background-image: url('grass.png'); text-align: center; color: #FFFFFF;" onclick="right()">></td>
			</tr>
			<tr>
				<td id="noedit" width="<?php echo $cwidth."px"; ?>" height="<?php echo $cheight."px"; ?>"></td>
				<td id="noedit" width="<?php echo $cwidth."px"; ?>" height="<?php echo $cheight."px"; ?>" style="background-image: url('grass.png'); text-align: center; color: #FFFFFF;" onclick="down()">\/</td>
				<td id="noedit" width="<?php echo $cwidth."px"; ?>" height="<?php echo $cheight."px"; ?>"></td>
			</tr>
			<tr>
				<td colspan="3" id="score" width="<?php echo ($cwidth*3)."px"; ?>" height="<?php echo $cheight."px"; ?>" style="background-image: url('grass.png'); text-align: center; color: #FFFFFF; font-size: 28px"><b>Score: 0</b></td>
			</tr>
			<tr>
				<td colspan="3" id="start" width="<?php echo ($cwidth*3)."px"; ?>" height="<?php echo $cheight."px"; ?>" style="background-image: url('grass.png'); text-align: center; color: #FFFFFF; font-size: 28px" onclick="restart()"><b>START</b></td>
			</tr>
			
		</table>
		
		<table cellspacing="0">
		
<?php 

	for($y = 0; $y < $rows; $y++)
	{
		echo "<tr>";
		for($x = 0; $x < $columns; $x++)
		{
			if($y == 0 || $y == $rows-1 || $x == 0 || $x == $columns-1)
			{
				echo "<td width=\"".$width."px\" height=\"".$height."px\" id=\"x".$x."y".$y."\" class=\"stone\"></td>";
			}
			else
			{
			
				if($x == intval(($columns-1)/2) && $y == intval(($rows-1)/2))
				{
					echo "<td width=\"".$width."px\" height=\"".$height."px\" id=\"x".$x."y".$y."\" snakeid=\"0\" snakedir=\"0\" class=\"snakehead_0\"></td>";
				}
				else if($x == intval(($columns-1)/2) && $y == intval(($rows-1)/2)+1)
				{
					echo "<td width=\"".$width."px\" height=\"".$height."px\" id=\"x".$x."y".$y."\" snakeid=\"1\" snakedir=\"0\" class=\"snakebody_0\"></td>";
				}
				else if($x == intval(($columns-1)/2) && $y == intval(($rows-1)/2)+2)
				{
					echo "<td width=\"".$width."px\" height=\"".$height."px\" id=\"x".$x."y".$y."\" snakeid=\"2\" snakedir=\"0\" class=\"snaketail_0\"></td>";
				}
				else
				{
					echo "<td width=\"".$width."px\" height=\"".$height."px\" id=\"x".$x."y".$y."\" class=\"grass\"></td>";
				}
				
			}
		}
		echo "</tr>";
	}
?>	
		</table>
		<div style="float:right;" id="debug"></div>
	</body>
</html>