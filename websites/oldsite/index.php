<?php
$page_title = "Beefy Server";
$to_root = "./";
include_once $to_root."header.inc";
?>
</head>
	<body>
		<?php include_once $to_root."heading.inc";?>		
		<div id="wrapper">	
			<?php include_once $to_root."navigation.inc";?>
			<div id="main">
				<h3><b>Welcome to Beefy Server!</b></h3>
				<p>For the much-welcomed recent visitors, MINECRAFT IS BACK UP! (Finally.. Thanks bukkit devs!)</p>
				<p>New site is much better and will eventually replace this hopefully soon</p>
				<p>This website is running on a home server for random fun experiments / mad science, so this site may change frequently.</p>
				<p><b>Public services for peoples to enjoy:</b></p>
				<p>Minecraft Server on port 25565 (See Minecraft page for details)</p>
				<p>Subsonic on port 4040 u: guest pw: 12345</p>		
				<h3>This Week's Randomly-Chosen Games to Play</h3>
				<a href="./newsite/">Preview of new site</a><br/>
				Generated by a <a href="<?php echo $to_root;?>python/randomgame.py">Python script</a>, weekly crontab command, and text files.
<?php
					$games_list = file("/home/leo/public_html/txt/this_weeks_casual_games.txt");
					echo "\t\t\t\t<ul>\n";
					foreach ($games_list as $number => $game_line) {
						echo "\t\t\t\t\t<li>".substr($game_line, 0, strlen($game_line)-2);
						if($number == 1){
							echo "</li>";
						} else {
							echo "</li>\n";
						}
						
					}
					echo "\n\t\t\t\t</ul>\n";?>
			</div>
			<div id = "footer">
				<?php include_once $to_root.'footer.inc';?>
			</div>
		</div>
	</body>
</html>
