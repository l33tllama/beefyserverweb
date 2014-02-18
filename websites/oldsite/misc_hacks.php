<?php
$page_title = "Misc Hacks";
$to_root = "./";
include_once $to_root."header.inc";
?>
</head>
	<body>
		<?php include_once $to_root."heading.inc";?>		
		<div id="wrapper">
			<?php include_once $to_root."navigation.inc"; ?>
			<div id="main">
				<h3><b>General Miscellaneous Stuff</b></h3>
				<h4>Played Quick Games</h4>
				<ul>
				<?php
					$played_quick_games = file($to_root."txt/played_games.txt");
					foreach ($played_quick_games as $game){
						echo "<li>$game</li>";
					}
				?>
				</ul>
				<h4>Played Single-Player Games:</h4>
				<ul id = "played_games">
				<?php 
					$played_games = file("./txt/sp_played_games.txt");
					foreach($played_games as $game){
						if($game != "\n"){
							echo "<li>$game</li>";
						}	
					}
				?>
				</ul>
				<a href = "govhackNBNdata/">GovHack - Internet Stats</a>
				<a href = "http://hackerspace.govhack.org/?q=groups/australian-internet-statistics">Team Page</a>
				<h3>About this server</h3>
				CPU: Intel Celeron 847 (Sandy Bridge dual core, 1.1GHz, 17W)<br/>
				Motherboard: ASUS C8H70hm-i-hdmi (Mini ITX with USB 3.0)<br/>
				HDD: 320GB 2.5"<br/>
				Case: Aywun MW-101 (60W)<br/>
				Uptime:<?php echo exec("uptime");?><br/>
				CPU Info: <?php echo exec('procinfo');?><br/>
			</div>
			<div id = "footer">
				<?php include_once $to_root.'footer.inc';?>
			</div>
		</div>
	</body>
</html>
