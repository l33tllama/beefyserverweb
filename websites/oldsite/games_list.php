<?php
$page_title = "My Games";
$to_root = "./";
include_once $to_root."header.inc";
?>
		<link  rel="stylesheet" type="text/css" href="./styles/my_games_style.css"></style>
	</head>
	<body>
		<?php include_once $to_root."heading.inc";?>		
		<div id="wrapper">
			<?php include_once $to_root."navigation.inc"; ?>
			<div id="main">
				<h class = "main_heading"><b>Games That I've Made</b></h>
				<table>
				<?php include_once 'my_games.inc'; ?>
				<?php 
				$game_id = 0;
				foreach ($games as $game_details) {
					if($game_id % 2 == 0){
						echo '<tr>';
					}
					echo "\n\t\t\t\t".'<td>'."\n\t\t\t\t\t";
					echo '<div class="game_item">';
					echo "\n\t\t\t\t\t\t\t";
					echo '<h class = "game_heading">'.$game_details[0].'</h><br/><br/>';
					echo "\n\t\t\t\t\t\t\t";
					echo '<center><div class="screenshot_wrapper"><img src = "'.$game_details[1].'" alt="'.$game_details[0].' screenshot" class="game_screenshot"/></div></center><br/>';
					echo "\n\t\t\t\t\t\t\t";
					echo '<a class = "play_button" href="'.$game_details[2].'">Play '.$game_details[0].' Now!</a><br/>';
					echo "\n\t\t\t\t\t\t\t";
					echo '<h class = "game_desc">'.$game_details[3].'</h>';
					echo "\n\t\t\t\t\t";
					echo '</div>';
					echo "\n\t\t\t\t";
					echo '</td>';
					if($game_id % 2 != 0){
						echo "\n\t\t\t\t".'</tr>';
					}
					$game_id = $game_id + 1;
				}
				?>
				
				</table>
				<center><img src="./images/400px-Gaben.jpg" width = "500" align="center"/></center>
			</div>
			<div id = "footer">
				<?php include_once $to_root.'footer.inc';?>
			</div>
		</div>
	</body>
</html>