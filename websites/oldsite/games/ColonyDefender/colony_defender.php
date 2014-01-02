<?php
$page_title = "Colony Defender";
$to_root = "../../";
include_once $to_root."header.inc";
?>
</head>
	<body>
		<?php include_once $to_root."heading.inc";?>		
		<div id="wrapper">
			<?php include_once $to_root."navigation.inc"; ?>
			<div id="main">
				<center>
				<embed src="ColonyDefender.swf" quality="high" 
				width="640" height="480" name="KXG164_Game" 
				loop="false"
				quality="high"
				type="application/x-shockwave-flash"
				pluginspage="http://www.adobe.com/go/getflashplayer">
				</embed></center>
				<p><b>Guide:</b><br/>
					Use WASD to move, <br/>
					left click to fire. <br/>
					Hold shift to lock ship in place whilst moving around map (dodgy, I know..)
				</p>
			</div>
			<div id = "footer">
				<?php include_once $to_root.'footer.inc';?>
			</div>
		</div>
	</body>
</html>