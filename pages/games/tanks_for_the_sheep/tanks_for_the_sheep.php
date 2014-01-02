<?php
$page_title = "Tanks For The Sheep";
$to_root = "../../";
include_once $to_root."header.inc";
$unity_game_file = "TanksForTheSheep.unity3d";
$game_width = 1024;
$game_height = 600;
include_once $to_root."/php/unity_game_header.inc";

?>
	<style type="text/css">
		#unityPlayer{
			position: relative;
			right: 135px;
			width: 1048px;
			padding-left: 8px;
			padding-right: 8px;
			border: solid 4px #bcbcbc;
			border-radius: 4px;
			background-color: #000000;
		}
	</style>
</head>
	<body>
		<?php include_once $to_root."heading.inc";?>		
		<div id="wrapper">
			<?php include_once $to_root."navigation.inc"; ?>
			<div id="main">
				<center>
				<p class="header"><span>Unity Web Player | </span>TanksForTheSheep</p>
				<div class="content">
					<div id="unityPlayer">
						<div class="missing">
							<a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now!">
								<img alt="Unity Web Player. Install now!" src="http://webplayer.unity3d.com/installation/getunity.png" width="193" height="63" />
							</a>
						</div>
						<div class="broken">
							<a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now! Restart your browser after install.">
								<img alt="Unity Web Player. Install now! Restart your browser after install." src="http://webplayer.unity3d.com/installation/getunityrestart.png" width="193" height="63" />
							</a>
						</div>
					</div>
				</div>
				<p class="footer">&laquo; created with <a href="http://unity3d.com/unity/" title="Go to unity3d.com">Unity</a> &raquo;</p>
				</center>
				<p><b>Guide:</b><br/>
					<b>Initial loading takes quite a while and may freeze the browser!</b> <br />
					Please be patient. I didn't have time to fix the bug before submitting this assignment.. <br />
					I plan to fix this by reducing the number of nav nodes.. or magically adding multi-threading (or not..) <br />
					Once in game, ignore the green balls, but left click to select sheep, and right click to move them <br />
					Try to position them to attack the enemy sheep with guns <br />
				</p>
			</div>
			<div id = "footer">
				<?php include_once $to_root.'footer.inc';?>
			</div>
		</div>
	</body>
</html>