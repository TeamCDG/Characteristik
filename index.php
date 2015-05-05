<?php
$af = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
$rootfolder = substr($af, 0, strpos($af, '/c')+2)."/";

include($_SERVER['DOCUMENT_ROOT'].$rootfolder."loginprotection.php");
$title = "Baum";
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/head.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/topnavi.php");
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."ajax/news.php");

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
		</style>
		
		<script type="text/javascript">
		function loadmore( obj ) {
		
			var button = $("#"+obj.id);
			var tr = $("#row_"+obj.id.split("_")[1]); 
			
			var type0 = tr.children().get(0).id.split('_')[1];
			var actual0 = $("div#news_"+type0+"_container_actual");			
			var min0 = -1;
			if($.trim(actual0.children().last().html())) 
				min0 = actual0.children().last().attr('id').split('_')[2];
			actual0.attr('id','news_'+type0+'_container_'+min0);
			
			var type1 = tr.children().get(1).id.split('_')[1];
			var actual1 = $("div#news_"+type1+"_container_actual");			
			var min1 = -1;
			if($.trim(actual1.children().last().html())) 
				min1 = actual1.children().last().attr('id').split('_')[2];
			actual1.attr('id','news_'+type1+'_container_'+min1);
			
			button.slideUp();
			
			var fin0 = false;
			var fin1 = false;
			
			ulL = actual0.parent();
			ulR = actual1.parent();
			
			$.get( "<?php echo $rootfolder."ajax/news.php"; ?>?type="+type0+"&min="+min0+"&count=10", function( data ) {
				if(data!="418")
			    {
					actual0.parent().html( actual0.parent().html() + "<div id=\"news_"+type0+"_container_actual\" style=\"display:none;\">" + data + "</div>");
					tr.children().get(0).setAttribute("data-count", parseInt(min0)+10);
					$("div#news_"+type0+"_container_actual").slideDown(400, function() {
						fin0 = true;
						
						if(fin0 && fin1) adjustHeight(ulL, ulR);
					});
					
				}
				else
				{
					fin0 = true;
					if(fin0 && fin1) adjustHeight(ulL, ulR);
				}
				
			});
			
			$.get( "<?php echo $rootfolder."ajax/news.php"; ?>?type="+type1+"&min="+min1+"&count=10", function( data ) {
					if(data!="418")
					{
						actual1.parent().html( actual1.parent().html() + "<div id=\"news_"+type1+"_container_actual\" style=\"display:none;\">" + data + "</div>");						
						tr.children().get(1).setAttribute("data-count", parseInt(min1)+10);
						$("div#news_"+type1+"_container_actual").slideDown(400, function() {
							fin1 = true;

							if(fin0 && fin1) adjustHeight(ulL, ulR);
						});
					}
					else
					{
						fin0 = true;
						if(fin0 && fin1) adjustHeight(ulL, ulR);
					}
					

			});		
			
			
			button.slideDown();
		}
		
		function adjustHeight(ulL, ulR)
		{
			ulL.css("line-height", "20px");
			ulR.css("line-height", "20px");
							
			var hdif = Math.abs(ulL.height()-ulR.height());
					
			if(ulL.height() > ulR.height())
			{
				var addh = Math.round(hdif / (ulR.children().length * 10));
				ulR.css("line-height", (20+addh)+"px");
			}
			else
			{
				var addh = Math.round(hdif / (ulL.children().length * 10));
				ulL.css("line-height", (20+addh)+"px");
			}
		}
		</script>
		
		<h1>Startseite</h1>
		<h2 id="important_info">Fehler bei Zitaten behoben, Lehrer können nun zitiert werden.</h2>
		<table cellspacing="0">
			<tr>
				<th class="br">Neuste Aktivität Schüler</th>
				<th class="b">Termine</th>
			</tr>
			<tr id="row_0">
				<td id="news_0" class="r" data-count="10"><ul><div id="news_0_container_actual"><?php news(0, -1, 10, $rootfolder); ?></div></ul></td>
				<td id="news_4" align="center" data-count="10"><img src="<?php echo $rootfolder; ?>images/construction.png"><div id="news_4_container_actual"></div></td>
			</tr>
			<tr>
				<td id="row_0_br" class="bt" colspan="2">
					<div onclick="loadmore(this)" id="row_0_button" class="buttonlink" title="Mehr laden">
						<a>Mehr Laden<img src="images/plus.png"></a>
					</div>
				</td>
			</tr>
		</table>
		<br><br>
		<table cellspacing="0">
			<tr>
				<th class="br">Neuste Aktivität Lehrer</th>
				<th class="b">Man munkelt aktuell, ...</th>
			</tr>
			<tr id="row_1">
				<td id="news_1" class="r" data-count="10"><ul><div id="news_1_container_actual"><?php news(1, -1, 10, $rootfolder); ?></div></ul></td>
				<td id="news_3" data-count="10"><ul><div id="news_3_container_actual"><?php news(3, -1, 10, $rootfolder); ?></div></ul></td>
			</tr>
			<tr>
				<td id="row_1_br" class="bt" colspan="2">
					<div onclick="loadmore(this)" id="row_1_button" class="buttonlink" title="Mehr laden">
						<a>Mehr Laden<img src="images/plus.png"></a>
					</div>
				</td>
			</tr>
		</table>
		<br><br>
		<table cellspacing="0">
			<tr>
				<th class="br">Neuste Zitate</th>
				<th class="b">Neuste Bilder</th>
			</tr>
			<tr  id="row_2">
				<td id="news_2" class="r" data-count="10"><ul><div id="news_2_container_actual"><?php news(2, -1, 10, $rootfolder); ?></div></ul></td>
				<td id="news_5" align="center" data-count="10"><img src="<?php echo $rootfolder; ?>images/construction.png"><div id="news_5_container_actual"><?php  ?><div></td>
			</tr>
			<tr>
				<td id="row_2_br" class="bt" colspan="2">
					<div onclick="loadmore(this)" id="row_2_button" class="buttonlink" title="Mehr laden">
						<a>Mehr Laden<img src="images/plus.png"></a>
					</div>
				</td>
			</tr>
		</table>
		
<?php 
include($_SERVER['DOCUMENT_ROOT'].$rootfolder."layout/footer.php");
?>