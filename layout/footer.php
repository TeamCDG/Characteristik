			<br><br>
			<?php if(rand(0,1000)>995) {?>
<pre>
              .,-:;//;:=,
          . :H@@@MM@M#H/.,+%;,
       ,/X+ +M@@M@MM%=,-%HMMM@X/,
     -+@MM; $M@@MH+-,;XMMMM@MMMM@+-
    ;@M@@M- XM@X;. -+XXXXXHHH@M@M#@/.
  ,%MM@@MH ,@%=             .---=-=:=,.
  =@#@@@MX.,                -%HX$$%%%:;
 =-./@M@M$                   .;@MMMM@MM:
 X@/ -$MM/                    . +MM@@@M$
,@M@H: :@:                    . =X#@@@@-
,@@@MMX, .                    /H- ;@M@M=
.H@@@@M@+,                    %MM+..%#$.
 /MMMM@MMH/.                  XM@MH; =;
  /%+%$XHH@$=              , .H@@@@MX,
   .=--------.           -%H.,@@@@@MX,
   .%MM@@@HHHXX$$$%+- .:$MMX =M@@MM%.
     =XMMM@MM@MM#H;,-+HMM@M+ /MMMX=
       =%@M@M#@$-.=$@MM@@@M; %M%=
         ,:+$+-,/H#MMMMMMM@= =,
               =++%%%%+/:-.</pre><?php } ?>
		</div>
		<div id="footer"> 
			<div style="float: left; margin-top: 6px;">
				<?php echo $version; ?> - 
			</div>
			
			<div id="keks" <?php if(isset($_COOKIE['userid'])) { ?> title="Du bleibst auf diesem Gerät eingeloggt, bis du dich ausloggst"<?php } else { ?> title="Du wirst auf diesem gerät ausgeloggt, sobald du die Seite schließt" <?php } ?>>
				Keks: <?php echo isset($_COOKIE['userid'])?"yep":"nope";  if(isset($_COOKIE['userid'])) { ?><img width="20px" height="20px" src="<?php echo $rootfolder; ?>images/cookie.png"> <?php } ?>
			</div>
			<div id="logout" class="buttonlink"  title="Logout">
				<a href="<?php echo $rootfolder."logout.php"; ?>">Logout<img src="<?php echo $rootfolder; ?>images/logout.png"></a>
			</div>
			<div id="settings" class="buttonlink" title="Einstellungen">
				<a href="<?php echo $rootfolder."usercfg/settings/"; ?>">Einstellungen<img src="<?php echo $rootfolder; ?>images/settings.png"></a>
			</div>	
			<div id="snake" class="buttonlink" title="play some snake :)">
				<a href="<?php echo $rootfolder."snake/"; ?>">Snake<img src="<?php echo $rootfolder; ?>images/snake.png"></a>
			</div>
			<div id="snake" class="buttonlink" title="Ein Problem melden">
				<a href="<?php echo $rootfolder;?>feedback/?ref=<?php echo str_replace('index.php', '', str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', $_SERVER['PHP_SELF']))); ?>">Ein Problem melden<img src="<?php echo $rootfolder; ?>images/info.png"></a>
			</div>
		</div>
	</body>
</html>