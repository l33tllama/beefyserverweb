<?php
try{
	/*
     * filtering an array
     */
    function filter_by_value ($array, $index, $value){
        if(is_array($array) && count($array)>0) 
        {
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];
//                 echo "$index value: " . $temp[$key] .", $value<br/>";
                
                if ($temp[$key] == $value){
                    $newarray[$key] = $array[$key];
                }
            }
          }
      return $newarray;
    } 

	/* Outputs
	 * "OK_"
	 * 		":STARRED" - changed starred status
	 * "ERR"
	 *		":TOO_MANY_STARRED" - too many starred
	 *		":NOT_FOUND" - game not found on current games list
	 *		":INPUT_VAR_UNSET" - one or more input vars unset
	 */
	if(isset($_GET['gamename']) && isset($_GET['starred']) && isset($_GET['username']) && isset($_GET['session_id'])){
		$game_name = $_GET['gamename'];
		$starred = $_GET['starred'];
		$starred = $starred == "false" ? false : true;
		$username = $_GET['username'];
		$session_id = $_GET['session_id'];
		$session_id = $_COOKIE['session_id'];
		
		// Maximum number of starred games at one time (to save space and increase chances of playing games)
		$MAX_STARRED = 2;
		
		// MongoDB connection
		$mongo_conn = new Mongo('localhost');
		
		// Connect to games DB
		$games_lists = $mongo_conn->games_lists;
		
		// Get user's DB
		$user_acct = $games_lists->users->findOne(array("username" => $username));
		
		// Check if session ID is correct (no need to set a new one..)
		if($user_acct != null && ($user_acct['session_id'] == $session_id)){
			//echo "User $username found and sessionID correct<br/>\n";
			
 			// Get games list
 			$user_game_list = $user_acct['steam_puap_games'];
 			$num_starred = 0;
 			
 			// Count amount of starred games
 			$starred_games = filter_by_value($user_game_list, 'Starred', $game_name);
 			$num_starred = count($starred_games);
 			if($num_starred == 0){
				//echo "No starred games :(<br/>";
			} else {
				//echo "Starred games: ";
				//print_r($starred_games);
				//echo "<br/>";
			}
			
			
			// Find user's current games and add up starred count
 			$current_games = filter_by_value($user_game_list, 'PlayedState', 'current');
 			
 			foreach($current_games as $curr_game){
				if($curr_game['Starred'] == true){
					$num_starred =  $num_starred + 1;
					//echo "Found starred game: ". $curr_game['GameName'] . "<br/>";
				} else {
					//echo "Game " . $curr_game['GameName'] . " is  not starred.<br/>";
				}
 			}
 			
 			//echo "Found $num_starred starred games<br/>";
 			
 			// Find the game to star/unstar if there are <=2 games starred
 			$new_users_games = array();
 			$starred = "Nothing starred";
 			$not_too_many = true;
 			foreach($user_game_list as $user_game){
				
				// Looking at current games
				if($user_game['PlayedState'] == 'current'){
				
					// Found the game we want to star
					if($user_game['GameNameClean'] == $game_name){
						//echo "Trying to star $game_name<br/>";
						
						// Checking if not too many games starred, or if the game is already starred (development)
						if(($num_starred <= $MAX_STARRED || $user_game['Starred'] == true)){
							if($user_game['Starred'] == false){
								$user_game['Starred'] = true;
								$starred = "STARRED";
								//echo "changing starred status for " . $user_game['GameName'] . " to ";
								//echo $user_game['Starred'] ? "True" : "False";
								//echo "<br/>";
							} else {
								$user_game['Starred'] = false;
								$starred = "UN-STARRED";
								//echo "changing starred status for " . $user_game['GameName'] . " to false<br/>";
							}
								
						} else {
							echo "ERR: Too many starred<br/>";
							$not_too_many = false;
							//echo "Can't star/unstar " . $user_game['GameName'] . "<br>";
						}
					}
				}
				array_push($new_users_games, $user_game);
 			}
 			if($not_too_many){
				//echo "New users games: <br/>";
				foreach($new_users_games as $new_game){
					if($new_game['PlayedState'] == "current"){
						//echo "Name:    " . $new_game['GameName']. "<br/>";
						//echo "Starred: ";
						//echo $new_game['Starred'] ? "True" : "False";
						//echo "<br/>";
					}
				}
				// update user's games list with new list that has the new starred game..
				$games_lists->users->update(array("username" => $username), array('$set' => array('steam_puap_games' => $new_users_games)));
				//print_r($new_users_games);
				echo "OK_$starred-$game_name<br/>";
			}
			
		} else {
			echo "ERR: Invalid session ID. (sent: $session_id / stored: " . $user_acct['session_id']. "<br/>";
		} 
				
		$mongo_conn->close();
	} else {
		// Not enough input variables set
		echo "ERR:INPUT_VAR_UNSET<br/>\n";
	}
} catch (MongoConnectionException $e){
	echo $e->getMessage();
}?>