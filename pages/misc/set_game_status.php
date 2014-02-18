<!DOCTYPE HTML>
<?php
	
	// Input variables - game_category, game_name, status
	if(isset($_GET['game_category'] && isset($_GET['game_name'] && isset($_GET['status'])){
		$game_category = explode($_GET['game_category'];
		$game_to_set = $_GET['game_name'];
		$game_change = $_GET['status'];
		
		$games_lists = array(array("new", "linux", $current_new_linux),
				array("new", "win", $current_new_win),
				array("fav", "linux", $current_fav_linux),
				array("fav", "win", $current_fav_win),
				array("other", "linux", $current_other_linux),
				array("other", "win", $current_other_win));
		
		$con = mysqli_connect("192.168.1.100", "mc_logger", "mc_logger1337", "minecraft_player_data");
		// Check connection
		if (mysqli_connect_errno($con)) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		} else {
			// Do MySQL Magicks here
			
			// First, convert category name for current games
			$current_category = strtolower($game_category);
			$current_category = "Current_$current_category";
			
			//If adding game to played list
			if($game_change == "add"){
				
				//Add if game not already on played list
				// "INSERT INTO {0}(GameName)\nSELECT * FROM (SELECT '{1}') AS tmp\nWHERE NOT EXISTS (\n\t SELECT GameName FROM {0} WHERE GameName = '{1}'\n) LIMIT 1"
				$add_query = "INSERT INTO Played_games(GameName) SELECT * FROM (SELECT $game_name"
				//$add_command = mysqli_query($con
				
			
			}
			
		}
		
		
		
		
		
		
		
		
		
		/* Old text-file based code
		// Open all the things
		$current_games_dir = "./pages/misc/games_lists/current_games/";
		$current_new_linux = file($current_games_dir . "new-linux.txt");
		$current_new_win = file($current_games_dir . "new-win.txt");
		$current_fav_linux = file($current_games_dir . "fav-linux.txt");
		$current_fav_win = file($current_games_dir . "fav-win.txt");
		$current_other_linux = file($current_games_dir . "other-linux.txt");
		$current_other_win = file($current_games_dir . "other-win.txt");
		
		$played_games_filename = "./pages/misc/games/games_lists/played-games.txt";
		
		if($game_change == "add"){
			$found = false;
			$new_games_list = "";
			// loop through each game list file and look for the right one containing the game we want to modify
			foreach($games_lists as $game_list){
			
				// *** Game category matches !!*** (0 - new/fav/other, 1 - platforms)
				
				if($game_list[0] == $game_category[0] && $game_list[1] == $game_category[1]){
				
					// Loop through all games in current games list file
					foreach($game_list[2] as $game){
						
						// If found game, set found to true for removal later
						if($game == $game_to_set){
							$found = true;
						} else {
							// Add to new list in case we're removing
							$new_games_list =  $new_games_list . $game . "\n";
						} 
					}
				}
			}
			// If game was found on current games list, add to played and remove from current by replaceing current list
			// With new list without current game
			if($found){
				// Check if not accidentally already on the list
				$played_games_file = file($played_games_filename);
				$already_added = false;
				foreach($played_games_file as $game){
					if($game == $game_name){
						$already_added = true;
					}
				}
				
				if($already_added == false){
					//Append current game to played list
					file_put_contents($played_games_filename, $game_name);
				
					file_put_contents($
				
				
				
			
		}
		8?
							
						
						
				
	
	

