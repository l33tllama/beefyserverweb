<!DOCTYPE HTML>
<html>
	<head>
		<script type='text/javascript' src="./js/jquery-1.9.1.min.js"></script>
	</head>
	<body>
		<div id = "heading">
			<h1>Game Selector</h1>
		</div>
		<div id="main">
			<p>This page is for Leo only! Do not press buttons, you might break something.</p>
			
			<button id = "game_changer">Finished Last Game, Give Me Another!</button>
			<button id = "clear_games">Debug - Clear All Games</button>
			<br/>
			New Game: <p id="new_game">New Game Goes Here</p>
			<h3>Played Games:</h3>
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
			<script>
				var game = "broke";
				$("#game_changer").click(function(event){
					$.get("/php/get_games_list.php",{	
					}, function(resp){
						game = resp;
						$("#new_game").text(game);
					});
					
					$.get("/txt/sp_played_games.txt",{
					}, function(resp){
						var games = resp.split("\n");
						var output = "";
						for (var i = 1; i < games.length-1; i++){
							output += ("<li>" + games[i] + "</li>"); 	
						}
						$("#played_games").html(output);
					});
					
				});
				$("#clear_games").click(function(event){
					$.post("/php/clear_sp_games_list.php", {
					}, function(resp){
						$("#new_game").text("New Game Goes Here");
						
					});
					$.get("/txt/sp_played_games.txt",{
					}, function(resp){
						var games = resp.split("\n");
						var output = "";
						for (var i = 1; i < games.length-1; i++){
							output += ("<li>" + games[i] + "</li>"); 	
						}
						$("#played_games").html(output);
						
					});
				});
			</script>
		</div>
	</body>
</html>