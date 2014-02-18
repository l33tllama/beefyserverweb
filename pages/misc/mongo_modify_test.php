<?php
	echo "Starting.<br/>";
function show_records($num_results, $cursor){
	// How many docs found
	echo "Found $num_games record(s). <br/>\n";
	if($num_results > 0){
		// Loop over results
		foreach($cursor as $obj){
			echo "Game Nume Full: " . $obj['GameName'] . "(cleaned: " . $obj['GameNameClean'] . ", catg: " . $obj['Category'] . ", plat: " . $obj['Platform'] . ") <br/>\n";
			echo "<br/>\n";
		}
	} else {
		echo "Error! No games found :(<br/>\n";
	}
}

function get_first_item($num_results, $cursor){
	if($num_results > 0){
		foreach($cursor as $obj){
			return $obj;
		}
	} else {
		return array("GameName" => "Not found", "GameNameClean" => "not-found");
	}
}
try{
	if(isset($_GET['gamename']) && isset($_GET['operation'])){
		$gamename = $_GET['gamename'];
		$operation = $_GET['operation'];
		// MongoDB connection
		$mongo_conn = new Mongo('localhost');
		
		// Connect to games DB
		$games_lists = $mongo_conn->games_lists;
		
		// Testing 'New_linux'
		$played_games = $games_lists->played_puap_games;
		$unplayed_games = $games_lists->unplayed_puap_games;
		$current_games = $games_lists->current_puap_games;
		
		// Find game by cleaned game name 
		$game_to_change_query = array('GameNameClean' => $_GET['gamename']);
		
		// If adding to played
		if ($operation == "add") {				
			echo "Adding $gamename to played games.<br/>";
			// Find game from unplayed list
			$upl_cursor = $unplayed_games->find($game_to_change_query);
			
			// Find number of records
			$num_games = $upl_cursor->count();
			
			$found_game_item = get_first_item($num_games, $upl_cursor);
			if ($found_game_item['GameName'] != "Not found") {
				echo "Found unplayed game: '" . $found_game_item['GameName'] . "' (category: " . $found_game_item['Category'] . ", platform: " . $found_game_item['Platform'] . ")<br/>";
				
				// Check if not alread on the list (some sort of glitch / testing)
				$played_cursor = $played_games->find($game_to_change_query);
				$count = $played_cursor->count();
				
				if ($count > 0) {
					echo "$gamename already on played list! Might want to remove from unplayed manually!<br/>";
				} else if ($count == 0) {	
					// Not already on played list -> Add to played list!
					$played_games->insert($found_game_item);	
					echo "Added $gamename to played list <br/>";
				}
				
				// Remove from unplayed
				$unplayed_games->remove($game_to_change_query);
				echo "Removed $gamename from unplayed list.<br/>";
				
				// Remove from current
				$current_games->remove($game_to_change_query);
				echo "Removed $gamename from current games.<br/>";
				
			} else {
				echo "Could not find unplayed game: '$gamename' :(<br/>\n";
			}			
			
		} // If removing from played, back to unplayed (changed mind - undo)
		else if ($operation == "remove") {
			echo "Removing $gamename from played list back to unplayed list.<br/>";
			// Find game in played list
			$played_cursor = $played_games->find($game_to_change_query);
			$num_games = $played_cursor->count();
			$found_game_item = get_first_item($num_games, $played_cursor);
			if ($num_games > 0) {
				echo "Found $gamename on played list! with Category: " . $found_game_item['Category'] . ", Platform: " . $found_game_item['Platform'] . ".<br/>";
				
				//Check if not already on unplayed list (glitch/testing)
				$upl_cursor = $unplayed_games->find($game_to_change_query);
				$upl_count = $upl_cursor->count();
				
				// Add back to unplayed games, if not already on there
				if ($upl_count > 0) {
					echo "Game: $gamename already on unplayed list, no need to re-add. Might want to remove from played manually!<br/>";
				} else if ($upl_count == 0) {
					// Not already on unplayed list, add back!
					$unplayed_games->insert($found_game_item);
					echo "Added $gamename BACK to unplayed list!<br/>";
				}
				
				// Add back to current games, ifnot already on there
				$cur_cursor = $current_games->find($found_game_item);
				$cur_count = $cur_cursor->count();
				
				if ($cur_count > 0) {
					echo "Game: $gamename already on current list!<br/>";
				} else if ($cur_count == 0) {
					$current_games->insert($found_game_item);
					echo "Added $gamename back to current games list.<br/>";
				}
				
				// Remove game from played list
				$played_games->remove($game_to_change_query);
				echo "Removed $gamename from played list.<br/>";
			} else {
				echo "Could not find played game: '$gamename' :(<br/>\n";
			}
		} else {
			echo "Invalid Operation!<br/>";
		}
		
		$limbo_record = $upl_cursor;
		var_dump($limbo_record->findOne());
		
		
		/*show_records($num_games, $upl_cursor); */
		/*
		$find_game_query = array('GameName' => 'Puddle');
		$find_new_linux_query = array('Category' => 'new', 'Platform' => 'linux');*/
		/*echo "Current DB: <br/>\n";*/
		//$unplayed_games->remove($find_query);
		
	
		// fetch all games_lists
		
		// $unplayed_games
		
		$mongo_conn->close();
	} else {
		echo "gamename and/or operation not set!<br/>";
	}
	
} catch (MongoConnectionException $e){
	echo $e->getMessage();
}

?>