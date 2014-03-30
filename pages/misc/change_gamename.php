<?php
	if(isset($_GET['gamename']) && isset($_GET['new_gamename'])){
		$old_game_name = $_GET['gamename'];
		$new_game_name = urldecode($_GET['new_gamename']);
		
		// Generatee new cleaned name
		$new_game_name_clean = str_replace(" ", "-", strtolower(preg_replace('/[^a-z\d ]/i', '', $new_game_name)));
		
		// Connect to MongoDB
		$mongo_conn = new Mongo('localhost');
		
		// Connect to games DB
		$games_lists = $mongo_conn->games_lists;
		
		// Get list of unplayed games
		$unplayed_games = $games_lists->unplayed_puap_games;
		
		// Find one game that matches the gamename
		$game_to_udpate = $unplayed_games->findOne(array("GameNameClean" => $old_game_name));
		if($game_to_udpate != null){		
			// Update game name
			$unplayed_games->update(
									array("GameNameClean" => $old_game_name), 
									array('$set' => array("GameName" => $new_game_name, "GameNameClean" => $new_game_name_clean)));
			echo "Game hopefully updated!<br/>";
			echo "New game name: $new_game_name <br/>";
			echo "Ne game name cleaned: $new_game_name_clean <br/>";
		} else {
			echo "Game not found: $old_game_name<br/>";
		}
		

// 		print_r($game_to_udpate);
	} else {
		echo "gamename and new_gamename not set!";
	} 
?>